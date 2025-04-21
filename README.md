
# KOHUB Service Documentation

## Overview
This PHP service class (`KOHUBService`) interacts with external APIs to handle entries, profiles, file uploads, image compression, and more. It provides a set of functions that can be used to manage receipts and profile entries.


## 📦 Installation

As this package is not released on Packagist, you need to install it directly from GitHub:

Add the GitHub repo to your `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/dinesh-np/kohub.git"
    }
  ]
}
```

```bash
composer require dinesh-np/kohub:dev-main
```

## Config Class Implementation
You can either extend the `KOHUBConfig` class or implement the `KOHUBConfigContract` interface.

### If you extend `KOHUBConfig`:
You must implement the following two functions:
```php
abstract public function getApiKey(): string; // API Key
abstract public function getPromoSlug(): string; // Promo Slug
```

### If you implement `KOHUBConfigContract`:
You must also implement the above two functions with others.

## Usage

### Example of extending `KOHUBConfig`:
```php
use DP0\Kohub\Config\KOHUBConfig;
use DP0\Kohub\Services\KOHUBService;

class TestConfig extends KOHUBConfig {
    public function getApiKey(): string {
        return "your_api_key_here";
    }
    
    public function getPromoSlug(): string {
        return "your_promo_slug_here";
    }
}

$config = new TestConfig();
$service = KOHUBService::init($config);
```

### Example for using `KOHUBService`:
```php
$data = [
    'values' => [
        'mobile' => '+61412345678',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'terms' => 1,
        'marketing' => 1,
        'state' => 'New South Wales',
        'receipt_upload' => 'https://example.com/uploads/receipt123.jpg',
        'address_line_1' => '123 Fake Street',
    ],
    'meta' => [
        'fingerprintId' => 'fpr-xyz-987654',
        'requestId' => 'req-abc-123456',
    ]
];

$service->submitEntry($data);
```

## Service Functions

### 1. `getEntryForm()`
Fetches the entry form from the provided API URL.
```php
$service->getEntryFrom();
```

### 2. `submitEntry()`
Submits an entry to the API using a POST request.
```php
$service->submitEntry($data);
```

### 3. `submitProfile()`
Submits a profile to the API using a POST request.
```php
$service->submitProfile($data);
```

### 4. `getUrlForReceiptUpload()`
Fetches a signed URL for uploading a receipt.
```php
$service->getUrlForReceiptUpload($fileDetails);
```

### 5. `putReceiptContentToSignedUrl()`
Uploads the file content to the provided signed URL. This also supports image compression before uploading.
```php
$service->putReceiptContentToSignedUrl($signedUrl, $filePath);
```

### 6. `uploadReceipt()`
Handles the entire process of uploading a receipt. Includes fetching a signed URL and uploading the file.
```php
$service->uploadReceipt($filePath);
```

### 7. `createEntry($data,$filePath, $fileName)`
Handles the entire process of Creating Entry.
```php
$service->uploadReceipt($data, $filePath, 'test_receipt.jpg');
```

### 8. `compressImage()`
Compresses an image to meet the file size requirements before uploading.
```php
$service->compressImage($imageData, $mimeType);
```

### 9. `sendRequest()`
Generic function to send HTTP requests (GET/POST/PUT) to the provided API URL.
```php
$service->sendRequest($url, $payload, $method);
```

## Logging
The service logs important actions for debugging. If logging is enabled in your configuration, the service logs each API request, response, and other relevant information.
```php
$this->log("Your log message", ['data' => $data]);
```

## Exception Handling
If an error occurs while making a request or during image compression, appropriate exceptions are thrown.
```php
throw new KOHUBApiRequestException("Error message", $statusCode);
```

## Dependencies
- PHP Imagick extension (`ext-imagick`)
- `psr/log` for logging
