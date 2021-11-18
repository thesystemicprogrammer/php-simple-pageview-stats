<?php

namespace App\Services\JWT;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;

class JWTDecoder
{

    public static function isJWTTokenValid($jwtToken): bool
    {
        $isValid = true;

        if (!isset($jwtToken)) {
            $isValid = false;
        } else {

            try {
                $jwtObject = JWT::decode($jwtToken, new Key(env("JWT_HS256_SECRET"), 'HS256'));

                if (!in_array('pageview', $jwtObject->roles)) {
                    $isValid = false;
                }
            } catch (Exception $e) {
                $isValid = false;
                Log::debug('JWT Decode exception: ', ['errorMessage' => $e]);
            }
        }

        return $isValid;
    }
}
