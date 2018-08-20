<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mlp\Cli\Console\Command;
use Magento\Eav\Model\Entity\Attribute;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
/**
 * Class GreetingCommand
 */
class Attributes extends Command
{
    /**
     * Name argument
     */
    const NAME_ARGUMENT = 'attributes';
    /**
     * Allow option
     */
    const ADD_ATTRIBUTES = 'add-attributes';
    /**
     * Anonymous name
     */
    const ANONYMOUS_NAME = 'Anonymous';
    /**
     * {@inheritdoc}
     */
    const DEL_ATTRIBUTES = 'delete-attributes';

    const SHOW_ATTRIBUTES = 'show-attributes';

    private $entityAttribute;

    private $eavSetupFactory;

    private $attributeSetFactory;

    private $attributeSet;

    private $categorySetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory,
                                AttributeSetFactory $attributeSetFactory,
                                CategorySetupFactory $categorySetupFactory,
                                Attribute $entityAttribute)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->entityAttribute = $entityAttribute;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('Mlp:Attributes')
            ->setDescription('Manage Attributes')
            ->setDefinition([
                new InputOption(
                    self::ADD_ATTRIBUTES,
                    '-a',
                    InputOption::VALUE_NONE,
                    'add attributes'
                ),
                new InputOption(
                    self::DEL_ATTRIBUTES,
                    '-d',
                    InputOption::VALUE_NONE,
                    'delete attributes'
                ),
                new InputOption(
                    self::SHOW_ATTRIBUTES,
                    '-s',
                    InputOption::VALUE_REQUIRED,
                    'show attribute'
                )
            ]);
        parent::configure();
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $showAtt = $input->getOption(self::SHOW_ATTRIBUTES);
        if ($showAtt){
           $attCode = $input->getParameterOption('-s');
           print_r($attCode."\n");
           $attribute = $this->getAttribute($attCode);
           print_r($attribute);
        }
        $delete = $input->getOption(self::DEL_ATTRIBUTES);
        if ($delete){
            $this->deleteAttributes();
        }
        $option = $input->getOption(self::ADD_ATTRIBUTES);
        if ($option) {
            $this->setAttributes();
            $output->writeln('<info>Hello!</info>');
        } else {
            throw new \InvalidArgumentException('Option ' . self::ADD_ATTRIBUTES . ' is missing.');
        }
    }

    protected function setAttributes(){
        $attributes = include ('attributes.php');
        foreach ($attributes as $attribute) {
            $eavSetup = $this->eavSetupFactory->create();
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                $attribute['attribute_code'],
                $attribute
            );
        }
    }

    protected function deleteAttributes(){
        $attributes = include ('attributes.php');
        foreach ($attributes as $attribute) {
            $eavSetup = $this->eavSetupFactory->create();
            $eavSetup->removeAttribute(4,$attribute['attribute_code']);
        }
    }

    protected function getAttribute($attCode){
        $attribute = $this->entityAttribute->loadByCode('catalog_product',$attCode);
        return $attribute->getData();
    }

}
