<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mlp\Cli\Console\Command;

use Braintree\Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Validation\ValidationException;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use function GuzzleHttp\default_ca_bundle;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use phpDocumentor\Reflection\Types\This;
use Mlp\Cli\Helper\Product;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\RuntimeException;

use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetupFactory;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

use Mlp\Cli\Helper\splitFile;
/**
 * Class GreetingCommand
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
    const ADD_SOREFOZ_PRODUCTS = 'add-sorefoz-products';

    const ADD_TELEFAC_PRODUCTS = 'add-telefac-products';

    const ADD_AUFERMA_PRODUCTS = 'add-auferma-products';

    const UPDATE_SOREFOZ_PRICES = 'update-sorefoz-prices';

    const ADD_ORIMA_PRODUCTS = "add-orima-products";
    /**
     * Anonymous name
     */
    const ANONYMOUS_NAME = 'Anonymous';

    /**
     * {@inheritdoc}
     */

    private $entityAttribute;

    private $eavSetupFactory;

    private $attributeSetFactory;

    private $attributeSet;


    private $productFactory;

    private $productRepository;

    private $searchCriteriaBuilder;

    private $state;

    private $config;

    private $filesystem;

    private $categoryLinkManagement;

    private $dataAttributeOptions;

    private $attributeManager;

    private $categoryManager;

    private $registry;

    private $stockStateInterface;

    private $stockRegistry;

    private $optionFactory;

    private $productRepositoryInterface;

    private $productManager;

    private $directory;

    private $sourceItemSaveI;

    private $sourceItemRepositoryI;

    private $searchCriteriaI;

    private $sourceItemIF;

    private $filterBuilder;

    private $filterGroupBuilder;

    public function __construct(EavSetupFactory $eavSetupFactory,
                                AttributeSetFactory $attributeSetFactory,
                                Attribute $entityAttribute,
                                ProductRepository $productRepository,
                                ProductFactory $productFactory,
                                Config $config,
                                Filesystem $filesystem,
                                State $state,
                                \Mlp\Cli\Helper\Data $dataAttributeOptions,
                                \Mlp\Cli\Helper\Attribute $attributeManager,
                                \Mlp\Cli\Helper\Category $categoryManager,
                                //\Mlp\Cli\Helper\Product $productManager,
                                \Magento\Framework\Registry $registry,
                                \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
                                \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
                                \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
                                \Magento\Framework\Filesystem\DirectoryList $directory,
                                \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSaveI,
                                \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepositoryI,
                                \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemIF,
                                SearchCriteriaBuilder $searchCriteriaBuilder,
                                \Magento\Framework\Api\FilterBuilder  $filterBuilder,
                                \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder)
    {
        $this->directory = $directory;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->entityAttribute = $entityAttribute;
        $this->productFactory = $productFactory;
        $this->state = $state;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->dataAttributeOptions = $dataAttributeOptions;
        $this->attributeManager = $attributeManager;
        $this->categoryManager = $categoryManager;
        $this->registry = $registry;
        $this->stockRegistry = $stockRegistry;
        $this->stockStateInterface = $stockStateInterface;
        $this->optionFactory = $optionFactory;
        $this->sourceItemSaveI = $sourceItemSaveI;
        $this->sourceItemRepositoryI = $sourceItemRepositoryI;
        $this->sourceItemIF = $sourceItemIF;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('Mlp:Products')
            ->setDescription('Manage Products')
            ->setDefinition([
                new InputOption(
                    self::ADD_SOREFOZ_PRODUCTS,
                    '-s',
                    InputOption::VALUE_NONE,
                    'add sorefoz products'
                ),
                new InputOption(
                    self::ADD_TELEFAC_PRODUCTS,
                    '-t',
                    InputOption::VALUE_NONE,
                    'add telefac products'
                ),
                new InputOption(
                    self::ADD_AUFERMA_PRODUCTS,
                    '-a',
                    InputOption::VALUE_NONE,
                    'add Auferma products'
                ),
                new InputOption(
                    self::ADD_ORIMA_PRODUCTS,
                    '-o',
                    InputOption::VALUE_NONE,
                    'add ORIMA products'
                ),
                new InputOption(
                    self::UPDATE_SOREFOZ_PRICES,
                    '-u',
                    InputOption::VALUE_NONE,
                    'update sorefoz prices'
                )
            ])->addArgument('categories', InputArgument::OPTIONAL, 'Categories?');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND); // or \Magento\Framework\App\Area::AREA_ADMINHTML, depending on your needs

        $addSorefozOption = $input->getOption(self::ADD_SOREFOZ_PRODUCTS);
        $categories = $input->getArgument('categories');
        if ($addSorefozOption) {
            $this->addSorefozProdCSV($categories);
            $output->writeln('<info>ACABEI DE ADICIONAR OS PRODUTOS SOREFOZ!</info>');
        }
        $addTelefacOption = $input->getOption(self::ADD_TELEFAC_PRODUCTS);
        if ($addTelefacOption) {
            $this->addTelefacProdCSV();
            $output->writeln('<info>ACABEI DE ADICIONAR OS PRODUTOS TELEFAC!</info>');
        }
        if ($input->getOption(self::ADD_AUFERMA_PRODUCTS)) {
            $this->addAufermaProdCSV();
            $output->writeln('<info>ACABEI DE ADICIONAR OS PRODUTOS Auferma!</info>');
        }
        if ($input->getOption(self::UPDATE_SOREFOZ_PRICES)){
            $this->updateSorefozPrices();
            $output->writeln('<info>ACABOU DE ATUALIZAR OS PREÇOS SOREFOZ!<info>');
        }
        if ($input->getOption(self::ADD_ORIMA_PRODUCTS)){
            $this->addOrimaProdCSV();
            $output->writeln('<info>ACABOU DE ADICIONAR OS PRODUTOS ORIMA!<info>');
        }
        else {
            throw new \InvalidArgumentException('Option ' . self::ADD_SOREFOZ_PRODUCTS .
                'OR' . self::ADD_TELEFAC_PRODUCTS . ' is missing.');
        }

    }



    protected function addAufermaProdCSV()
    {
        /*
            Codigo,0
            Nome,1
            CodCurto,2
            PVPBase,3
            PesoBrt,4
            Marca,5
            FamiliaAuferma,6
            NomeXtra 7
            Gama,8
            Familia, 9
            subfamilia 10
            stock 11
        */


        $categories = $this->categoryManager->getCategoriesArray();
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Auferma.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Auferma products" . "\n");
        $row = 0;
        if (($handle = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/Console/Command/aufermaInterno.csv", "r")) !== FALSE) {
            while (!feof($handle)) {
                $row++;
                if (($data = fgetcsv($handle, 4000, ",")) !== FALSE) {
                    if ($row == 1 || strcmp($data[8], "ACESSÓRIOS E BATERIAS") == 0 ||
                        strcmp($data[9], "AR CONDICIONADO") == 0) {
                        continue;
                    }
                    $sku = trim($data[0]);
                    try {
                        $product = $this->productRepository->get($sku, true, null, true);
                        if ($product->getStatus() == 2) {
                            continue;
                        }
                    } catch (NoSuchEntityException $exception) {
                        $name = trim($data[1]);
                        $gama = trim($data[8]);
                        $familia = trim($data[9]);
                        $subfamilia = trim($data[10]);
                        $description = trim($data[7]);
                        $meta_description = "";
                        $manufacter = trim($data[5]);
                        $length = 0;
                        $width = 0;
                        $height = 0;
                        $weight = trim($data[4]);
                        $price = (int)trim($data[3]);

                        try{
                            $productInterno = new \Mlp\Cli\Helper\Product($sku, $name, $gama, $familia, $subfamilia, $description,
                                $meta_description, $manufacter, $length, $width, $height, $weight, $price,
                                $this->productRepository, $this->productFactory, $this->categoryManager,
                                $this->dataAttributeOptions, $this->attributeManager, $this->stockRegistry,
                                $this->config, $this->optionFactory, $this->productRepositoryInterface, $this->directory);

                            $product = $productInterno->add_product($categories, $logger, $data[2]);
                        }catch (\Exception $e){
                            continue;
                        }
                    }
                    $this->updateStock($product, $data[11]);
                }
            }
            fclose($handle);
        }
    }

    protected function addTelefacProdCSV(){
        /*
         * 0 - Nome
         * 1 - marca
         * 2 - codigo
         * 3 - sku
         * 4 - descrição
         * 5 - pvp
         * 6 - stock
         * 7 - preço custo
         * 8 - gama
         * 9 - familia
         * 10 - sub familia
         */
        $categories = $this->categoryManager->getCategoriesArray();
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Telefac.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Telefac products" . "\n");
        $row = 0;
        if (($handle = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/Console/Command/telefac_interno.csv", "r")) !== FALSE) {
            print_r("abri ficheiro\n");
            while (!feof($handle)) {
                if (($data = fgetcsv($handle, 4000, ",", '"')) !== FALSE) {
                    $row++;
                    $sku = trim($data[3]);
                    if (strlen($sku) == 13) {
                        try {
                            $product = $this->productRepository->get($sku, true, null, true);
                            if ($product->getStatus() == 2) {
                                print_r($sku . "\n");
                                continue;
                            }
                        } catch (NoSuchEntityException $exception) {
                            $name = trim($data[0]);
                            $gama = trim($data[8]);
                            $familia = trim($data[9]);
                            $subfamilia = trim($data[10]);
                            $description = trim($data[4]);
                            $meta_description = "";
                            $manufacter = trim($data[1]);
                            $length = 0;
                            $width = 0;
                            $height = 0;
                            $weight = trim($data[4]);
                            $price = (int)trim($data[7]) * 1.23 * 1.20;

                            $productInterno = new \Mlp\Cli\Helper\Product($sku, $name, $gama, $familia, $subfamilia, $description,
                                $meta_description, $manufacter, $length, $width, $height, $weight, $price,
                                $this->productRepository, $this->productFactory, $this->categoryManager,
                                $this->dataAttributeOptions, $this->attributeManager, $this->stockRegistry,
                                $this->config, $this->optionFactory, $this->productRepositoryInterface);

                            $productInterno->add_product($categories, $logger, $data[3]);
                        }
                        $this->updateStock($sku, $data[6]);

                        }
                    }
            }
            fclose($handle);
            }else {
               print_r("Não abriu o ficheiro");
            }
    }

    protected function addOrimaProdCSV(){
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
        $categories = $this->categoryManager->getCategoriesArray();
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Orima.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Orima products" . "\n");
        $row = 0;
        if (($handle = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/Console/Command/Orima.csv", "r")) !== FALSE) {
            print_r("abri ficheiro\n");
            while (!feof($handle)) {
                $row++;
                if (($data = fgetcsv($handle, 4000, ";", '"')) !== FALSE) {
                    if ($row == 1 ) {
                        continue;
                    }
                    $sku = trim($data[8]);
                    if (strlen($sku) == 13) {
                        try {
                            $product = $this->productRepository->get($sku, true, null, true);
                            if ($product->getStatus() == 2) {
                                print_r($sku . "\n");
                                continue;
                            }
                        } catch (NoSuchEntityException $exception) {
                            $name = trim($data[0]);
                            $gama = trim($data[4]);
                            $familia = trim($data[5]);
                            $subfamilia = trim($data[6]);
                            $description = trim($data[9]);
                            $meta_description = "";
                            $manufacter = trim($data[7]);
                            $length = 0;
                            $width = 0;
                            $height = 0;
                            $weight = trim($data[4]);
                            $price = (int)trim($data[2]) * 1.23 * 1.20;
                            $imagem = trim($data[10]); //ref Orima
                            $etiquetaEner = trim($data[11]);// EAN

                            $categories = $this->categoryManager->getCategoriesArray();
                            $productInterno = new \Mlp\Cli\Helper\Product($sku, $name, $gama, $familia, $subfamilia, $description,
                                $meta_description, $manufacter, $length, $width, $height, $weight, $price,
                                $this->productRepository, $this->productFactory, $this->categoryManager,
                                $this->dataAttributeOptions, $this->attributeManager, $this->stockRegistry,
                                $this->config, $this->optionFactory, $this->productRepositoryInterface, $this->directory);

                            $productInterno->setOrimaCategories();
                            $this->getImages($sku, $imagem, $etiquetaEner);
                            $product = $productInterno->add_product($categories, $logger, $sku);

                        }
                        $stock = $this->setOrimaStock($data[3]);
                        $this->setStock($sku,'orima',$stock);

                    }
                    print_r($row." - sku: ".$sku." stock: ".$stock."-".$data[3]."\n");
                    unset($data);
                }
            }
            fclose($handle);
        }else {
            print_r("Não abriu o ficheiro");
        }
    }


    protected function updatePrice($sku, $price){
        try{
            $product = $this->productRepository->get($sku, true, null, true);
            $product->setPrice($price);
            $this->productRepository->save($product);
            print_r("price updated - " . $sku . "\n");
        }catch (\Exception $ex){
            print_r("update price exception - " . $ex->getMessage() . "\n");
        }
    }


    protected  function getImages($sku, $img, $etiqueta){
        try {
            if (preg_match('/^http/', (string)$etiqueta) == 1) {
                $ch = curl_init($etiqueta);
                $fp = fopen($this->directory->getRoot()."/pub/media/catalog/product/" . $sku. '_e.jpeg', 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch,CURLOPT_TIMEOUT,2);
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
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
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
                curl_setopt($ch,CURLOPT_TIMEOUT,2);
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

    protected function getAttribute($attCode)
    {
        $attribute = $this->entityAttribute->loadByCode('catalog_product', $attCode);
        return $attribute->getData();
    }

    private function setOrimaStock($stock)
    {
        return (int)filter_var($stock, FILTER_SANITIZE_NUMBER_INT);;
    }

    private function setStock($sku,$source,$quantity)
    {
        $filterSku = $this -> filterBuilder
            -> setField("sku")
            -> setValue($sku)
            -> create();
        $sourceFilter = $this -> filterBuilder
            -> setField("source_code")
            -> setValue($source)
            -> create();

        $filterGroup1 = $this -> filterGroupBuilder -> setFilters([$filterSku]) -> create();
        $filterGroup2 = $this -> filterGroupBuilder -> setFilters([$sourceFilter]) -> create();
        $searchC = $this -> searchCriteriaBuilder -> setFilterGroups([$filterGroup1, $filterGroup2]) -> create();
        $sourceItem = $this -> sourceItemRepositoryI -> getList($searchC) -> getItems();

        if (empty($sourceItem)) {
            $item = $this -> sourceItemIF -> create();
            $item -> setQuantity($quantity);
            $item -> setStatus(1);
            $item -> setSku($sku);
            $item -> setSourceCode($source);
            $this->sourceItemSaveI->execute([$item]);
        } else {
            foreach ($sourceItem as $item) {
                $item -> setQuantity($quantity);
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


}
