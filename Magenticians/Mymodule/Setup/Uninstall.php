<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 10-07-2018
 * Time: 19:29
 */

namespace Magenticians\Mymodule\Setup;


class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
    protected $eavSetupFactory;

    protected $_entityAttribute;

    protected $_entityAttributeCollection;

    protected $_entityAttributeOptionCollection;

    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
                                \Magento\Eav\Model\Entity\Attribute $entityAttribute,
                                \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $entityAttributeCollection,
                                \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $entityAttributeOptionCollection)
    {
        $this->eavSetupFactory = $eavSetupFactory;

        $this->_entityAttribute = $entityAttribute;
        $this->_entityAttributeCollection = $entityAttributeCollection;
        $this->_entityAttributeOptionCollection = $entityAttributeOptionCollection;
    }



    public function uninstall(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create();

        $attributes = include ("attributes.php");
        foreach ($attributes as $attributeCode){
            $attribute = $this->_entityAttribute->loadByCode('catalog_product',$attributeCode['code']);
            $eavSetup->removeAttribute(4, $attribute['code']);
        }

        $setup->endSetup();

    }
}