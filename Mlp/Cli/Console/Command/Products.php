<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mlp\Cli\Console\Command;

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
                                \Magento\Framework\Registry $registry)
    {
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
            $this->addTelefacProducts();
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
        $writer = new \Zend\Log\Writer\Stream('/var/www/html/var/log/SorefozCSV' . date('d:m:y') . '.log');
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
                    $num = count($data);
                    //EAN - 18
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
                            $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', trim($data[3]));
                            $product->setCustomAttribute('manufacturer', $optionId);
                            print_r($product->getCustomAttribute('manufacturer'));
                            $subFamilia = $this->categoryManager->setSubFamiliaSorefoz(trim($data[9]));
                            $familia = $this->categoryManager->setFamiliaSorefoz(trim($data[7]));
                            $gama = $this->categoryManager->setGamaSorefoz(trim($data[5]));
                            $product->setCustomAttribute('description', $data[26]);
                            $product->setCustomAttribute('meta_description', $data[25]);
                            $product->setCustomAttribute('ts_dimensions_height',$data[23]);
                            $product->setCustomAttribute('ts_dimensions_length',$data[21]);
                            $product->setCustomAttribute('ts_dimensions_width',$data[22]);
                            $product->setWebsiteIds([1]);
                            //$attributeSetId = $this->attributeManager->getAttributeSetId($familia,$subFamilia);
                            $product->setAttributeSetId(4); // Attribute set id
                            $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                            $product->setTaxClassId(0); // Tax class id
                            $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)

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
                        }
                    } else {
                        continue;
                    }
                    $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', trim($data[3]));
                    $product->setCustomAttribute('manufacturer', $optionId);
                    $product->setCustomAttribute('ts_dimensions_length',(int)$data[21]/10);
                    $product->setCustomAttribute('ts_dimensions_width',(int)$data[22]/10);
                    $product->setCustomAttribute('ts_dimensions_height',(int)$data[23]/10);
                    $product->setCustomAttribute('tax_class_id',2);
                    $product->setWeight($data[19]);
                    $product->setLength(50);
                    $product->setCustomAttribute('width',(int)$data[22]);
                    $preco = (int)$data[12] * 1.23;
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
                    //STOCK
                    try {
                        switch ($data[29]) {
                            case 'Sim':
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
                                $product->setStockData(
                                    array(
                                        'use_config_manage_stock' => 0,
                                        'manage_stock' => 1,
                                        'is_in_stock' => 0,
                                        'qty' => 0
                                    )
                                );
                                break;
                        }
                    } catch (\Exception $ex) {
                        print_r($ex->getMessage());
                        $product->setStockData(
                            array(
                                'use_config_manage_stock' => 0,
                                'manage_stock' => 1,
                                'is_in_stock' => 0,
                                'qty' => 0
                            )
                        );
                    }
                    try {
                        $product->save();

                    } catch (\Exception $exception) {
                        $logger->info($sku . " Deu merda. Exception:  " . $exception->getMessage());
                        print_r($exception->getMessage());
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
        $writer = new \Zend\Log\Writer\Stream('/var/log/Auferma' . date('d:m:y') . '.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Auferma products" . "\n");

        $row = 1;
        if (($handle = fopen("/var/www/html/app/code/Mlp/Cli/Console/Command/aufermaInterno.csv", "r")) !== FALSE) {
            while (!feof($handle)) {
                if (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (strcmp($data[8], "ACESSÓRIOS E BATERIAS") == 0) {
                        continue;
                        print_r("data: " . $data . "\n");
                    }
                    $row++;
                    $num = count($data);
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
                            print_r("Gama: " . $gama . "\nFamilia: " . $familia . "\nsubfamilia: " . $subFamilia . "\n" . $exception . "\n");
                        }
                        $product->setWebsiteIds([1]);
                        #$attributeSetId = $this->attributeManager->getAttributeSetId($familia, $subFamilia);
                        #$product->setAttributeSetId($attributeSetId); // Attribute set id
                        $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                        $product->setTaxClassId(0); // Tax class id
                        $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                        $this->setImages($product, $logger, $data[2] . ".jpg");
                    }
                    $preco = $data[3];
                    $product->setPrice($preco);
                    $product->setStatus(Status::STATUS_ENABLED);
                    if (strcmp('sim', $data[11]) == 0) {
                        $product->setStockData(
                            array(
                                'use_config_manage_stock' => 0,
                                'manage_stock' => 1,
                                'is_in_stock' => 1,
                                'qty' => 5
                            )
                        );
                    } else {
                        $product->setStockData(
                            array(
                                'use_config_manage_stock' => 0,
                                'manage_stock' => 1,
                                'is_in_stock' => 0,
                                'qty' => 0
                            )
                        );
                    }

                    try {
                        $product->save();

                    } catch (\Exception $exception) {
                        $logger->info($sku . " Deu merda. Exception:  " . $exception->getMessage());
                        print_r($exception->getMessage());
                    }
                    print_r($sku . "->" . $row . "->" . microtime(true) . "\n");
                }
            }
            fclose($handle);
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
                $logger->info($sku . " Deu merda. Exception:  " . $exception->getMessage());
            }
            $this->categoryManager->setTelefacCategories($subFamilia, $product->getSku(), $logger);

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
        $baseMediaPath = $this->config->getBaseMediaPath();
        try {
            $images = $product->getMediaGalleryImages();
            if (!$images || $images->getSize() == 0) {
                $product->addImageToMediaGallery($baseMediaPath . "/" . $ImgName, ['image', 'small_image', 'thumbnail'], false, false);
            }
        } catch (RuntimeException $exception) {
            print_r("run time exception");
        } catch (LocalizedException $localizedException) {
            $logger->info($product->getName() . "  Sem Imagem");
            print_r($ImgName . "  Sem Imagem ");
        }
    }

    protected function deleteProducts()
    {
        $products = $this->productFactory->create()->getCollection();
        $products->addFieldToSelect('*');
        $products->load();
        foreach ($products as $product) {
            try {
                $this->productRepository->deleteById($product->getSku());
            } catch (\Exception $ex) {
                print_r($ex->getMessage() . "\n");
            }
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

    /*

    AUFERMA
    Codigo,0
    Nome,1
    CodCurto,2
    PVPBase,3
    PesoBrt,4
    Marca,5
    Familia Auf,6
    NomeXtra 7
    gama 8
    familia 9
    sub familia 10
    stock 11



    */

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
}