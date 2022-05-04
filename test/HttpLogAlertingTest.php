<?php declare(strict_types=1);
namespace Test;

use PHPUnit\Framework\TestCase;

final class HttpLogAlertingTest extends TestCase
{
    // Test aims to check correct alerts are displayed
    // with a file as parameter
    public function testLogAlertingWithFile() {
        // Use traffic threshold to 10 requests per second
        $out = explode(PHP_EOL, shell_exec("php http_log_monitoring.php --file test/fixtures/Log_File.txt"));
        // Expected high traffic
        $expected = "High traffic generated an alert - hits = 1619, triggered at 02/07/2019 21:13:01";
        $this->assertContains($expected, $out, '[Http Log Alerting] not working as expected');
        // Expected traffic to recover
        $expected = "Recovered traffic generated an alert - hits = 363, triggered at 02/07/2019 21:15:02";
        $this->assertContains($expected, $out, '[Http Log Alerting] not working as expected');
        // Expected high traffic
        $expected = "High traffic generated an alert - hits = 2608, triggered at 02/07/2019 21:17:04";
        $this->assertContains($expected, $out, '[Http Log Alerting] not working as expected');
    }

    // Test aims to check correct alerts are displayed
    // read from standard input
    public function testLogAlertingWithInput() {
        // command to run in shell
        // Use traffic threshold to 1 request per second
        $cmd = "php http_log_monitoring.php --traffic-threshold 1";

        $descriptorspec = array(
            0 => array("pipe", "r"), // stdin is a pipe that the child will read from
            1 => array("pipe", "w") // stdout is a pipe that the child will write to
        );
        $process = proc_open($cmd, $descriptorspec, $pipes);

        if (is_resource($process)) {
            // Write input test
            $input = "";
            for ($i = 0; $i < 252; $i ++) {
                $input .= '"10.0.0.2","-","apache",1549573860,"GET /api/user HTTP/1.0",200,1234' . "\n";
            }
            $input .= '"10.0.0.2","-","apache",1549573991,"GET /api/user HTTP/1.0",200,1234' . "\n";
            fwrite($pipes[0], $input);
            fclose($pipes[0]);
            
            // Retrieve ouput
            $output = explode(PHP_EOL, stream_get_contents($pipes[1]));
            fclose($pipes[1]);
            proc_close($process);
            
            $expected = "High traffic generated an alert - hits = 252, triggered at 02/07/2019 21:13:01";
            $this->assertContains($expected, $output, '[Http Log Alerting] not working as expected');
        }
    }
}