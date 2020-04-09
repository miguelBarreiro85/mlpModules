<?php


namespace Mlp\Cli\Console\Command;


use Braintree\Exception;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DirectoryList;
use Mlp\Cli\Helper\Manufacturer as Manufacturer;
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
    private $state;
    private $produtoInterno;
    private $loadCsv;


    public function __construct(DirectoryList $directory,
                                \Mlp\Cli\Helper\Category $categoryManager,
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Model\ProdutoInterno $productoInterno,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
                                LoadCsv $loadCsv)
    {

        $this -> directory = $directory;
        $this -> categoryManager = $categoryManager;
        $this -> productRepository = $productRepositoryInterface;
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
            $row++;
            print_r($row." - ");
            $this->setSorefozData($data);

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
            if($this->getProductSorefozStatus() == 0){
                print_r(" - disabled\n");
                continue;
            }
            try {
                $product = $this -> productRepository -> get($this->produtoInterno->sku, true, null, true);
            } catch (NoSuchEntityException $exception) {
                $manufacturer = Manufacturer::getSorefozManufacturer($this->produtoInterno->manufacturer);
                $this->produtoInterno->manufacturer = $manufacturer;
                $product = $this->produtoInterno -> add_product($categories, $logger, $this->produtoInterno->sku);
            }
            if(isset($product)){
                $this -> produtoInterno -> addSpecialAttributesSorefoz($product, $logger);
                try {
                    print_r(" - Setting stock: " . $data[29] . "\n");
                    $this -> setSorefozStock($product->getSku(), $data[29]);
                } catch (\Exception $ex) {
                    print_r("Update stock exception - " . $ex -> getMessage() . "\n");
                }
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
            $this->produtoInterno->updatePrice($sku, $price);
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

    public function setSorefozData($data) {
        $functionTim = function ($data){
            return trim($data);
        };


        if (preg_match("/sim/i",$data[27]) == 1){
            $stock = 1;
        }else {
            $stock = 0;
        }

        if (preg_match("/sim/i",$data[16]) == 1) {
            $status = 2;
        }else{
            $status = 1;
        }
        $data = array_map($functionTim,$data);
        $this->produtoInterno->sku = $data[18];
        $this->produtoInterno->name = $data[1];
        $this->produtoInterno->gama = $data[5];
        $this->produtoInterno->familia = $data[7];
        $this->produtoInterno->subfamilia = $data[9];
        $this->produtoInterno->description = $data[25];
        $this->produtoInterno->meta_description = $data[24];
        $this->produtoInterno->manufacturer = $data[3];
        $this->produtoInterno->length = (int)$data[20];
        $this->produtoInterno->width = (int)$data[21];
        $this->produtoInterno->height = (int)$data[22];
        $this->produtoInterno->weight = (int)$data[19];
        $this->produtoInterno->price = (int)str_replace(".", "", $data[12]) * 1.23 * 1.30;
        $this->produtoInterno->status = $status;
        $this->produtoInterno->image = $data[23];
        $this->produtoInterno->classeEnergetica = $data[25];
        $this->produtoInterno->imageEnergetica = $data[26];
        $this->produtoInterno->stock = $stock;
    }

    public function getProductSorefozStatus()
    {
        if ($this->produtoInterno->status == 1) {
            return 1;
        }
        else{
            return 0;
        }
    }
}
