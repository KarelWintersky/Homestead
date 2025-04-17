<?php

use Homestead\Homestead;

Homestead::init();
$credentials = Homestead::loadCredentials();

$redis = new \Arris\Toolkit\RedisClient();

if (isset($credentials['redis'])) {
    $cr = $credentials['redis'];
    $redis->connect($cr['host'], $cr['port'], $cr['database'], $cr['enable']);
}

Homestead::$service_redis = $redis;