<?php

namespace AbuseIPDB\ResponseObjects;

use Illuminate\Http\Client\Response as HttpResponse;

class BlacklistResponse extends AbuseResponse
{
    /* these properties reflect the body of a response from the blacklist endpoint */
    

    public function __construct(HTTPResponse $httpResponse)
    {
        // construct new instance of the parent object
        parent::__construct($httpResponse);

        // grab the body of the response as an object
        $data = $this->object()->data;

        // assign respective properties from response to the object


    }
}
