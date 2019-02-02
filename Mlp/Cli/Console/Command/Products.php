<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mlp\Cli\Console\Command;

use Braintree\Exception;
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
    /**
     * Anonymous name
     */
    const ANONYMOUS_NAME = 'Anonymous';
    /**
     * {@inheritdoc}
     */
    const DEL_PRODUCTS = 'delete-products';

    const SHOW_PRODUCTS = 'show-products';

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
                                \Magento\Framework\Registry $registry,
                                \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
                                \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
                                \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface)
    {
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
                    self::DEL_PRODUCTS,
                    '-d',
                    InputOption::VALUE_NONE,
                    'delete products'
                ),
                new InputOption(
                    self::SHOW_PRODUCTS,
                    '-sh',
                    InputOption::VALUE_REQUIRED,
                    'show product'
                )
            ]);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND); // or \Magento\Framework\App\Area::AREA_ADMINHTML, depending on your needs

        $showAtt = $input->getOption(self::SHOW_PRODUCTS);
        if ($showAtt) {
            $attCode = $input->getParameterOption('-s');
            print_r($attCode . "\n");
            $attribute = $this->getAttribute($attCode);
            print_r($attribute);
        }
        $delete = $input->getOption(self::DEL_PRODUCTS);
        if ($delete) {
            print_r("Delete products");
            $this->deleteProducts();
        }
        $addSorefozOption = $input->getOption(self::ADD_SOREFOZ_PRODUCTS);
        if ($addSorefozOption) {
            $this->addSorefozProducts_csv();
            $output->writeln('<info>ACABEI DE ADICIONAR OS PRODUTOS SOREFOZ!</info>');
        }
        $addTelefacOption = $input->getOption(self::ADD_TELEFAC_PRODUCTS);
        if ($addTelefacOption) {
            $this->addTelefacProducts_csv();
            $output->writeln('<info>ACABEI DE ADICIONAR OS PRODUTOS TELEFAC!</info>');
        }
        if ($input->getOption(self::ADD_AUFERMA_PRODUCTS)) {
            $this->addAufermaProducts_csv();
            $output->writeln('<info>ACABEI DE ADICIONAR OS PRODUTOS Auferma!</info>');
        } else {
            throw new \InvalidArgumentException('Option ' . self::ADD_SOREFOZ_PRODUCTS .
                'OR' . self::ADD_TELEFAC_PRODUCTS . ' is missing.');
        }

    }

    protected function addSorefozProducts_csv()
    {
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
        $categories = $this->categoryManager->getCategoriesArray();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $writer = new \Zend\Log\Writer\Stream('/var/www/html/var/log/Sorefoz.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Sorefoz products" . "\n");

        $row = 0;
        if (($handle = fopen("/var/www/html/app/code/Mlp/Cli/Console/Command/tot_jlcb_utf.csv", "r")) !== FALSE) {
            print_r("abri ficheiro\n");
            //print_r($handle);
            while (!feof($handle)) {
                if (($data = fgetcsv($handle, 4000, ";")) !== FALSE) {
                    $row++;
                    if ($row == 1) {
                        continue;
                    }
                    print_r($row . ":" . $data[2] . "\n");
                    if (strcmp($data[5], "ACESSÓRIOS E BATERIAS") == 0 || strcmp($data[7], "MAT. PROMOCIONAL / PUBLICIDADE") == 0
                        || strcmp($data[7], "FERRAMENTAS") == 0) {
                        continue;
                    }
                    $sku = trim($data[18]);
                    if (strlen($sku) == 13) {
                        try {
                            $product = $this->productRepository->get($sku, true, null, true);
                            if ($product->getStatus() == 2) {
                                print_r($sku . "\n");
                                continue;
                            }
                        } catch (NoSuchEntityException $exception) {
                            //Produto NOVO
                            $product = $this->productFactory->create();
                            $product->setSku($sku);
                            $this->getImages($product, $data);
                            $product->setName(trim($data[1]));
                            $subFamilia = $this->categoryManager->setSubFamiliaSorefoz(trim($data[9]));
                            $familia = $this->categoryManager->setFamiliaSorefoz(trim($data[7]));
                            $gama = $this->categoryManager->setGamaSorefoz(trim($data[5]));
                            $product->setCustomAttribute('description', $data[26]);
                            $product->setCustomAttribute('meta_description', $data[25]);
                            $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', trim($data[3]));
                            $product->setCustomAttribute('manufacturer', $optionId);
                            $product->setCustomAttribute('ts_dimensions_length',(int)$data[21]/10);
                            $product->setCustomAttribute('ts_dimensions_width',(int)$data[22]/10);
                            $product->setCustomAttribute('ts_dimensions_height',(int)$data[23]/10);
                            $product->setCustomAttribute('tax_class_id',2); //taxable goods id
                            $product->setWeight($data[19]);
                            $product->setWebsiteIds([1]);
                            //$attributeSetId = $this->attributeManager->getAttributeSetId($familia,$subFamilia);
                            $product->setAttributeSetId(4); // Attribute set id
                            $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                            $product->setTaxClassId(2); // Tax class id
                            $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                            $product->setCreatedAt(date("Y/m/d"));
                            $product->setCustomAttribute('news_from_date',date("Y/m/d"));
                            try {
                                $product->setCategoryIds([$categories[$gama], $categories[$familia], $categories[$subFamilia]]);
                            } catch (\Exception $ex) {
                                print_r($ex->getMessage());
                                $this->categoryManager->createCategory($gama, $familia, $subFamilia, $categories);
                                $categories = $this->categoryManager->getCategoriesArray();
                                $product->setCategoryIds([$categories[$gama], $categories[$familia], $categories[$subFamilia]]);
                            }
                            $this->setImages($product, $logger, $product->getSku() . "_e.jpeg");
                            $this->setImages($product, $logger, $product->getSku() . ".jpeg");
                        } catch (\Exception $ex){
                            //Se der outro erro a ler o produto do repositório
                            print_r($ex->getMessage());
                            continue;
                        }

                    } else {
                        //Se o sku for inválido passa ao proximo produto e não adiciona este
                        //TODO adicionar  um log para ver se é importante o produto
                        continue;
                    }
                    $preco = (int)str_replace(".","",$data[12]);
                    if ($preco < 400){
                        $preco = $preco * 1.20;
                    }else{
                        $preco = $preco * 1.15;
                    }

                    $product->setPrice($preco);
                    //GAMA
                    switch ($data[16]) {
                        case 'sim':
                            $product->setStatus(Status::STATUS_DISABLED);
                            break;
                        default:
                            $product->setStatus(Status::STATUS_ENABLED);
                    }
                    try {
                        $product->save();

                    } catch (\Exception $exception) {
                        $logger->info($sku . " Deu merda a salvar: Exception:  " . $exception->getMessage());
                        print_r($exception->getMessage());
                    }
                    //STOCK
                    try {
                        switch ($data[29]) {
                            case 'Sim':
                                $stockItem=$this->stockRegistry->getStockItem($product->getId()); // load stock of that product
                                $stockItem->setIsInStock(true); //set updated data as your requirement
                                $stockItem->setQty(9); //set updated quantity
                                $stockItem->setManageStock(false);
                                $stockItem->setUseConfigNotifyStockQty(false);
                                $stockItem->save(); //save stock of item
                            //Para por os atributos
                                $optionId = $this->dataAttributeOptions->createOrGetId('is_in_stock', 'SIM');
                                $product->setCustomAttribute('is_in_stock',$optionId);
                                break;
                            default:
                                $stockItem=$this->stockRegistry->getStockItem($product->getId()); // load stock of that product
                                $stockItem->setIsInStock(false); //set updated data as your requirement
                                $stockItem->setQty(0); //set updated quantity
                                $stockItem->setManageStock(false);
                                $stockItem->setUseConfigNotifyStockQty(false);
                                $stockItem->save(); //save stock of item
                            //Para por os atributos
                                $optionId = $this->dataAttributeOptions->createOrGetId('is_in_stock', 'NÃO');
                                $product->setCustomAttribute('is_in_stock',$optionId);
                                break;
                        }
                    } catch (\Exception $ex) {
                        print_r("\nStock: ".$data[29]."\n".$ex->getMessage()."\n");
                        $stockItem=$this->stockRegistry->getStockItem($product->getId()); // load stock of that product
                        $stockItem->setIsInStock(false); //set updated data as your requirement
                        $stockItem->setQty(0); //set updated quantity
                        $stockItem->setManageStock(false);
                        $stockItem->setUseConfigNotifyStockQty(false);
                        $stockItem->save(); //save stock of item
                    //Para por os atributos
                        $optionId = $this->dataAttributeOptions->createOrGetId('is_in_stock', 'NÃO');
                        $product->setCustomAttribute('is_in_stock',$optionId);
                        break;

                    }
                    print_r($sku . "->" . $row . "->" . microtime(true) . "\n");
                }
            }
            fclose($handle);
        } else {
            print_r("Não abriu o ficheiro");
        }
    }

    protected function addAufermaProducts_csv()
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
        print_r("start\n");
        $categories = $this->categoryManager->getCategoriesArray();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $writer = new \Zend\Log\Writer\Stream('/var/www/html/var/log/Auferma.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Auferma products" . "\n");

        $row = 0;
        if (($handle = fopen("/var/www/html/app/code/Mlp/Cli/Console/Command/aufermaInterno.csv", "r")) !== FALSE) {
            while (!feof($handle)) {
                $row++;
                if (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($row == 1 || strcmp($data[8], "ACESSÓRIOS E BATERIAS") == 0 ||
                        strcmp($data[9], "AR CONDICIONADO") == 0) {
                        continue;
                    }
                    $sku = trim($data[0]);
                    try {
                        $product = $this->productRepository->get($sku, true, null, true);
                        /*if ($product->getStatus() == 2){
                            print_r($sku."\n");
                            continue;
                        }*/
                    } catch (NoSuchEntityException $exception) {
                        $product = $this->productFactory->create();
                        $product->setSku($sku);
                        $product->setName(trim($data[1]));
                        $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', trim($data[5]));
                        $product->setCustomAttribute('manufacturer', $optionId);
                        $product->setCustomAttribute('description', $data[7]);
                        $subFamilia = $this->categoryManager->setSubFamiliaSorefoz(trim($data[10]));
                        $familia = $this->categoryManager->setFamiliaSorefoz(trim($data[9]));
                        $gama = $this->categoryManager->setGamaSorefoz(trim($data[8]));
                        try {
                            $product->setCategoryIds([$categories[$gama], $categories[$familia], $categories[$subFamilia]]);
                        } catch (\Exception $exception) {
                            print_r($exception->getMessage() . "\n");
                            $this->categoryManager->createCategory($gama, $familia, $subFamilia, $categories);
                            $categories = $this->categoryManager->getCategoriesArray();
                            $product->setCategoryIds([$categories[$gama], $categories[$familia], $categories[$subFamilia]]);
                        }
                        $product->setWebsiteIds([1]);
                        //$attributeSetId = $this->attributeManager->getAttributeSetId($familia, $subFamilia);
                        $product->setAttributeSetId(4); // Attribute set id
                        $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                        $product->setTaxClassId(2); // Tax class id
                        $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                        $this->setImages($product, $logger, $data[2] . ".jpg");
                    }
                    //$product->getIma
                    $product->setTaxClassId(2); // Tax class id
                    $preco = (int)trim($data[3]);
                    if ($preco == 0) {
                        $logger->info("Preço igual a 0: " . $product->getName());
                    }
                    $preco = $preco / 1.23;
                    $product->setPrice($preco);
                    $product->setStatus(Status::STATUS_ENABLED);
                    try {
                        $product->save();

                    } catch (\Exception $exception) {
                        $logger->info($sku . " Deu merda a gravar:  " . $exception->getMessage());
                        print_r($exception->getMessage());
                    }
                    if ($product->getOptions() == null) {
                        $this->add_warranty_option($product);
                        $value = $this->getInstallationValue($familia);
                        if ($value > 0) {
                            $this->add_installation_option($product, $value);
                        }

                    }
                    //STOCK
                    try {
                        switch ($data[11]) {
                            case 'sim':
                                $stockItem = $this->stockRegistry->getStockItem($product->getId()); // load stock of that product
                                $stockItem->setIsInStock(true); //set updated data as your requirement
                                $stockItem->setQty(9); //set updated quantity
                                $stockItem->setManageStock(false);
                                $stockItem->setUseConfigNotifyStockQty(false);
                                $stockItem->save(); //save stock of item
                                //Para por os atributos
                                $optionId = $this->dataAttributeOptions->createOrGetId('is_in_stock', 'SIM');
                                $product->setCustomAttribute('is_in_stock', $optionId);
                                break;
                            default:
                                $stockItem = $this->stockRegistry->getStockItem($product->getId()); // load stock of that product
                                $stockItem->setIsInStock(false); //set updated data as your requirement
                                $stockItem->setQty(0); //set updated quantity
                                $stockItem->setManageStock(false);
                                $stockItem->setUseConfigNotifyStockQty(false);
                                $stockItem->save(); //save stock of item
                                //Para por os atributos
                                $optionId = $this->dataAttributeOptions->createOrGetId('is_in_stock', 'NÃO');
                                $product->setCustomAttribute('is_in_stock', $optionId);
                                break;
                        }
                    } catch (\Exception $ex) {
                        print_r("\nStock: " . $data[6] . "\n" . $ex->getMessage() . "\n");
                        //Para por os atributos
                        $optionId = $this->dataAttributeOptions->createOrGetId('is_in_stock', 'NÃO');
                        $product->setCustomAttribute('is_in_stock', $optionId);
                        break;
                    }
                    print_r($sku . "->" . $row . "->" . microtime(true) . "\n");
                }
            }
            fclose($handle);
        }
    }

    protected function addTelefacProducts_csv(){
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $writer = new \Zend\Log\Writer\Stream('/var/www/html/var/log/Telefac.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Telefac products" . "\n");
        $row = 0;
        if (($handle = fopen("/var/www/html/app/code/Mlp/Cli/Console/Command/telefac_interno.csv", "r")) !== FALSE) {
            print_r("abri ficheiro\n");
            //print_r($handle);
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
                            //Produto NOVO
                            $product = $this->productFactory->create();
                            $product->setSku($sku);
                            $product->setName(trim($data[0]));
                            $subFamilia = $this->categoryManager->setSubFamiliaSorefoz(trim($data[10]));
                            $familia = $this->categoryManager->setFamiliaSorefoz(trim($data[9]));
                            $gama = $this->categoryManager->setGamaSorefoz(trim($data[8]));
                            $product->setCustomAttribute('description', $data[3]);
                            $product->setCustomAttribute('meta_description', $data[3]);
                            $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', trim($data[1]));
                            $product->setCustomAttribute('manufacturer', $optionId);
                            $product->setCustomAttribute('tax_class_id',2); //taxable goods id
                            $product->setWebsiteIds([1]);
                            //$attributeSetId = $this->attributeManager->getAttributeSetId($familia,$subFamilia);
                            $product->setAttributeSetId(4); // Attribute set id
                            $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                            $product->setTaxClassId(2); // Tax class id
                            $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                            $product->setCreatedAt(date("Y/m/d"));
                            $product->setCustomAttribute('news_from_date',date("Y/m/d"));
                            $this->setImages($product,$logger,$data[3].".jpg");
                            $this->setImages($product,$logger,$data[3]."_2.jpg");
                            try {
                                $product->setCategoryIds([$categories[$gama], $categories[$familia], $categories[$subFamilia]]);
                            } catch (\Exception $ex) {
                                print_r($ex->getMessage());
                                $this->categoryManager->createCategory($gama, $familia, $subFamilia, $categories);
                                $categories = $this->categoryManager->getCategoriesArray();
                                $product->setCategoryIds([$categories[$gama], $categories[$familia], $categories[$subFamilia]]);
                            }
                        } catch (\Exception $ex){
                            //Se der outro erro a ler o produto do repositório
                            print_r($ex->getMessage());
                            continue;
                        }

                    } else {
                        //Se o sku for inválido passa ao proximo produto e não adiciona este
                        //TODO adicionar  um log para ver se é importante o produto
                        continue;
                    }
                    $preco = (int)str_replace(".","",$data[7]);
                    $preco = $preco * 1.30;
                    $preco = $preco * 1.23;
                    $product->setPrice($preco);
                    try {
                        $product->save();
                    } catch (\Exception $exception) {
                        $logger->info($sku . " Deu merda a salvar: Exception:  " . $exception->getMessage());
                        print_r($exception->getMessage());
                    }
                    if ($product->getOptions() == null){
                        $this->add_warranty_option($product);
                        $value = $this->getInstallationValue($familia);
                        if ($value > 0){
                            $this->add_installation_option($product,$value);
                        }

                    }
                    //STOCK
                    try {
                        switch ($data[6]) {
                            case 'Sim':
                                $stockItem=$this->stockRegistry->getStockItem($product->getId()); // load stock of that product
                                $stockItem->setIsInStock(true); //set updated data as your requirement
                                $stockItem->setQty(9); //set updated quantity
                                $stockItem->setManageStock(false);
                                $stockItem->setUseConfigNotifyStockQty(false);
                                $stockItem->save(); //save stock of item
                                //Para por os atributos
                                $optionId = $this->dataAttributeOptions->createOrGetId('is_in_stock', 'SIM');
                                $product->setCustomAttribute('is_in_stock',$optionId);
                                break;
                            default:
                                $stockItem=$this->stockRegistry->getStockItem($product->getId()); // load stock of that product
                                $stockItem->setIsInStock(false); //set updated data as your requirement
                                $stockItem->setQty(0); //set updated quantity
                                $stockItem->setManageStock(false);
                                $stockItem->setUseConfigNotifyStockQty(false);
                                $stockItem->save(); //save stock of item
                                //Para por os atributos
                                $optionId = $this->dataAttributeOptions->createOrGetId('is_in_stock', 'NÃO');
                                $product->setCustomAttribute('is_in_stock',$optionId);
                                break;
                        }
                    } catch (\Exception $ex) {
                        print_r("\nStock: ".$data[6]."\n".$ex->getMessage()."\n");
                        break;

                    }
                    print_r($sku . "->" . $row . "->" . microtime(true) . "\n");
                }
            }
            fclose($handle);
        } else {
            print_r("Não abriu o ficheiro");
        }
    }

    protected function getImages($product, $data)
    {
        try {
            if (preg_match('/^http/', (string)$data[28]) == 1) {
                $ch = curl_init($data[28]);
                $fp = fopen("/var/www/html/pub/media/catalog/product/" . $product->getSku() . '_e.jpeg', 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch,CURLOPT_TIMEOUT,100);
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,100);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);
            }

        } catch (\Exception $ex) {
            print_r($ex->getMessage());
        }
        try {
            if (preg_match('/^http/', $data[24]) == 1) {
                $ch = curl_init($data[24]);
                $fp = fopen("/var/www/html/pub/media/catalog/product/" . $product->getSku() . ".jpeg", 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
                curl_setopt($ch,CURLOPT_TIMEOUT,1);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);
            }

        } catch (\Exception $ex) {
            print_r($ex->getMessage());
        }
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
        } catch (RuntimeException $exception) {
            print_r("run time exception");
        } catch (LocalizedException $localizedException) {
            $logger->info($product->getName() . "Image name" . $ImgName . "  Sem Imagem");
            print_r($ImgName . "  Sem Imagem ");
        }
    }

    protected function deleteAttributes()
    {
        $attributes = include('attributes.php');

        foreach ($attributes as $attribute) {
            $eavSetup = $this->eavSetupFactory->create();
            $eavSetup->removeAttribute(4, $attribute['attribute_code']);
        }
    }

    protected function getAttribute($attCode)
    {
        $attribute = $this->entityAttribute->loadByCode('catalog_product', $attCode);
        return $attribute->getData();
    }

    protected function add_warranty_option($product){
        $one_year = $this->get_one_year_warranty_price((int)$product->getPrice());
        $three_years = $this->get_three_years_warranty_price((int)$product->getPrice());

        $options = [
            [
                'title' => 'Extensão de garantia',
                'type' => 'checkbox',
                'is_require' => false,
                'sort_order' => 4,
                'values' => [
                    [
                        'title' => '1 ano',
                        'price' => $one_year,
                        'price_type' => 'fixed',
                        'sort_order' => 0,
                    ],
                    [
                        'title' => '3 anos',
                        'price' => $three_years,
                        'price_type' => 'fixed',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ];

         foreach ($options as $arrayOption) {
             $option = $this->optionFactory->create();
             $option->setProductId($product->getId())
                ->setStoreId($product->getStoreId())
                ->addData($arrayOption);
             $option->save();
             $product->addOption($option);
         }
         $this->productRepositoryInterface->save($product);

    }

    protected function add_installation_option($product, $value){
        $options = [
            [
                'title' => 'Serviço de instalação',
                'type' => 'checkbox',
                'is_require' => false,
                'sort_order' => 4,
                'values' => [
                    [
                        'title' => 'Instalação de equipamento',
                        'price' => $value,
                        'price_type' => 'fixed',
                        'sort_order' => 0,
                    ],
                ],
            ],
        ];

        foreach ($options as $arrayOption) {
            $option = $this->optionFactory->create();
            $option->setProductId($product->getId())
                ->setStoreId($product->getStoreId())
                ->addData($arrayOption);
            $option->save();
            $product->addOption($option);
        }
        $this->productRepositoryInterface->save($product);
    }

    protected function get_one_year_warranty_price($price){
        return 30;
    }

    protected function get_three_years_warranty_price($price){
        return 85;
    }

    protected function getInstallationValue($familia)
    {
        switch ($familia){
            case 'ENCASTRE':
                return 54.90;
            case 'FOGÕES':
                return 39.90;
            case 'ESQUENTADORES/CALDEIRAS':
                return 74.90;
            case 'TERMOACUMULADORES':
                return 64.90;
            case 'AR CONDICIONADO':
                return 180;
            default:
                return 0;
        }
    }

    
    protected function addSorefozProducts()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $writer = new \Zend\Log\Writer\Stream('/var/log/addSorefozProducts.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        print_r("Adding products");

        $spreadsheetSorefoz = IOFactory::load("/var/www/html/app/code/Mlp/Cli/Console/Command/tot_jlcb.xlsx");

        $aSheet = $spreadsheetSorefoz->getActiveSheet();
        foreach ($aSheet->getRowIterator() as $aRow) {
            if ($aRow->getRowIndex() == 1) {
                continue;
            }
            /*if (strcmp(trim($aSheet->getCell('H' . $aRow->getRowIndex())), 'TELEVISÃO') != 0) {
                print_r("continua\n");
                continue;
            }*/
            if (strcmp($aSheet->getCell('Q' . $aRow->getRowIndex()), 'MAT. PROMOCIONAL / PUBLICIDADE') == 0) {
                continue;
            }
            $sku = trim($aSheet->getCell('S' . $aRow->getRowIndex())->getValue());
            if (strlen($sku) == 13) {
                try {
                    $product = $this->productRepository->get($sku, true, null, true);
                } catch (NoSuchEntityException $exception) {
                    $product = $this->productFactory->create();
                    $product->setSku($sku);
                }
            } else {
                continue;
            }
            //Declarar variáveis dentro do primeiro ciclo for.
            $gama = '';
            $familia = '';
            $subFamilia = '';

            foreach ($aRow->getCellIterator() as $aCell) {
                //Regex para partir a coluna da linha para depois ser inserido na folha magento
                $cord = $aCell->getCoordinate();
                preg_match_all('~[A-Z]+|\d+~', $cord, $split);
                switch ($aCell->getColumn()) {
                    case 'B':
                        $product->setName(trim($aCell->getValue()));
                        break;
                    case 'D':
                        //Manufacter
                        $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', trim($aCell->getValue()));
                        $product->setCustomAttribute('manufacturer', $optionId);
                        break;
                    case 'F':
                        $gama = $this->setGamaSorefoz(trim($aCell->getValue()));
                        break;
                    case 'H':
                        $familia = trim($aCell->getValue());
                        break;
                    case 'J':
                        $subFamilia = trim($aCell->getValue());
                        break;
                    case 'M':
                        $preco = $aCell->getValue() * 1.05;
                        $preco += +7.5;
                        $product->setPrice($preco);
                        break;
                    //Gama | Fora de Gama
                    case 'Q':
                        switch ($aCell->getValue()) {
                            case 'sim':
                                $product->setStatus(Status::STATUS_DISABLED);
                                break;
                            case 'não':
                                $product->setStatus(Status::STATUS_ENABLED);
                                break;
                        }
                        break;
                    //PESO
                    case 'T':
                        $product->setWeight($aCell->getValue());
                        break;
                    //Volume
                    case 'U':
                        $product->setCustomAttribute('volume', $aCell->getValue());
                        break;
                    case 'V':
                        $product->setCustomAttribute('comprimento', $aCell->getValue());
                        //$produto->atributosAdicionais .= ',Comprimento='.$aCell->getValue();
                        break;
                    case 'W':
                        $product->setCustomAttribute('largura', $aCell->getValue());
                        //$produto->atributosAdicionais .= ',Largura='.$aCell->getValue();
                        break;
                    case 'X':
                        $product->setCustomAttribute('altura', $aCell->getValue());
                        //$produto->atributosAdicionais .= ',Altura='.$aCell->getValue();
                        break;
                    //Link imagem
                    case 'Y':
                        $result = exec('jpeginfo -c /var/www/html/pub/media/catalog/product/' . $sku . ".jpeg");
                        if (preg_match('/ERROR/', $result)) {
                            print_r($result);
                            exec('rm /var/www/html/pub/media/catalog/product/' . $sku . ".jpeg");
                            $logger->info('Deleted image: ' . $sku);
                        }
                        break;
                    //Caracteristicas
                    case 'Z':
                        //$attributeValues = $this->attributeManager->addSorefozAttributes($aCell->getValue(),$familia,$subFamilia);
                        $product->setCustomAttribute('description', $aCell->getValue());
                        $product->setCustomAttribute('meta_description', $aCell->getValue());
                        /*if(isset($attributeValues)){
                            foreach($attributeValues as $attributeValue){
                                $product->setCustomAttribute($attributeValue['attribute_code'],$attributeValue['option_id']);
                            }
                        }*/
                        break;
                    //Stock
                    case 'AA':
                        switch ($aCell->getValue()) {
                            case 'sim':
                                $product->setStockData(
                                    array(
                                        'use_config_manage_stock' => 0,
                                        'manage_stock' => 1,
                                        'is_in_stock' => 1,
                                        'qty' => 999999999
                                    )
                                );
                                break;
                            case 'nao':
                                $product->setStockData(
                                    array(
                                        'use_config_manage_stock' => 0,
                                        'manage_stock' => 1,
                                        'is_in_stock' => 0,
                                        'qty' => 999999999
                                    )
                                );
                                break;
                        }
                        break;
                    default:
                        break;
                }
            }
            $product->setWebsiteIds([1]);
            $attributeSetId = $this->attributeManager->getAttributeSetId($familia, $subFamilia);
            $product->setAttributeSetId($attributeSetId); // Attribute set id
            $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
            $product->setTaxClassId(0); // Tax class id
            $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
            $this->setImages($product, $logger, $product->getSku());
            try {

                $product->save();
                print_r($product->getSku() . "\n");
            } catch (\Exception $exception) {
                $logger->info($sku . " Deu merda. Exception:  " . $exception->getMessage());
            }
            $this->categoryManager->setCategories($gama, $familia, $subFamilia, $product->getSku(), $logger);
        }
    }
    protected function addTelefacProducts()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $writer = new \Zend\Log\Writer\Stream('/var/log/TelefacProducts.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        print_r("Adding Telefac products");

        $spreadsheetSorefoz = IOFactory::load("/var/www/html/app/code/Mlp/Cli/Console/Command/tab_telefac.xlsx");

        $aSheet = $spreadsheetSorefoz->getActiveSheet();
        foreach ($aSheet->getRowIterator() as $aRow) {
            if ($aRow->getRowIndex() == 1) {
                continue;
            }
            /*if (strcmp(trim($aSheet->getCell('H' . $aRow->getRowIndex())), 'TELEVISÃO') != 0) {
                print_r("continua\n");
                continue;
            }*/
            $sku = trim($aSheet->getCell('B' . $aRow->getRowIndex())->getValue());
            if (strlen($sku) == 13) {
                try {
                    $product = $this->productRepository->get($sku, true, null, true);
                } catch (NoSuchEntityException $exception) {
                    $product = $this->productFactory->create();
                    $product->setSku($sku);
                }
            } else {
                continue;
            }
            $gama = '';
            $familia = '';
            $subFamilia = '';

            foreach ($aRow->getCellIterator() as $aCell) {
                //Regex para partir a coluna da linha para depois ser inserido na folha magento
                switch ($aCell->getColumn()) {
                    case 'A':
                        $product->setName(trim($aCell->getValue()));
                        break;
                    case 'G':
                        //Manufacter
                        $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', trim($aCell->getValue()));
                        $product->setCustomAttribute('manufacturer', $optionId);
                        break;
                    case 'F':
                        $subFamilia = trim($aCell->getValue());
                        break;
                    case 'D':
                        $preco = $aCell->getValue();
                        $product->setPrice($preco);
                        break;
                    //Gama | Fora de Gama
                    case 'E':
                        switch ($aCell->getValue()) {
                            case 'Sim':
                                $product->setStatus(Status::STATUS_ENABLED);
                                $product->setStockData(
                                    array(
                                        'use_config_manage_stock' => 0,
                                        'manage_stock' => 1,
                                        'is_in_stock' => 1,
                                        'qty' => 999999999
                                    )
                                );
                                break;
                            default:
                                $product->setStatus(Status::STATUS_DISABLED);
                                break;
                        }
                        break;
                    case 'C':
                        $product->setCustomAttribute('description', $aCell->getValue());
                        $product->setCustomAttribute('meta_description', $aCell->getValue());

                        break;
                }
            }
            $product->setWeight(0);
            $product->setWebsiteIds([1]);
            $product->setAttributeSetId(4); // Attribute set id
            $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
            $product->setTaxClassId(0); // Tax class id
            $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
            $this->setImages($product, $logger, $product->getSku());
            try {
                $product->save();
            } catch (\Exception $exception) {
                $logger->info($sku . " Deu merda a salvar Exception:  " . $exception->getMessage());
            }
            $this->categoryManager->setTelefacCategories($subFamilia, $product->getSku(), $logger);

        }
    }
}