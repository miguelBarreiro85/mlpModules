<?php


namespace Mlp\Cli\Console\Command;


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
    private $productFactory;
    private $dataAttributeOptions;
    private $attributeManager;
    private $config;
    private $optionFactory;
    private $productRepositoryInterface;
    private $state;
    private $produtoInterno;
    private $loadCsv;

    public function __construct(DirectoryList $directory,
                                \Mlp\Cli\Helper\Category $categoryManager,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
                                \Magento\Catalog\Model\ProductFactory $productFactory,
                                \Mlp\Cli\Helper\Data $dataAttributeOptions,
                                \Mlp\Cli\Helper\Attribute $attributeManager,
                                \Magento\Catalog\Model\Product\Media\Config $config,
                                \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Model\ProdutoInterno $productoInterno,
                                \Mlp\Cli\Helper\LoadCsv $loadCsv){

        $this->directory = $directory;
        $this->categoryManager = $categoryManager;
        $this->productRepository = $productRepositoryInterface;
        $this->productFactory = $productFactory;
        $this->dataAttributeOptions = $dataAttributeOptions;
        $this->attributeManager = $attributeManager;
        $this->config = $config;
        $this->optionFactory = $optionFactory;
        $this->productRepositoryInterface = $productRepositoryInterface;
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
                $this->produtoInterno->setOrimaData($data);
            }catch(\Exception $e){
                $logger->info("Error setOrimaData: $data");
                continue;
            }
            if (strlen($this->produtoInterno->getSku()) != 13) {
                print_r("Wrong sku - ");
                $logger->info("Wrong Sku: ".$this->produtoInterno->getSku());
                continue;
            }
            if (!is_null($categoriesFilter)){
                if (strcmp($categoriesFilter,$this->produtoInterno->getSubFamilia()) != 0){
                    print_r("wrong familie - ");
                    continue;
                }
            }
            try {
                $this -> productRepository -> get($this->produtoInterno->getSku(), true, null, true);
            } catch (NoSuchEntityException $exception) {
                $categories = $this->categoryManager->getCategoriesArray();
                print_r(" - Setting Categories - ");
                $this->produtoInterno->setOrimaCategories();
                $manufacturer = Manufacturer::getOrimaManufacturer($this->produtoInterno->getManufacturer());
                $this->produtoInterno->setManufacturer($manufacturer);
                $this->produtoInterno -> add_product($categories, $logger, $this->produtoInterno->getSku());
                //$this -> produtoInterno -> addSpecialAttributesOrima($product, $logger);
            }
            try {
                print_r(" - Setting stock: ");
                $this->produtoInterno->setStock($this->produtoInterno->getSku(),'orima');
                print_r($this->produtoInterno->getStock(). "\n");
            } catch (\Exception $ex) {
                print_r("Update stock exception - " . $ex -> getMessage() . "\n");
            }

        }



    }


    private function updateStocks()
    {
    }

    private function setOrimaStock($stock)
    {
        return (int)filter_var($stock, FILTER_SANITIZE_NUMBER_INT);;
    }



}
