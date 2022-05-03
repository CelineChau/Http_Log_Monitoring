<?php

// import
require_once(__DIR__.'/Http_Log.php');
require_once(__DIR__.'/utils.php');

// args
$shortopts = "";
$filepath = '';
$longopts = array(
	"file:", // file path
	"traffic-threshold:", // traffic threshold, default = 10
    "frequency:", // timelapse between log in millisecond
);
$options = getopt($shortopts, $longopts);

// check file input
// var_dump($options);
if (isset($options['file'])) {
    // echo 'In option file';
    // check if file exists
    if (file_exists($options['file'])) {
        $filepath = $options['file'];
        // echo ' file exists ';
    } else {
        echo ' [error] please check the file path ';
        exit(1);
    }
}

// params log monitoring
// dictionary of last 10 sec sections
$dict_sections = [];
$console_log_monitoring = false;

// threshold for hit alert
// default 10 requests per 2 sec
$threshold = $options['traffic-threshold'] ?? 10;
// frequency, default 0
$frequency = !empty($options['frequency']) ? intval($options['frequency']) : 0;

// timers
$timer_10_sec = "";
$timer_2_min  = "";

// requests stats
$hits = 0; // counting requests for alert
$high_traffic = false;
$requests = 0;
$nb_get_requests = 0;
$nb_post_requests = 0;
$success_requests = 0;


// Log alerting and monitoring
if (!empty($filepath)) {
    // get lines from file
    $handle = fopen($filepath, "r") or die("Couldn't get handle");
    // read from file
    printf("Http Log Monitoring from file " . $filepath ." : \n");
    if ($handle) {
        try {
            fgetcsv($handle);
            while (!feof($handle)) {
                $line = fgets($handle);
                // skip first header line
                if (checkInputLine($line)) {
                    $log = new Http_Log(str_getcsv($line));
                    // Log stats
                    httpLogMonitor(
                        $log,
                        $timer_10_sec,
                        $dict_sections,
                        $requests,
                        $nb_get_requests,
                        $nb_post_requests,
                        $success_requests
                    );
                    // Log alert
                    HttpLogAlert(
                        $log,
                        $timer_2_min,
                        $threshold,
                        $hits,
                        $high_traffic
                    );
                    // slow logs display
                    usleep($frequency);
                }
            }
        } catch (Exception $e) {
            fclose($handle);
            echo "Error: " . $e->getMessage;
            exit(1);
        }
        fclose($handle);
    }    
} else {
    // read from standard input
    printf("Http Log Monitoring form standard input : \n");
    while(true) {
        // read line from shell
        $input = readline();
        // check line has correct format
        if (!checkInputLine($input)) {
            printf("Please enter line with ['remotehost','rfc931','authuser','date','request','status','bytes'] : \n");
            continue;
        } else {
            try {
                $line = str_getcsv($input);
                $log = new Http_Log($line);
                // Log stats
                httpLogMonitor(
                    $log,
                    $timer_10_sec,
                    $dict_sections,
                    $requests,
                    $nb_get_requests,
                    $nb_post_requests,
                    $success_requests
                );
                // Log alert
                HttpLogAlert(
                    $log,
                    $timer_2_min,
                    $threshold,
                    $hits,
                    $high_traffic
                );
                // slow logs display
                usleep($frequency);
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage;
                exit(1);
            }
        }
    }
}
