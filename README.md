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
The main functions of the package are stored in the namespace `Abuseipdb\AbuseIPDB.php`. For your convenience, this package uses a facade to allow access to the main functions. 
To use the facade, including the following in the file you wish to use it in: 

```
use AbuseipdbLaravel\Facades\AbuseIPDB;
```
Then the functions can be called statically:
```
$response = AbuseIPDB::check('127.0.0.1');
```

These functions will be explained in greater detail later in the documentation. 

### Quick start for SuspiciousOperationException reporting:

This package has support for automatically reporting instances of Symfony's `SuspiciousOperationException` in your Laravel project. To use this functionality, include the following code in your projects `app\Exceptions\Handler.php`:

At the top of file: 

```
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use AbuseipdbLaravel\AbuseIPDBExceptionReporter;

```

Inside of the Handler's `register()` function:

```
 $this->stopIgnoring(SuspiciousOperationException::class);

```

Inside of register function's `$this->reportable(function (Throwable $e) {}`
```

if ($e instanceof SuspiciousOperationException) {
    AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
}

```            
If your handler does not contain the aforementioned `$this->reportable`, then include the following:
 $this->reportable(function (Throwable $e) {
    if ($e instanceof SuspiciousOperationException) {
        AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
    }    
});

```

Now your project will automatically report to AbuseIPDB if a SuspiciousOperationException is thrown. 

