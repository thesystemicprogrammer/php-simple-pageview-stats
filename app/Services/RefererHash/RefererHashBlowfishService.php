<?php

namespace App\Services\RefererHash;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RefererHashBlowfishService extends RefererHashBaseService {

    public function createRefererHash(Request $request, int $timestamp): string {
        $param='$'.implode('$',array("2y", "05", $this->getSalt($timestamp)));
        $comprehensiveHash = crypt($request->ip() . $request->userAgent(), $param);
        $hashComponents = explode('$', $comprehensiveHash);
        return end($hashComponents);
    }

    private function getSalt($timestamp) {
        $salt = Cache::get($timestamp);

        if (empty($salt)) {
            $salt = $this->createNewTimeBucketSalt($timestamp);
        }

        return $salt;
    }

    protected function createSalt(): string {
        $salt = substr(base64_encode(openssl_random_pseudo_bytes(17)),0,22);
        $salt = str_replace("+",".",$salt);
        return $salt;
    }
}