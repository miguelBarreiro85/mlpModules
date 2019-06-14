<?php
namespace Mlp\Cli\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;


class ProductAttributes extends Command
{

    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        parent::__construct();

    }
    protected function configure()
    {
        $this->setName('Mlp:ProductAttributes');
        $this->setDescription('Add PRoduct Attributes command');

        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pAttributes = [
                        ["nome" => "tipo_placa_encastre", "attributeSet" => ["PLACAS"]],
                        ["nome" => "rotacao_mlr", "attributeSet" => ["MLR"] ],
                        ["nome" => "eficiencia_energetica", "attributeSet" => ["Default"]],
                        ["nome" => "capacidade_kg", "attributeSet" => ["MLR","MSR"]],
                        ["nome" => "capacidade_mll", "attributeSet" => ["MLL"]],
                        ["nome" => "programas_mll", "attributeSet" => ["MLL"]],
                        ["nome" => "medidas_fogao", "attributeSet" => ["FOGÕES"]],
                        ["nome" => "tipo_forno", "attributeSet" => ["FOGÕES","FORNOS"]],
                        ["nome" => "medidas", "attributeSet" => ["CONGELADORES"]],
                        ["nome" => "largura", "attributeSet" => ["FRIGORIFICOS"]],
                        ["nome" => "profundidade", "attributeSet" => ["FRIGORIFICOS"]],
                        ["nome" => "altura", "attributeSet" => ["FRIGORIFICOS"]],
                        ["nome" => "capacidade_forno", "attributeSet" => ["FORNOS"]],
                        ["nome" => "tipo_exaustor", "attributeSet" => ["EXAUSTORES"]],
                        ["nome" => "potencia_ac_int", "attributeSet" => ["AC"]],
                        ["nome" => "potencia_ac_ext", "attributeSet" => ["AC"]],
                        ["nome" => "potencia_ac_conj", "attributeSet" => ["AC"]],
                        ["nome" => "tamanho_ecra", "attributeSet" => ["TV"]],

        ];
        /*
                        ","profundidade", "altura", "capacidade_forno",
            "tipo_exaustor","potencia_ac_int","potencia_ac_ext","potencia_ac_conj","tamanho_ecra"];
        */
        $eavSetup = $this->eavSetupFactory->create([]);
        foreach ($pAttributes as $attribute ) {
            //$eavSetup->removeAttribute(4,$attribute);
            //print_r("removed ".$attribute."\n");
            //continue;

            try{
                print_r($attribute."\n");
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attribute,
                    [
                        'type' => 'text',
                        'backend' => '',
                        'frontend' => '',
                        'label' => $attribute,
                        'input' => 'select',
                        'class' => '',
                        'source' => '',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => '',
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => false,
                        'unique' => false,
                        'apply_to' => '',
                        'attribute_set_id' => '1'
                    ]
                );
            }catch (\Exception $ex){
                print_r($ex->getMessage()."\n");
            }


            foreach ($attribute['attributeSet'] as $set){
                $setId = $eavSetup->getAttributeSetId(4, $set);
                $attributeId = $eavSetup->getAttributeId(4,$attribute['nome']);
                $groupId = $eavSetup->getAttributeGroupId(4,$setId,"general");
                $eavSetup->addAttributeToSet(4, $setId,$groupId, $attributeId );
            }


        }
        //return $newAttribute->getAttributeSetId();
    }
}