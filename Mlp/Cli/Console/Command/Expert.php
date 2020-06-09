<?php


namespace Mlp\Cli\Console\Command;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
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

use Mlp\Cli\Helper\CategoriesConstants as Cat;
use Mlp\Cli\Helper\Manufacturer as Manufacturer;
use Mlp\Cli\Helper\SqlHelper as SqlHelper;
class Expert extends Command
{

    /**
     * Filter Prodcuts
     */
    const UPDATE_PRODUCTS = 'update-products';
    const ADD_IMAGES = 'add-images';
    

    private $directory;
    
    private $productRepository;
    private $state;
    private $produtoInterno;
    private $loadCsv;
    private $imagesHelper;
    private $sqlHelper;

    public function __construct(DirectoryList $directory,
                                \Mlp\Cli\Helper\SqlHelper $sqlHelper,
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Model\ProdutoInterno $productoInterno,
                                \Mlp\Cli\Helper\LoadCsv $loadCsv,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
                                \Mlp\Cli\Helper\imagesHelper $imagesHelper){

        $this->directory = $directory;
        $this->sqlHelper = $sqlHelper;
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
                    self::UPDATE_PRODUCTS,
                    '-u',
                    InputOption::VALUE_NONE,
                    'Update Products'
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
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Expert.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $categories = $input->getArgument('categories');   
        $updateProducts = $input->getOption(self::UPDATE_PRODUCTS);
        if ($updateProducts){
            $this->updateProducts($logger,$categories);
        }
        $addImages = $input->getOption(self::ADD_IMAGES);
        if ($addImages) {
            $this->addImages($categories);
        }
        
        else {
            throw new \InvalidArgumentException('Option is missing.');
        }
    }



    protected function updateProducts($logger, $categoriesFilter = null){
        print_r("Getting Csv\n");
        $this->downloadCsv($logger);
        print_r("Updating Expert products" . "\n");
        $row = 0;
        $statusAttributeId = $this->sqlHelper->sqlGetAttributeId('status');
        $priceAttributeId = $this->sqlHelper->sqlGetAttributeId('price');

        foreach ($this -> loadCsv -> loadCsv('/Expert/Expert.csv', ";") as $data) {
            //Update status sql
            $sku = trim($data[1]);
            print_r($row++." - ".$sku." - ");
            if (strlen($sku) == 12 || strlen($sku) == 13) {
                if ($this->sqlHelper->sqlUpdateStatus($sku,$statusAttributeId[0]["attribute_id"])){
                    //update price anda stock
                    $price = $this->produtoInterno->getPrice((int)trim($data[7]));
                    if ($price == 0){
                        print_r(" price 0\n");
                        $logger->info(Cat::ERROR_PRICE_ZERO.$sku);
                        continue;
                    }
                    $this->sqlHelper->sqlUpdatePrice($sku,$priceAttributeId[0]["attribute_id"],$price);
                    $this->produtoInterno->sku = $sku;
                    $this->setStock($data[16]);    
                    $this->produtoInterno->setStock($logger,"expert");
                    print_r("updated - stock\n");
                }else {
                    //Add Product
                    print_r("Not found - Set data new product - ");
                    if (!$this->setData($data,$logger)){
                        print_r(" - ERROR WITH DATA\n");
                        continue;
                    }
                    print_r("add product - ");
                    $this->produtoInterno -> add_product($logger, $this->produtoInterno->sku);
                    print_r("\n");
                    
                }
            } else {
                print_r("Sku invalido\n");
                $logger->info(Cat::ERROR_WRONG_SKU.$sku);
            }
        }



    }

    private function downloadCsv($logger){
        print_r("ok\nDownloading new Csv\n");
        $ch = curl_init("https://experteletro.pt/webservice.php?key=42b91123-75ba-11ea-8026-a4bf011b03ee&pass=bWlndWVs");
        $fp = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/Csv/Expert/Expert.csv", 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
        curl_setopt($ch,CURLOPT_TIMEOUT,0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if (curl_exec($ch)){
            print_r("OK\n");
            curl_close($ch);
            fclose($fp);
        }else {
            print_r("Download Error");
            $logger->info(Cat::ERROR_DOWNLOAD_CSV);
            unlink($this->directory->getRoot()."/app/code/Mlp/Cli/Csv/Expert/Expert.csv");
        }
    }

    private function setStock($stock){
        if (preg_match("/Disponivel/i",$stock) == 1){
            $this->produtoInterno->stock = 1;
            $this->produtoInterno->status = Status::STATUS_ENABLED;
        }else {
            $this->produtoInterno->stock = 0;
            $this->produtoInterno->status = Status::STATUS_DISABLED;
        }
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
        
        
    
        $this->produtoInterno->sku = $data[1];
        
        $this->produtoInterno->manufacturer = $data[4];
        
        /*
        if (
            !preg_match("/MAXELL/i", $data[4]) &&
            !preg_match("/SAMSUNG/i", $data[4]) &&
            !preg_match("/PURO/i", $data[4]) &&
            !preg_match("/VIVANCO/i", $data[4]) &&
            !preg_match("/HISENSE/i", $data[4]) && 
            !preg_match("/TECNOGAS/i", $data[4]) &&
            !preg_match("/CASO/i", $data[4]) &&
            !preg_match("/FULLWAT/i", $data[4]) &&
            !preg_match("/CANON/i", $data[4]) &&
            !preg_match("/KEF/i", $data[4]) &&
            !preg_match("/G3 FERRARI/i", $data[4]) &&
            !preg_match("/R. HOBBS/i", $data[4]) &&
            !preg_match("/LE CREUSET/i", $data[4]) &&
            !preg_match("/KENWOOD/i", $data[4]) &&
            !preg_match("/ONE FOR ALL/i", $data[4]) &&
            !preg_match("/PLAYSTATION/i", $data[4]) &&
            !preg_match("/FLECK/i", $data[4])
            
        ) {
            return 0;
        }*/
        
        
        $this->produtoInterno->price = $this->produtoInterno->getPrice((int)trim($data[7]));

        
        $this->setStock($data[16]);
        if($this->produtoInterno->price == 0){
            print_r(" - price 0 - ");
            $logger->info(Cat::ERROR_PRICE_ZERO.$this->produtoInterno->sku);
            return  0;
        }

        if($this->produtoInterno->stock == 0){
            return 0;
        }

        $this->produtoInterno->name = $data[5];
        $this->produtoInterno->description = $data[10];
        $this->produtoInterno->meta_description = $data[10];
        
        $this->produtoInterno->length = null;
        $this->produtoInterno->width = null;
        $this->produtoInterno->height = null;
        $this->produtoInterno->weight = null;
        $this->produtoInterno->image = $data[13];
        $this->produtoInterno->classeEnergetica = $data[18];
        $this->produtoInterno->imageEnergetica = $data[19];

        if(preg_match("/Expert/i",$data[2])){
            print_r(" - gama: expert - ");
            return 0;
        }
        
        
        [$this->produtoInterno->gama,$this->produtoInterno->familia,
            $this->produtoInterno->subFamilia] = ExpertCategories::setExpertCategories($data[2],$logger,
                                                                                $this->produtoInterno->sku);
    
        return 1;
    }

    private function addImages($categoriesFilter) 
    {

        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Expert.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        
        $row = 0;
        foreach ($this->loadCsv->loadCsv('/Expert/Expert.csv',";") as $data) {
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

