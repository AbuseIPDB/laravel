<?php

namespace AbuseipdbLaravel\ResposneObjects;

use AbuseipdbLaravel\ResposneObjects\AbuseResponse;
use Illuminate\Http\Client\Response as HttpResponse;

class CheckResponse extends AbuseResponse{

    public function __construct(HttpResponse $httpResponse){

        parent::__construct($httpResponse);

        
    }
}
?>