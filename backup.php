<?php

// import
require_once(__DIR__.'/Http_Log.php');
require_once(__DIR__.'/utils.php');

// args
$shortopts = "";
$filepath = '';
$longopts = array(
    // file path
	"file:",
    // traffic threshold, default = 10
	"traffic-threshold:",
);
$options = getopt($shortopts, $longopts);

// get lines from file
// var_dump($options);
if (isset($options['file'])) {
    // echo 'In option file';
    // check if file exists
    if (file_exists($options['file'])) {
        $filepath = $options['file'];
        // echo ' file exists ';
    } else {
        echo ' [error] please check the file path ';
        exit;
    }
}

// params log monitoring
$row = 0;
// dictionary of last 10 sec sections
$dict_sections = [];
$console_log_monitoring = false;
// timers
$timer_10_sec = 0;
$timer_2_min  = 0;
// requests info
$requests = 0;
$nb_get_requests = 0;
$nb_post_requests = 0;
$success_requests = 0;



// get lines from file
if (!empty($filepath)) {
    $handle = fopen($filepath, "r") or die("Couldn't get handle");
    if ($handle) {
        // increment row
        $row += 1;
        // skip first header line
        fgetcsv($handle);
        while (!feof($handle)) {
            // while (!feof($handle) && ($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($row > 1) {
                $line = str_getcsv(fgets($handle));
                echo ' line ' . $line;
                $log = new Http_Log($line);
                // 10 sec check
                // Initialisation
                if ($timer_10_sec == 0) {
                    $timer_10_sec = $log->getDate();
                    $timer_2_min  = $log->getDate();
                } else {
                    $console_log_monitoring = timeout(10, $timer_10_sec, $log->getDate());
                    if (!empty($console_log_monitoring)) {
                        // display traffic stats
                        // log time
                        $timestamp = $timer_10_sec + 10;
                        $log_info = [
                            'time'            => date('m/d/Y H:i:s', $timestamp),
                            'hit section'     => getMaxValueFromAssoArray($dict_sections),
                            'requests'        => $requests,
                            'GET requests'    => $nb_get_requests,
                            'POST requests'   => $nb_post_requests,
                            'success request' => $success_requests,
                        ];
                        displayTrafficStat($log_info);
    
                        // clean http log monitoring
                        $requests               = 0;
                        $nb_get_requests        = 0;
                        $nb_post_requests       = 0;
                        $success_requests       = 0;
                        $dict_sections          = [];
                        $console_log_monitoring = false;
                        
                        // set new timestamp
                        $timer_10_sec = $log->getDate();
                    }
                }
    
                // add section to dictionary
                if (isset($dict_sections[$log->getSection()])) {
                    $dict_sections[$log->getSection()] += 1;
                } else {
                    $dict_sections[$log->getSection()] = 1;
                }
                $requests += 1;
                // check request method
                if ($log->getRequestMethod() === 'GET') {
                    $nb_get_requests += 1;
                } else {
                    $nb_post_requests += 1;
                }
                // check request status
                if ($log->getStatus() === 200) {
                    $success_requests += 1;
                }
    
                // // 2 min check
                // $section = $log->getSection();
                // $method = $log->getRequestMethod();
                // echo ' section : ' . $section;
                // echo ' method : ' . $method;
            }
            fclose($handle);
        }
    }    
}

// get lines from input
