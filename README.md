# Http Log Monitoring & Alerting
========================================

## Monitoring :
The project displays log traffic stats after every 10 seconds of log lines.
Traffic stats render : 
  - time : when the traffic stats is displayed,
  - hit section : the section of the web site with the most hits,
  - requests : number of requests done after 10 sec,
  - GET requests : number of GET requests done after 10 sec,
  - POST requests : number of POST requests done after 10 sec,
  - success requests : number of successful requests after 10 sec

## Alerting
The project shows an alert :
  - when log traffic is high after every 2 minutes of log lines.
  - when log traffic recovered
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

### You can add an option traffic threshold :
```
php http_log_monitoring.php --file {filepath} --traffic-threshold {threshold}
```

## Lauch the test
```
phpunit Test
```
