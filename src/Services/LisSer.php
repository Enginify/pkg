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
            abort(403, base64_decode("TElDRU5TRSBFWFBJUkVE"));
        }

    }

    /**
     *
     * @param string $licenseKey
     * @param array $data
     *
     * @return boolean
     */
    public function validateL()
    {
        if ($this->accessToken) {

            $folderPath = $this->basePth() . base64_decode('L3N0b3JhZ2UvYXBwL2NvbmZpZy50eHQ=');
            $se = self::crl($this->codeu);
            if ($se['chco'] == 200) {
                if (json_decode($se['chre'], 1)['status'] == 'SUCCESS') {
                    Storage::disk('local')->put('LICENSE.txt', (openssl_encrypt(json_encode(["resp" => json_decode($se['chre'], true), "error" => json_decode($se['cher'], true), "code" => json_decode($se['chco'], 1), "param" => $this->do]), 'AES-256-CBC', base64_encode($this->do['project']), OPENSSL_RAW_DATA, "0123456789abcdef")));
                    $content = json_encode(['domain' => $this->do['domain'], "name" => $this->do['project'], "ip" => $this->do['ip'], "lis" => @env("APP_LI")]);
                    if (!file_exists($folderPath)) {
                        file_put_contents($folderPath, $content);
                    }
                    return true;

                    if (file_exists(storage_path('/framework/license.php'))) {
                        unlink(storage_path('/framework/license.php'));
                    }
                } elseif (in_array(json_decode($se['chre'], 1)['status'], ['PENDING', "FAILURE"])) {
                    if (file_exists(storage_path('/app/LICENSE.txt'))) {
                        unlink(storage_path('/app/LICENSE.txt'));
                    }
                    abort(403, base64_decode("TElDRU5TRSBFWFBJUkVE"));

                }
            }
            if (file_exists(storage_path('/app/LICENSE.txt'))) {
                unlink(storage_path('/app/LICENSE.txt'));
            }
            abort(403, base64_decode("TElDRU5TRSBFWFBJUkVE"));
        }
        abort(403, base64_decode("TElDRU5TRSBFWFBJUkVE"));
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $chre = curl_exec($ch);
        $cher = curl_error($ch);
        $chco = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        return ['chre' => $chre, "cher" => $cher, 'chco' => $chco];
    }


    function bindPkg()
    {


    }
}
