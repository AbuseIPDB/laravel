<?php 
 namespace AbuseipdbLaravel\ResponseObjects;

 use AbuseipdbLaravel\ResponseObjects\AbuseResponse;
 use Illuminate\Http\Client\Response as HttpResponse;

class ErrorResponse extends AbuseResponse {

    protected string $detail;
    protected int $status;
    protected object $source;

    public function __construct(HttpResponse $httpResponse){
        
        parent::__construct($httpResponse);
        $data = $httpResponse -> object() -> errors[0];
        
        $this -> detail = $data -> detail;
        $this -> status = $data -> status;
        $this -> source = (object)[];
        $this -> source -> parameter = $data -> source -> parameter;

    }
}


?>