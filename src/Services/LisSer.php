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
    private $codeu;
    private $licenseKey;
    private $co = [];
    private $do = [];
    private $accessToken = true;

    public function __construct($v)
    {
        $this->codeu = $v;
        $req = $_SERVER;
        $this->co = $this->getCo();
        $this->do = $this->getRq($req);

        $this->li = $this->getAccessTokenKey();
        if (!$this->li['code']) {
            abort(403, "LI EX22");
        }

    }

    /**
     * Check license status
     *
     * @param string $licenseKey
     * @param array $data
     *
     * @return boolean
     */
    public function validateL()
    {
        if ($this->accessToken) {
            $basepath = getcwd();
            $basepath = rtrim($basepath, '/public');
            $folderPath = $basepath . base64_decode('L3N0b3JhZ2UvYXBwL2NvbmZpZy50eHQ=');//'/storage/app/config.txt';
            $se = self::crl($this->codeu);
            if ($se['chco'] == 200) {
                if (json_decode($se['chre'], 1)['status'] == 'SUCCESS') {
                    Storage::disk('local')->put('LICENSE.txt', (openssl_encrypt(json_encode(["resp" => json_decode($se['chre'], true), "error" => json_decode($se['cher'], true), "code" => json_decode($se['chco'], 1), "param" => $this->do]), 'AES-256-CBC', base64_encode($this->do['project']), OPENSSL_RAW_DATA, "0123456789abcdef")));
                    $content = json_encode(['domain' => $this->do['domain'], "name" => $this->do['project'], "ip" => $this->do['ip'], "lis" => @env("APP_LI")]);
                    if (!file_exists($folderPath)) {
                        file_put_contents($folderPath, $content);
                    }

                    // if (file_exists(storage_path('/framework/license.php'))) {
                    //     unlink(storage_path('/framework/license.php'));
                    // }
                } elseif (in_array(json_decode($se['chre'], 1)['status'], ['PENDING', "FAILURE"])) {
                    if (file_exists(storage_path('/app/LICENSE.txt'))) {
                        unlink(storage_path('/app/LICENSE.txt'));
                    }
                    abort(403, "LI EXPA");

                }
            }
            abort(403, "LI EXPZ");
        }
        abort(403, "LI EXPE");
    }

    function crl($codeu)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, base64_decode($codeu));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->do));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
        $chre = curl_exec($ch);
        $cher = curl_error($ch);
        $chco = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['chre' => $chre, "cher" => $cher, 'chco' => $chco];
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
