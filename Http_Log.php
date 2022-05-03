<?php
class Http_Log {
    private string $authuser;
    private string $date;
    private string $request;
    private int $status;
    private int $bytes;

    function __construct(array $log_info) {
        if (!empty($log_info) && count($log_info) == 7) {
            $this->authuser = $log_info[2];
            $this->date     = $log_info[3];
            $this->request  = $log_info[4];
            $this->status   = intval($log_info[5]);
            $this->bytes    = intval($log_info[6]);
        }
    }
    
    // getter
    public function getAuthUser() {
        return $this->authuser;
    }
    public function getDate() {
        return $this->date;
    }
    public function getRequest() {
        return $this->request;
    }
    public function getStatus() {
        return $this->status;
    }
    public function getBytes() {
        return $this->bytes;
    }

    // get Section from request
    public function getSection() {
        $pattern  = "(/\w+)";
        $section = '';
        if(preg_match($pattern, $this->request, $matches)) {
            // var_dump($matches);
            $section = $matches[0];
        }
        return $section;
    }

    // get Request methods
    public function getRequestMethod() {
        $method = strtok($this->request, " ");
        return $method;
    }
}