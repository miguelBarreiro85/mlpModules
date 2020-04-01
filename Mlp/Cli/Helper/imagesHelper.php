<?php


namespace Mlp\Cli\Helper;


class imagesHelper
{
    public static function getImages($sku, $img, $etiqueta,$directory){
        try {
            if (preg_match('/^http/', (string)$etiqueta) == 1) {
                $ch = curl_init($etiqueta);
                $fp = fopen($directory->getRoot()."/pub/media/catalog/product/" . $sku. '_e.jpeg', 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch,CURLOPT_TIMEOUT,2);
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                if (curl_exec($ch)){
                    curl_close($ch);
                    fclose($fp);
                }else {
                    unlink($directory->getRoot()."/pub/media/catalog/product/" . $sku . "_e.jpeg");
                }
            }

        } catch (\Exception $ex) {
            print_r($ex->getMessage());
        }
        try {
            if (preg_match('/^http/', $img) == 1) {
                $ch = curl_init($img);
                $fp = fopen($directory->getRoot()."/pub/media/catalog/product/" . $sku . ".jpeg", 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
                curl_setopt($ch,CURLOPT_TIMEOUT,2);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                if (curl_exec($ch)){
                    curl_close($ch);
                    fclose($fp);
                }else {
                    unlink($directory->getRoot()."/pub/media/catalog/product/" . $sku . ".jpeg");
                }


            }

        } catch (\Exception $ex) {
            print_r($ex->getMessage());
        }
    }
}
