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
        $expected .= "time : 02/07/2019 21:19:02, hit section : /api, requests : 2, GET requests : 2, POST requests : 0, success requests : 2\n";
        $this->assertEquals($expected, $out, '[Http Log Monitoring] not working as expected');
    } 

    // Test aims to check correct log stats are returned
    // read from standard input
    public function testLogMonitoringWithInput() {
        // $this->assertEquals($expected, $out, '[Http Log Monitoring] not working as expected');
    } 
}