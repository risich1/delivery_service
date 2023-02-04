<?php

namespace App\Service;

use RateLimit\Exception\LimitExceeded;
use RateLimit\Rate;
use RateLimit\RedisRateLimiter;
use Redis;

class HttpService {

    protected Redis $redis;

    public function __construct(Redis $redis) {
        $this->redis = $redis;
    }

    public function isAllowByRateLimit($token): bool {
        try {
            $rateLimiter = new RedisRateLimiter(Rate::perMinute($_ENV['REQUESTS_PER_MINUTE']), $this->redis);
            $rateLimiter->limit($token);
            return true;
        } catch (LimitExceeded $e) {
            return false;
        }
    }

}
