<?php


namespace Mlp\Cli\Helper;



class imagesHelper
{
    private $config;
    private $directory;

    public function __construct(\Magento\Framework\Filesystem\DirectoryList $directory,
                                \Magento\Catalog\Model\Product\Media\Config $config)
    {
        $this->directory = $directory;
        $this->config = $config;
    }

    public function getImages($sku, $img = null, $etiqueta = null){
        try {
            if (preg_match('/^http/', (string)$etiqueta) == 1) {
                $ch = curl_init($etiqueta);
                $fp = fopen($this->directory->getRoot()."/pub/media/catalog/product/" . $sku. '_e.jpeg', 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch,CURLOPT_TIMEOUT,0);
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                if (curl_exec($ch)){
                    curl_close($ch);
                    fclose($fp);
                }else {
                    unlink($this->directory->getRoot()."/pub/media/catalog/product/" . $sku . "_e.jpeg");
                }
            }

        } catch (\Exception $ex) {
            print_r($ex->getMessage());
        }
        try {
            if (preg_match('/^http/', $img) == 1) {
                $ch = curl_init($img);
                $fp = fopen($this->directory->getRoot()."/pub/media/catalog/product/" . $sku . ".jpeg", 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
                curl_setopt($ch,CURLOPT_TIMEOUT,0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                if (curl_exec($ch)){
                    curl_close($ch);
                    fclose($fp);
                }else {
                    unlink($this->directory->getRoot()."/pub/media/catalog/product/" . $sku . ".jpeg");
                }


            }

        } catch (\Exception $ex) {
            print_r($ex->getMessage());
        }
    }

    public function setImages($product, $logger, $ImgName)
    {
        $baseMediaPath = $this->config->getBaseMediaPath();
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        try{
            $type = finfo_file($finfo, $this->directory->getRoot()."/pub/media/".$baseMediaPath. "/" . $ImgName);
        }catch (\Exception $exception){
            //finfo exception
            //print_r("Product.php setImages: ". $exception );
        }
        if (isset($type) && in_array($type, array("image/png", "image/jpeg", "image/gif"))) {
            //this is a image
            try {
                $images = $product->getMediaGalleryImages();
                if (!$images || $images->getSize() == 0) {
                    $product->addImageToMediaGallery($baseMediaPath . "/" . $ImgName, ['image', 'small_image', 'thumbnail'], false, false);
                }
            } catch (\RuntimeException $exception) {
                print_r("run time exception" . $exception->getMessage() . "\n");
            } catch (\Exception $localizedException) {
                $logger->info("SEM IMAGEM: " . $product->getSku());
            }
        } else {
            //not a image
            $logger->info("SEM IMAGEM: " . $product->getSku());
        }

    }
}
