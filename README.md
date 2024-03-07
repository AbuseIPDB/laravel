# AbuseIPDB Laravel API Integration 

Package to easily integrate AbuseIPDB API with your Laravel project. 

## Installation

To install using Composer:

    composer require abuseipdb/laravel

Add `ABUSEIPDB_API_KEY` to your `.env` file. Keys are made on the <a href="https://www.abuseipdb.com/" target="_blank">AbuseIPDB website</a> for users with accounts. 

```
ABUSEIPDB_API_KEY=your_key
```

Remember, the AbuseIPDB API keys are to be treated like private keys -- don't have them publicly accessible!

For your application's safety, your `.env` file should never be public or committed to source control.

## Usage

### Using Main Package Functions:
The main functions of the package are stored in the namespace `Abuseipdb\AbuseIPDBLaravel.php`. For your convenience, this package uses a facade to allow access to the main functions. 
To use the facade, including the following in the file you wish to use it in: 

```php
use AbuseIPDB\Facades\AbuseIPDB;
```
Then the functions can be called statically:
```php
$response = AbuseIPDB::check('127.0.0.1');
```

These functions will be explained in greater detail later in the documentation. 

### Quick start for SuspiciousOperationException reporting:

This package has support for automatically reporting instances of Symfony's `SuspiciousOperationException` in your Laravel project. To use this functionality, include the following code in your projects `app\Exceptions\Handler.php`:

At the top of file: 

```php
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use AbuseIPDB\AbuseIPDBExceptionReporter;

```

Inside of the Handler's `register()` function:

```php
 $this->stopIgnoring(SuspiciousOperationException::class);
```

Inside of register function's `$this->reportable(function (Throwable $e) {}`
```php
if ($e instanceof SuspiciousOperationException) {
    AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
}
```            
If your handler's `register()` does not contain the aforementioned `$this->reportable`, then include the following:
```php
 $this->reportable(function (Throwable $e) {
    if ($e instanceof SuspiciousOperationException) {
        AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
    }    
});

```

Now your project will automatically report to AbuseIPDB if a SuspiciousOperationException is thrown. 

## Main Functions

This package implements functions to easily interact with endpoints of the AbuseIPDB API. 
These functions are stored in the `AbuseIPDBLaravel.php` and can be called statically with the `AbuseIPDB` facade. 

### makeRequest()

The `makeRequest` function handles all API requests made through the package. The function's signature is as follows:

```php
public function makeRequest($endpointName, $parameters, $acceptType = 'application/json') : ?Response
```
`makeRequest` accepts 3 parameters, and returns a response of type `Illuminate\Http\Client\Response`.

#### Parameters:

`endpointName`: Name of the AbuseIPDB API endpoint where the request will be made. 
The following endpoints are supported by the API: 

`check, reports, blacklist, report, check-block, bulk-report, clear-address`

Please refer the <a href="https://docs.abuseipdb.com/" target="_blank">AbuseIPDB API Documentation</a> for more information about the API.

This function will automatically make the correct HTTP Request type, whether the endpoint needs `get`, `post` or `delete`.

`parameters`: Any parameters that need to be passed to the API. This should an array, and will send parameters for any HTTP request method used by the API. 

`acceptType`: Sets the Accept header for the request. By default this will be `application/json`. This may need to be set as `text/plain` if request is being made to the `blacklist` endpoint and plaintext response is desired.

#### Return Type: 
 
This function returns a `Illuminate\Http\Client\Response` object. The documentation for this object is <a href="https://laravel.com/api/10.x/Illuminate/Http/Client/Response.html" target = "_blank">here</a>.

### Endpoint specific methods
As of current version, this package also has methods to request the check and report endpoints. 
When requests are made to these endpoints, endpoint-specific response objects will be returned, which extend the 
`Illuminate\Http\Client\Response` object. 

If there is an error, a custom ErrorResponse object will be returned. 
Objects are made to package the responses received by the API, and contain accessible properties that store the data from the response. 

### check() method

The `check()` method makes a request to the check endpoint of the AbuseIPDB API. Its signature is as follows: 

```php
public function check($ipAddress, $maxAgeInDays = null, $verbose = null): ResponseObjects\CheckResponse
```
#### Parameters 

`ipAddress`: IP address to be checked by the API.

`maxAgeInDays`: Optional: Age of reports used to check the IP address. Must be between 1 and 365 if set, default is 30.

`verbose`: Optional: If set, returns full reports array for the IP address.

#### Return Type

Returns a `ResponseObjects\CheckResponse` object. Please refer to documentation below regarding this object.

### report() method 

The `report()` method makes a request to the report endpoint of the AbuseIPDB API. Its signature is as follows: 
```php
public function report($ip, $categories, $comment = null): ResponseObjects\ReportResponse
```
#### Parameters

`ip`: IP address to be reported.

`categories`: Single category or array of categories, provided as numbers between 1 and 30. Please refer to AbuseIPDB's reference on their category numbers <a href="https://www.abuseipdb.com/categories" target="_blank">here</a>.

`comment`: Optional: Include information about the attack, such as an error log message.

#### Return Type

Returns a `ResponseObjects\ReportResponse` object. Please refer to documentation below regarding this object.

### reports() method

The `reports()` method makes a request to the reports endpoint of the AbuseIPDB API. Its signature is as follows:
```php
public function reports(string $ipAddress, int $maxAgeInDays = 30, int $page = 1, int $perPage = 25): ReportsPaginatedResponse
```
#### Parameters

`ipAddress`: The IP address to get reports for

`maxAgeInDays`: Optional: Age of reports used to check the IP address. Must be between 1 and 365 if set, the default is 30.

`page`: Optional: Current page of pagination. Must be at least 1, the default is 1.

`perPage`: Optional: Quantity of results per page. Must be between 1 and 100, the default is 25.

#### Return Type

Returns a `ResponseObjects\ReportResponse` object. Please refer to documentation below regarding this object.

### Response Objects
All custom response objects extend a custom AbuseResponse class, which extracts certain headers from the response and makes them accessible. 

When handling responses that call an endpoint with custom responses, include the following at the top of file with requesting code: 

```php
use AbuseIPDB\ResponseObjects;
```
Then those object types can be referenced as follows:

```php
ResponseObjects\AbuseResponse
ResponseObjects\CheckResponse
ResponseObjects\ReportResponse
ResponseObjects\ReportsPaginatedResponse
```
#### AbuseResponse
The AbuseResponse makes specific headers sent with a response from AbuseIPDB's API more accessible. The following properties are accessible from the object:

```php
use AbuseIPDB\ResposneObjects\AbuseResponse; 
$response = new AbuseResponse($httpResponse);

$response -> x_ratelimit_limit
$response -> x_ratelimit_remaining;
$response -> content_type;
$response -> cache_control;
$response -> cf_cache_status;
```

Since the custom endpoint-specific response objects extend the AbuseResponse object, you may access these properties from the child object. You may also access any method from the `Illuminate\Http\Client\Response` class, such as `headers()` or `status()`.
#### CheckResponse

The CheckResponse object reflects the response data given from making a check response to the API. 
The following properties are included:

```php
use AbuseIPDB\ResponseObjects\CheckResponse; 
$response = new CheckResponse($httpResponse);

$response -> ipAddress;
$response -> isPublic;
$response -> ipVersion;
$response -> isWhitelisted
$response -> abuseConfidenceScore;
$response -> countryCode;
$response -> usageType;
$response -> isp;
$response -> domain;
$response -> hostnames;
$response -> isTor;
$response -> totalReports
$response -> numDistinctUsers;
$response -> lastReportedAt;
$response -> countryName;
$response -> reports 

```

#### ReportResponse

The ReportResponse object reflects the response data given from making a report response to the API. 
The following properties are included:

```php
use AbuseIPDB\ResponseObjects\ReportResponse; 
$response = new ReportResponse($httpResponse);

$response -> $ipAddress;
$response -> $abuseConfidenceScore; 
```

#### ReportsPaginatedResponse

```php
use AbuseIPDB\ResponseObjects\ReportsPaginatedResponse;

$response = new ReportsPaginatedResponse($httpResponse);

$response->total;
$response->page;
$response->perPage;
$response->lastPage;
$response->nextPageUrl;
$response->previousPageUrl;
```

## Exceptions

In the event of an error, this package will throw an expection from the `Abuseipdb\Exceptions` namespace. Those exceptions include the following:

```php
InvalidAcceptTypeException //Accept Type was set to type other than application/json or text/plain.
InvalidEndpointException //Endpoint name provided was not a valid endpoint for the API.
InvalidParameterException //Parameter passed in was invalid for the API.
MissingParameterException //A required parameter for an endpoint was missing.
PaymentRequiredException //402 error was thrown by API, indicating feature needs a higher subscription.
TooManyRequestsException //429 error was thrown by API, indicating request limit has been exceeded.
UnprocessableContentException //422 error was thrown by API, indicating request parameters could not be handled, either missing or incorrect.
UnconventionalErrorException //Error code other than 402, 422, or 429 was returned by the API.
```
To handle these exceptions, use like the following:

```php
    use AbuseIPDB\Exceptions; 

    try {
        /* some code */
    }
    catch(Throwable $e){
        if($e instanceof Exceptions\TooManyRequestsException){
            //429 was thrown, do something to address issue
        }
    }
```

Additionally, be wary of the <a href="https://www.php.net/manual/en/class.argumentcounterror.php" target="_blank">ArgumentCountError</a> that will be thrown if any functions are called with the incorrect number of arguments.

## Testing

If using the provided test suite, please note that the test will only run error-free once every 15 minutes. The AbuseIPDB API will throw an error if an IP address is reported more than once in a period of 15 minutes, so any report endpoint tests will throw errors. Any tests that do not use the report endpoint will still work without any waiting period. 

Also, you will need to manually insert your API key inside of `phpunit.xml` where labeled `Insert Key Here`.


