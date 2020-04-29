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

class Jpdi extends Command
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
            ->setDescription('Manage Jpdi csv')
            ->setDefinition([
                new InputOption(
                    self::ADD_PRODUCTS,
                    '-a',
                    InputOption::VALUE_NONE,
                    'Add new Products'
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
        $addProducts = $input->getOption(self::ADD_PRODUCTS);
        if ($addProducts) {
            $this->addProducts($categories);
        }
        else {
            throw new \InvalidArgumentException('Option ' . self::FILTER_PRODUCTS . ' is missing.');
        }
    }

    protected function addProducts($categoriesFilter){
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Jpdi.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Jpdi products" . "\n");
        $row = 0;
        foreach ($this->loadCsv->loadCsv('/Jpdi/jpdi.csv',";") as $data) {
            $row++;
            print_r($row." - ");
            try{
                if(!$this->setData($data,$logger)){
                    print_r("\n");
                    continue;
                }
            }catch(\Exception $e){
                $logger->info("Error setData: ".$row);
                continue;
            }
            if (!is_null($categoriesFilter)){
                if (strcmp($categoriesFilter,$this->produtoInterno->subFamilia) != 0){
                    print_r("wrong familie - ");
                    continue;
                }
            }
            try {
                $product = $this -> productRepository -> get($this->produtoInterno->sku, true, null, true);
            } catch (NoSuchEntityException $exception) {
                
                $this->produtoInterno->manufacturer =  Manufacturer::getExpertManufacturer($this->produtoInterno->manufacturer);
                $this->produtoInterno -> add_product($logger, $this->produtoInterno->sku);
                //$this -> produtoInterno -> addSpecialAttributesExpert($product, $logger);
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


    private function setData($data,$logger)
    {
        /*
        0 - EAN
        1 - REF FABRICANTE
        2 - DESIGNAÇAO
        3 - PREÇO FINAL
        4 - STOCK
        5 - GAMA
        6 - FAMILIA
        7 - SUBFAMILIA
        8 - 
        9 - 
        10 - MARCA
        11 - 
        12 -PREÇO UNITARIO
        13 - TAXA
         */
        $functionTim = function ($data){
            return trim($data);
        };

        $data = array_map($functionTim,$data);
        
        if (preg_match("/Imediata/i",$data[4]) == 1)
        {
            $stock = 1;
            $status = 1;
        }else {
            $stock = 0;
            $status = 2;
        }

        $this->produtoInterno->sku = $data[0];
        
        if (strlen($this->produtoInterno->sku) != 13) {
            print_r("Wrong sku - ");
            $logger->info("Wrong Sku: ".$this->produtoInterno->sku);
            return 0;
        }
        $this->produtoInterno->stock = $stock;
        $this->produtoInterno->status = $status;
        $this->produtoInterno->price = (int)filter_var($data[3], FILTER_SANITIZE_NUMBER_INT) * 1.20 * 1.23;
        
        try {
            print_r(" - setting stock ");
            $this->produtoInterno->setStock("expert");
        }catch (\Exception $e){
            print_r($e->getMessage());
        }
        

        if($this->produtoInterno->price == 0 || $this->produtoInterno->stock == 0){
            print_r(" - Out of stock or price 0 - ");
            return  0;
        }

        $this->produtoInterno->name = $data[2];
        $this->produtoInterno->description = $data[2];
        $this->produtoInterno->meta_description = $data[2];
        $this->produtoInterno->manufacturer = $data[10];
        $this->produtoInterno->length = null;
        $this->produtoInterno->width = null;
        $this->produtoInterno->height = null;
        $this->produtoInterno->weight = null;
        $this->produtoInterno->image = $data[13];
        $this->produtoInterno->classeEnergetica = $data[18];
        $this->produtoInterno->imageEnergetica = $data[19];
        
        [$this->produtoInterno->gama,$this->produtoInterno->familia,
            $this->produtoInterno->subFamilia] = ExpertCategories::setExpertCategories($data[2],$logger,
                                                                                $this->produtoInterno->sku);
        if(preg_match("/Expert/i",$this->produtoInterno->gama)){
            print_r(" - gama: expert - ");
            return 0;
        }
        return 1;
    }
}