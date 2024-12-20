<p align="center">
    <a href="https://github.com/AbuseIPDB/laravel" target="_blank">
        <img src=".github/logo.png" alt="AbuseIPDB Logo" />
    </a>
</p>

<p align="center">
    <a href="https://packagist.org/packages/AbuseIPDB/laravel"><img src="https://img.shields.io/packagist/dt/AbuseIPDB/laravel.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/AbuseIPDB/laravel"><img src="https://img.shields.io/packagist/v/AbuseIPDB/laravel.svg" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/AbuseIPDB/laravel"><img src="https://img.shields.io/packagist/l/AbuseIPDB/laravel.svg" alt="License"></a>
</p>


# AbuseIPDB

Package to easily integrate the AbuseIPDB API with your Laravel project.

## Installation

You can install the package via composer:

```shell
  composer require abuseipdb/laravel
```

After installing, you need to add `ABUSEIPDB_API_KEY` to your `.env` file.

```dotenv
ABUSEIPDB_API_KEY=your_key
```

Register on [abuseipdb.com](https://www.abuseipdb.com/) to get a free API key.

> [!WARNING]
> Remember, the AbuseIPDB API keys are to be treated like private keys -- don't have them publicly accessible!

## Usage

### Methods

All methods are static, and can be called using the `AbuseIPDB` facade.

`Check`

Inspect details regarding the IP address queried.

```php
AbuseIPDB::check('127.0.0.1');
```

Optional parameters:
- `maxAgeInDays`: The maximum age of reports to return (1-365), defaults to 30
- `verbose`: Whether to include verbose information (reports), defaults to false

`Report`

Report an IP address to AbuseIPDB. At least one category must be specified.

```php
AbuseIPDB::report('127.0.0.1', categories: [18, 22]);
```

Optional parameters:
- `comment`: An optional comment to include with the report, for example a logged indicator of attack
- `timestamp`: An optional timestamp to include with the report indicating the time of attack

`Reports`

Get the reports for a single IP address (v4 or v6).

```php
AbuseIPDB::reports('127.0.0.1');
```

Optional parameters:
- `maxAgeInDays`: The maximum age of reports to return (1-365), defaults to 30
- `page`: The page number to get for the paginated response, defaults to 1
- `perPage`: The number of reports to get per page (1-100), defaults to 25

`Blacklist`

Get the AbuseIPDB blacklist.

```php
AbuseIPDB::blacklist();
```

Optional parameters:
- `confidenceMinimum`: The minimum confidence score to include an IP in the blacklist (25-100), defaults to 100
- `limit`: The maximum number of blacklisted IPs to return, defaults to 10000
- `plaintext`: Whether to return the blacklist in plaintext (a plain array of IPs), defaults to false
- `onlyCountries`: Only include IPs from these countries (use 2-letter country codes)
- `exceptCountries`: Exclude IPs from these countries (use 2-letter country codes)
- `ipVersion`: The IP version to return (4 or 6), defaults to both

`CheckBlock`

Checks an entire subnet against the AbuseIPDB database.

```php
AbuseIPDB::checkBlock('127.0.0.1/28');
```

Optional parameters:
- `maxAgeInDays`: The maximum age of reports to return (1-365), defaults to 30

`BulkReport`

Report multiple IP addresses to AbuseIPDB in bulk from a csv string.

```php
AbuseIPDB::bulkReport('bulk-report.csv');
```

`ClearAddress`

Deletes your reports for a specific address from the AbuseIPDB database.

```php
AbuseIPDB::clearAddress('127.0.0.1');
```

> [!TIP]
> You can find a complete documentation of the available methods [here](https://docs.abuseipdb.com).

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
