<?php

require_once 'vendor/autoload.php';
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

/**
 *  First:
 *  get top user-agents list from apache's access_log
 *  cat /var/log/httpd/access_log* | awk -F'"' '{print $6}' | sort | uniq -c | sort -rn > top-user-agents.txt
 *  Second:
 *     php DetectFromTopUserAgents.php
 *  Finally:
 *     view detect_apache_log_result*.csv file
*/

// OPTIONAL: Set version truncation to none, so full versions will be returned
// By default only minor versions will be returned (e.g. X.Y)
// for other options see VERSION_TRUNCATION_* constants in DeviceParserAbstract class
DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

// to generate csv file name
date_default_timezone_set('Asia/shanghai');
/**
 * @param $userAgent
 */
function detectDevice($fp, $userAgent)
{
    echo $userAgent, ' ';
    $dd = new DeviceDetector($userAgent);
// OPTIONAL: Set caching method
// By default static cache is used, which works best within one php process (memory array caching)
// To cache across requests use caching in files or memcache
//$dd->setCache(new Doctrine\Common\Cache\PhpFileCache('./tmp/'));

// OPTIONAL: Set custom yaml parser
// By default Spyc will be used for parsing yaml files. You can also use another yaml parser.
// You may need to implement the Yaml Parser facade if you want to use another parser than Spyc or [Symfony](https://github.com/symfony/yaml)
//$dd->setYamlParser(new DeviceDetector\Yaml\Symfony());

// OPTIONAL: If called, getBot() will only return true if a bot was detected  (speeds up detection a bit)
//    $dd->discardBotInformation();

// OPTIONAL: If called, bot detection will completely be skipped (bots will be detected as regular devices then)
//    $dd->skipBotDetection();

    $dd->parse();
    $result = '';
    if ($dd->isBot()) {
        // handle bots,spiders,crawlers,...
        $botInfo = $dd->getBot();
        $result = $result.echo_info('bot', $botInfo);
    } else {
        $result = $result.',';
        $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
        $osInfo = $dd->getOs();
        $device = $dd->getDevice();
        $brand = $dd->getBrand();
        $model = $dd->getModel();
        $result = $result.echo_info('$clientInfo', $clientInfo);
        $result = $result.echo_info('$osInfo', $osInfo);
        echo '$device', $device, ',';
        echo '$brand: ', $brand, ',';
        echo '$model: ', $model, ',';
        $result = $result.$device.','.$brand.','.$model;
    }
    fwrite($fp, $result."\n");
    echo "\n";
}

function echo_info($des='', $info){
    echo $des, ': ';
    $result = '';
    if($info !=null && !empty($info)) {
        foreach ($info as $item) {
            if(is_array($item)){
                $result = $result.echo_info('', $item);
            }else {
                echo $item, ' ';
                $result = $result . $item . ' ';
            }
        }
    }
    echo ',';
    return $result.',';
}

$title = ['requests','user-agent','bot', 'clientInfo','os','device-type',"device-brand",'device-model'];

$handle = fopen("top-user-agents.txt", "r");
if ($handle) {
    $linen_umber = 0;
    $fp = fopen("detect_apache_log_result".date("YmdHis").'.csv', "w") or exit("Unable to open file!");
    foreach($title as $t){
        fwrite($fp, $t.',');
    }
    fwrite($fp, "\n");
    
    while (($line = fgets($handle)) !== false) {
        // process the line read.
        $linen_umber ++;
        fwrite($fp, substr(rtrim($line),0,8).',');
        $userAgent = "\"".substr(rtrim($line),7)."\"";
        fwrite($fp, $userAgent.',');

        detectDevice($fp, $userAgent);
    }
    fclose($fp);
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
    echo "\nline coud： ", $linen_umber;
} else {
    echo "\n Error: can not read file";
}

