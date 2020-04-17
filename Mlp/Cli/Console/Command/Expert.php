<?php


namespace Mlp\Cli\Console\Command;

use \Mlp\Cli\Helper\Category as CategoryManager;
use \Mlp\Cli\Helper\Expert\ExpertCategories;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DirectoryList;
use Symfony\Component\Console\Command\Command;
use Mlp\Cli\Helper\imagesHelper as ImagesHelper;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


use Mlp\Cli\Helper\Manufacturer as Manufacturer;

class Expert extends Command
{

    /**
     * Filter Prodcuts
     */
    const FILTER_PRODUCTS = 'filter-products';
    const ADD_PRODUCTS = 'add-products';
    const UPDATE_STOCKS = 'update-stocks';
    const ADD_IMAGES = 'add-images';

    private $directory;
    private $categoryManager;
    private $productRepository;
    private $state;
    private $produtoInterno;
    private $loadCsv;
    private $imagesHelper;

    public function __construct(DirectoryList $directory,
                                \Mlp\Cli\Helper\Category $categoryManager,                          
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Model\ProdutoInterno $productoInterno,
                                \Mlp\Cli\Helper\LoadCsv $loadCsv,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
                                \Mlp\Cli\Helper\imagesHelper $imagesHelper){

        $this->directory = $directory;
        $this->categoryManager = $categoryManager;
        $this->state = $state;
        $this->produtoInterno = $productoInterno;
        $this->loadCsv = $loadCsv;
        $this->productRepository = $productRepository;
        $this->imagesHelper = $imagesHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('Mlp:Expert')
            ->setDescription('Manage Expert csv')
            ->setDefinition([
                new InputOption(
                    self::FILTER_PRODUCTS,
                    '-f',
                    InputOption::VALUE_NONE,
                    'Filter Expert csv'
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
                ),
                new InputOption(
                    self::ADD_IMAGES,
                    '-i',
                    InputOption::VALUE_NONE,
                    'Add Images'
                )
            ])->addArgument('categories', InputArgument::OPTIONAL, 'Categories?');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $categories = $input->getArgument('categories');
        $filterProducts = $input->getOption(self::FILTER_PRODUCTS);
        if ($filterProducts) {
            $this->filterProducts();
        }
        $addProducts = $input->getOption(self::ADD_PRODUCTS);
        if ($addProducts) {
            $this->addProducts($categories);
        }
        $updateStocks = $input->getOption(self::UPDATE_STOCKS);
        if ($updateStocks){
            $this->updateStocks();
        }
        $addImages = $input->getOption(self::ADD_IMAGES);
        if ($addImages) {
            $this->addImages($categories);
        }
        else {
            throw new \InvalidArgumentException('Option ' . self::FILTER_PRODUCTS . ' is missing.');
        }
    }

    private function filterProducts(){

    }

    protected function addProducts($categoriesFilter){
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Expert.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Expert products" . "\n");
        $row = 0;
        foreach ($this->loadCsv->loadCsv('Expert.csv',";") as $data) {
            $row++;
            print_r($row." - ");
            try{
                $this->setData($data,$logger);
            }catch(\Exception $e){
                $logger->info("Error setData: ".$row);
                continue;
            }
            if (strlen($this->produtoInterno->sku) != 13) {
                print_r("Wrong sku - ");
                $logger->info("Wrong Sku: ".$this->produtoInterno->sku);
                continue;
            }
            if (!is_null($categoriesFilter)){
                if (strcmp($categoriesFilter,$this->produtoInterno->subFamilia) != 0){
                    print_r("wrong familie - ");
                    continue;
                }
            }
            try {
                $this -> productRepository -> get($this->produtoInterno->sku, true, null, true);
            } catch (NoSuchEntityException $exception) {
                
                $this->produtoInterno->manufacturer =  Manufacturer::getExpertManufacturer($this->produtoInterno->manufacturer);
                $this->produtoInterno -> add_product($logger, $this->produtoInterno->sku);
                //$this -> produtoInterno -> addSpecialAttributesExpert($product, $logger);
            }
            try {
                print_r(" - Setting stock: ");
                $this->produtoInterno->updatePrice();
                $this->produtoInterno->setStock('expert');
                print_r($this->produtoInterno->stock. "\n");
            } catch (\Exception $ex) {
                print_r("Update stock exception - " . $ex -> getMessage() . "\n");
                $logger->info("Setting stock error: ".$this->produtoInterno->sku);
            }

        }



    }

    private function setCategories(){

    }
    private function updateStocks(){

    }
    private function setData($data,$logger)
    {
        /*
        0 - Referencia
        1 - EAN
        2 - familia
        3 - PArt
        4 - Marca
        5 - Nome
        6 - Preço custo
        7 - reducao
        8 - preço comercial
        9 - Atualização
        10 - Resumo
        11 - Atributos
        12 -imagens
        13 - galeria
        14 - filtros
        15 - disponibilidade
        16 - expert url
        17 - eficienciaenergetica
        18 - eficienciaenergeticaimg
        19 - fichaue
        20 - desenhostec
        21 - criacao
         */
        $functionTim = function ($data){
            return trim($data);
        };

        $data = array_map($functionTim,$data);

        if (preg_match("/Indisponivel/i",$data[15]) == 1){
            $stock = 0;
            $status = 2;
        }else {
            $stock = 1;
            $status = 1;
        }

        

        
        $this->produtoInterno->sku = $data[1];
        $this->produtoInterno->name = $data[5];

        $this->produtoInterno->description = $data[10];
        $this->produtoInterno->meta_description = $data[10];
        $this->produtoInterno->manufacturer = $data[7];
        $this->produtoInterno->length = null;
        $this->produtoInterno->width = null;
        $this->produtoInterno->height = null;
        $this->produtoInterno->weight = null;
        $this->produtoInterno->price = (int)trim($data[9]);
        $this->produtoInterno->status = $status;
        $this->produtoInterno->image = $data[13];
        $this->produtoInterno->classeEnergetica = $data[18];
        $this->produtoInterno->imageEnergetica = $data[19];
        $this->produtoInterno->stock = $stock;

        
        [$this->produtoInterno->gama,$this->produtoInterno->familia,
            $this->produtoInterno->subFamilia] = ExpertCategories::setExpertCategories($data[2],$logger,
                                                                                $this->produtoInterno->sku);
    }

    private function addImages($categoriesFilter) 
    {

        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Expert.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        
        $row = 0;
        foreach ($this->loadCsv->loadCsv('Expert.csv',";") as $data) {
            $row++;
            print_r($row." - ");
            $this->setData($data,$logger);
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
                $this->productRepository->save($product);
                print_r("\n");
            } catch (\Exception $exception) {
                print_r($exception->getMessage());
            }
        }
    }
}

