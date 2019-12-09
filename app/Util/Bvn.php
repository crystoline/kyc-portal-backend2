<?php


namespace App\Util;


use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Bvn
{
    public const CACHE_KEY =  'bvn_auth_token';
    /**
     * @var string
     */
    private $bvn_url;
    /**
     * @var string
     */
    private $user_name;
    /**
     * @var string
     */
    private $password;

    public function __construct()
    {
        $this->bvn_url = 'https://uplbc.com/bvn/api';
        $this->user_name = setting('bvn_user_id', 'capricon@upperlink.ng');
        $this->password = setting('bvn_password', 'test');
    }
    public static function post($url, array $data, $action_name, $headers = array())
    {
        // request()->header()
        $httpClient = new Client();


        $data_string = json_encode($data);
        $return = [];
        $headers = array_merge($headers, [
            'Content-Type' => 'application/json',
        ]);
        $headers_string = json_encode($headers);
        try {

            $response = $httpClient->request('POST', $url, [
                'headers' => $headers,
                'body' => $data_string,
                'verify' => false,
            ]);

            $response_code = $response->getStatusCode();


            if ($response_code === 200) {
                $content = $response->getBody()->getContents();
                Log::error("Success ({$response_code}): occurred: \nMethod: {$action_name} \nUrl: {$url}\nHeaders: {$headers_string} \nData: {$data_string}\nResponse {$content}");

                $return = json_decode($content, true);

            } else {
                $content = $response->getBody()->getContents();
                Log::error("Http Error({$response_code}): occurred: \nMethod: {$action_name} \nUrl: {$url}\nHeaders: {$headers_string} \nData: {$data_string}\nResponse {$content}");
            }
        } catch (GuzzleException $e) {

        }catch (Exception $exception) {
            $message = $exception->getMessage();
            $trace = $exception->getTraceAsString();
            Log::error("Error: occurred: \nMethod: {$action_name} \nUrl: {$url} \nHeaders: {$headers_string}\nData: {$data_string}\nMessage {$message} \nTrace: {$trace}");
        }

        return $return;

    }

    /**
     * @return string
     */
    public function login (): string
    {
        return Cache::get(self::CACHE_KEY, static function () {


            $url = $this->bvn_url.'/login';
            $data =  [
                'email' => $this->user_name,
                'password' => $this->password
            ];
            $response = self::post($url, $data, __METHOD__);
            if(isset($response['token'])){
                Cache::put(self::CACHE_KEY, $response['token'], Carbon::now()->addMinutes(30));
                return $response['token'];
            }
            return '';
        });
    }

    public function booleanSingle ($request_data){

    }

    /**
     * @param $bvn
     * @return array|mixed
     */
    public function otherPartiesSingle ($bvn){
        $token =  $this->login();
        if($token){
            $url =  $this->bvn_url.'/verify/other-parties';
            $data = [ 'bvn' => [$bvn]];
            $headers = [
                'Authorization' => "Bearer {$token}"
            ];
            return self::post($url, $data, __METHOD__, $headers);
        }
        return [];
    }
}