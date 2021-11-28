<?php
namespace Systems\Lib;

class BpjsService
{
    protected static $lastStatus = null;

    public static function get($url, $datafields = [], $consid, $secretkey)
    {
        return self::request('GET', $url, $datafields, $consid, $secretkey);
    }

    public static function post($url, $datafields = [], $consid, $secretkey)
    {
        return self::request2('POST', $url, $datafields, $consid, $secretkey);
    }

    public static function put($url, $datafields = [], $consid, $secretkey)
    {
        return self::request2('PUT', $url, $datafields, $consid, $secretkey);
    }

    public static function delete($url, $datafields = [],  $consid, $secretkey)
    {
        return self::request2('DELETE', $url, $datafields, $consid, $secretkey);
    }

    public static function getStatus()
    {
        return self::$lastStatus;
    }

    protected static function request($type, $url, $datafields, $consid, $secretkey)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $consid."&".$tStamp, $secretkey, true);
        $encodedSignature = base64_encode($signature);
        $ch = curl_init();
        $headers = array(
         'X-cons-id: '.$consid.'',
         'X-timestamp: '.$tStamp.'' ,
         'X-signature: '.$encodedSignature.'',
         'Content-Type:application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

        if (!empty($datafields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datafields));
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        self::$lastStatus = curl_error($ch);
        curl_close($ch);

        return $output;
    }
    protected static function request2($type, $url, $datafields, $consid, $secretkey)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $consid."&".$tStamp, $secretkey, true);
        $encodedSignature = base64_encode($signature);
        $ch = curl_init();
        $headers = array(
         'X-cons-id: '.$consid.'',
         'X-timestamp: '.$tStamp.'' ,
         'X-signature: '.$encodedSignature.'',
         'Content-Type:Application/x-www-form-urlencoded',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

        if (!empty($datafields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datafields);
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        self::$lastStatus = curl_error($ch);
        curl_close($ch);

        return $output;
    }
}
