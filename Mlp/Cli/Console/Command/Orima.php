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
    const FILTER_PRODUCTS = 'filter-products';
    const ADD_PRODUCTS = 'add-products';
    const UPDATE_INTERNO = 'update-interno';

    private $directory;
    private $categoryManager;
    private $productRepository;
    private $state;
    private $produtoInterno;
    private $loadCsv;
    private $registry;

    public function __construct(\Magento\Framework\Registry $registry,
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
                    self::UPDATE_INTERNO,
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
            $this->addProducts($logger, $categories);
        }
        $updateStocks = $input->getOption(self::UPDATE_INTERNO);
        if ($updateStocks){
            $this->updateInterno($logger);
        }
        else {
            throw new \InvalidArgumentException('Option ' . self::FILTER_PRODUCTS . ' is missing.');
        }
    }

    private function filterProducts()
    {
    }

    protected function addProducts($logger, $categoriesFilter = null){    
        print_r("Adding Orima products" . "\n");
        $row = 0;
        foreach ($this->loadCsv->loadCsv('/Orima/OrimaInterno.csv',";") as $data) {
            $row++;
            print_r($row." - ");
            try{
                if (!$this->setOrimaData($data,$logger)){
                    print_r("\n");
                    continue;
                };
            }catch(\Exception $e){
                $logger->info(Cat::ERROR_SET_PRODUCT_DATA.$row);
                continue;
            }
            if (!is_null($categoriesFilter)){
                if (strcmp($categoriesFilter,$this->produtoInterno->subFamilia) != 0){
                    print_r("wrong familie - ");
                    continue;
                }
            }
            try {
                print_r("getting product");
                $product = $this ->productRepository -> get($this->produtoInterno->sku, true, null, true);
            } catch (NoSuchEntityException $exception) {
                $this->setOrimaCategories($logger);
                $this->produtoInterno->manufacturer =  Manufacturer::getOrimaManufacturer($this->produtoInterno->manufacturer);
                $this->produtoInterno -> add_product($logger, $this->produtoInterno->sku);
                print_r("\n");
                continue;
                //$this -> produtoInterno -> addSpecialAttributesOrima($product, $logger);
            }
            if(isset($product)){
                print_r(" - Setting price: \n");
                $this->produtoInterno->updatePrice($logger);
            }

        }
    }

    private function updateInterno($logger)
    {
        $orimaLines = 0;
        $internoLines = 0;
        $fileUrlOrima = $this->directory->getRoot()."/app/code/Mlp/Cli/Csv/Orima/Orima.csv";
        $fileUrlInterno = $this->directory->getRoot()."/app/code/Mlp/Cli/Csv/Orima/OrimaInterno.csv";
      
        if (($handle = fopen($fileUrlOrima, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 5000, ";")) !== FALSE) {
                $orimaLines++;
            }
            fclose($handle);
        }

        if (($handle = fopen($fileUrlInterno, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 5000, ";")) !== FALSE) {
                $internoLines++;
            }
            fclose($handle);
        }

        print_r("Numero de linhas Orima: ".$orimaLines."\n");
        print_r("Numero de linhas Interno: ".$internoLines."\n");
        
        
        $linesToRemove = [];
        $currentLineInterno = 0;

        if (($handleInterno = fopen($fileUrlInterno, "r")) !== FALSE) {
            //ignora a 1ª linha
            fgetcsv($handleInterno, 5000, ";");
            while (($internoData = fgetcsv($handleInterno, 5000, ";")) !== FALSE) {
                if (strlen(trim($internoData[8])) != 13) {
                    continue;
                }
                $currentLineOrima = 1;
                if (($handleOrima = fopen($fileUrlOrima, "r")) !== FALSE) {
                    fgetcsv($handleOrima, 5000, ";");
                    while (($orimaData = fgetcsv($handleOrima, 5000, ";")) !== FALSE) {
                        print_r($internoData[8]." - Line interno: ".$currentLineInterno." - line: ".$currentLineOrima." - ".$orimaData[8]."\n");
                        if (strcmp($internoData[8],$orimaData[8]) == 0) {
                            $logger->info(Cat::WARN_FOUND_PRODUCT_SKU.$internoData[8]);
                            break;
                        }
                        
                        if ($currentLineOrima == $orimaLines){
                            //last line, not found, Add to array
                            $logger->info(Cat::ERROR_ADD_EAN_TO_OLD_EANFILE.$internoData[8]);
                            $linesToRemove[] = [trim($internoData[8]),trim($internoData[0])];
                        }
                        $currentLineOrima++;
                    }
                    fclose($handleOrima);
                }
                $currentLineInterno++;
            }
            fclose($handleInterno);
        }

        print_r($linesToRemove);
        foreach($linesToRemove as $data){
            try {
                //Vamos por o produto com stock Orima a 0, se tiver a 0 em todos os fornecedores podemos apagar (Cron Semanal por exemplo)
                print_r($data[0]."\n");
                $this->produtoInterno->sku = $data[0];
                $this->produtoInterno->stock = 0;
                $this->produtoInterno->setStock($logger,'orima');
                //$logger->info(Cat::WARN_OLD_PRODUCT.$this->produtoInterno->sku);
            }catch(\Exception $e) {
                print_r(Cat::ERROR_SET_STOCK_ZERO_TO_REMOVE.$e->getMessage());
            }
        }
    }

    private function setOrimaStock($stock)
    {
        return (int)filter_var($stock, FILTER_SANITIZE_NUMBER_INT);;
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
        /*
        if (!preg_match("/ORIMA/i", $this->produtoInterno->manufacturer)) {
            print_r($data[7]);
            return 0;
        }*/
        
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
