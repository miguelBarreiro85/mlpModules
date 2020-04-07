<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 19-03-2019
 * Time: 10:10
 */

namespace Mlp\Cli\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Model\ProductFactory as ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Validation\ValidationException;
use \Mlp\Cli\Helper\Category as CategoryManager;
use \Mlp\Cli\Helper\Data as DataAttributeOptions;
use \Mlp\Cli\Helper\Attribute as Attribute;
use \Mlp\Cli\Helper\ProductOptions as ProductOptions;
class ProdutoInterno
{
    //atributos
    private $sku;
    private $name;
    private $gama;
    private $familia;
    private $subfamilia;
    private $description;
    private $meta_description;
    private $manufacturer;
    private $length;
    private $width;
    private $height;
    private $weight;
    private $price;
    private $status;
    private $image;
    private $classeEnergetica;
    private $imageEnergetica;
    private $stock;
    //stock
    //Classes
    private $productFactory;
    private $categoryManager;
    private $dataAttributeOptions;
    private $attributeManager;
    private $config;
    private $optionFactory;
    private $productRepositoryInterface;
    private $directory;
    private $filterGroupBuilder;
    private $sourceItemRepositoryI;
    private $sourceItemIF;
    private $searchCriteriaBuilder;
    private $sourceItemSaveI;
    private $filterBuilder;
    private $imagesHelper;
    private $productResource;
    private $productOptions;

    public function __construct( ProductFactory $productFactory,
                                CategoryManager $categoryManager,
                                DataAttributeOptions $dataAttributeOptions,
                                Attribute $attributeManager,
                                Config $config,
                                OptionFactory $optionFactory,
                                ProductRepositoryInterface $productRepositoryInterface,
                                 \Magento\Catalog\Model\ResourceModel\Product $productResource,
                                \Magento\Framework\Filesystem\DirectoryList $directory,
                                 \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
                                 \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepositoryI,
                                 \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemIF,
                                 \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
                                 \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSaveI,
                                 \Magento\Framework\Api\FilterBuilder  $filterBuilder,
                                \Mlp\Cli\Helper\imagesHelper $imagesHelper,
                                \Mlp\Cli\Helper\ProductOptions $productOptions)
    {

        $this->directory = $directory;
        $this->productFactory = $productFactory;
        $this->categoryManager = $categoryManager;
        $this->dataAttributeOptions = $dataAttributeOptions;
        $this->attributeManager = $attributeManager;
        $this->config = $config;
        $this->optionFactory = $optionFactory;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->sourceItemRepositoryI = $sourceItemRepositoryI;
        $this->sourceItemIF = $sourceItemIF;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceItemSaveI = $sourceItemSaveI;
        $this->filterBuilder = $filterBuilder;
        $this->imagesHelper = $imagesHelper;
        $this->productResource = $productResource;
        $this->productOptions = $productOptions;
    }

    public function setData($sku, $name, $gama, $familia, $subfamilia,
                            $description, $meta_description, $manufacturer,
                            $length, $width, $height, $weight, $price) {
        $this->sku = $sku;
        $this->name = $name;
        $this->gama = $gama;
        $this->familia = $familia;
        $this->subfamilia = $subfamilia;
        $this->description = $description;
        $this->meta_description = $meta_description;
        $this->manufacturer = $manufacturer;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->weight = $weight;
        $this->price = $price;

    }



    public function addSpecialAttributesSorefoz(\Magento\Catalog\Model\Product $product,$logger){
        $attributes = $this->attributeManager->getSpecialAttributes($this->gama, $this->familia, $this->subfamilia, $this->description, $this->name);
        if (isset($attributes)){
            foreach ($attributes as $attribute) {
                $product->setCustomAttribute($attribute['code'], $attribute['value']);
                try {
                    $this -> productResource -> saveAttribute($product, $attribute['code']);
                } catch (\Exception $e) {
                    print_r($e->getMessage());
                }
            }
        }
        try {
            //$product->save();
        } catch (\Exception $exception) {
            $logger->info(" - " . $this->sku . " Save product: Exception:  " . $exception->getMessage());
            print_r("- " . $exception->getMessage() . " Save product exception" . "\n");
        }
    }
    public function add_product($categories, $logger, $imgName) {
        $product = $this->productFactory->create();
        $product->setSku($this->sku);
        $product->setName($this->name);
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        //Set Categories
        $pCategories = $this->categoryManager->setCategories($this->gama, $this->familia, $this->subfamilia, $this->name);

        $product->setCustomAttribute('description', $this->description);
        $product->setCustomAttribute('meta_description', $this->meta_description);
        $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', strval($this->manufacturer));
        $product->setCustomAttribute('manufacturer', $optionId);
        $product->setCustomAttribute('ts_dimensions_length', $this->length / 10);
        $product->setCustomAttribute('ts_dimensions_width', $this->width / 10);
        $product->setCustomAttribute('ts_dimensions_height', $this->height / 10);
        $product->setCustomAttribute('tax_class_id', 2); //taxable goods id
        $product->setWeight($this->weight);
        $product->setWebsiteIds([1]);
        $attributeSetId = $this->attributeManager->getAttributeSetId($pCategories['familia'], $pCategories['subfamilia']);
        $product->setAttributeSetId($attributeSetId); // Attribute set id
        $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
        $product->setTaxClassId(2); // Tax class id
        $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
        $product->setCreatedAt(date("Y/m/d"));
        $product->setCustomAttribute('news_from_date', date("Y/m/d"));

        $this->setCategories($product, $pCategories,$categories);


        $this->imagesHelper->getImages($this->sku,$this->image,$this->imageEnergetica);
        $this->imagesHelper->setImages($product, $logger, $imgName . "_e.jpeg");
        $this->imagesHelper->setImages($product, $logger, $imgName . ".jpeg");

        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        //Preço
        $product->setPrice($this->price);

        //Salvar produto
        try {
            print_r("saving product.. - ");
            $product = $this->productRepositoryInterface->save($product);
        } catch (\Exception $exception) {
            $logger->info(" - " . $this->sku . " Save product: Exception:  " . $exception->getMessage());
            print_r("- " . $exception->getMessage() . " Save product exception" . "\n");
            return null;
        }
        try{
            $this->productOptions->add_warranty_option($product,$pCategories['gama'], $pCategories['familia'], $pCategories['subfamilia']);
            $value = $this->productOptions->getInstallationValue($pCategories['familia']);
            if ($value > 0){
                $this->productOptions->add_installation_option($product,$value);
            }
            print_r("saving Options.. - ");
            $this->productRepositoryInterface->save($product);

            return $product;
        }catch (\Exception $e){
            print_r("add options exception - ".$e->getMessage());
        }

    }



    private function setCategories($product,array $pCategories, $categories)
    {
        try {
            if (isset($pCategories['subfamilia'])){
                $product->setCategoryIds([$categories[$pCategories['gama']],
                    $categories[$pCategories['familia']], $categories[$pCategories['subfamilia']]]);
            }else {
                $product->setCategoryIds([$categories[$pCategories['gama']],
                    $categories[$pCategories['familia']]]);
            }

        } catch (\Exception $ex) { //Adicionar nova categoria
            try{
                $this->categoryManager->createCategory($pCategories['gama'], $pCategories['familia'], $pCategories['subfamilia'], $categories);
                $categories = $this->categoryManager->getCategoriesArray();
                if (isset($pCategories['subfamilia'])){
                    $product->setCategoryIds([$categories[$pCategories['gama']],
                        $categories[$pCategories['familia']], $categories[$pCategories['subfamilia']]]);
                }else {
                    $product->setCategoryIds([$categories[$pCategories['gama']],
                        $categories[$pCategories['familia']]]);
                }
            }catch (\Exception $ex){
                print_r("\nErro ao adicionar nova categtoria ". $ex->getMessage() .
                    " ". $product->getSku() ."\n");
                print_r($pCategories);
            }

        }
    }
    public function setStock($sku,$source)
    {
        $filterSku = $this->filterBuilder
            -> setField("sku")
            -> setValue($sku)
            -> create();
        $sourceFilter = $this->filterBuilder
            -> setField("source_code")
            -> setValue($source)
            -> create();

        $filterGroup1 = $this->filterGroupBuilder->setFilters([$filterSku])->create();
        $filterGroup2 = $this->filterGroupBuilder->setFilters([$sourceFilter])->create();
        $searchC = $this->searchCriteriaBuilder->setFilterGroups([$filterGroup1, $filterGroup2]) -> create();
        $sourceItem = $this -> sourceItemRepositoryI->getList($searchC) -> getItems();

        if (empty($sourceItem)) {
            $item = $this -> sourceItemIF -> create();
            $item -> setQuantity($this->stock);
            $item -> setStatus(1);
            $item -> setSku($sku);
            $item -> setSourceCode($source);
            try {
                $this -> sourceItemSaveI -> execute([$item]);
            } catch (CouldNotSaveException $e) {
                print_r($e->getMessage());
            } catch (InputException $e) {
                print_r($e->getMessage());
            } catch (ValidationException $e) {
                print_r($e->getMessage());
            }
        } else {
            foreach ($sourceItem as $item) {
                $item -> setQuantity($this->stock);
                try {
                    $this -> sourceItemSaveI -> execute([$item]);
                } catch (CouldNotSaveException $e) {
                    print_r($e->getMessage());
                } catch (InputException $e) {
                    print_r($e->getMessage());
                } catch (ValidationException $e) {
                    print_r($e->getMessage());
                }
            }
        }
    }


    public function setSorefozData($data) {
        $functionTim = function ($data){
            return trim($data);
        };


        if (preg_match("/sim/i",$data[27]) == 1){
            $stock = 1;
        }else {
            $stock = 0;
        }

        if (preg_match("/sim/i",$data[16]) == 1) {
            $status = 2;
        }else{
            $status = 1;
        }
        $data = array_map($functionTim,$data);
        $this -> sku = $data[18];
        $this -> name = $data[1];
        $this -> gama = $data[5];
        $this -> familia = $data[7];
        $this -> subfamilia = $data[9];
        $this -> description = $data[25];
        $this -> meta_description = $data[24];
        $this -> manufacturer = $data[3];
        $this -> length = (int)$data[20];
        $this -> width = (int)$data[21];
        $this -> height = (int)$data[22];
        $this -> weight = (int)$data[19];
        $this -> price = (int)str_replace(".", "", $data[12]) * 1.23 * 1.30;
        $this->status = $status;
        $this->image = $data[23];
        $this->classeEnergetica = $data[25];
        $this->imageEnergetica = $data[26];
        $this->stock = $stock;
        return $this;
    }

    public function getProductSorefozStatus()
    {
        if ($this->status == 1) {
            return 1;
        }
        else{
            return 0;
        }
    }

    public function getSku(){
        return $this->sku;
    }

    public function getSubFamilia()
    {
        return $this->subfamilia;
    }

    public function setOrimaData($data)
    {
        /*
         * 0 - Nome
         * 1 - ref orima
         * 2 - preço liquido
         * 3 - stock
         * 4 - gama
         * 5 - familia
         * 6 - subfamilia
         * 7 - marca
         * 8 - EAN
         * 9 - Detalhes
         * 10 - Imagem
         * 11 - etiqueta energetica
         * 12 - manual de instruções
         * 13 - esquema tecnico
         */
        $functionTim = function ($data){
            return trim($data);
        };

        $data = array_map($functionTim,$data);
        $this -> sku = $data[8];
        $this -> name = $data[0];
        $this -> gama = $data[4];
        $this -> familia = $data[5];
        $this -> subfamilia = $data[6];
        $this -> description = $data[9];
        $this -> meta_description = $data[9];
        $this -> manufacturer = $data[7];
        $this -> length = null;
        $this -> width = null;
        $this -> height = null;
        $this -> weight = null;
        $this -> price = (int)trim($data[2]) * 1.23 * 1.20;
        $this->status = 1;
        $this->image = $data[10];
        $this->classeEnergetica = null;
        $this->imageEnergetica = $data[11];
        $this->stock = (int)filter_var($data[3], FILTER_SANITIZE_NUMBER_INT);
        return $this;
    }

    public function setOrimaCategories()
    {
        try {
            [$mlpGama, $mlpFamilia, $mlpSubFamilia] = CategoryManager::setCategoriesOrima($this -> gama, $this -> familia, $this -> subfamilia);
        } catch (Exception $e) {
        }
        $this->gama = $mlpGama;
        $this->familia = $mlpFamilia;
        $this->subfamilia = $mlpSubFamilia;
    }
    public function addSpecialAttributesOrima(\Magento\Catalog\Api\Data\ProductInterface $product, \Zend\Log\Logger $logger)
    {
    }

    public function getImageUrl()
    {
        return $this->image;
    }

    public function getEtiquetaUrl()
    {
        return $this->imageEnergetica;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function setManufacturer(string $manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    public function getManufacturer()
    {
        return $this->manufacturer;
    }

}



