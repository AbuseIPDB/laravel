<?php 
 namespace AbuseipdbLaravel\ResponseObjects;

 use AbuseipdbLaravel\ResponseObjects\AbuseResponse;
 use Illuminate\Http\Client\Response as HttpResponse;

class ErrorResponse extends AbuseResponse {

    public string $detail;
    public int $status;
    public object $source;

    public function __construct(HttpResponse $httpResponse){
        
        parent::__construct($httpResponse);
        $data = $this -> object() -> errors[0];
        
        $this -> detail = $data -> detail;
        $this -> status = $data -> status;
        $this -> source = (object)[];
        $this -> source -> parameter = $data -> source -> parameter;

    }
}


?>