<?php

require_once 'vendor/autoload.php';

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

// OPTIONAL: Set version truncation to none, so full versions will be returned
// By default only minor versions will be returned (e.g. X.Y)
// for other options see VERSION_TRUNCATION_* constants in DeviceParserAbstract class
DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

/**
 * @param $userAgent
 */
function detectDevice($userAgent)
{
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
   // $dd->skipBotDetection();

    $dd->parse();

    if ($dd->isBot()) {
        // handle bots,spiders,crawlers,...
        $botInfo = $dd->getBot();
        echo_info('bot', $botInfo);
    } else {
        $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
        $osInfo = $dd->getOs();
        $device = $dd->getDevice();
        $brand = $dd->getBrand();
        $model = $dd->getModel();
        echo_info('$clientInfo', $clientInfo);
        echo_info('$osInfo', $osInfo);
        echo '$device：', $device, ',';
        echo '$brand: ', $brand, ',';
        echo '$model: ', $model, ',';
    }
}

function echo_info($des, $info){
    echo $des, ': ';
    if($info != null) {
        foreach ($info as $item) {
            echo $item, ' ';
        }
    }
    echo ',';
}

$userAgent = 'spider';

detectDevice($userAgent);