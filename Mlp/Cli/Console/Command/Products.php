<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mlp\Cli\Console\Command;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Mlp\Cli\Model\ProdutoInterno;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mlp\Cli\Helper\imagesHelper as imagesHelper;
use Mlp\Cli\Helper\CategoriesConstants as Cat;
/**
 * Class Products
 */
class Products extends Command
{
    /**
     * Name argument
     */
    const NAME_ARGUMENT = 'products';
    /**
     * Allow option
     */
    const CHANGE_MANUFACTURER = 'change-manufacturer';

    /**
     * Anonymous name
     */
    const ANONYMOUS_NAME = 'Anonymous';

    const ADD_SOREFOZ_IMAGES = "add-sorefoz-images";

    const CHANGE_PRODUCTS_CATEGORIES = 'change-products-categories';

    const DELETE_PRODUCTS_BY_CATEGORY_ID = 'delete-products-by-category-id';

    const DISABLE_PRODUCTS = 'disable-products';
    const UNIQUE_PRODUCTS_MANUFACTURER_BY_VENDOR = 'list-unique-manufacturers-by-vendor';
    const ADD_PRODUCTS = 'add-products';
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private $sortOrderBuilder;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productResource;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;
    /**
     * @var \Mlp\Cli\Helper\Data
     */
    private $dataAttributeOptions;

    /**
     * {@inheritdoc}
     */

    private $productCollectionFactory;

    private $resourceConnection;

    private $productoInterno;

    private $sorefoz;

    private $directory;

    private $loadCsv;

    private $sourceItemRepositoryI;
    

    private $categoryManager;
    public function __construct(
                                \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepositoryI,
                                \Magento\Framework\App\ResourceConnection $resourceConnection,
                                \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
                                \Mlp\Cli\Model\ProdutoInterno $productoInterno,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
                                SearchCriteriaBuilder $searchCriteriaBuilder,
                                FilterBuilder $filterBuilder,
                                \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
                                \Magento\Catalog\Model\ResourceModel\Product $productResource,
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Helper\Data  $dataAttributeOptions,
                                \Mlp\Cli\Console\Command\Sorefoz $sorefoz,
                                \Magento\Framework\Filesystem\DirectoryList $directory,
                                \Mlp\Cli\Helper\Category $categoryManager,
                                \Mlp\Cli\Helper\LoadCsv $loadCsv)
    {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->productResource = $productResource;
        $this->state = $state;
        $this->dataAttributeOptions =$dataAttributeOptions;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->productoInterno = $productoInterno;
        $this->sorefoz = $sorefoz;
        $this->directory = $directory;
        $this->loadCsv = $loadCsv;
        $this->categoryManager = $categoryManager;
        $this->sourceItemRepositoryI = $sourceItemRepositoryI;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('Mlp:Products')
            ->setDescription('Manage Products')
            ->setDefinition([
                new InputOption(
                    self::CHANGE_MANUFACTURER,
                    '-m',
                    InputOption::VALUE_NONE,
                    'change manufacturer'
                ),
                new InputOption(
                    self::ADD_SOREFOZ_IMAGES,
                    '-i',
                    InputOption::VALUE_NONE,
                    'Add Sorefoz Images'
                ),
                new InputOption(
                    self::CHANGE_PRODUCTS_CATEGORIES,
                    '-c',
                    InputOption::VALUE_NONE,
                    'Add Sorefoz Images'
                ),
                new InputOption(
                    self::DELETE_PRODUCTS_BY_CATEGORY_ID,
                    '-d',
                    InputOption::VALUE_NONE,
                    'Delete products by category id'
                ),
                new InputOption(
                    self::DISABLE_PRODUCTS,
                    '-D',
                    InputOption::VALUE_NONE,
                    'Disable products without stock'
                ),
                new InputOption(
                    self::UNIQUE_PRODUCTS_MANUFACTURER_BY_VENDOR,
                    '-U',
                    InputOption::VALUE_NONE,
                    'SHOW UNIQUE MANUFACTURER BY VENDOR'
                ),
                new InputOption(
                    self::ADD_PRODUCTS,
                    '-a',
                    InputOption::VALUE_NONE,
                    'ADD PRODUTCS'
                )
            ])
            ->addArgument('oldManufacturer', InputArgument::OPTIONAL, 'oldManufacturer')
            ->addArgument('newManufacturer', InputArgument::OPTIONAL, 'newManufacturer')
            ->addArgument('oldCat', InputArgument::OPTIONAL, 'oldCat')
            ->addArgument('newCat', InputArgument::OPTIONAL, 'newCat');;
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Mlp_Products.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $changeManufacturer = $input->getOption(self::CHANGE_MANUFACTURER);
        $oldManufacturer = $input->getArgument('oldManufacturer');
        $newManufacturer = $input->getArgument('newManufacturer');
        if ($changeManufacturer) {
            $this->changeManufacturer($oldManufacturer,$newManufacturer);
            $output->writeln('<info>ACabei</info>');
            return true;
        }
        $addSorefozImages = $input->getOption(self::ADD_SOREFOZ_IMAGES);
        if($addSorefozImages) {
            $this->addImages(null);
            return true;
        }
        $changeCat = $input->getOption(self::CHANGE_PRODUCTS_CATEGORIES);
        $oldCat = $input->getArgument('oldCat');
        $newCat = $input->getArgument('newCat');
        if($changeCat){
            $this->changeCategories($oldCat,$newCat);
            return true;
        }
        $deleteProductsByCategoryId = $input->getOption(self::DELETE_PRODUCTS_BY_CATEGORY_ID);
        if($deleteProductsByCategoryId) {
            print_r("delete");
            $this->categoryManager->deleteProductsByCategoryId($oldCat);
            return true;
        }
        $disableProductsWithoutStock = $input->getOption(self::DISABLE_PRODUCTS);
        if ($disableProductsWithoutStock) {
            print_r("Disabling products without stock");
            $this->disableAllProductsSql();
            return true;
        }
        $uniqueManufacturer = $input->getOption(self::UNIQUE_PRODUCTS_MANUFACTURER_BY_VENDOR);
        if($uniqueManufacturer) {
            $this->detectUniqueManufaturers();
            return true;
        }
        $addProducts = $input->getOption(self::ADD_PRODUCTS);
        if($addProducts) {
            //$this->addProducts($logger);
            $this->disableAllProductsSql();
            return true;
        }
        else {
            throw new \InvalidArgumentException('Option  ELSE');
        }

    }


    private function addProducts($logger){
        //disable all products
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('status',Status::STATUS_ENABLED)->create();
        $products = $this->productRepository->getList($searchCriteria)->getItems();
        foreach($products as $product){
            try {
                $product->setStatus(Status::STATUS_DISABLED);
                print_r($product->getSku()."\n");
                $logger->info(Cat::WARN_DISABLING_PRODUCT.$product->getSku());
                $this->productRepository->save($product);
            }catch (\Exception $e) {
                $logger->info(Cat::ERROR_DISABLING_PRODUCT.$product->getSku());
            }
            
        }
    }

    private function disableAllProductsSql(){
        $sqlStatusAttributeId = 'SELECT attribute_id from eav_attribute where attribute_code like "status"';
        $connection =  $this->resourceConnection->getConnection();
        $statusAttributeId = $connection->fetchAll($sqlStatusAttributeId);
        $sqlUpdateStatus = 'UPDATE catalog_product_entity_int 
                            SET value = 2
                            WHERE attribute_id = '.$statusAttributeId[0]["attribute_id"];
        $connection->query($sqlUpdateStatus);
        
        
        
    }
    protected function getAttribute($attCode)
    {
        $attribute = $this->entityAttribute->loadByCode('catalog_product', $attCode);
        return $attribute->getData();
    }

    private function changeManufacturer(?string $oldManufacturer, ?string $newManufacturer)
    {
        //select attribute_id from eav_attribute where attribute_code like "manufacturer"; 83 de momento
        //select option_id from eav_attribute_option_value where value like "orima"; para ver qual é o numero $oldManufacturerCode

        $sqlManufacturerAttributeId = 'SELECT attribute_id from eav_attribute where attribute_code like "manufacturer"';
        $connection =  $this->resourceConnection->getConnection();
        $dataManufacturerAttributeId = $connection->fetchAll($sqlManufacturerAttributeId);
        $oldManufacturerCode = $dataManufacturerAttributeId[0]["attribute_id"];
        $sqlOldManufacturerCode = 'select option_id from eav_attribute_option_value where value like"'.$oldManufacturer.'"';
        $dataOldManufacturerCode = $connection->fetchAll($sqlOldManufacturerCode);
        $collection = $this->productCollectionFactory->create();
        $collection->getSelect()
            ->joinInner(["manufacturer" => "catalog_product_entity_int"],
                'e.entity_id = manufacturer.entity_id AND manufacturer.attribute_id ='.$oldManufacturerCode.' AND manufacturer.value ='.(int)$dataOldManufacturerCode[0]["option_id"],
                []);
        foreach ($collection as $product) {
            print_r($product->getSku()."\n");
            $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', $newManufacturer);
            $product->setCustomAttribute('manufacturer', $optionId);
            $this->productResource->saveAttribute($product,"manufacturer");
        }
    }

    private function addImages($categoriesFilter) 
    {

        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Sorefoz.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        
        $row = 0;
        foreach ($this->loadCsv->loadCsv('/Sorefoz/tot_jlcb_utf.csv',";") as $data) {
            print_r($data);
            $row++;
            print_r($row." - ");
            $this->sorefoz->setSorefozData($data,$logger);
            if (strlen($this->productoInterno->sku) != 13) {
                print_r("invalid sku - \n");
                continue;
            }
            if (!is_null($categoriesFilter)){
                if (strcmp($categoriesFilter,$this->productoInterno->subFamilia) != 0){
                    print_r($this->productoInterno->sku . " - Fora de Gama \n");
                    continue;
                }
            }
            if($this->sorefoz->getProductSorefozStatus() == 0){
                print_r(" - disabled\n");
                continue;
            }
            try {
                $product = $this -> productRepository -> get($this->productoInterno->sku, true, null, true);
                imagesHelper::getImages($this->productoInterno->sku, $this->productoInterno->image, $this->productoInterno->imageEnergetica);
                imagesHelper::setImages($product, $logger, $this->productoInterno->sku);
            } catch (\Exception $exception) {
                print_r($exception);
            }
        }
    }
    
    private function changeCategories($oldCat,$newCat){
        $this->categoryManager->changeProductCategories($oldCat,$newCat);
    }

    private function disableOutOfStockProducts(){
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('status',1,'in')->create();
        $products = $this->productRepository->getList($searchCriteria)->getItems();
        print_r("Products: ".count($products));
        foreach ($products as $product) {
            //print_r($product->getSku());
            print_r($product->getSku()."\n");
            $searchC = $this->searchCriteriaBuilder->addFilter('sku',$product->getSku()) -> create();
            $sourceItems = $this -> sourceItemRepositoryI->getList($searchC) -> getItems();
            foreach ($sourceItems as $item) {
                print_r("sku: ".$product->getSku()." - source: ".$item->getSourceCode(). " - Quantity: ".$item->getQuantity()."\n");
                if ($item->getQuantity() != 0){
                    $product->setStatus(1);
                    $this->productRepository->save($product);
                    break;
                }
            }
            //Se não saiu no break é porque os stocks estão todos a 0 podemos fazer disabled para remover mais tarde
            $product->setStatus(2);
            $this->productRepository->save($product);
        }
    }


    private function detectUniqueManufaturers(){
        $sorefozManufacturers = $this->getManufacturers('sorefoz');
        print_r($sorefozManufacturers);

        $expertManufacturers = $this->getManufacturers('expert');
        print_r($expertManufacturers);

        $expertUniqueManufacturer = [];
        foreach($expertManufacturers as $key => $value) {
            if (!array_key_exists($key, $sorefozManufacturers)) {
                if ((int)$value > 50){
                    $expertUniqueManufacturer[$key] = $value;
                }
                
            }
        }
        print_r($expertUniqueManufacturer);
    }

    private function getManufacturers($vendor) {
        $manufacturers = [];
        if (preg_match("/sorefoz/", $vendor) == 1) {
            $fileUrl = $this->directory->getRoot() ."/app/code/Mlp/Cli/Csv/Sorefoz/tot_jlcb_utf.csv";
            $manufacturerColumn = 3;
        }elseif (preg_match("/expert/", $vendor) == 1) {
            $fileUrl = $this->directory->getRoot() ."/app/code/Mlp/Cli/Csv/Expert/ExpertNovo.csv";
            $manufacturerColumn = 4;
        }

        if (($handle = fopen($fileUrl, "r")) !== FALSE) {
            //ignorar a 1ª linha,
            fgetcsv($handle, 5000, ";");
            while (($data = fgetcsv($handle, 5000, ";")) !== FALSE) {
                if (!array_key_exists(trim($data[$manufacturerColumn]),$manufacturers)){
                    $manufacturers[trim($data[$manufacturerColumn])] = 1; 
                }else {
                    $manufacturers[trim($data[$manufacturerColumn])] = $manufacturers[trim($data[$manufacturerColumn])] + 1;
                }
            }
            fclose($handle);
        }
        return $manufacturers;
    }
}
