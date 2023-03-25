<?php

namespace App\Adapters;

class AutoRefreshingDropBoxTokenService
{
    public function getToken($key, $secret, $refreshToken)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $res = $client->request('POST', "https://{$key}:{$secret}@api.dropbox.com/oauth2/token", [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ],
            ]);
            if ($res->getStatusCode() == 200) {
                return json_decode($res->getBody(), true)['access_token'];
            } else {
                info(json_decode($res->getBody(), true));

                return false;
            }
        } catch (\Exception $e) {
            info($e->getMessage());

            return false;
        }
    }
}
