<?php
namespace Mlp\Cli\Console\Command;

//use False\True;
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
                        ["nome" => "tipo_placa_encastre", "label" => "Tipo", "attributeSet" => ["PLACAS"]],
                        ["nome" => "rotacao_mlr", "label" => "Rotação", "attributeSet" => ["MLR"] ],
                        ["nome" => "eficiencia_energetica", "label" => "Eficiencia", "attributeSet" =>
                            ["MLR","MLL","MSR","FORNOS","EXAUSTORES","AC","FRIGORIFICOS","CONGELADORES","TV"]],
                        ["nome" => "capacidade_kg", "label" => "Capacidade", "attributeSet" => ["MLR","MSR"]],
                        ["nome" => "capacidade_mll", "label" => "Capacidade", "attributeSet" => ["MLL"]],
                        ["nome" => "programas_mll", "label" => "Programas", "attributeSet" => ["MLL"]],
                        ["nome" => "medidas_fogao", "label" => "Medidas", "attributeSet" => ["FOGÕES"]],
                        ["nome" => "tipo_forno", "label" => "Forno", "attributeSet" => ["FOGÕES","FORNOS"]],
                        ["nome" => "medidas", "label" => "Medidas", "attributeSet" => ["CONGELADORES"]],
                        ["nome" => "largura", "label" => "Largura", "attributeSet" => ["FRIGORIFICOS"]],
                        ["nome" => "profundidade", "label" => "Profundidade", "attributeSet" => ["FRIGORIFICOS"]],
                        ["nome" => "altura", "label" => "Altura","attributeSet" => ["FRIGORIFICOS"]],
                        ["nome" => "capacidade_forno", "label" => "Capacidade", "attributeSet" => ["FORNOS"]],
                        ["nome" => "tipo_exaustor", "label" => "Tipo Exaustor", "attributeSet" => ["EXAUSTORES"]],
                        ["nome" => "potencia_ac_int", "label" => "Potencia", "attributeSet" => ["AC"]],
                        ["nome" => "potencia_ac_ext", "label" => "Potencia", "attributeSet" => ["AC"]],
                        ["nome" => "potencia_ac_conj", "label" => "Potencia", "attributeSet" => ["AC"]],
                        ["nome" => "tamanho_ecra", "label" => "Ecra", "attributeSet" => ["TV"]],
                        ["nome" => "color", "label" => "Cor", "attributeSet" => ["MLR","MLL","MSR",
                            "FORNOS","EXAUSTORES","AC","FRIGORIFICOS","CONGELADORES","TV"]],
                        ["nome" => "manufacturer", "label" => "marca", "attributeSet" => ["MLR","MLL","MSR",
                            "FORNOS","EXAUSTORES","AC","FRIGORIFICOS","CONGELADORES","TV"]],
                        ["nome" => "tipo_auscultadores", "label" => "Tipo","attributeSet"=>["AUSCULTADORES"]],
                        ["nome" => "conectividade_auscultadores", "label" => "Conectividade","attributeSet"=>["AUSCULTADORES"]],

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
                print_r($attribute["nome"]."\n");
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attribute["nome"],
                    [
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => $attribute["label"],
                        'input' => 'select',
                        'class' => '',
                        //'source' => '',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => true,
                        'default' => '',
                        'searchable' => true,
                        'filterable' => true,
                        'comparable' => true,
                        'visible_on_front' => true,
                        'used_in_product_listing' => false,
                        'unique' => false,
                        'apply_to' => ''
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