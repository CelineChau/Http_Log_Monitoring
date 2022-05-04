# Http Log Monitoring & Alerting
========================================

## Monitoring :
The project displays log traffic stats after **every 10 seconds** of log lines.
Traffic stats render : 
  - **time** : when the traffic stats is displayed,
  - **hit section** : the section of the web site with the most hits,
  - **requests** : number of requests done after 10 sec,
  - **GET requests** : number of GET requests done after 10 sec,
  - **POST requests** : number of POST requests done after 10 sec,
  - **success requests** : number of successful requests after 10 sec
 
**Time commitment** : around 5 hours

## Alerting
The project shows an alert :
  - when log traffic is **high** after **every 2 minutes** of log lines.
  - when log traffic **recovered**

Log traffic is considered high if the total traffic exceeds a number of requests per 2 minutes.

Log traffic is considered as recovered when the total traffic drops again below the threshold of number of requests.
This threshold value can be configured, default value = 10 requests per sec.

## Lauch the script
Please, make sure you have installed PHP.
If not done yet, you can check this documentation : https://kinsta.com/blog/install-php/ .

### with a filepath :
```
php http_log_monitoring.php --file {filepath}
```

### with input shell :
```
php http_log_monitoring.php
```
It will read the input from the standard input.
Please, provide input with the following pattern : **"remotehost","rfc931","authuser","date","request","status","bytes"\n**

You can stop the program with : **CTRL + D**.

### Optional arguments :
- **--file {filepath}** : provide a filepath,
- **--traffic-threshold {threshold}** : provide the threshold of number of requests per sec,
- **--frequency {frenquency}** : provide an integer > 0 to slow the log display (in milliseconds),

```
php http_log_monitoring.php --file {filepath} --traffic-threshold {threshold} --frequency {frequency}
```

## Lauch the test
```
phpunit Test
```
The framework **PHPUnit** is used to write the test. You can check the documentation for more info : https://phpunit.readthedocs.io/en/9.5/
Test repository includes :
  - **fixtures** : fixtures files to test the project
  - **UtilsTest.php** : test script to test utils functions
  - **HttpLogMonitoring** : test script to test the log monitoring displaying traffic stats
  - **HttpLogAlerting** : test script to test the traffic log alerting when recovered or high


## Future improvement
To improve the project :
  - Improve tests with more error case
  - When reading standard input, close the program when no input for more than 5 minutes
  - Improve error messages 
