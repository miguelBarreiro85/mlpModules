<?php


namespace Mlp\Cli\Console\Command;

use Exception;
use Mlp\Cli\Helper\Orima\OrimaCategories;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DirectoryList;
use Symfony\Component\Console\Command\Command;
use Mlp\Cli\Helper\imagesHelper as ImagesHelper;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Mlp\Cli\Helper\Manufacturer as Manufacturer;
use Mlp\Cli\Helper\CategoriesConstants as Cat;
class Orima extends Command
{

    /**
     * Filter Prodcuts
     */
    const ADD_PRODUCTS = 'add-products';
    const FILTER_PRODUCTS = 'filter-products';


    private $directory;
    private $categoryManager;
    private $productRepository;
    private $state;
    private $produtoInterno;
    private $loadCsv;
    private $registry;
    private $sqlHelper;

    public function __construct(\Mlp\Cli\Helper\SqlHelper $sqlHelper,
                                \Magento\Framework\Registry $registry,
                                DirectoryList $directory,
                                \Mlp\Cli\Helper\Category $categoryManager,                          
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Model\ProdutoInterno $productoInterno,
                                \Mlp\Cli\Helper\LoadCsv $loadCsv,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepository){

        $this->directory = $directory;
        $this->categoryManager = $categoryManager;
        $this->state = $state;
        $this->produtoInterno = $productoInterno;
        $this->loadCsv = $loadCsv;
        $this->productRepository = $productRepository;
        $this->registry = $registry;
        $this->sqlHelper= $sqlHelper;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('Mlp:Orima')
            ->setDescription('Manage Orima csv')
            ->setDefinition([
                new InputOption(
                    self::ADD_PRODUCTS,
                    '-a',
                    InputOption::VALUE_NONE,
                    'Add new Products'
                ),
                new InputOption(
                    self::FILTER_PRODUCTS,
                    '-f',
                    InputOption::VALUE_NONE,
                    'Filter Products'
                )
            ])->addArgument('categories', InputArgument::OPTIONAL, 'Categories?');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Orima.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $categories = $input->getArgument('categories');
        $filterProducts = $input->getOption(self::FILTER_PRODUCTS);
        if ($filterProducts) {
            $this->filterProducts();
        }
        $addProducts = $input->getOption(self::ADD_PRODUCTS);
        if ($addProducts) {
            $this->updateProducts($logger, $categories);
        }
        $filterProducts = $input->getOption(self::FILTER_PRODUCTS);
        if ($filterProducts) {
            $this->filterProducts();
        }
        else {
            throw new \InvalidArgumentException('Option ' . self::FILTER_PRODUCTS . ' is missing.');
        }
    }

    private function filterProducts()
    {
        print_r(phpinfo());
    }

    protected function updateProducts($logger, $categoriesFilter = null){    
        print_r("Updating Orima products" . "\n");
        
        $row = 0;
        $statusAttributeId = $this->sqlHelper->sqlGetAttributeId('status');
        $priceAttributeId = $this->sqlHelper->sqlGetAttributeId('price');

        foreach ($this->loadCsv->loadCsv('/Orima/Orima.csv',";") as $data) {
            $row++;
            $sku = trim($data[8]);
            print_r($row." - ");
            if ($this->sqlHelper->sqlUpdateStatus($sku,$statusAttributeId[0]["attribute_id"])){
                //update price anda stock
                $price = $this->produtoInterno->getPrice((int)$data[2]);
                if ($price == 0){
                    print_r(" price 0\n");
                    $logger->info(Cat::ERROR_PRICE_ZERO.$sku);
                    continue;
                }
                $this->sqlHelper->sqlUpdatePrice($sku,$priceAttributeId[0]["attribute_id"],$price);
                $this->produtoInterno->sku = $sku;
                $this->produtoInterno->stock = (int)filter_var($data[3], FILTER_SANITIZE_NUMBER_INT);  
                $this->produtoInterno->setStock($logger,"orima");
                print_r("updated - stock\n");
            }else {
                try{
                    if (!$this->setOrimaData($data,$logger)){
                        print_r("\n");
                        continue;
                    };
                }catch(\Exception $e){
                    $logger->info(Cat::ERROR_SET_PRODUCT_DATA.$row);
                    print_r("\n");
                    continue;
                }
                $this->setOrimaCategories($logger);
                $this->produtoInterno -> add_product($logger, $this->produtoInterno->sku);
                print_r("\n");
                continue;
            }
        }
    }

    private function setOrimaData($data,$logger)
    {
        /*
         * 0 A- Nome
         * 1 B- ref orima
         * 2 C- preço liquido
         * 3 d- stock
         * 4 e- gama
         * 5 f- familia
         * 6 g- subfamilia
         * 7 h- marca
         * 8 i- EAN
         * 9 j- Detalhes
         * 10 k- Imagem
         * 11 l- etiqueta energetica
         * 12 m- manual de instruções
         * 13 n- esquema tecnico
         */
        
        $functionTim = function ($data){
            return trim($data);
        };

        $data = array_map($functionTim,$data);

        
        $this->produtoInterno->sku = $data[8];
        if (strlen($this->produtoInterno->sku) < 13) {
            print_r("Wrong sku - ");
            $logger->info(Cat::ERROR_WRONG_SKU.$this->produtoInterno->sku);
            return 0;
        }
        
        $this->produtoInterno->manufacturer = Manufacturer::getOrimaManufacturer($data[7]);
    
        $this->produtoInterno->price = $this->produtoInterno->getPrice((int)$data[2]);
        $this->produtoInterno->stock = (int)filter_var($data[3], FILTER_SANITIZE_NUMBER_INT);
       
        print_r(" - setting stock ");
        $this->produtoInterno->setStock($logger,"orima");
       
        if($this->produtoInterno->price == 0){
            print_r(" - price 0 - ");
            $logger->info(Cat::ERROR_PRICE_ZERO.$this->produtoInterno->sku);
            return  0;
        }
        if($this->produtoInterno->stock == 0){
            print_r(" - Out of stock - ");
            return 0;
        }

        
        $this->produtoInterno->name = $data[0];
        $this->produtoInterno->gama = $data[4];
        $this->produtoInterno->familia = $data[5];
        $this->produtoInterno->subFamilia = $data[6];
        $this->produtoInterno->description = $data[9];
        $this->produtoInterno->meta_description = $data[9];
        
        $this->produtoInterno->length = null;
        $this->produtoInterno->width = null;
        $this->produtoInterno->height = null;
        $this->produtoInterno->weight = null;
        
        
        $this->produtoInterno->status = 1;
        $this->produtoInterno->image = $data[10];
        $this->produtoInterno->classeEnergetica = null;
        $this->produtoInterno->imageEnergetica = $data[11];
        
        return 1;
    }

    private function setOrimaCategories($logger)
    {
        try {
            [$mlpGama, $mlpFamilia, $mlpSubFamilia] = OrimaCategories::getCategoriesOrima(
                $this->produtoInterno->gama,
                $this->produtoInterno->familia,
                $this->produtoInterno->subFamilia,
                $logger,
                $this->produtoInterno->sku);
            $this->produtoInterno->gama = $mlpGama;
            $this->produtoInterno->familia = $mlpFamilia;
            $this->produtoInterno->subFamilia = $mlpSubFamilia;
        } catch (\Exception $e) {
            $logger->info(Cat::ERROR_GET_CATEGORIAS.$this->produtoInterno->sku);
        }
        
    }
}
