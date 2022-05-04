<?php

// import
require_once(__DIR__.'/models/Http_Log.php');
require_once(__DIR__.'/models/Http_Log_Monitoring.php');
require_once(__DIR__.'/utils/utils.php');
require_once(__DIR__.'/utils/constants.php');

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
    // check if file exists
    if (file_exists($options['file'])) {
        $filepath = $options['file'];
    } else {
        echo "Error: please check the file path \n";
        exit(1);
    }
}

// threshold for hit alert, need to be > 0
// default 10 requests per 2 sec
$threshold = !empty($options['traffic-threshold']) && $options['traffic-threshold'] > 0 ? $options['traffic-threshold'] : DEFAULT_THRESHOLD;

// frequency (slow log display), default 0
$frequency = !empty($options['frequency']) ? intval($options['frequency']) : DEFAULT_FREQUENCY;

// Http Log Monitoring
$http_log_monitoring = new Http_Log_Monitoring();

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
                    $http_log_monitoring->httpLogMonitor($log);
                    // Log alert
                    $http_log_monitoring->httpLogAlert($log, $threshold);
                    // slow logs display
                    usleep($frequency);
                }
            }
        } catch (Exception $e) {
            fclose($handle);
            echo "Error: " . $e->getMessage . "\n";
            exit(1);
        }
        fclose($handle);
    }    
} else {
    // read from standard input
    printf("Http Log Monitoring form standard input : \n");
    // read line from shell
    while($input = readline()) {
        // check line has correct format
        if (!checkInputLine($input)) {
            printf("Please enter line with ['remotehost','rfc931','authuser','date','request','status','bytes'] : \n");
            continue;
        } else {
            try {
                $line = str_getcsv($input);
                $log = new Http_Log($line);
                // Log stats
                $http_log_monitoring->httpLogMonitor($log);
                // Log alert
                $http_log_monitoring->HttpLogAlert($log, $threshold);
                // slow logs display
                usleep($frequency);
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage . "\n";
                exit(1);
            }
        }
    }
}
