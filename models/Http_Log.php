<?php
class Http_Log {
    private string $authuser;
    private string $date;
    private string $request;
    private int $status;
    private int $bytes;

    function __construct(array $log_info) {
        try {
            $this->authuser = $log_info[2];
            $this->date     = $log_info[3];
            $this->request  = $log_info[4];
            $this->status   = intval($log_info[5]);
            $this->bytes    = intval($log_info[6]);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage;
            exit(1);
        }
    }
    
    // getter
    public function getAuthUser(): string {
        return $this->authuser;
    }
    public function getDate(): string{
        return $this->date;
    }
    public function getRequest(): string{
        return $this->request;
    }
    public function getStatus(): int {
        return $this->status;
    }
    public function getBytes(): int {
        return $this->bytes;
    }

    // get Section from request
    public function getSection(): string {
        $pattern  = "(/\w+)";
        $section = '';
        if(preg_match($pattern, $this->request, $matches)) {
            // var_dump($matches);
            $section = $matches[0];
        }
        return $section;
    }

    // get Request methods
    public function getRequestMethod(): string {
        $method = strtok($this->request, " ");
        return $method;
    }
}