<?php

namespace DP0\Kohub\Services;

use DP0\Kohub\Contracts\KOHUBConfigContract;
use DP0\Kohub\Exception\KOHUBApiRequestException;
use Exception;
use Psr\Log\LoggerInterface;

class KOHUBService
{
    private KOHUBConfigContract $config;
    private LoggerInterface|null $logger;
    private int $maxRetries;
    private int $retryDelay;
    private array $headers;
    private int $curlTimeOut;

    public function __construct(KOHUBConfigContract $config)
    {
        $this->config = $config;
        $this->logger = $config->getLogger();
        $this->maxRetries = $config->getRetries();
        $this->retryDelay = $config->getRetryDelay();
        $this->headers = $config->getHeaders();
        $this->curlTimeOut = $config->getCurlTimeOut();
    }

    public static function init(KOHUBConfigContract $config): self
    {
        return new self($config);
    }

    private function log(string $message, array $context = []): void
    {
        if(!$this->config->isLoggerEnabled()){
           return;
        }
        if ($this->logger) {
            $this->logger->info($message, $context);
        } else {
            error_log($message . (!empty($context) ? ' ' . json_encode($context) : ''));
        }
    }

    public function getEntryFrom(): string
    {
        $url = $this->config->getEntryFromUrl();
        $response = $this->sendRequest($url, '{}', 'GET');
        $this->log(__METHOD__ . ' response', ['response' => $response]);
        return $response;
    }

    public function submitEntry(array $payload): string
    {
        $response = $this->sendRequest($this->config->getPostEntryFormUrl(), json_encode($payload), 'POST');
        $this->log(__METHOD__ . ' response', ['response' => $response]);
        return $response;
    }

    public function submitProfile(array $payload): string
    {
        $response = $this->sendRequest($this->config->getPostProfileFormUrl(), json_encode($payload), 'POST');
        $this->log(__METHOD__ . ' response', ['response' => $response]);
        return $response;
    }

    public function getUrlForReceiptUpload(array $fileDetails): string
    {
        $response = $this->sendRequest($this->config->getSignedUrlForReceiptUpload(), json_encode($fileDetails), 'POST');
        $this->log(__METHOD__ . ' response', ['response' => $response]);
        return $response;
    }

    public function putReceiptContentToSignedUrl(string $signedUrl, string $filePath, array $headers = []): string
    {
        $file = file_get_contents($filePath);
        $file = $this->compressImage($file, mime_content_type($filePath));
        $headers = array_merge($this->headers, $headers);
        $response = $this->sendRequest($signedUrl, $file, 'PUT', $headers);
        $this->log(__METHOD__ . ' response', ['response' => $response]);
        return $response;
    }

    public function uploadReceipt(string $filePath, string $fileName = ''): array
    {
        if (!file_exists($filePath)) {
            throw new KOHUBApiRequestException("File not found: $filePath", 422);
        }

        if (!is_readable($filePath)) {
            throw new KOHUBApiRequestException("File not readable: $filePath", 422);
        }

        $fileDetails = [
            'fileSize' => filesize($filePath),
            'fileName' => $fileName ?: basename($filePath),
        ];

        $response = json_decode($this->getUrlForReceiptUpload($fileDetails), true);

        if (!isset($response['data']['signedURL'], $response['data']['objectURL'])) {
            throw new KOHUBApiRequestException("Invalid upload response", 422, $response);
        }

        $this->putReceiptContentToSignedUrl(
            $response['data']['signedURL'],
            $filePath,
            [
                'Content-Type'   => 'application/octet-stream',
                'Content-Length' => $fileDetails['fileSize'],
            ]
        );

        $this->log(__METHOD__ . ' completed', ['objectURL' => $response['data']['objectURL']]);
        return $response;
    }

    public function createEntry(array $payLoad, $filePath = '', $fileName=''): string
    {
      if($filePath && file_exists($filePath)){
          $response = $this->uploadReceipt($filePath, $fileName);
          $payLoad['values']['receipt_upload'] = $response['data']['objectURL'];
      }
      $response = $this->submitEntry($payLoad);
      $responseData = json_decode($response, true);
        //check profile requires
        if (isset($responseData['data']['requiresProfile'])) {
            $payLoad['values']['entryUuid'] = $responseData['data']['entryUuid'];
            return $this->submitProfile($payLoad);
        }
        return $response;
    }

    public function sendRequest(string $url, string $payload = '{}', string $method = 'POST', ?array $customHeaders = []): string
    {
        $attempt = 1;
        $headers = array_merge($this->headers, $customHeaders);

        do {
            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => strtoupper($method),
                CURLOPT_HTTPHEADER     => $this->formatCurlHeaders($headers),
                CURLOPT_TIMEOUT        => $this->curlTimeOut,
            ]);

            if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            }

            $response = curl_exec($ch);
            $error = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($error || $httpCode < 200 || $httpCode >= 300) {
                $this->log("Request failed", compact('url', 'httpCode', 'error', 'attempt', 'response'));

                if ($attempt >= $this->maxRetries) {
                    throw new KOHUBApiRequestException("Failed after {$attempt} attempts", $httpCode, [
                        'code'     => $httpCode,
                        'url'      => $url,
                        'message'  => $error ?: "HTTP $httpCode",
                        'attempt'  => $attempt,
                        'response' => $response,
                    ]);
                }

                usleep($this->retryDelay * (2 ** $attempt) * 1000);
                $attempt++;
            } else {
                $this->log("Request succeeded", compact('url', 'httpCode', 'response'));
                return $response;
            }
        } while ($attempt <= $this->maxRetries);

        throw new KOHUBApiRequestException("Request failed after retries.");
    }

    public function compressImage(string $imageData, string $mimeType): string
    {
        $maxSize = $this->config->getReceiptUploadFileSizeLimit();
        $resizeImage = $this->config->getResizeImageUpload();
        $input = tempnam(sys_get_temp_dir(), 'imgin');
        $output = tempnam(sys_get_temp_dir(), 'imgout');

        try {
            file_put_contents($input, $imageData);
            $originalSize = filesize($input);

            if ($originalSize <= $maxSize) {
                $this->log(__METHOD__ . ' skipped compression', ['size' => $originalSize, 'maxSize' => $maxSize]);
                return file_get_contents($input);
            }
            $imagick = new \Imagick($input);
            $imagick->setResourceLimit(\Imagick::RESOURCETYPE_MEMORY, $this->config->getImageMagickMemoryLimit());
            $quality = 95;

            do {
                if (file_exists($output)) {
                    unlink($output);
                }

                $clone = clone $imagick;

                if (in_array($mimeType, ['image/jpeg', 'image/jpg'])) {
                    $clone->setImageFormat('jpeg');
                    $clone->setImageCompressionQuality($quality);
                } elseif ($mimeType === 'image/png') {
                    $clone->setImageFormat('png');
                    $clone->stripImage();
                    $clone->setImageCompressionQuality($quality);
                    $clone->setOption('png:compression-level', 9);
                    $clone->setOption('png:compression-strategy', 1);

                    if ($quality < 80) {
                        $colors = max(32, round($quality * 2.55));
                        $clone->quantizeImage($colors, \Imagick::COLORSPACE_RGB, 0, false, false);
                    }

                    if ($quality < 60 && $resizeImage) {
                        $scale = $quality / 60;
                        $newWidth = round($clone->getImageWidth() * $scale);
                        $newHeight = round($clone->getImageHeight() * $scale);
                        $clone->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1);
                    }
                } else {
                    throw new \Exception("Unsupported image type: $mimeType", 422);
                }

                $clone->writeImage($output);
                $size = filesize($output);
                $this->log(__METHOD__ . " compress attempt", ['quality' => $quality, 'size' => $size]);

                $quality -= ($size > $maxSize * 1.5) ? 10 : 5;
                $clone->destroy();

            } while ($size > $maxSize && $quality > 5);

            if ($size > $maxSize) {
                throw new \Exception("Compressed image still exceeds {$maxSize} bytes", 422);
            }

            $this->log(__METHOD__ . " success", ['finalSize' => $size]);
            return file_get_contents($output);
        } catch (\Exception $e) {
            $this->log(__METHOD__ . " failed", ['error' => $e->getMessage()]);
            throw new \Exception("Image compression failed: " . $e->getMessage(), 422);
        } finally {
            if (isset($imagick)) {
                $imagick->clear();
                $imagick->destroy();
            }
            if (file_exists($input)) unlink($input);
            if (file_exists($output)) unlink($output);
        }
    }

    protected function formatCurlHeaders(array $headers): array
    {
        $formatted = [];

        foreach ($headers as $key => $value) {
            $formatted[] = "$key: $value";
        }

        return $formatted;
    }

}
