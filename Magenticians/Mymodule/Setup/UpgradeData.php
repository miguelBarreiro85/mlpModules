<?php
namespace Magenticians\Mymodule\Setup;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;
    private $attributeSetFactory;
    private $attributeSet;
    private $categorySetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory, AttributeSetFactory $attributeSetFactory, CategorySetupFactory $categorySetupFactory )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(),'1.0.9','<'))
        {
            $setup->startSetup();

            // TO CREATE ATTRIBUTE SET
            $data = $this->getAtributeSet();
            foreach ($data as $attSet) {
                $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

                $attributeSet = $this->attributeSetFactory->create();
                $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
                $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
                $attSet['entity_type_id'] = $entityTypeId;

                $attributeSet->setData($data);
                $attributeSet->validate();
                $attributeSet->save();
                $attributeSet->initFromSkeleton($attributeSetId);
                $attributeSet->save();
            }


            // TO CREATE PRODUCT ATTRIBUTE
            $attributes = $this->getAtributes();
            foreach ($attributes as $attribute) {
                var_dump($attribute);
                $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'text_my_custom_attribute',
                    $attribute
                );
            }


            $setup->endSetup();
        }
    }

    private function getAtributeSet()
    {
        $data = [
            [
                'attribute_set_name' => 'MyCustomAttribute',
                'entity_type_id' => '',
                'sort_order' => 200
            ],
            [
                'attribute_set_name' => 'MLR',
                'entity_type_id' => '',
                'sort_order' => 210
            ],
            [
                'attribute_set_name' => 'MLL',
                'entity_type_id' => '',
                'sort_order' => 220
            ]
        ];
        return $data;
    }

    private function getAttributes()
    {
        $attributes = [
            [
                'type' => 'varchar',
                'label' => 'Capacidade',
                'backend' => '',
                'input' => 'text',
                'wysiwyg_enabled'   => false,
                'source' => '',
                'required' => false,
                'sort_order' => 5,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'attribute_set' => 'MLR',
            ],
            [
                'type' => 'varchar',
                'label' => 'Rotação',
                'backend' => '',
                'input' => 'text',
                'wysiwyg_enabled'   => false,
                'source' => '',
                'required' => false,
                'sort_order' => 5,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'attribute_set' => 'MLR',
            ]

        ];
        return $attributes;
    }
}