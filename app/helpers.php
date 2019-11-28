<?php

use App\Models\Setting;
use Illuminate\Support\Facades\App;

if (! function_exists('setting')) {


    /**
     * @param null $key
     * @param null $default
     * @return Setting|bool|mixed
     */
    function setting($key = null, $default = null)
     {

         if ($key === null) {
             return new Setting();
         }

         if (is_array($key)) {
             return  Setting::set($key[0], $key[1]);
         }

         /** @var Setting $value */
         $value =  Setting::get($key);

         return $value ?? value($default);
     }
 }

if (! function_exists('requestIsConsole')) {

    function requestIsConsole()
    {
        return defined('STDIN') || strpos(PHP_SAPI, 'cli') !== false;
    }
}

if(!function_exists('snake_case')){
    function snake_case($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}

if(!function_exists('send_sms_infobip')){
    function send_sms_infobip(string $from,string $to, string $text,string $token = 'QWxhZGRpbjpvcGVuIHNlc2FtZQ==', $is_test = false){
        $data = [
            'from' => $from,
            'to' => $to,
            'text' => $text,
        ];
        $base_url = $is_test? '':'';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://{$base_url}/sms/2/text/single",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                "authorization: Basic {$token}",
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo /*"cURL Error #:" . */$err;
        } else {
            echo $response;
        }
    }
}

