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

    protected function addProducts(){
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
        if (($handle = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/Console/Command/Orima/Orima.csv", "r")) !== FALSE) {
            print_r("abri ficheiro\n");
            while (!feof($handle)) {
                $row++;
                if (($data = fgetcsv($handle, 4000, ";", '"')) !== FALSE) {
                    if ($row == 1 ) {
                        continue;
                    }
                    $sku = trim($data[8]);
                    if (strlen($sku) == 13) {
                        try {
                            $product = $this->productRepository->get($sku, true, null, true);
                            if ($product->getStatus() == 2) {
                                print_r($sku . "\n");
                                continue;
                            }
                        } catch (NoSuchEntityException $exception) {
                            $name = trim($data[0]);
                            $gama = trim($data[4]);
                            $familia = trim($data[5]);
                            $subfamilia = trim($data[6]);
                            $description = trim($data[9]);
                            $meta_description = "";
                            $manufacter = trim($data[7]);
                            $length = 0;
                            $width = 0;
                            $height = 0;
                            $weight = trim($data[4]);
                            $price = (int)trim($data[2]) * 1.23 * 1.20;
                            $imagem = trim($data[10]); //ref Orima
                            $etiquetaEner = trim($data[11]);// EAN

                            $categories = $this->categoryManager->getCategoriesArray();
                            /** @var TYPE_NAME $sku */
                            $this->produtoInterno->setData($sku,$name,$gama,$familia,
                                $subfamilia,$description,$meta_description,$manufacter,
                                $length,$width,$height,$weight,$price);


                            $this->produtoInterno->setOrimaCategories();
                            ImagesHelper::getImages($sku, $imagem, $etiquetaEner,$this->directory);
                            $this->produtoInterno->add_product($categories, $logger, $sku);

                        }
                        $stock = $this->setOrimaStock($data[3]);
                        $this->produtoInterno->setStock($sku,'orima',$stock);

                    }
                    print_r($row." - sku: ".$sku." stock: ".$stock."-".$data[3]."\n");
                    unset($data);
                }
            }
            fclose($handle);
        }else {
            print_r("Não abriu o ficheiro");
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
