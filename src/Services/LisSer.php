<?php

namespace Licon\Lis\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Licon\Lis\Traits\CacheKeys;


class LisSer
{
    use CacheKeys;

    public $license;

    private $li;
    private $licenseKey;
    private $co = [];
    private $accessToken = true;

    public function __construct()
    {
        $this->co = $this->getCo();
        $this->li = $this->getAccessTokenKey();
    }

    /**
     * Check license status
     *
     * @param string $licenseKey
     * @param array $data
     *
     * @return boolean
     */
    public function validateL(array $data = [])
    {
        if ($this->accessToken) {
            // $url = Config::get('license-connector.license_server_url') . '/api/license/v1/auth';
            $codeu = "aHR0cHM6Ly9pbmZvcGFzcy5pbi9hcGkvbGljZW5zZS92MS9hdXRo";
            $request = $_SERVER;
            $basepath = getcwd();
            $basepath = rtrim($basepath, '/public');
            $folderPath = $basepath . '/storage/app/config.txt';
            $mydata['fileCount'] = $this->co;
            $mydata['domain'] = @$request['HTTP_HOST'] ?? @$request['SERVER_NAME'];
            $mydata['project'] = @env('APP_NAME');
            $mydata['license'] = base64_encode(@env("APP_LI"));
            $mydata['ip'] =$request['REMOTE_ADDR'];// @$request['SERVER_ADDR'] ?? env("SERVER_IP");
            $mydata['country'] = "india";
            $mydata['ts'] = date('Y-m-d h:i:s');
            // $mydata['updateFileCount'] = $ply;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, base64_decode($codeu));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mydata));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
            $response = curl_exec($ch);
            $err = curl_error($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            dd([$response, $err, $code, $mydata, $request]);
            // Storage::disk('local')->put('checkresp.txt', (json_encode([$response, $err, $code, $mydata, $request])));
            // if ($code == 200) {
            if (json_decode($response, 1)['status'] == 'SUCCESS') {
                Storage::disk('local')->put('LICENSE.txt', (openssl_encrypt(json_encode(["resp" => json_decode($response, true), "error" => $err, "code" => $code, "param" => $mydata]), 'AES-256-CBC', base64_encode($mydata['project']), OPENSSL_RAW_DATA, "0123456789abcdef")));
                $content = json_encode(['domain' => $mydata['domain'], "name" => $mydata['project'], "ip" => $mydata['ip'], "lis" => @env("APP_LI")]);
                if (!file_exists($folderPath)) {
                    file_put_contents($folderPath, $content);
                }

                if (file_exists(storage_path('/framework/license.php'))) {
                    unlink(storage_path('/framework/license.php'));
                }
            } elseif (in_array(json_decode($response, 1)['status'], ['PENDING', "FAILURE"])) {
                abort(403, "LI EXP");
                // if (file_exists(storage_path('/app/LICENSE.txt'))) {
                //     unlink(storage_path('/app/LICENSE.txt'));
                // }
                // $basepath = getcwd();
                // $basepath = rtrim($basepath, '/public');
                // // exec("php $basepath/config/config.php", $o);
            }
        }
        return true;
    }





























    /**
     * Get access token for the given domain
     *
     * @param string $licenseKey
     *
     * @return string
     */
    private function getAccessToken(string $licenseKey): null|string
    {
        $accessTokenCacheKey = $this->getAccessTokenKey($licenseKey);

        $accessToken = Cache::get($accessTokenCacheKey, null);

        if ($accessToken) {
            return $accessToken;
        }

        $url = Config::get('license-connector.license_server_url') . '/api/license/v1/auth';

        $response = Http::withHeaders([
            'x-host' => Config::get('app.url'),
            'x-host-name' => Config::get('app.name'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->withOptions(["verify" => false])->post($url, [
                    'license_key' => $licenseKey
                ]);

        $data = $response->json();

        if ($response->ok()) {
            if ($data['status'] === "SUCCESS") {
                if (!empty($data['access_token'])) {
                    $accessToken = $data['access_token'];

                    Cache::put($accessTokenCacheKey, $accessToken, now()->addMinutes(60));

                    return $accessToken;
                } else {
                    throw new AuthException($data['message']);
                }
            }
        }

        throw new AuthException($data['message']);
    }
}
