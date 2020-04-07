<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mlp\Cli\Console\Command;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Class Products
 */
class Products extends Command
{
    /**
     * Name argument
     */
    const NAME_ARGUMENT = 'products';
    /**
     * Allow option
     */
    const CHANGE_MANUFACTURER = 'change-manufacturer';

    /**
     * Anonymous name
     */
    const ANONYMOUS_NAME = 'Anonymous';
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private $sortOrderBuilder;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productResource;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;
    /**
     * @var \Mlp\Cli\Helper\Data
     */
    private $dataAttributeOptions;

    /**
     * {@inheritdoc}
     */

    private $productCollectionFactory;

    public function __construct(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
                                ProductRepositoryInterface $productRepository,
                                SearchCriteriaBuilder $searchCriteriaBuilder,
                                FilterBuilder $filterBuilder,
                                \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
                                \Magento\Catalog\Model\ResourceModel\Product $productResource,
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Helper\Data  $dataAttributeOptions)
    {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->productResource = $productResource;
        $this->state = $state;
        $this->dataAttributeOptions =$dataAttributeOptions;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('Mlp:Products')
            ->setDescription('Manage Products')
            ->setDefinition([
                new InputOption(
                    self::CHANGE_MANUFACTURER,
                    '-m',
                    InputOption::VALUE_NONE,
                    'change manufacturer'
                ),
            ])
            ->addArgument('oldManufacturer', InputArgument::OPTIONAL, 'oldManufacturer')
            ->addArgument('newManufacturer', InputArgument::OPTIONAL, 'newManufacturer');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $changeManufacturer = $input->getOption(self::CHANGE_MANUFACTURER);
        $oldManufacturer = $input->getArgument('oldManufacturer');
        $newManufacturer = $input->getArgument('newManufacturer');
        if ($changeManufacturer) {
            $this->changeManufacturer($oldManufacturer,$newManufacturer);
            $output->writeln('<info>ACabei</info>');
        }
        else {
            throw new \InvalidArgumentException('Option  ELSE');
        }

    }


    protected function updatePrice($sku, $price){
        try{
            $product = $this->productRepository->get($sku, true, null, true);
            $product->setPrice($price);
            $this->productRepository->save($product);
            print_r("price updated - " . $sku . "\n");
        }catch (\Exception $ex){
            print_r("update price exception - " . $ex->getMessage() . "\n");
        }
    }


    protected function getAttribute($attCode)
    {
        $attribute = $this->entityAttribute->loadByCode('catalog_product', $attCode);
        return $attribute->getData();
    }

    private function changeManufacturer(?string $oldManufacturerCode, ?string $newManufacturer)
    {
        //select option_id from eav_attribute_option_value where value like "orima"; para ver qual é o numero
        $collection = $this->productCollectionFactory->create();
        $collection->getSelect()
            ->joinInner(["manufacturer" => "catalog_product_entity_int"],
                'e.entity_id = manufacturer.entity_id AND manufacturer.attribute_id = 83 AND manufacturer.value ='.$oldManufacturerCode,
                []);
        foreach ($collection as $product) {
            print_r($product->getSku()."\n");
            $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', $newManufacturer);
            $product->setCustomAttribute('manufacturer', $optionId);
            $this->productResource->saveAttribute($product,"manufacturer");
        }
    }


}
