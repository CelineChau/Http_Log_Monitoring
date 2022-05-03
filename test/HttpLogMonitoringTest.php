<?php
namespace Test;
use PHPUnit\Framework\TestCase;

final class HttpLogMonitoringTest extends TestCase
{
    // Test aims to check correct log stats are returned
    // with a file as parameter
    public function testLogMonitoringWithFile() {
        $out = shell_exec("php http_log_monitoring.php --file test_logs.txt");
        $expected = "Http Log Monitoring from file test_logs.txt : \n";
        $expected .= 'time : 02/07/2019 21:19:02, hit section : /api, requests : 2, GET requests : 2, POST requests : 0, success request : 2';
        $this->assertEquals($expected, $out, '[Http Log Monitoring] not working as expected');
    } 

    // Test aims to check correct log stats are returned
    // read from standard input
    public function testLogMonitoringWithInput() {
        // $output = null;
        // $input = '
        //     "10.0.0.1","-","apache",1549574332,"GET /api/user HTTP/1.0",200,1234
        //     "10.0.0.4","-","apache",1549574333,"GET /report HTTP/1.0",200,1136
        //     "10.0.0.1","-","apache",1549574434,"GET /api/user HTTP/1.0",200,1194
        // ';
        // shell_exec('php http_log_monitoring.php');
        // echo '"10.0.0.1","-","apache",1549574332,"GET /api/user HTTP/1.0",200,1234';
        // echo '"10.0.0.4","-","apache",1549574333,"GET /report HTTP/1.0",200,1136';
        // echo '10.0.0.1","-","apache",1549574434,"GET /api/user HTTP/1.0",200,1194';
        // print_r($output);
        // $out = shell_exec("php http_log_monitoring.php");
        // $expected = 'time : 02/07/2019 21:19:02, hit section : /api, requests : 2, GET requests : 2, POST requests : 0, success request : 2';
        // $this->assertEquals($expected, $out, '[Http Log Monitoring] not working as expected');
    } 
}