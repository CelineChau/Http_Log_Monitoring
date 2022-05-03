<?php

// import
require_once(__DIR__.'/constants.php');

// methods

// return boolean checking delay (in sec) exceeded
function timeout(int $delay, string $start_time, string $current_time): Bool {
    $diff_time = $current_time - $start_time;
    return $diff_time > $delay;
}

// convert associative array to string format
function mappedImplode(string $glue, array $array, string $symbol = '='): string {
    return implode($glue, array_map(
        function($k, $v) use($symbol) {
            return $k . $symbol . $v;
        },
        array_keys($array),
        array_values($array)
    ));
}

// Output the log stats info
function displayTrafficStat(array $log_infos): void {
    $output = mappedImplode(', ', $log_infos, ' : ');
    printf($output."\n");
}

// Output the log alert
function displayLogAlert(int $hits, string $timestamp, Bool $high_traffic): void {
    if ($high_traffic) {
        printf("High traffic generated an alert - hits = ".$hits.", triggered at ".$timestamp."\n");
    } else {
        printf("Recovered traffic generated an alert - hits = ".$hits.", triggered at ".$timestamp."\n");
    }
}

// Retrieve key of the max value from associative array
function getMaxValueFromAssoArray(array $array): string {
    $value = max($array);
    return array_search($value, $array);
}

// Check input parameters
function checkInputLine(string $line): Bool {
    try {
        $to_array = str_getcsv($line);
        if (count($to_array) !== NB_INPUT_PARAMS) {
            return false;
        }
    } catch (Exception $e) {
        throw $e;
    }
    return true;
}

// Handle log stats monitoring each 10 sec
function httpLogMonitor(Http_Log $log, string &$timer_10_sec, array &$dict_sections,
    int &$requests, int &$nb_get_requests, int &$nb_post_requests, int &$success_requests) {
    if ($timer_10_sec <= $log->getDate()) {
        if (empty($timer_10_sec)) {
            // Initialisation
            $timer_10_sec = $log->getDate();
        } else {
            // check if exceeds 10 sec
            $time_exceed = timeout(TIME_LOG_MONITORING, $timer_10_sec, $log->getDate());
            if (!empty($time_exceed)) {
                // display traffic stats
                // log time
                $timestamp = $timer_10_sec + TIME_LOG_MONITORING;
                $log_info = [
                    'time'             => date('m/d/Y H:i:s', $timestamp),
                    'hit section'      => getMaxValueFromAssoArray($dict_sections),
                    'requests'         => $requests,
                    'GET requests'     => $nb_get_requests,
                    'POST requests'    => $nb_post_requests,
                    'success requests' => $success_requests,
                ];
                displayTrafficStat($log_info);
    
                // reset http log monitoring
                $requests         = 0;
                $nb_get_requests  = 0;
                $nb_post_requests = 0;
                $success_requests = 0;
                $dict_sections    = [];
                $time_exceed      = false;
                
                // set new timestamp
                $timer_10_sec = $log->getDate();
            }
        }

        // add section to dictionary
        $section = $log->getSection();
        if (isset($dict_sections[$section])) {
            $dict_sections[$section] += 1;
        } else {
            $dict_sections[$section] = 1;
        }
        $requests ++;
        // check request method
        if ($log->getRequestMethod() === 'GET') {
            $nb_get_requests ++;
        } else {
            $nb_post_requests ++;
        }
        // check request status
        if ($log->getStatus() === 200) {
            $success_requests ++;
        }
    }
}

// Handle log alert
function HttpLogAlert(Http_Log $log, string &$timer_2_min, int $threshold, int &$hits, Bool &$high_traffic) {
    if ($timer_2_min <= $log->getDate()) {
        if (empty($timer_2_min)) {
            // Initialisation
            $timer_2_min = $log->getDate();
        } else {
            // check if exceeds 2 min (120s)
            $time_exceed = timeout(TIME_LOG_ALERT, $timer_2_min, $log->getDate());
            if (!empty($time_exceed)) {
                if ($hits > ($threshold * TIME_LOG_ALERT)) {
                    // High traffic above traffic threshold
                    $high_traffic = true;
                    $timestamp = date('m/d/Y H:i:s', $timer_2_min + TIME_LOG_ALERT + 1);
                    displayLogAlert($hits, $timestamp, $high_traffic);
                }  else if ($high_traffic) {
                    // Recovered traffic below traffic threshold
                    $high_traffic = false;
                    $timestamp = date('m/d/Y H:i:s', $timer_2_min + TIME_LOG_ALERT + 1);
                    displayLogAlert($hits, $timestamp, $high_traffic);
                }
                // reset http log alert infos
                $hits = 0;
                // set new timestamp
                $timer_2_min = $log->getDate();
            }
        }
        ++ $hits;
    }
}
