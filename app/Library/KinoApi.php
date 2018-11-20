<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 06.11.2018
 * Time: 6:53
 */

namespace App\Library;


class KinoApi
{

    public $min_seconds_to_sleep = 10;
    public $max_seconds_to_sleep = 60;

    public function bolshoi_post($url, $post_fields) {
        $ch = curl_init($url);
        $header = array();
        $header[] = "application/json";
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.84 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_MAXREDIRS,99);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post_fields);

        $s=curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if (strlen($err)>0) {
            return false;
        }

        $this->sleep($this->max_seconds_to_sleep);
        return $s;
    }

    public function bolshoi($url) {
        $ch = curl_init($url);
        $header = array();
        $header[] = "Accept: */*";
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.84 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_MAXREDIRS,99);
        $s=curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if (strlen($err)>0) {
            return false;
        }
        //$this->sleep($this->max_seconds_to_sleep);
        return $s;
    }

    public function get($url, $referer) {
        $ch = curl_init($url);
        $header = array();
        $header[] = "Referer: " . $referer;
        $header[] =	"Origin: https://kinowidget.kinoplan.ru";
        $header[] = "Content-type: application/json";
        $header[] = "X-Application-Token: 51859cc5bb6556ae4cdc19e92b5ff834";
        $header[] = "X-Platform: widget";
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.84 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_MAXREDIRS,99);

        $s=curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if (strlen($err)>0) {
            return false;
        }
        $this->sleep($this->max_seconds_to_sleep);
        return $s;
    }

    public function sleep($seconds) {
        if ($seconds < $this->min_seconds_to_sleep)
            $seconds = $this->min_seconds_to_sleep + 1;
        sleep(rand($this->min_seconds_to_sleep, $seconds));
    }
}