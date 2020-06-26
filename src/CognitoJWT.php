<?php

namespace Modules\CognitoGuard\src;

use \Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Modules\CognitoGuard\Entities\PublicKey;
use phpseclib\Crypt\RSA;
use phpseclib\Math\BigInteger;


class CognitoJWT
{
    public static function getPublicKey(string $kid, string $region, string $userPoolId): ?string
    {
        //Get jwks from URL, because the jwks may change.
        $jwksUrl = sprintf('https://cognito-idp.%s.amazonaws.com/%s/.well-known/jwks.json', $region, $userPoolId);
        $client = new Client();
        $response = $client->get($jwksUrl);
        $body = $response->getBody()->getContents();
        if ($body) {
            $json = json_decode($body, false);
            if ($json && isset($json->keys) && is_array($json->keys)) {
                foreach ($json->keys as $jwk) {
                    if ($jwk->kid === $kid) {
                        return static::jwkToPem($jwk);
                    }
                }
            }
        }
        return null;
    }

    public static function jwkToPem(object $jwk): ?string
    {
        if (isset($jwk->e) && isset($jwk->n)) {
            $rsa = new RSA();
            $rsa->loadKey([
                'e' => new BigInteger(JWT::urlsafeB64Decode($jwk->e), 256),
                'n' => new BigInteger(JWT::urlsafeB64Decode($jwk->n),  256)
            ]);
            return $rsa->getPublicKey();
        }
        return null;
    }

    public static function getKid(string $jwt): ?string
    {
        $tks = explode('.', $jwt);
        if (count($tks) === 3) {
            $header = JWT::jsonDecode(JWT::urlsafeB64Decode($tks[0]));
            if (isset($header->kid)) {
                return $header->kid;
            }
        }
        return null;
    }

    public static function verifyToken(string $jwt, string $region, string $userPoolId): ?object
    {
        $publicKey = null;
        $kid = static::getKid($jwt);
        if ($kid) {
            $row = PublicKey::find($kid);
            if ($row) {
                $publicKey = $row->public_key;
            } else {
                $publicKey = static::getPublicKey($kid, $region, $userPoolId);
                $row = PublicKey::create(['kid' => $kid, 'public_key' => $publicKey]);
            }
        }

        if ($publicKey) {
            return JWT::decode($jwt, $publicKey, array('RS256'));
        }
        return null;
    }
}
