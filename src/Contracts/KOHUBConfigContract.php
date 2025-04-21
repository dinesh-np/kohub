<?php

namespace DP0\Kohub\Contracts;
use Psr\Log\LoggerInterface;
interface KOHUBConfigContract
{
    public function getApiKey(): string;
    public function getEnvironment():string;
    public function getEndpoint(): string;
    public function getPromoSlug(): string;
    public function getRetries(): int;
    public function getRetryDelay(): int;
    public function getUserAgent(): string;
    public function getHeaders(): array;
    public function getCurlTimeOut(): int;
    public function getEntryFromUrl(): string;
    public function getPostEntryFormUrl(): string;
    public function getSubmitVerificationCodeUrl(): string;
    public function getProfileFormUrl(): string;
    public function getPostProfileFormUrl(): string;
    public function getCreateEntrantFormUrl(): string;
    public function getPostCreateEntrantFormUrl(): string;
    public function getCheckEntryCountUrl(): string;
    public function getEntryFormSuccessUrl(): string;
    public function getResendMobileVerificationUrl(): string;
    public function getCheckEntrantUrl(): string;
    public function getUpdateEntrantMarketingChoiceUrl(): string;
    public function getSignedUrlForReceiptUpload(): string;
    public function getReceiptUploadFileSizeLimit(): int;
    public function getResizeImageUpload():bool;
    public function getImageMagickMemoryLimit():int;
    public function getLogger(): ?LoggerInterface;
    public function isLoggerEnabled(): bool;
}
