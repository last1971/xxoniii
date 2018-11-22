<?php

use Illuminate\Foundation\Inspiring;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('test', function () {
    $url = 'https://kinomax.ru/order/checkstatus?seq=8E73781D-5D94-3074-2C60-4A02497D384C';
    $saveToFile = '';
    $host = 'kinomax.ru';
    $path = '/order/checkstatus?seq=8E73781D-5D94-3074-2C60-4A02497D384C';


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
            $md5sum = '';
            $content_length = false;
            $chunk_length = false;

            $startLine = fgets($fp, 128);

            if ($startLine && preg_match('#^HTTP/1.\d?\s+200\s+#', $startLine)) {
                while (!feof($fp)) {
                    if (!$body_start) {
                        $header = fgets($fp, 128);
                        echo $header;
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
                            echo "Reading data...\n";
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
                echo "Failed to read data: " . $startLine . "\n";
            }
        }

        fclose($fp);
        echo $saveToFile;
    } else {
        echo 'Error: ' . $errno . '#' . $error . "\n";
    }
})->describe('test');

