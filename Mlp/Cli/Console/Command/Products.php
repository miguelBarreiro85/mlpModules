<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mlp\Cli\Console\Command;

use Braintree\Exception;
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

    public function __construct(EavSetupFactory $eavSetupFactory,
                                AttributeSetFactory $attributeSetFactory,
                                Attribute $entityAttribute,
                                ProductRepository $productRepository,
                                SearchCriteriaBuilder $searchCriteriaBuilder,
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
                                \Magento\Framework\Filesystem\DirectoryList $directory)
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
        //$this->productManager= $productManager;

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
        }else {
            throw new \InvalidArgumentException('Option ' . self::ADD_SOREFOZ_PRODUCTS .
                'OR' . self::ADD_TELEFAC_PRODUCTS . ' is missing.');
        }

    }

    protected function addSorefozProdCSV($categoriesFilter)
    {
        print_r($categoriesFilter);
        /*
        Referencia - 0
        Descrição 1
        CodMarca 2
        NOME_MARCA 3
        Cod.Gama 4
        NOME_GAMA 5
        Cod.Familia 6
        NOME_FAMILIA 7
        SUBFAMILIA 8
        NOME_SUBFAMILIA 9
        PVP_MARCA 10
        PVP_CENTRAL 11
        PR_LIQUIDO 12
        EM_PROMOÇ+O 13
        EXCLUSIVO 14
        OFERTA 15
        FORA_GAMA 16
        PartNr 17
        EAN 18
        Peso(kg) 19
        Volume(dm3) 20
        Comprimento(mm) 21
        Largura(mm) 22
        Altura(mm) 23
        LinkImagem 24
        Caracteristicas_Resumo 25
        Caracteristicas_Completa 26
        Classe_energetica 27
        Link_Classe_Energetica 28
        Stock 29
        */
        $this->state->emulateAreaCode(
            'adminhtml',
            function () use ($categoriesFilter) {
                $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Sorefoz.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                print_r("Adding Sorefoz products" . "\n");
                $row = 0;
                if (($handle = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/Console/Command/tot_jlcb_utf.csv", "r")) !== FALSE) {
                    while (!feof($handle)) {
                        if (($data = fgetcsv($handle, 4000, ";")) !== FALSE) {
                            if($data == "\n" || $data == "\r\n" || $data == "")
                            {
                                throw new Exception("Empty line found.");
                            }
                            if($data === false && !feof($handle))
                            {
                                echo "Error reading file besides EOF";
                            }
                            elseif($data === false && feof($handle))
                            {
                                echo "We are at the end of the file.\n";

                                //check status of the stream
                                $meta = stream_get_meta_data($handle);
                                var_dump($meta);
                            }
                            else {
                                try{
                                    $row++;
                                    if (!is_null($categoriesFilter)){
                                        if (strcmp($data[7], $categoriesFilter)!=0){
                                            continue;
                                        }
                                    }
                                    if ($row == 1 || strcmp($data[5], "ACESSÓRIOS E BATERIAS") == 0 || strcmp($data[7], "MAT. PROMOCIONAL / PUBLICIDADE") == 0
                                        || strcmp($data[7], "FERRAMENTAS") == 0 || strcmp(trim($data[16]), "sim") == 0) {
                                        continue;
                                    }
                                    print_r($row . " - ");
                                    $categories = $this->categoryManager->getCategoriesArray();
                                    $this->addSorefozProduct($data, $logger, $categories);
                                }catch (\Exception $ex){
                                    print_r(" - " . $ex->getMessage() . " - ");
                                }

                            }
                        }
                    }
                    fclose($handle);
                } else {
                    print_r("Não abriu o ficheiro\n");
                }
            }
        );
    }

    protected function addSorefozProduct($data, $logger, $categories)
    {

        $sku = trim($data[18]);
        if (strlen($sku) == 13) {
            try {
                $product = $this->productRepository->get($sku, true, null, true);
                if ($product->getStatus() == 2) {
                    return;
                }
            } catch (NoSuchEntityException $exception) {
                $name = trim($data[1]);
                $gama = trim($data[5]);
                $familia = trim($data[7]);
                $subfamilia = trim($data[9]);
                $description = trim($data[25]);
                $meta_description = trim($data[26]);
                $manufacter = trim($data[3]);
                $length = trim($data[21]);
                $width = trim($data[22]);
                $height = trim($data[23]);
                $weight = trim ($data[19]);
                $price = (int)str_replace(".", "", $data[12]);

                $this->getImages($sku, $data );
                $productInterno = new \Mlp\Cli\Helper\Product($sku, $name, $gama, $familia,$subfamilia,$description,
                    $meta_description,$manufacter,$length,$width,$height,$weight,$price,
                    $this->productRepository,$this->productFactory, $this->categoryManager,
                    $this->dataAttributeOptions, $this->attributeManager, $this->stockRegistry,
                    $this->config, $this->optionFactory, $this->productRepositoryInterface,$this->directory);

                $productInterno->add_product($categories,$logger, $sku);
            }
            try{
                $this->updateStock($sku, $data[29]);
            }catch (\Exception $ex){
                print_r("Update stock exception - ".$ex->getMessage() . "\n");
            }

        }
    }

    protected function updateSorefozPrices(){
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Sorefoz.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Sorefoz products" . "\n");
        $row = 0;
        if (($handle = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/Console/Command/tot_jlcb_utf.csv", "r")) !== FALSE) {
            while (!feof($handle)) {
                if (($data = fgetcsv($handle, 4000, ";")) !== FALSE) {
                    try{
                        $row++;
                        if ($row == 1 || strcmp($data[5], "ACESSÓRIOS E BATERIAS") == 0 || strcmp($data[7], "MAT. PROMOCIONAL / PUBLICIDADE") == 0
                            || strcmp($data[7], "FERRAMENTAS") == 0 || strcmp(trim($data[16]), "sim") == 0) {
                            continue;
                        }
                        print_r($row . " - ");
                        $sku = trim($data[18]);
                        $price = (int)str_replace(".", "", $data[12]);
                        $price = $price * 1.23 * 1.20;
                        $this->updatePrice($sku, $price);
                    }catch (\Exception $ex){
                        print_r(" - " . $ex->getMessage() . " - ");
                    }
                }
            }
        }
    }

    protected function disableSorefozProducts()
    {
        $this->state->emulateAreaCode(
            'adminhtml',
            function () {
                $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Sorefoz.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                print_r("Disable Sorefoz products" . "\n");
                $row = 0;
                if (($handle = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/Console/Command/tot_jlcb_utf.csv", "r")) !== FALSE) {
                    print_r("abri ficheiro\n");
                    while (!feof($handle)) {
                        if (($data = fgetcsv($handle, 4000, ";")) !== FALSE) {
                            $row++;
                            if ($row == 1) {
                                continue;
                            }
                            print_r($row . "\n");
                            if (strcmp($data[5], "ACESSÓRIOS E BATERIAS") == 0 || strcmp($data[7], "MAT. PROMOCIONAL / PUBLICIDADE") == 0
                                || strcmp($data[7], "FERRAMENTAS") == 0 || strcmp(trim($data[16]), "sim") != 0) {
                                continue;
                            }
                            $sku = trim($data[18]);
                            if (strlen($sku) == 13) {
                                try {
                                    $product = $this->productRepository->get($sku, true, null, true);
                                    if ($product->getStatus() != 2) {
                                        $product->setStatus(Status::STATUS_DISABLED);
                                        try {
                                            $product->save();
                                        } catch (\Exception $ex) {
                                            print_r("save: " . $ex->getMessage() . "\n");
                                        }
                                        continue;
                                    } else {
                                        continue;
                                    }
                                } catch (NoSuchEntityException $exception) {
                                    continue;
                                }

                            }
                        }
                    }
                }
            }
        );
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

                        $productInterno = new \Mlp\Cli\Helper\Product($sku, $name, $gama, $familia, $subfamilia, $description,
                            $meta_description, $manufacter, $length, $width, $height, $weight, $price,
                            $this->productRepository, $this->productFactory, $this->categoryManager,
                            $this->dataAttributeOptions, $this->attributeManager, $this->stockRegistry,
                            $this->config, $this->optionFactory, $this->productRepositoryInterface);

                        $productInterno->add_product($categories, $logger, $data[2]);
                    }
                    $this->updateStock($sku, $data[11]);
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

    protected function updateStock($sku, $stock){
        //STOCK
        try {
            $product = $this->productRepository->get($sku, true, null, true);
            $stockItem = $this->stockRegistry->getStockItem($product->getId()); // load stock of that product
            switch ($stock) {
                case 'Sim':
                case 'sim':
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
            print_r($product->getSku() . " - stock updated" . "\n");
        }catch (\Exception $ex) {
            print_r("\nStock error: " . $ex->getMessage() . "\n");
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

    protected function getImages($sku, $data)
    {
        try {
            if (preg_match('/^http/', (string)$data[28]) == 1) {
                $ch = curl_init($data[28]);
                $fp = fopen($this->directory->getRoot()."/pub/media/catalog/product/" . $sku. '_e.jpeg', 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch,CURLOPT_TIMEOUT,1);
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
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
            if (preg_match('/^http/', $data[24]) == 1) {
                $ch = curl_init($data[24]);
                $fp = fopen($this->directory->getRoot()."/pub/media/catalog/product/" . $sku . ".jpeg", 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
                curl_setopt($ch,CURLOPT_TIMEOUT,1);
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

}