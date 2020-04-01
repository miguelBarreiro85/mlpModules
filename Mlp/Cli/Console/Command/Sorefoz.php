<?php


namespace Mlp\Cli\Console\Command;


use Braintree\Exception;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DirectoryList;
use Mlp\Cli\Helper\splitFile;
use Symfony\Component\Console\Command\Command;
use Mlp\Cli\Helper\imagesHelper as ImagesHelper;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;



class Sorefoz extends Command
{

    /**
     * Filter Prodcuts
     */
    const FILTER_PRODUCTS = 'filter-products';
    const ADD_PRODUCTS = 'add-products';
    const UPDATE_STOCKS = 'update-stocks';

    private $directory;

    private $categoryManager;
    private $productRepository;
    private $productFactory;
    private $dataAttributeOptions;
    private $attributeManager;
    private $config;
    private $optionFactory;
    private $productRepositoryInterface;
    private $state;
    private $produtoInterno;

    public function __construct(DirectoryList $directory,
                                \Mlp\Cli\Helper\Category $categoryManager,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
                                \Magento\Catalog\Model\ProductFactory $productFactory,
                                \Mlp\Cli\Helper\Data $dataAttributeOptions,
                                \Mlp\Cli\Helper\Attribute $attributeManager,
                                \Magento\Catalog\Model\Product\Media\Config $config,
                                \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Model\ProdutoInterno $productoInterno)
    {

        $this -> directory = $directory;
        $this -> categoryManager = $categoryManager;
        $this -> productRepository = $productRepositoryInterface;
        $this -> productFactory = $productFactory;
        $this -> dataAttributeOptions = $dataAttributeOptions;
        $this -> attributeManager = $attributeManager;
        $this -> config = $config;
        $this -> optionFactory = $optionFactory;
        $this -> productRepositoryInterface = $productRepositoryInterface;
        $this -> state = $state;
        $this -> produtoInterno = $productoInterno;
        parent ::__construct();
    }

    protected function configure()
    {
        $this -> setName('Mlp:Sorefoz')
            -> setDescription('Manage Sorefoz Products')
            -> setDefinition([
                new InputOption(
                    self::FILTER_PRODUCTS,
                    '-f',
                    InputOption::VALUE_NONE,
                    'Filter Orima csv'
                ),
                new InputOption(
                    self::ADD_PRODUCTS,
                    '-a',
                    InputOption::VALUE_NONE,
                    'Add new Products'
                ),
                new InputOption(
                    self::UPDATE_STOCKS,
                    '-u',
                    InputOption::VALUE_NONE,
                    'Update Stocks and State (Active or inactive)'
                )
            ])->addArgument('categories', InputArgument::OPTIONAL, 'Categories?');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this -> state -> setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $filterProducts = $input -> getOption(self::FILTER_PRODUCTS);
        $categories = $input->getArgument('categories');
        if ($filterProducts) {
            $this -> filterProducts();
        }
        $addProducts = $input -> getOption(self::ADD_PRODUCTS);
        if ($addProducts) {
            $this -> addSorefozProdCSV($categories);
        }
        $updateStocks = $input -> getOption(self::UPDATE_STOCKS);
        if ($updateStocks) {
            $this -> updateStocks();
        } else {
            throw new \InvalidArgumentException('Option ' . self::FILTER_PRODUCTS . ' is missing.');
        }
    }

    protected function addSorefozProdCSV($categoriesFilter)
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
        $this->state->emulateAreaCode(
            'adminhtml',
            function () use ($categoriesFilter) {
                $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Sorefoz.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                print_r("Adding Sorefoz products" . "\n");
                $row = 0;

                $fileUrl = $this->directory->getRoot()."/app/code/Mlp/Cli/Console/Command/tot_jlcb_utf.csv";
                $fileCount = splitFile::split_file($fileUrl, $this->directory->getRoot()."/app/code/Mlp/Cli/fileChunks/");

                for ($i = 1; $i < $fileCount; $i++) {
                    if (($handle = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/fileChunks/output".$i.".csv", "r")) !== FALSE) {
                        while (!feof($handle)) {
                            if (($data = fgetcsv($handle, 4000, ",")) !== FALSE) {
                                if($data == "\n" || $data == "\r\n" || $data == "")
                                {
                                    throw new Exception("Empty line found.\n");
                                }
                                if($data === false && !feof($handle))
                                {
                                    print_r("Error reading file besides EOF\n");
                                }
                                elseif($data === false && feof($handle))
                                {
                                    print_r("We are at the end of the file.\n");

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
                $price = (int)str_replace(".", "", $data[12]) * 1.23 * 1.30;

                ImagesHelper::getImages($sku, $data[24], $data[28], $this->directory);
                $this->produtoInterno->setData($sku, $name, $gama, $familia,$subfamilia,$description,
                    $meta_description,$manufacter,$length,$width,$height,$weight,$price);

                $product = $this->produtoInterno->add_product($categories,$logger, $sku);
                $this->produtoInterno->addSpecialAttributesSorefoz($product,$logger);
            }
            try{
                $this->setSorefozStock($sku,$data[29]);
                print_r(" stock: ".$data[29]."\n");
            }catch (\Exception $ex){
                print_r("Update stock exception - ".$ex->getMessage() . "\n");
            }

        }
    }

    private function setSorefozStock($sku,$stock)
    {
        if(preg_match("/sim/",$stock)==1){
            $stock = 1;
        }else{
            $stock = 0;
        }
        $this->produtoInterno->setStock($sku,"sorefoz",$stock);
    }
    protected function updateSorefozPrices(){
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Sorefoz.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Updating Sorefoz prices" . "\n");
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
}
