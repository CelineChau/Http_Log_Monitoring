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