<?php


namespace Mlp\Cli\Console\Command;


use Braintree\Exception;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DirectoryList;
use Mlp\Cli\Helper\splitFile;
use Mlp\Cli\Helper\imagesHelper as ImagesHelper;
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
                                LoadCsv $loadCsv)
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
        $this->loadCsv = $loadCsv;

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
        } else {
            throw new \InvalidArgumentException('Option  is missing.');
        }
    }


    protected function addSorefozProducts($categoriesFilter = null)
    {

        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Sorefoz.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $categories = $this->categoryManager->getCategoriesArray();
        $row = 0;
        foreach ($this->loadCsv->loadCsv('tot_jlcb_utf.csv',";") as $data) {
            if ($row == 0){
                $row++;
                continue;
            }
            $row++;
            print_r($row." - ");
            $this->produtoInterno->setSorefozData($data);
            if (strlen($this->produtoInterno->getSku()) != 13) {
                //Log sku e name
                continue;
            }
            if (!is_null($categoriesFilter)){
                if (strcmp($categoriesFilter,$this->produtoInterno->getSubFamilia()) != 0
                    || $this->produtoInterno->getProductSorefozStatus() == false){
                    print_r($this->produtoInterno->getSku() . " - Fora de Gama ");
                    continue;
                }
            }
            try {
                $product = $this -> productRepository -> get($this->produtoInterno->getSku(), true, null, true);
            } catch (NoSuchEntityException $exception) {
                ImagesHelper ::getImages($this->produtoInterno->getSku(), $data[24], $data[28], $this -> directory);
                $product = $this->produtoInterno -> add_product($categories, $logger, $this->produtoInterno->getSku());
                $this -> produtoInterno -> addSpecialAttributesSorefoz($product, $logger);
            }
            try {
                $this -> setSorefozStock($product->getSku(), $data[29]);
                print_r(" -  stock: " . $data[29] . "\n");
            } catch (\Exception $ex) {
                print_r("Update stock exception - " . $ex -> getMessage() . "\n");
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
    protected function updateSorefozPrices()
    {
        $writer = new \Zend\Log\Writer\Stream($this -> directory -> getRoot() . '/var/log/Sorefoz.log');
        $logger = new \Zend\Log\Logger();
        $logger -> addWriter($writer);
        print_r("Updating Sorefoz prices" . "\n");

        foreach ($this -> loadCsv -> loadCsv('tot_jlcb_utf.csv', ";") as $data) {

            $this -> updatePrice($sku, $price);
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
}
