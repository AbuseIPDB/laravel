# AbuseIPDB Laravel API Integration

Package to easily integrate the AbuseIPDB API with your Laravel project.

## Installation

To install using Composer:

    composer require abuseipdb/laravel

Add `ABUSEIPDB_API_KEY` to your `.env` file. You can obtain a free key from the <a href="https://www.abuseipdb.com/" target="_blank">AbuseIPDB website</a> once you've registered an account.

    ABUSEIPDB_API_KEY=your_key

Remember, the AbuseIPDB API keys are to be treated like private keys -- don't have them publicly accessible!

For your application's safety, your `.env` file should never be public or committed to source control.

## Usage

### Using Main Package Functions

The main functions of the package are found in `Abuseipdb\AbuseIPDBLaravel.php`. The recommended way to access these functions is through the included facade. To use it, use:

```php
use AbuseIPDB\Facades\AbuseIPDB;
```

Then the functions can be called statically (non-exhaustive list):

```php
$checkResponse = AbuseIPDB::check('127.0.0.1');
$reportResponse = AbuseIPDB::report('127.0.0.1', categories: [18, 22]);
$reportsResponse = AbuseIPDB::reports('127.0.0.1', maxAgeInDays:10);
$blacklistResponse = AbuseIPDB::blacklist(limit: 1000);
```

This is the recommended method of accessing the functionality of the AbuseIPDB package.

### Quick start for SuspiciousOperationException reporting

This package has support for automatically reporting instances of Symfony's `SuspiciousOperationException` in your Laravel project. To use this functionality, include the following code in your projects `app\Exceptions\Handler.php`:

#### At the top of file

```php
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use AbuseIPDB\AbuseIPDBExceptionReporter;
```

#### Inside of the Handler's `register()` function

```php
 $this->stopIgnoring(SuspiciousOperationException::class);
```

#### Inside of register function's `$this->reportable(function (Throwable $e) {}`

```php
if ($e instanceof SuspiciousOperationException) {
    AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
}
```

#### If your handler's `register()` does not contain the aforementioned `$this->reportable`

```php
 $this->reportable(function (Throwable $e) {
    if ($e instanceof SuspiciousOperationException) {
        AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
    }    
});
```

Now, your project will automatically report to AbuseIPDB when a `SuspiciousOperationException` is thrown.

## Main Functions

This package implements functions to easily interact with endpoints of the AbuseIPDB API. These functions are found in `AbuseIPDBLaravel.php` and can be called statically with the `AbuseIPDB` facade.

### Parent response object

All custom response objects extend the custom AbuseResponse class, which extracts certain headers from the response and makes them accessible programmatically. See below:

```php
use AbuseIPDB\ResponseObjects\AbuseResponse; 
$response = new AbuseResponse($httpResponse);

$response->x_ratelimit_limit
$response->x_ratelimit_remaining;
$response->content_type;
$response->cache_control;
$response->cf_cache_status;
```

When handling responses that call an endpoint with custom responses, include the following at the top of file with requesting code:

```php
use AbuseIPDB\ResponseObjects;
```

Then those object types can be referenced as follows:

```php
ResponseObjects\AbuseResponse
ResponseObjects\CheckResponse
ResponseObjects\ReportResponse
```

etc.

### Endpoint specific methods

As of current version, this package has methods to access all public APIv2 endpoints. When requests are made to these endpoints, endpoint-specific response objects will be returned, which extend the `Illuminate\Http\Client\Response` object. If there is an error, a custom exception will be thrown. Check our <a href="https://docs.abuseipdb.com/" target="_blank">APIv2 documentation</a> for a list of returned values from each endpoint.

### check

The `check()` method makes a request to the <a href="https://docs.abuseipdb.com/#check-endpoint" target="_blank">check endpoint</a> of the AbuseIPDB API. Its signature is

```php
public function check(string $ipAddress, int $maxAgeInDays = 30, bool $verbose = false): ResponseObjects\CheckResponse
```

string `$ipAddress` The IP address to check

int `$maxAgeInDays` The maximum age of reports to return (1-365), defaults to 30

bool `$verbose` Whether to include verbose information (reports), defaults to false

### report

The `report()` method makes a request to the <a href="https://docs.abuseipdb.com/#report-endpoint" target="_blank">report endpoint</a> of the AbuseIPDB API. Its signature is

```php
public function report(string $ip, array|int $categories, string $comment = null, DateTime $timestamp = null): ResponseObjects\ReportResponse
```

string `$ip` The IP address to report

array|int `$categories` Either one or multiple categories to report the IP address for

string|null `$comment` An optional comment to include with the report, for example a logged indicator of attack

DateTime|null `$timestamp` An optional timestamp to include with the report indicating the time of attack

### reports

The `reports()` method makes a request to the <a href="https://docs.abuseipdb.com/#reports-endpoint" target="_blank">reports endpoint</a> of the AbuseIPDB API. Its signature is

```php
public function reports(string $ipAddress, int $maxAgeInDays = 30, int $page = 1, int $perPage = 25): ResponseObjects\ReportsPaginatedResponse
```

string `$ipAddress` The IP address to get reports for

int `$maxAgeInDays` The maximum age of reports to return (1-365), defaults to 30

int `$page` The page number to get for the paginated response

int `$perPage` The number of reports to get per page

### blacklist

The `blacklist()` method makes a request to the <a href="https://docs.abuseipdb.com/#blacklist-endpoint" target="_blank">blacklist endpoint</a> of the AbuseIPDB API. Its signature is

```php
public function blacklist(int $confidenceMinimum = 100, int $limit = 10000, bool $plaintext = false, $onlyCountries = [], $exceptCountries = [], int $ipVersion = null): ResponseObjects\BlacklistResponse|ResponseObjects\BlacklistPlaintextResponse
```

int `$confidenceMinimum` The minimum confidence score to include an IP in the blacklist

int `$limit` The maximum number of blacklisted IPs to return, defaults to 10000

bool `$plaintext` Whether to return the blacklist in plaintext (a plain array of IPs), defaults to false

array `$onlyCountries` Only include IPs from these countries (use 2-letter country codes)

array `$exceptCountries` Exclude IPs from these countries (use 2-letter country codes)

int|null `$ipVersion` The IP version to return (4 or 6), defaults to both

### checkBlock

The `checkBlock()` method makes a request to the <a href="https://docs.abuseipdb.com/#check-block-endpoint" target="_blank">check-block endpoint</a> of the AbuseIPDB API. Its signature is

```php
public function checkBlock(string $network, int $maxAgeInDays = 30): ResponseObjects\CheckBlockResponse
```

string `$network` The network to check in CIDR notation (e.g. 127.0.0.1/28)

int `$maxAgeInDays` The maximum age of reports to return (1-365), defaults to 30

### bulkReport

The `bulkReport()` method makes a request to the <a href="https://docs.abuseipdb.com/#bulk-report-endpoint" target="_blank">bulk-report endpoint</a> of the AbuseIPDB API. Its signature is

```php
public function bulkReport(string $csvFileContents): ResponseObjects\BulkReportResponse
```

string `$csvFileContents` The contents of the csv file to upload

### clearAddress

The `clearAddress()` method makes a request to the <a href="https://docs.abuseipdb.com/#clear-address-endpoint" target="_blank">clear-address endpoint</a> of the AbuseIPDB API. Its signature is

```php
public function clearAddress(string $ipAddress): ResponseObjects\ClearAddressResponse
```

string `$ipAddress` The IP address to clear reports for

## Exceptions

In the event of an error, this package will throw an expection from the `Abuseipdb\Exceptions` namespace. Those exceptions include the following:

```php
InvalidParameterException   // Parameter passed in was invalid for the API.
MissingAPIKeyException  // Your API key in your .env file was not found or invalid.
PaymentRequiredException    // 402 error was thrown by API, indicating feature needs a higher subscription.
TooManyRequestsException    // 429 error was thrown by API, indicating request limit has been exceeded.
UnprocessableContentException   // 422 error was thrown by API, indicating request parameters could not be handled, either missing or incorrect.
UnconventionalErrorException    // Error code other than 402, 422, or 429 was returned by the API.
```

If you'd like to handle these exceptions:

```php
use AbuseIPDB\Exceptions; 

try {
    /* some code */
}
catch(Throwable $e) {
    if($e instanceof Exceptions\TooManyRequestsException) {
        //429 was thrown, do something to address issue
    }
}
```

## Testing (for package developers)

If using the provided test suite, please note that the test will only run error-free once every 15 minutes. The AbuseIPDB API will throw an error if an IP address is reported more than once in a period of 15 minutes, so any report endpoint tests will throw errors. Any tests that do not use the report endpoint will still work without any waiting period.

To add your API key for tests, copy the `.env.testing.example` file to `.env.testing` and fill in the `ABUSEIPDB_API_KEY` and `BAD_IP_TO_TEST` vars (you can get one from the AbuseIPDB site, make sure it has plenty of reports against it). The `ABUSEIPDB_API_BASE_URL` can be left as is.
