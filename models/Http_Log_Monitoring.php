<?php
class Http_Log_Monitoring {
    // Handle monitoring each 10 sec
    private string $timer_10_sec;
    private array $dict_sections;
    private int $requests_per_10_sec;
    private int $get_requests_per_10_sec;
    private int $post_requests_per_10_sec;
    private int $success_requests;

    // Handle alert each 2 min
    private string $timer_2_min;
    private int $requests_per_2min;
    private Bool $high_traffic;

    function __construct() {
        $this->timer_10_sec             = "";
        $this->dict_sections            = [];
        $this->requests_per_10_sec      = 0;
        $this->get_requests_per_10_sec  = 0;
        $this->post_requests_per_10_sec = 0;
        $this->success_requests         = 0;

        $this->timer_2_min       = "";
        $this->requests_per_2min = 0;
        $this->high_traffic      = false;
    }

    // methods
    // Handle log stats monitoring each 10 sec
    public function httpLogMonitor(Http_Log $log) {
        try {
            if ($this->timer_10_sec <= $log->getDate()) {
                if (empty($this->timer_10_sec)) {
                    // Initialisation
                    $this->timer_10_sec = $log->getDate();
                } else {
                    // check if exceeds 10 sec
                    $time_exceed = timeout(TIME_LOG_MONITORING, $this->timer_10_sec, $log->getDate());
                    if (!empty($time_exceed)) {
                        // display traffic stats
                        // log time
                        $timestamp = $this->timer_10_sec + TIME_LOG_MONITORING;
                        $log_info = [
                            'time'             => date('m/d/Y H:i:s', $timestamp),
                            'hit section'      => getMaxValueFromAssoArray($this->dict_sections),
                            'requests'         => $this->requests_per_10_sec,
                            'GET requests'     => $this->get_requests_per_10_sec,
                            'POST requests'    => $this->post_requests_per_10_sec,
                            'success requests' => $this->success_requests,
                        ];
                        displayTrafficStat($log_info);
    
                        // reset http log monitoring
                        $this->requests_per_10_sec      = 0;
                        $this->get_requests_per_10_sec  = 0;
                        $this->post_requests_per_10_sec = 0;
                        $this->success_requests         = 0;
                        $this->dict_sections            = [];
                        
                        // set new timestamp
                        $this->timer_10_sec = $log->getDate();
                    }
                }
    
                // add section to dictionary
                $section = $log->getSection();
                if (isset($this->dict_sections[$section])) {
                    $this->dict_sections[$section] += 1;
                } else {
                    $this->dict_sections[$section] = 1;
                }
                $this->requests_per_10_sec ++;
                // check request method
                if ($log->getRequestMethod() === 'GET') {
                    $this->get_requests_per_10_sec ++;
                } else {
                    $this->post_requests_per_10_sec ++;
                }
                // check request status
                if ($log->getStatus() === 200) {
                    $this->success_requests ++;
                }
            }
        } catch (Exception $e) {
            echo "Error monitoring : " . $e->getMessage . "\n";
            exit(1);
        }
    }

    // Handle log alert
    public function httpLogAlert(Http_Log $log, int $threshold) {
        try {
            if ($this->timer_2_min <= $log->getDate()) {
                if (empty($this->timer_2_min)) {
                    // Initialisation
                    $this->timer_2_min = $log->getDate();
                } else {
                    // check if exceeds 2 min (120s)
                    $time_exceed = timeout(TIME_LOG_ALERT, $this->timer_2_min, $log->getDate());
                    if (!empty($time_exceed)) {
                        if ($this->requests_per_2min > ($threshold * TIME_LOG_ALERT)) {
                            // High traffic above traffic threshold
                            $this->high_traffic = true;
                            $timestamp = date('m/d/Y H:i:s', $this->timer_2_min + TIME_LOG_ALERT + 1);
                            displayLogAlert($this->requests_per_2min, $timestamp, $this->high_traffic);
                        }  else if ($this->high_traffic) {
                            // Recovered traffic below traffic threshold
                            $this->high_traffic = false;
                            $timestamp = date('m/d/Y H:i:s', $this->timer_2_min + TIME_LOG_ALERT + 1);
                            displayLogAlert($this->requests_per_2min, $timestamp, $this->high_traffic);
                        }
                        // reset http log alert infos
                        $this->requests_per_2min = 0;
                        // set new timestamp
                        $this->timer_2_min = $log->getDate();
                    }
                }
                $this->requests_per_2min ++;
            }
        } catch (Exception $e) {
            echo "Error alerting : " . $e->getMessage . "\n";
            exit(1);
        }
    }
}