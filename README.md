# piwik DeviceDetector tool/demo
  piwik DeviceDetector is Universal Device Detection library, that parses User Agents and detects devices.
  But when there are many UNKNOWN devices in piwik visit logs，u can not know which fuck User Agent not be detected, and have no information to config mobiles.yml file to detect new device. this tool try resolve this problem.
  
## 1st:
- get top user-agents list from apache's access_log, u should config apache log format to include User Agent .
- `cat /var/log/httpd/access_log* | awk -F'"' '{print $6}' | sort | uniq -c | sort -rn > top-user-agents.txt`

## 2nd:
- use [composer](https://packagist.org/) ,run ` curl -sS https://getcomposer.org/installer | php` and install depend library `php composer.phar install`
-  run `php DetectFromTopUserAgents.php` 

## 3rd: 
- view detect_apache_log_result*.csv file with ms Excel to filter device-type == null 
- update vendor/piwik/device-detector/regexes/device/mobiles.yml with new device regex

## Finally:
- copy undetect user agent info to  `testUserAgent.php`
- run `php testUserAgent.php` to test  new device regex  
