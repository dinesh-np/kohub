<?php

namespace DP0\Kohub\Config;

use DP0\Kohub\Contracts\KOHUBConfigContract;
use DP0\Kohub\Enums\APIListEnum;
use DP0\Kohub\Enums\KOHUBEnvironment;
use DP0\Kohub\Services\SimpleFileLogger;
use Psr\Log\LoggerInterface;

abstract class KOHUBConfig implements KOHUBConfigContract
{
    abstract public function getApiKey(): string; // api-key
    abstract public function getPromoSlug(): string; // promo slug

    public function getLogger($filePath = ''): ?LoggerInterface
    {
        if (empty($filePath)) {
            $filePath = getcwd() . '/kohub.log';
        }

        return new SimpleFileLogger($filePath);
    }
    public function isLoggerEnabled(): bool
    {
        return false;
    }

    public function getEndpoint(): string
    {
        if ($this->getEnvironment() === KOHUBEnvironment::STAGING->value) {
            return "https://api.kostaging.io/api/v4";
        }
        return "https://api.kohub.io/api/v4";
    }
    public function getEnvironment(): string
    {
        return "production";
    }

    public function getRetries(): int
    {
        return 0;
    }

    public function getRetryDelay(): int
    {
        return 0;
    }
    public function getCurlTimeOut(): int
    {
        return 30;
    }

    public function getUserAgent(): string
    {
        return "";
    }

    public function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getApiKey(),
        ];
    }

    public function trimSlashFromUrl(string $url)
    {
        return ltrim(rtrim($url, '/'), '/');
    }

    public function getUrlWithSlug()
    {
        return $this->trimSlashFromUrl($this->getEndpoint()) . '/' . $this->trimSlashFromUrl($this->getPromoSlug());
    }

    public function getEntryFromUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::GET_ENTRY_FORM;
    }

    public function getPostEntryFormUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::POST_ENTRY_FORM;
    }

    public function getSubmitVerificationCodeUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::SUBMIT_VERIFICATION_CODE;
    }

    public function getProfileFormUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::GET_PROFILE_FORM;
    }

    public function getPostProfileFormUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::POST_PROFILE_FORM;
    }

    public function getCreateEntrantFormUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::GET_CREATE_ENTRANT_FORM;
    }

    public function getPostCreateEntrantFormUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::POST_CREATE_ENTRANT_FORM;
    }

    public function getCheckEntryCountUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::CHECEK_ENTRY_COUNT;
    }

    public function getEntryFormSuccessUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::GET_ENTRY_FORM_SUCCESS;
    }

    public function getResendMobileVerificationUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::RESEND_MOBILE_VERIFICATION;
    }

    public function getCheckEntrantUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::CHECK_ENTRANT;
    }

    public function getUpdateEntrantMarketingChoiceUrl(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::UPDATE_ENTRANT_MARKETING_CHOICE;
    }

    public function getSignedUrlForReceiptUpload(): string
    {
        return $this->getUrlWithSlug() . APIListEnum::GET_SIGNED_URL_FOR_RECIPT_UPLOAD;
    }
    public function getReceiptUploadFileSizeLimit(): int
    {
        return 2 * 1024 * 1024; // 2MB
    }
    public function getResizeImageUpload(): bool
    {
        return true;
    }
    public function getImageMagickMemoryLimit(): int
    {
        return 256; // 256MB
    }
}
