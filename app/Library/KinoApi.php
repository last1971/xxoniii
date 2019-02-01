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

    public $min_seconds_to_sleep = 3;
    public $max_seconds_to_sleep = 20;

    public function get_chunck_https($host, $path) {
        sleep(10);
        $saveToFile = '';
        $fp = fsockopen('ssl://'. $host, 443, $errno, $error, 30);
        $readBlockSize = 512;

        if ($fp) {

            $wfp = true;

            if ($wfp) {
                $request = "GET $path HTTP/1.1\r\n";
                $request .= "Host: $host\r\n";
                $request .= "Connection: close\r\n";
                $request .= "User-Agent: php-download/1.0\r\n";
                $request .= "\r\n";

                fwrite($fp, $request);

                $body_start = false;
                $content_length = false;
                $chunk_length = false;

                $startLine = fgets($fp, 128);

                if ($startLine && preg_match('#^HTTP/1.\d?\s+200\s+#', $startLine)) {
                    while (!feof($fp)) {
                        if (!$body_start) {
                            $header = fgets($fp, 128);
                            //echo $header;
                            $colon_pos = strpos($header, ':');
                            $header_name = strtolower(trim(substr($header, 0, $colon_pos)));
                            $header_value = trim(substr($header, $colon_pos+1));
                            if ($header_name == 'content-md5') {
                                $md5sum = bin2hex(base64_decode($header_value));
                            } else if ($header_name == 'content-length') {
                                $content_length = (int) $header_value;
                            }
                            if ($header == "\r\n") {
                                $body_start = true;
        //                        echo "Reading data...\n";
                            }
                        } else {

                            if ($content_length !== false && $content_length > 0) {
                                $data = fread($fp, $readBlockSize);
                                $saveToFile .= $data;
                            } else {
                                if ($chunk_length === false) {
                                    $data = trim(fgets($fp, 128));
                                    $chunk_length = hexdec($data);
                                } else if ($chunk_length > 0) {
                                    $read_length = $chunk_length > $readBlockSize ? $readBlockSize : $chunk_length;
                                    $chunk_length -= $read_length;
                                    $data = fread($fp, $read_length);
                                    $saveToFile .= $data;
                                    if ($chunk_length <= 0) {
                                        fseek($fp, 2, SEEK_CUR);
                                        $chunk_length = false;
                                    }
                                } else {
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    return "Failed to read data: " . $startLine . "\n";
                }
            }

            fclose($fp);
            return $saveToFile;
        } else {
            return 'Error: ' . $errno . '#' . $error . "\n";
        }
    }


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

        curl_setopt($ch,CURLOPT_TIMEOUT,120);
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