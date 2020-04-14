<?php


namespace Mlp\Cli\Console\Command;

use \Mlp\Cli\Helper\Category as CategoryManager;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DirectoryList;
use Symfony\Component\Console\Command\Command;
use Mlp\Cli\Helper\imagesHelper as ImagesHelper;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Mlp\Cli\Helper\Manufacturer as Manufacturer;

class Orima extends Command
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
    private $state;
    private $produtoInterno;
    private $loadCsv;

    public function __construct(DirectoryList $directory,
                                \Mlp\Cli\Helper\Category $categoryManager,                          
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Model\ProdutoInterno $productoInterno,
                                \Mlp\Cli\Helper\LoadCsv $loadCsv){

        $this->directory = $directory;
        $this->categoryManager = $categoryManager;
        $this->state = $state;
        $this->produtoInterno = $productoInterno;
        $this->loadCsv = $loadCsv;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('Mlp:Orima')
            ->setDescription('Manage Orima csv')
            ->setDefinition([
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
        else {
            throw new \InvalidArgumentException('Option ' . self::FILTER_PRODUCTS . ' is missing.');
        }
    }

    private function filterProducts()
    {
    }

    protected function addProducts($categoriesFilter){
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Orima.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Orima products" . "\n");
        $row = 0;
        foreach ($this->loadCsv->loadCsv('Orima.csv',";") as $data) {
            $row++;
            print_r($row." - ");
            try{
                $this->setOrimaData($data);
            }catch(\Exception $e){
                $logger->info("Error setOrimaData: ".$row);
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
                $this->setOrimaCategories();
                $this->produtoInterno->manufacturer =  Manufacturer::getOrimaManufacturer($this->produtoInterno->manufacturer);
                $this->produtoInterno -> add_product($logger, $this->produtoInterno->sku);
                //$this -> produtoInterno -> addSpecialAttributesOrima($product, $logger);
            }
            try {
                print_r(" - Setting stock: ");
                $this->produtoInterno->setStock($this->produtoInterno->sku,'orima');
                print_r($this->produtoInterno->stock. "\n");
            } catch (\Exception $ex) {
                print_r("Update stock exception - " . $ex -> getMessage() . "\n");
                $logger->info("Setting stock error: ".$this->produtoInterno->sku);
            }

        }



    }


    protected function desativateProducts() {
        
    }

    private function updateStocks()
    {
    }

    private function setOrimaStock($stock)
    {
        return (int)filter_var($stock, FILTER_SANITIZE_NUMBER_INT);;
    }

    private function setOrimaData($data)
    {
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
        $functionTim = function ($data){
            return trim($data);
        };

        $data = array_map($functionTim,$data);
        $this->produtoInterno->sku = $data[8];
        $this->produtoInterno->name = $data[0];
        $this->produtoInterno->gama = $data[4];
        $this->produtoInterno->familia = $data[5];
        $this->produtoInterno->subFamilia = $data[6];
        $this->produtoInterno->description = $data[9];
        $this->produtoInterno->meta_description = $data[9];
        $this->produtoInterno->manufacturer = $data[7];
        $this->produtoInterno->length = null;
        $this->produtoInterno->width = null;
        $this->produtoInterno->height = null;
        $this->produtoInterno->weight = null;
        $this->produtoInterno->price = (int)trim($data[2]) * 1.23 * 1.20;
        $this->produtoInterno->status = 1;
        $this->produtoInterno->image = $data[10];
        $this->produtoInterno->classeEnergetica = null;
        $this->produtoInterno->imageEnergetica = $data[11];
        $this->produtoInterno->stock = (int)filter_var($data[3], FILTER_SANITIZE_NUMBER_INT);
    }

    private function setOrimaCategories()
    {
        try {
            [$mlpGama, $mlpFamilia, $mlpSubFamilia] = CategoryManager::setCategoriesOrima(
                $this->produtoInterno->gama,
                $this->produtoInterno->familia,
                $this->produtoInterno->subFamilia);
        } catch (\Exception $e) {

        }
        $this->produtoInterno->gama = $mlpGama;
        $this->produtoInterno->familia = $mlpFamilia;
        $this->produtoInterno->subFamilia = $mlpSubFamilia;
    }
}
