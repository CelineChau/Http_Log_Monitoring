<?php
namespace Test;
use PHPUnit\Framework\TestCase;

final class HttpLogMonitoringTest extends TestCase
{
    // Test aims to check correct log stats are returned
    // with a file as parameter
    public function testLogMonitoringWithFile() {
        $out = shell_exec("php http_log_monitoring.php --file test/fixtures/test_logs.txt");
        $expected = "Http Log Monitoring from file test/fixtures/test_logs.txt : \n";
        $expected .= "time : 02/07/2019 21:19:02, hit section : /api, requests : 2, GET requests : 2, POST requests : 0, success requests : 2\n";
        $this->assertEquals($expected, $out, '[Http Log Monitoring] not working as expected');
    } 

    // Test aims to check correct log stats are returned
    // read from standard input
    public function testLogMonitoringWithInput() {
        // command to run in shell
        $cmd = "php http_log_monitoring.php";

        $descriptorspec = array(
            0 => array("pipe", "r"), // stdin is a pipe that the child will read from
            1 => array("pipe", "w") // stdout is a pipe that the child will write to
        );

        $process = proc_open($cmd, $descriptorspec, $pipes);

        if (is_resource($process)) {
            // Write input test
            fwrite(
                $pipes[0],
                '"10.0.0.1","-","apache",1549574332,"GET /api/user HTTP/1.0",200,1234
                "10.0.0.4","-","apache",1549574333,"GET /report HTTP/1.0",200,1136
                "10.0.0.1","-","apache",1549574434,"GET /api/user HTTP/1.0",200,1194'
            );
            fclose($pipes[0]);
            
            // Retrieve ouput
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            proc_close($process);
        
            $expected = "Http Log Monitoring form standard input : \n";
            $expected .= "time : 02/07/2019 21:19:02, hit section : /api, requests : 2, GET requests : 2, POST requests : 0, success requests : 2\n";
            $this->assertEquals($expected, $output, '[Http Log Monitoring] not working as expected');
        }
    } 
}