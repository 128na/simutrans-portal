<?php

declare(strict_types=1);

namespace App\Adapters;

use Exception;

class AutoRefreshingDropBoxTokenService
{
    public function getToken(string $key, string $secret, string $refreshToken): string
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', sprintf('https://%s:%s@api.dropbox.com/oauth2/token', $key, $secret), [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ],
        ]);
        if ($res->getStatusCode() == 200) {
            /** @var array{access_token:string} */
            $data = json_decode($res->getBody(), true);

            return $data['access_token'];
        }

        throw new Exception('get refresh token failed: '.$res->getBody());
    }
}
