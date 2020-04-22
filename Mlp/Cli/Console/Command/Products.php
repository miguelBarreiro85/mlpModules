<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mlp\Cli\Console\Command;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Mlp\Cli\Model\ProdutoInterno;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mlp\Cli\Helper\imagesHelper as imagesHelper;
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

    

    private $categoryManager;
    public function __construct(
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
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $changeManufacturer = $input->getOption(self::CHANGE_MANUFACTURER);
        $oldManufacturer = $input->getArgument('oldManufacturer');
        $newManufacturer = $input->getArgument('newManufacturer');
        if ($changeManufacturer) {
            $this->changeManufacturer($oldManufacturer,$newManufacturer);
            $output->writeln('<info>ACabei</info>');
        }
        $addSorefozImages = $input->getOption(self::ADD_SOREFOZ_IMAGES);
        if($addSorefozImages) {
            $this->addImages(null);
        }
        $changeCat = $input->getOption(self::CHANGE_PRODUCTS_CATEGORIES);
        $oldCat = $input->getArgument('oldCat');
        $newCat = $input->getArgument('newCat');
        if($changeCat){
            $this->changeCategories($oldCat,$newCat);
        }
        $deleteProductsByCategoryId = $input->getOption(self::DELETE_PRODUCTS_BY_CATEGORY_ID);
        if($deleteProductsByCategoryId) {
            $this->categoryManager->deleteProductsByCategoryId($oldCat);
        }
        else {
            throw new \InvalidArgumentException('Option  ELSE');
        }

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
        foreach ($this->loadCsv->loadCsv('tot_jlcb_utf.csv',";") as $data) {
            print_r($data);
            $row++;
            print_r($row." - ");
            $this->sorefoz->setSorefozData($data);
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
}
