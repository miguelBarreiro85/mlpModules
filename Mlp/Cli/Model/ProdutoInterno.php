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
    public $sku;
    public $name;
    public $gama;
    public $familia;
    public $subFamilia;
    public $description;
    public $meta_description;
    public $manufacturer;
    public $length;
    public $width;
    public $height;
    public $weight;
    public $price;
    public $status;
    public $image;
    public $classeEnergetica;
    public $imageEnergetica;
    public $stock;
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
        $attributes = $this->attributeManager->getSpecialAttributes($this->gama, $this->familia, $this->subFamilia, $this->description, $this->name);
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
    
    public function add_product($logger, $imgName) {
        $product = $this->productFactory->create();
        $product->setSku($this->sku);
        $product->setName($this->name);
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        //Set Categories
        $pCategories = $this->categoryManager->setCategories($this->gama, $this->familia, $this->subFamilia, $this->name);

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

        $this->setCategories($product,$pCategories);
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
            print_r($product->getSku()." - ");
        } catch (\Exception $exception) {
            $logger->info(" - " . $this->sku . " Save product: Exception:  " . $exception->getMessage());
            print_r("- " . $exception->getMessage() . " Save product exception" . "\n");
            return null;
        }

        //Adicionar opções de garantia e instalação
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



    private function setCategories($product, $pCategories)
    {
        
        $categories = $this->categoryManager->getCategoriesArray();
        try {
            if (isset($pCategories['subfamilia'])){
                $product->setCategoryIds([$categories[$pCategories['gama']],
                    $categories[$pCategories['familia']], $categories[$pCategories['subfamilia']]]);
            }else {
                $product->setCategoryIds([$categories[$pCategories['gama']],
                    $categories[$pCategories['familia']]]);
            }

        } catch (\Exception $ex) { 
            print_r(" - ".$ex->getMessage()." - ");
            //Adicionar nova categoria
            try{
                $this->categoryManager->createCategory($pCategories['gama'], $pCategories['familia'], $pCategories['subfamilia'], $categories);
            }catch (\Exception $ex){
                print_r(" - Erro ao adicionar nova categtoria ". $ex->getMessage());
            }
            try{
                $categories = $this->categoryManager->getCategoriesArray();
                if (isset($pCategories['subfamilia'])){
                    $product->setCategoryIds([$categories[$pCategories['gama']],
                        $categories[$pCategories['familia']], $categories[$pCategories['subfamilia']]]);
                }else {
                    $product->setCategoryIds([$categories[$pCategories['gama']],
                        $categories[$pCategories['familia']]]);
                }
            }catch(\Exception $e){
                print_r(" - Erro ao atribuir categoria: ".$e->getMessage());
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

    public function updatePrice($sku, $price){
        try{
            $product = $this->productRepository->get($sku, true, null, true);
            $product->setPrice($price);
            $this->productRepository->save($product);
            print_r("price updated - " . $sku . "\n");
        }catch (\Exception $ex){
            print_r("update price exception - " . $ex->getMessage() . "\n");
        }
    }
    
    public function addSpecialAttributesOrima(\Magento\Catalog\Api\Data\ProductInterface $product, \Zend\Log\Logger $logger)
    {
    }




}



