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
    private $sorefozCategories;
    private $resourceConnection;

    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection,
                                DirectoryList $directory,
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
        $this->resourceConnection = $resourceConnection;

        parent ::__construct();
    }

    protected function configure()
    {
        $this -> setName('Mlp:Sorefoz')
            -> setDescription('Manage Sorefoz Products')
            -> setDefinition([
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
                    'Update Sorefoz Products'
                ),
                new InputOption(
                    self::ADD_IMAGES,
                    '-i',
                    InputOption::VALUE_NONE,
                    'Add images to products'
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
        $addProducts = $input -> getOption(self::ADD_PRODUCTS);
        if ($addProducts) {
            $this->addSorefozProducts($categories);
        }
        $updateStocks = $input -> getOption(self::UPDATE_STOCKS);
        if ($updateStocks) {
            $this -> updateSorefozProducts();
        }
        $addImages = $input->getOption(self::ADD_IMAGES);
        if($addImages) {
            $this->addImages($categories);
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

        $this->getCsvFromFTP($logger);
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
                    $this->produtoInterno->updatePrice($logger);
                } catch (\Exception $ex) {
                    print_r("Update stock exception - " . $ex -> getMessage() . "\n");
                }
            }
        }
    }
    
    protected function updateSorefozProducts()
    {
        $writer = new \Zend\Log\Writer\Stream($this -> directory -> getRoot() . '/var/log/Sorefoz.log');
        $logger = new \Zend\Log\Logger();
        $logger -> addWriter($writer);
        print_r("Updating Sorefoz products" . "\n");
        $this->getCsvFromFTP($logger);
        $row = 0;

        $statusAttributeId = $this->sqlGetAttributeId('status');
        $priceAttributeId = $this->sqlGetAttributeId('price');

        foreach ($this -> loadCsv -> loadCsv('/Sorefoz/tot_jlcb_utf.csv', ";") as $data) {
            //Update status sql
            $sku = trim($data[18]);
            print_r($row++." - ".$sku." - ");
            if (strlen($sku) > 12) {
                if ($this->sqlUpdateStatus($sku,$statusAttributeId[0]["attribute_id"])){
                    //update price anda stock
                    $price = $this->produtoInterno->getPrice((int)str_replace(".", "", $data[12]));
                    if ($price == 0){
                        print_r(" price 0\n");
                        $logger->(Cat::ERROR_PRICE_ZERO.$sku);
                        continue;
                    }
                    $this->sqlUpdatePrice($sku,$priceAttributeId[0]["attribute_id"],$price);
                    $this->produtoInterno->sku = $sku;
                    $stock = $this->getStock($data[29]);    
                    $this->produtoInterno->stock = $stock;
                    $this->produtoInterno->setStock($logger,"sorefoz");
                    print_r("updated - stock\n");
                }else {
                    //Add Product
                    print_r("Not found - Add Product - ");
                    if (!$this->setSorefozData($data,$logger)){
                        print_r("\n");
                        continue;
                    }
                    $this->produtoInterno -> add_product($logger, $this->produtoInterno->sku);
                    print_r("\n");
                }
            } else {
                print_r("Sku invalido\n");
                $logger->info(Cat::ERROR_WRONG_SKU.$sku);
            }
        }
    }

    private function getStock($stock) {
        if (preg_match("/sim/i",$stock) == 1){
            return 1;
        }else {
            return 0;
        }
    }

    private function sqlUpdatePrice($sku,$priceAttributeId,$price){
        $sqlEntityId = 'SELECT entity_id from catalog_product_entity where sku like "'.$sku.'"';
        $connection =  $this->resourceConnection->getConnection();
        $entityId = $connection->fetchAll($sqlEntityId);
        if (!empty($entityId)) {
            $sqlUpdateStatus = 'UPDATE catalog_product_entity_decimal 
                    SET value = '.$price.'
                    WHERE attribute_id = '.$priceAttributeId.' AND entity_id = '.$entityId[0]["entity_id"];
            $connection->query($sqlUpdateStatus);
            print_r("updated price - ");
            return true;
        } else {
            return false;
        }
    }

    private function sqlGetAttributeId($attribute) {
        $sqlStatusAttributeId = 'SELECT attribute_id from eav_attribute where attribute_code like "'.$attribute.'"';
        $connection =  $this->resourceConnection->getConnection();
        $statusAttributeId = $connection->fetchAll($sqlStatusAttributeId);
        return $statusAttributeId;
    }

    private function sqlUpdateStatus($sku,$statusId){
        $sqlEntityId = 'SELECT entity_id from catalog_product_entity where sku ='.$sku;
        $connection =  $this->resourceConnection->getConnection();
        $entityId = $connection->fetchAll($sqlEntityId);
        if (!empty($entityId)) {
            $sqlUpdateStatus = 'UPDATE catalog_product_entity_int 
                    SET value = 1
                    WHERE attribute_id = '.$statusId.' AND entity_id = '.$entityId[0]["entity_id"];
            $connection->query($sqlUpdateStatus);
            print_r("Enabled Product - ");
            return true;
        } else {
            return false;
        }
    }


    public function setSorefozData($data,$logger) {
        //Tirar os espaços em branco
        $functionTim = function ($el){
            return trim($el);
        };
        $data = array_map($functionTim,$data);

        $this->produtoInterno->sku = $data[18];
        $this->produtoInterno->name = $data[1];
        if (strlen($this->produtoInterno->sku) < 12) {
            print_r("Wrong sku - ");
            $logger->info(Cat::ERROR_WRONG_SKU.$this->produtoInterno->sku." - ".$this->produtoInterno->name);
            return 0;
        }
        
        $stock = $this->getStock($data[29]);

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
        $this->produtoInterno->setStock($logger,"sorefoz");
       
        if($this->produtoInterno->price == 0){
            //Se o preço for 0 desativar produto e ver o que se passa
            $logger->info(Cat::ERROR_PRICE_ZERO.$this->produtoInterno->sku);
            $this->produtoInterno->status = 2;
        }

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

    private function getCsvFromFTP($logger) {
        $file = 'tot_jlcb.csv';
        $local_file = $this->directory->getRoot()."/app/code/Mlp/Cli/Csv/Sorefoz/".$file;
        // set up basic connection
        $conn_id = ftp_connect('www.sorefoz.pt');

        // login with username and password
        if (ftp_login($conn_id, 'loj0078', 'nyvt64#')){
            ftp_pasv($conn_id, true);
            if (ftp_get($conn_id, $local_file, $file)) {
                print_r( "Successfully written to $local_file\n");
            } else {
                print_r("ftp_get problem\n");
            }
        } else {
            print_r("ftp_login Connection problem\n");
        }

        // try to download $server_file and save to $local_file
        

        // close the connection
        ftp_close($conn_id);

        $local_utf_file = $this->directory->getRoot()."/app/code/Mlp/Cli/Csv/Sorefoz/tot_jlcb_utf.csv";
        
        //Convet to csv to UTF-8
        $in = fopen($local_file, "r");
        $out = fopen($local_utf_file, "w+");

        $start = microtime(true);

        while(($line = fgets($in)) !== false) {
            $converted = iconv("ISO-8859-1","UTF-8",$line);
            fwrite($out, $converted);
        }

        $elapsed = microtime(true) - $start;
        print_r("Iconv took $elapsed seconds\n");
    }
}
