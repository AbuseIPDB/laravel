# AbuseIPDB Laravel API Integration 

Package to easily integrate AbuseIPDB API with your Laravel project. 

## Installation

To install using Composer:

    composer require abuseipdb/abuseipdb-laravel

Add `ABUSEIPDB_API_KEY` to your `.env` file. Keys are made on the [AbuseIPDB website](https://www.abuseipdb.com/) for users with accounts. 

```
ABUSEIPDB_API_KEY=your_key
```

## Usage

### Using Main Package Functions:
The main functions of the package are stored in the namespace `Abuseipdb\AbuseIPDBLaravel.php`. For your convenience, this package uses a facade to allow access to the main functions. 
To use the facade, including the following in the file you wish to use it in: 

```php
use AbuseipdbLaravel\Facades\AbuseIPDB;
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
use AbuseipdbLaravel\AbuseIPDBExceptionReporter;

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
If your handler does not contain the aforementioned `$this->reportable`, then include the following:
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

Please refer the [AbuseIPDB API documentation](https://docs.abuseipdb.com/) for more information about the API.

This function will automatically make the correct HTTP Request type, whether the endpoint needs `get`, `post` or `delete`.

`parameters`: Any parameters that need to be passed to the API. This should an array, and will send parameters for any HTTP request method used by the API. 

`acceptType`: Sets the Accept header for the request. By default this will be `application/json`. This may need to be set as `text/plain` if request is being made to the `blacklist` endpoint and plaintext response is desired.

#### Return Type: 
 
This function returns a `Illuminate\Http\Client\Response` object. The documentation for this object is [here](https://laravel.com/api/10.x/Illuminate/Http/Client/Response.html).

### Endpoint specific methods
As of current version, this package also has methods to request the check and report endpoints. 
When requests are made to these endpoints, endpoint-specific response objects will be returned, which extend the 
`Illuminate\Http\Client\Response` object. 

If there is an error, a custom ErrorResponse object will be returned. 
Objects are made to package the responses received by the API, and contain accessible properties that store the data from the response. 

### Response Objects
All custom response objects extend a custom AbuseResponse class, which extracts certain headers from the response and makes them accessible. 

When handling responses that call an endpoint with custom responses, include the following at the top of file with requesting code: 

```php
use AbuseipdbLaravel\ResponseObjects;
```
Then those object types can be referenced as follows:

```php
ResponseObjects\CheckResponse
ResponseObjects\ReportResponse
ResponseObjects\ErrorResponse
```
#### ErrorResponse 

ErrorResponse object is returned when an error occurs. The object contains 3 accessible properties:

`detail`: Explains the reason that the error was thrown. 
`status`: The HTTP error code returned. 
`parameter`: Identifies the source of the error, e.g. "ip". 

Example usage: 

```php
$response = AbuseIPDB::check('127.0.0.1', 31); //error will be thrown since 31 is invalid category
if($response instanceof ResponseObjects\ErrorResponse){
    $statusCode = $response -> status; //example access to object property
}
```

