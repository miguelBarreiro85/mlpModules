<?php
namespace Mlp\Cli\Console\Command;

use Braintree\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\AttributeSetManagement;
use Magento\Eav\Model\Entity\TypeFactory;

class MlpAttributeSet extends Command
{
    private $attributeSetManagement;
    private $eavTypeFactory;
    private $attributeSetFactory;
    public function __construct(AttributeSetManagement $attributeSetManagement,
                                SetFactory $attributeSetFactory,
                                TypeFactory $eavTypeFactory)
    {
        $this->eavTypeFactory = $eavTypeFactory;
        $this->attributeSetManagement = $attributeSetManagement;
        $this->attributeSetFactory = $attributeSetFactory;
        parent::__construct();

    }
    protected function configure()
    {
        $this->setName('Mlp:AttributeSet');
        $this->setDescription('Demo command line');

        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $attributeSets = ["TV","PORTATEIS","TABLETS","CALCULADORAS","COMPUTADORES","MONITORES","MEMORIAS",
                            "IMPRESSORAS","GPS","TELEMOVEIS","SECADORES_CABELO", "BALANCAS_WC", "MODULADORES", "DEPILADORAS",
                            "MAQUINAS_BARBEAR", "APARADORES", "MLR", "MSR", "MLL", "FOGÕES","MICROONDAS","FRIGORIFICOS", "CONGELADORES",
                            "CALDEIRAS_GAS", "ESQUENTADORES", "ESQUENTADORES_ELETRICOS", "TERMOACUMULADORES_ELETRICOS", "FORNOS", "PLACAS",
                            "EXAUSTORES", "MLL_ENCASTRE", "MLSR", "GARRAFEIRAS", "MISTURADORAS", "LAVA_LOUÇAS", "DESUMIDIFICADORES", "AC", "PAINEIS_SOLARES",
                            "ACUMULADORES_AGUA", "VENTOINHAS", "ASPIRADORES", "FERROS_ENGOMAR", "FERROS_CALDEIRA", "GRELHADORES", "BALANCAS_COZINHA","AUTO_RADIOS",
                            "AUTO_ALTIFALANTES", "AUTO_SISTEMAS NAVEGACAO", "AUTO_AMPLIFICADORES", "CAMARAS", "HOME_CINEMA","LEITOR_DVD","TDT", "RATOS", "TECLADOS",
                            "FERROS_VAPOR","HUMIDIFICADORES","EMISSORES_TERMICOS","CLIMATIZADORES","AUSCULTADORES"];
        $entityTypeCode = 'catalog_product';
        $entityType     = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
        $defaultSetId   = $entityType->getDefaultAttributeSetId();

        foreach ($attributeSets as $attributeSetName ) {
            try{
                $attributeSet = $this->attributeSetFactory->create();
                $data = [
                    'attribute_set_name' => $attributeSetName,
                    'entity_type_id' => $entityType->getId(),
                    'sort_order' => 200,
                ];
                $attributeSet->setData($data);

                $this->attributeSetManagement->create($entityTypeCode, $attributeSet, $defaultSetId);
            }catch (\Exception $ex){
                print_r($ex->getMessage()."\n");
            }

        }
        //return $newAttribute->getAttributeSetId();
    }
}