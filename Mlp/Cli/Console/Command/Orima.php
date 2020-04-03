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

    public function __construct(DirectoryList $directory,
                                \Mlp\Cli\Helper\Category $categoryManager,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
                                \Magento\Catalog\Model\ProductFactory $productFactory,
                                \Mlp\Cli\Helper\Data $dataAttributeOptions,
                                \Mlp\Cli\Helper\Attribute $attributeManager,
                                \Magento\Catalog\Model\Product\Media\Config $config,
                                \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Model\ProdutoInterno $productoInterno){

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
            ]);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $filterProducts = $input->getOption(self::FILTER_PRODUCTS);
        if ($filterProducts) {
            $this->filterProducts();
        }
        $addProducts = $input->getOption(self::ADD_PRODUCTS);
        if ($addProducts) {
            $this->addProducts();
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

        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Orima.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Orima products" . "\n");
        $row = 0;
        foreach ($this->loadCsv->loadCsv('tot_jlcb_utf.csv',";") as $data) {
            if ($row == 0){
                $row++;
                continue;
            }
            $row++;
            print_r($row." - ");
            $this->produtoInterno->setOrimaData($data);
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

    private function updateStocks()
    {
    }

    private function setOrimaStock($stock)
    {
        return (int)filter_var($stock, FILTER_SANITIZE_NUMBER_INT);;
    }

}
