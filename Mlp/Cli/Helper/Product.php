<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 19-03-2019
 * Time: 10:10
 */

namespace Mlp\Cli\Helper;

use Magento\Catalog\Model\ProductRepository as ProductRepository;
use Magento\Catalog\Model\ProductFactory as ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use \Mlp\Cli\Helper\Category as CategoryManager;
use \Mlp\Cli\Helper\Data as DataAttributeOptions;
use \Mlp\Cli\Helper\Attribute as Attribute;

class Product
{
    //atributos
    private $sku;
    private $name;
    private $gama;
    private $familia;
    private $subfamilia;
    private $description;
    private $meta_description;
    private $manufacter;
    private $length
    private $width;
    private $height;
    private $weight;
    private $price;
    //stock
    //Classes
    private $productRepository;
    private $productFactory;
    private $categoryManager;
    private $dataAttributeOptions;
    private $attributeManager;
    private $stockRegistry;
    
    public function __construct($sku, $name, $gama, $familia, $subfamilia,
                                $description, $meta_description, $manufacter,
                                    $length, $width, $height, $weight, $price,
                                        ProductRepository $productRepository,
                                        ProductFactory $productFactory,
                                        CategoryManager $categoryManager,
                                DataAttributeOptions $dataAttributeOptions,
                                Attribute $attributeManager, StockRegistryInterface $stockRegistry)
    {
        $this->sku = $sku;
        $this->name = $name;
        $this->gama = $gama;
        $this->familia = $familia;
        $this->subfamilia = $subfamilia;
        $this->description = $description;
        $this->meta_description = $meta_description;
        $this->manufacter = $manufacter;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->weight = $weight;
        $this->price = $price;

        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->categoryManager = $categoryManager;
        $this->dataAttributeOptions = $dataAttributeOptions;
        $this->attributeManager = $attributeManager;
        $this->stockRegistry = $stockRegistry;
    }

    public function add_product($categories, $logger) {
                $product = $this->productFactory->create();
                $product->setSku($this->sku);
                $product->setName($this->name);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $subFamilia = $this->categoryManager->setSubFamiliaSorefoz($this->subfamilia);
                $familia = $this->categoryManager->setFamiliaSorefoz($this->familia);
                $gama = $this->categoryManager->setGamaSorefoz($this->gama);
                $product->setCustomAttribute('description', $this->description);
                $product->setCustomAttribute('meta_description', $this->meta_description);
                $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', $this->manufacter);
                $product->setCustomAttribute('manufacturer', $optionId);
                $product->setCustomAttribute('ts_dimensions_length', $this->length / 10);
                $product->setCustomAttribute('ts_dimensions_width', $this->width / 10);
                $product->setCustomAttribute('ts_dimensions_height', $this->height / 10);
                $product->setCustomAttribute('tax_class_id', 2); //taxable goods id
                $product->setWeight($this->weight);
                $product->setWebsiteIds([1]);
                $attributeSetId = $this->attributeManager->getAttributeSetId($familia, $subFamilia);
                $product->setAttributeSetId($attributeSetId); // Attribute set id
                $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                $product->setTaxClassId(2); // Tax class id
                $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                $product->setCreatedAt(date("Y/m/d"));
                $product->setCustomAttribute('news_from_date', date("Y/m/d"));
                try {
                    $product->setCategoryIds([$categories[$gama], $categories[$familia], $categories[$subFamilia]]);
                } catch (\Exception $ex) { //Adicionar nova categoria
                    try{
                        print_r("\nErro ao adicionar nova categtoria ". $ex->getMessage() . "\n");
                        $this->categoryManager->createCategory($gama, $familia, $subFamilia, $categories);
                        $categories = $this->categoryManager->getCategoriesArray();
                        $product->setCategoryIds([$categories[$gama], $categories[$familia], $categories[$subFamilia]]);
                    }catch (\Exception $ex){
                        print_r($ex->getMessage() ."\n");
                    }

                }
                $this->setImages($product, $logger, $product->getSku() . "_e.jpeg");
                $this->setImages($product, $logger, $product->getSku() . ".jpeg");
                $product->setStatus(Status::STATUS_ENABLED);
                //Preço
                $preco = (int)str_replace(".","",$this->price);
                $preco = $preco * 1.30;
                $preco = $preco * 1.23;
                $product->setPrice($preco);
                //Salvar produto
                try {
                    $product->save();
                } catch (\Exception $exception) {
                    $logger->info($sku . " Deu merda a salvar: Exception:  " . $exception->getMessage());
                    print_r($exception->getMessage() . " Save product exception" . "\n");
                }
                if ($product->getOptions() == null){
                    $this->add_warranty_option($product, $gama, $familia, $subFamilia);
                    $value = $this->getInstallationValue($familia);
                    if ($value > 0){
                        $this->add_installation_option($product,$value);
                    }

                }

                $preco = (int)str_replace(".", "", $this->price);
                if ($preco < 400) {
                    $preco = $preco * 1.20;
                } else {
                    $preco = $preco * 1.15;
                }
                $product->setPrice($preco);
            }

        protected function setImages($product, $logger, $ImgName)
        {
            print_r($product->getSku());
            $baseMediaPath = $this->config->getBaseMediaPath();
            try {
                $images = $product->getMediaGalleryImages();
                if (!$images || $images->getSize() == 0) {
                    $product->addImageToMediaGallery($baseMediaPath . "/" . $ImgName, ['image', 'small_image', 'thumbnail'], false, false);
                }
            } catch (\RuntimeException $exception) {
                print_r("run time exception" . $exception->getMessage() . "\n");
            } catch (\Exception $localizedException) {
                $logger->info($product->getName() . "Image name" . $ImgName . "  Sem Imagem");
                print_r($ImgName . "  Sem Imagem ");
            }
        }

        public function setStock($stock){
            try {
                $product = $this->productRepository->get($this->sku, true, null, true);
                $stockItem = $this->stockRegistry->getStockItem($product->getId()); // load stock of that product
                switch ($stock) {
                    case 'Sim':
                        $stockItem->setIsInStock(true); //set updated data as your requirement
                        $stockItem->setQty(9); //set updated quantity
                        $stockItem->setManageStock(false);
                        $stockItem->setUseConfigNotifyStockQty(false);
                        break;
                    default:
                        $stockItem = $this->stockRegistry->getStockItem($product->getId()); // load stock of that product
                        $stockItem->setIsInStock(false); //set updated data as your requirement
                        $stockItem->setQty(0); //set updated quantity
                        $stockItem->setManageStock(false);
                        $stockItem->setUseConfigNotifyStockQty(false);
                        break;
                }
                $stockItem->save(); //save stock of item
                $this->stockRegistry->updateStockItemBySku($product->getSku(),$stockItem);
            }catch (\Exception $ex) {
                print_r("\nStock: " . $product->getSku()  . $ex->getMessage() . "\n");
            }
            try{
                $this->productRepository->save($product);
            }catch (\Exception $ex){
                print_r($ex->getMessage());
            }
        }
    }



