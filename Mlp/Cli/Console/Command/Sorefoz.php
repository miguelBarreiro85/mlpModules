<?php


namespace Mlp\Cli\Console\Command;

use Mlp\Cli\Helper\CategoriesConstants as Cat;
use Braintree\Exception;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DirectoryList;
use Mlp\Cli\Helper\Manufacturer as Manufacturer;
use Mlp\Cli\Helper\splitFile;
use Mlp\Cli\Helper\imagesHelper;
use Mlp\Cli\Helper\LoadCsv;
use Symfony\Component\Console\Command\Command;
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
    const ADD_IMAGES = 'add-images';
    const DISABLE_PRODUCTS = 'disable-products';

    private $directory;

    private $categoryManager;
    private $productRepository;
    private $state;
    private $produtoInterno;
    private $loadCsv;
    private $imagesHelper;
    private $sorefozCategories;

    public function __construct(DirectoryList $directory,
                                \Mlp\Cli\Helper\Category $categoryManager,
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Model\ProdutoInterno $produtoInterno,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
                                LoadCsv $loadCsv,
                                imagesHelper $imagesHelper,
                                \Mlp\Cli\Helper\Sorefoz\SorefozCategories $sorefozCategories)
    {

        $this -> directory = $directory;
        $this -> categoryManager = $categoryManager;
        $this -> productRepository = $productRepositoryInterface;
        $this -> state = $state;
        $this -> produtoInterno = $produtoInterno;
        $this->loadCsv = $loadCsv;
        $this->imagesHelper = $imagesHelper;
        $this->sorefozCategories = $sorefozCategories;

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
                    'Update Stocks, State and Price'
                ),
                new InputOption(
                    self::ADD_IMAGES,
                    '-i',
                    InputOption::VALUE_NONE,
                    'Add images to products'
                ),
                new InputOption(
                    self::DISABLE_PRODUCTS,
                    '-d',
                    InputOption::VALUE_NONE,
                    'Disable Products'
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
        $categories = $input->getArgument('categories');
        $filterProducts = $input -> getOption(self::FILTER_PRODUCTS);
        if ($filterProducts) {
            $this -> filterProducts();
        }
        $addProducts = $input -> getOption(self::ADD_PRODUCTS);
        if ($addProducts) {
            $this->addSorefozProducts($categories);
        }
        $updateStocks = $input -> getOption(self::UPDATE_STOCKS);
        if ($updateStocks) {
            $this -> updateStocks();
        }
        $addImages = $input->getOption(self::ADD_IMAGES);
        if($addImages) {
            $this->addImages($categories);
        }
        $disableProducts = $input->getOption(self::DISABLE_PRODUCTS);
        if($disableProducts) {
            $this->disableSorefozProducts();
        }
        else {
            throw new \InvalidArgumentException('Option  is missing.');
        }
    }


    protected function addSorefozProducts($categoriesFilter = null)
    {

        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Sorefoz.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        
        $row = 0;
        foreach ($this->loadCsv->loadCsv('/Sorefoz/tot_jlcb_utf.csv',";") as $data) {
            $row++;
            print_r($row." - ");
            if (!$this->setSorefozData($data,$logger)){
                print_r("\n");
                continue;
            }
            
            if (!is_null($categoriesFilter)){
                if (strcmp($categoriesFilter,$this->produtoInterno->familia) != 0){
                    print_r("\n");
                    continue;
                }
            }
            try {
                $product = $this -> productRepository -> get($this->produtoInterno->sku, true, null, true);
            } catch (NoSuchEntityException $exception) {
                $product = $this->produtoInterno -> add_product($logger, $this->produtoInterno->sku);
                //$this -> produtoInterno -> addSpecialAttributesSorefoz($product, $logger);
            }
            if(isset($product)){
                try {
                    print_r(" - Setting price: \n");
                    $this->produtoInterno->updatePrice();
                } catch (\Exception $ex) {
                    print_r("Update stock exception - " . $ex -> getMessage() . "\n");
                }
            }
        }
    }
    
    protected function updateSorefozPrices()
    {
        $writer = new \Zend\Log\Writer\Stream($this -> directory -> getRoot() . '/var/log/Sorefoz.log');
        $logger = new \Zend\Log\Logger();
        $logger -> addWriter($writer);
        print_r("Updating Sorefoz prices" . "\n");

        foreach ($this -> loadCsv -> loadCsv('tot_jlcb_utf.csv', ";") as $data) {
            $this->setSorefozData($data,$logger);
            $this->produtoInterno->updatePrice();
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
                if (($handle = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/Csv/Sorefoz/tot_jlcb_utf.csv", "r")) !== FALSE) {
                    print_r("abri ficheiro\n");
                    fgetcsv($handle, 4000, ";");
                    while (!feof($handle)) {
                        if (($data = fgetcsv($handle, 4000, ";")) !== FALSE) {
                            $row++;
                            print_r($row);
                            $sku = trim($data[18]);
                            if (strlen($sku) == 13) {
                                if (preg_match("/sim/i",$data[16]) == 1) {
                                    try {
                                        print_r(" - ".$sku);
                                        $product = $this->productRepository->get($sku, true, null, true);
                                        if ($product->getStatus() != 2) {
                                            $product->setStatus(Status::STATUS_DISABLED);
                                            try {
                                                $this->productRepository->save($product);
                                                print_r(" - disabled\n");
                                            } catch (\Exception $ex) {
                                                print_r("save: " . $ex->getMessage() . "\n");
                                            }
                                            continue;
                                        } else {
                                            print_r("\n");
                                            continue;
                                        }
                                    } catch (NoSuchEntityException $exception) {
                                        print_r(" - não existe\n");
                                        continue;
                                    }
                                }else {
                                    print_r(" - Em Gama\n");
                                }

                            }
                            print_r("\n");
                        }
                    }
                }
            }
        );
    }

    protected function updateCategories($categories) {
        //LoadCSV
        //Load Produtos
        //Muda as categorias
        //Salva os produtos
    }

    protected function updateAttributes(){
        //LoadCSV
        //Load Produtos
        //Muda as categorias
        //Salva os produtos
    }

    private function filterProducts()
    {
    }

    private function updateStocks()
    {
    }

    public function setSorefozData($data,$logger) {
        //Tirar os espaços em branco
        $functionTim = function ($el){
            return trim($el);
        };
        $data = array_map($functionTim,$data);

        $this->produtoInterno->sku = $data[18];
        if (strlen($this->produtoInterno->sku) != 13) {
            print_r("Wrong sku - ");
            $logger->info("Wrong Sku: ".$this->produtoInterno->sku);
            return 0;
        }
        print_r($data[29]);
        if (preg_match("/sim/i",$data[29]) == 1){
            $stock = 1;
        }else {
            $stock = 0;
        }
        if (preg_match("/sim/i",$data[16]) == 1) {
            $status = 2;
            print_r("fora gama - ");
            return 0;
        }else{
            $status = 1;
        }
        
        $this->produtoInterno->status = $status;


        $this->produtoInterno->stock = $stock;
        $this->produtoInterno->price = (int)trim($data[11]);

        print_r(" - setting stock ");
        $this->produtoInterno->setStock("sorefoz");
       
        if($this->produtoInterno->price == 0){
            //Se o preço for 0 desativar produto e ver o que se passa
            $logger->info(Cat::ERROR_PRECO_ZERO.$this->produtoInterno->sku);
            $this->produtoInterno->status = 2;
        }

        $this->produtoInterno->name = $data[1];

        try {
            [$gama,$familia,$subFamilia] =  $this->sorefozCategories
                ->getCategories($data[5],$data[7],$data[9],
                                $logger,$this->produtoInterno->sku,$this->produtoInterno->name);        
        }catch (Exception $e) {
            $logger->info(Cat::ERROR_GET_CATEGORIAS.$this->produtoInterno->sku." - ".$e->getMessage());
            return 0;
        }
        
        
        $this->produtoInterno->gama = $gama;
        $this->produtoInterno->familia = $familia;
        $this->produtoInterno->subFamilia = $subFamilia;
        $this->produtoInterno->description = $data[25];
        $this->produtoInterno->meta_description = $data[24];
        $this->produtoInterno->manufacturer = Manufacturer::getSorefozManufacturer($data[3]);
        $this->produtoInterno->length = (int)$data[20];
        $this->produtoInterno->width = (int)$data[21];
        $this->produtoInterno->height = (int)$data[22];
        $this->produtoInterno->weight = (int)$data[19];
        $this->produtoInterno->price = $this->produtoInterno->getPrice((int)str_replace(".", "", $data[12]));
        $this->produtoInterno->status = $status;
        $this->produtoInterno->image = $data[24];
        $this->produtoInterno->classeEnergetica = $data[25];
        $this->produtoInterno->imageEnergetica = $data[28];
        $this->produtoInterno->stock = $stock;
        return 1;
        
    }

    
    private function addImages($categoriesFilter) 
    {

        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Sorefoz.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        
        $row = 0;
        foreach ($this->loadCsv->loadCsv('/Sorefoz/tot_jlcb_utf.csv',";") as $data) {
            $row++;
            print_r($row." - ");
            $this->setSorefozData($data,$logger);
            if (strlen($this->produtoInterno->sku) != 13) {
                print_r("invalid sku - \n");
                continue;
            }
            if (!is_null($categoriesFilter)){
                if (strcmp($categoriesFilter,$this->produtoInterno->subFamilia) != 0){
                    print_r($this->produtoInterno->sku . " - Fora de Gama \n");
                    continue;
                }
            }
            try {
                print_r($this->produtoInterno->sku);
                $product = $this -> productRepository -> get($this->produtoInterno->sku, true, null, true);
                $this->imagesHelper->getImages($product->getSku(), $this->produtoInterno->image, $this->produtoInterno->imageEnergetica);
                $this->imagesHelper->setImages($product, $logger, $this->produtoInterno->sku);
                print_r("\n");
            } catch (\Exception $exception) {
                print_r($exception->getMessage());
            }
        }
    }
}
