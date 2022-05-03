<?php
namespace Test;
use PHPUnit\Framework\TestCase;

// import
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/utils.php');

final class UtilsTest extends TestCase
{
    public function testMappedImplode(): void {
        $log_info = [
            'time'            => '02/07/2019 21:19:02',
            'hit section'     => '/api',
            'requests'        => 5,
            'GET requests'    => 5,
            'POST requests'   => 0,
            'success request' => 2,
        ];
        $expected = 'time : 02/07/2019 21:19:02, hit section : /api, requests : 5, GET requests : 5, POST requests : 0, success request : 2';
        $output   = mappedImplode(', ', $log_info, ' : ');
        $this->assertEquals($expected, $output, 'Util function [mappedImplode] not working');
    }

    public function testGetMaxValueFromAssoArray(): void {
        $values = [
            '/api' => 6,
            '/report' => 1,
            '/hello' => 2,
        ];
        $expected = '/api';
        $output = getMaxValueFromAssoArray($values);
        $this->assertEquals($expected, $output, 'Util function [getMaxValueFromAssoArray] not working');
    }

    public function testTimeout(): void {
        // timeout after 10s
        $timer_10_sec = time() - 11;
        $output = timeout(10, $timer_10_sec, time());;
        $this->assertTrue($output, 'Util function [timeout] not working');

        // timer before 10s
        $timer_10_sec = time() - 9;
        $output = timeout(10, $timer_10_sec, time());;
        $this->assertFalse($output, 'Util function [timeout] not working');
    }

    public function testCheckInputLine(): void {
        // False case
        $line = 'Test line';
        $output = checkInputLine($line);
        $this->assertFalse($output, 'Util function [checkInputLine] not working');
        // True case
        $line = '"10.0.0.4","-","apache",1549574333,"GET /report HTTP/1.0",200,1136';
        $output = checkInputLine($line);
        $this->assertTrue($output, 'Util function [checkInputLine] not working');
    }

}