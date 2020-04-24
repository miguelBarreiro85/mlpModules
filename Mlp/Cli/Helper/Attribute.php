<?php


namespace Mlp\Cli\Helper;


use Mlp\Cli\Helper\CategoriesConstants as Cat;
use Braintree\Exception;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Mlp\Cli\Helper\Data;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\AttributeSetManagement;
use Magento\Eav\Model\AttributeSetRepository;


class Attribute
{

    private $valueFactory;

    private $dataAttributeOptions;

    private $attributeSetCollection;

    private $eavTypeFactory;

    private $attributeSetFactory;

    private $attributeSetManagement;

    public function __construct(\Magento\Framework\Api\AttributeValueFactory $valueFactory,
                                Data $dataAttributeOptions,
                                CollectionFactory $attributeSetCollection,
                                typeFactory $eavTypeFactory,
                                SetFactory $attributeSetFactory,
                                AttributeSetManagement $attributeSetManagement)
    {
        $this->valueFactory = $valueFactory;
        $this->dataAttributeOptions = $dataAttributeOptions;
        $this->attributeSetCollection = $attributeSetCollection;
        $this->eavTypeFactory = $eavTypeFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeSetManagement = $attributeSetManagement;
    }

    public function addSorefozAttributes($description, $familia, $subfamilia)
    {
        $attributes = [];
        switch ($familia) {
            case 'TELEVISÃO':
                preg_match('/Diagonal de Ecrã: (\d+) cm/', $description, $matches);
                if (isset($matches[1])) {
                    $optionId = $this->dataAttributeOptions->createOrGetId('diagonal', $matches[1]);
                    $attributeValue['attribute_code'] = 'diagonal';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes, $attributeValue);
                    return $attributes;
                }
                break;
        }
        switch ($subfamilia) {
            case 'PORTÁTEIS':
                preg_match('/Processador: (\w+)/', $description, $matches);
                if (isset($matches[1])) {
                    $optionId = $this->dataAttributeOptions->createOrGetId('processador', $matches[1]);
                    $attributeValue['attribute_code'] = 'processador';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes, $attributeValue);
                }
                preg_match('/Memória RAM: (\w+)/', $description, $matches2);
                if (isset($matches2[1])) {
                    $optionId = $this->dataAttributeOptions->createOrGetId('ram', $matches2[1]);
                    $attributeValue['attribute_code'] = 'ram';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes, $attributeValue);
                }
                preg_match('/Capacidade: (\d+)/', $description, $matches3);
                if (isset($matches3[1])) {
                    $optionId = $this->dataAttributeOptions->createOrGetId('disco', $matches3[1]);
                    $attributeValue['attribute_code'] = 'disco';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes, $attributeValue);
                }
                return $attributes;
                break;
        }
    }

    public function getAttributeSetId($familia, $subfamilia)
    {
        try {
            switch ($familia) {
                //Imagem SOM
                case Cat::TELEVISAO:
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'TV')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case Cat::CAMARAS_FOTOGRAFICAS:
                case Cat::CAMARAS_VIDEO:
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'CAMARAS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'SIST.HOME CINEMA':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'HOME_CINEMA')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'DVD /BLURAY /TDT':
                    switch ($subfamilia){
                        case 'LEITOR DE DVD':
                        case 'LEITOR BLURAY/HDDVD':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'LEITOR_DVD')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'RECEPTORES TDT':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'TDT')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }

                case 'DVD /BLURAY /TDT':
                //Informatica
                case 'COMPUTADORES E TABLET\'S':
                    switch ($subfamilia) {
                        case 'PORTÁTEIS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'PORTATEIS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'TABLET\'S':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'TABLETS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'CALCULADORAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'CALCULADORAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'DESKTOPS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'COMPUTADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'ACESSÓRIOS':
                            switch ($subfamilia){
                                case 'RATOS':
                                    $attributeSet = $this->attributeSetCollection->create()
                                        ->addFieldToSelect('attribute_set_id')
                                        ->addFieldToFilter('attribute_set_name', 'RATOS')
                                        ->getFirstItem()
                                        ->toArray();
                                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                                    return $attributeSetId;
                                case 'TECLADOS':
                                    $attributeSet = $this->attributeSetCollection->create()
                                        ->addFieldToSelect('attribute_set_id')
                                        ->addFieldToFilter('attribute_set_name', 'TECLADOS')
                                        ->getFirstItem()
                                        ->toArray();
                                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                                    return $attributeSetId;
                            }
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'COMPUTADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;


                    }
                case 'MONITORES':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'MONITORES')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'MEMÓRIAS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'MEMORIAS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'IMPRESSORAS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'IMPRESSORAS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'GPS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'GPS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                //COMUNICAÇÕES
                case 'TELEMÓVEIS / CARTÕES':
                    switch ($subfamilia) {
                        case 'TELEMÓVEIS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'TELEMOVEIS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;
                    }
                //Pequenos Domésticos
                case 'APARELHOS DE COZINHA':
                    switch ($subfamilia) {
                        case 'GRELHADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'GRELHADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'BALANÇAS DE COZINHA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'BALANCAS_COZINHA')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }
                case 'CUIDADO DE ROUPA':
                    switch ($subfamilia) {
                        case 'FERROS A VAPOR':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'FERROS_VAPOR')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'FERROS COM CALDEIRA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'FERROS_CALDEIRA')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;

                    }
                case 'ASSEIO PESSOAL':
                    switch ($subfamilia) {
                        case 'SECADORES DE CABELO':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'SECADORES_CABELO')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'BALANÇAS DE W.C.':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'BALANCAS_WC')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MODELADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MODULADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'DEPILADORAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'DEPILADORAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MAQUINAS DE BARBEAR':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MAQUINAS_BARBEAR')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'APARADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'APARADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }
                case 'APARELHOS DE LIMPEZA':
                    switch ($subfamilia) {
                        case 'ASPIRADOR SEM SACO':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'ASPIRADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'ASPIRADOR COM SACO':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'ASPIRADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }
                case 'ARTIGOS DE MENAGE':
                    switch ($subfamilia) {

                    }


                //Grandes domesticos
                case 'MAQUINAS LAVAR ROUPA':
                    switch($subfamilia) {
                        case 'MLR LAVAR E SECAR ROUPA':
                            $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'MLSR')
                            ->getFirstItem()
                            ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MLR')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        }
                case 'MAQUINAS SECAR ROUPA':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'MSR')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'MAQUINAS LAVAR LOUÇA':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'MLL')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'FOGÕES':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'FOGÕES')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'MICROONDAS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'MICROONDAS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'FRIGORIFICOS/COMBINADOS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'FRIGORIFICOS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'CONGELADORES':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'CONGELADORES')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'ESQUENTADORES/CALDEIRAS':
                    switch ($subfamilia) {
                        case 'CALDEIRAS C/GÁS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'CALDEIRAS_GAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'ESQUENTADORES C/GÁS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'ESQUENTADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'ESQUENTADORES - ELÉCTRICOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'ESQUENTADORES_ELETRICOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;//Default
                    }
                case 'TERMOACUMULADORES':
                    switch ($subfamilia) {
                        case 'TERMOACUMULADORES - ELÉCTRICOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'TERMOACUMULADORES_ELETRICOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;
                    }
                case 'ENCASTRE':
                    switch ($subfamilia) {
                        case 'FORNOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'FORNOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'PLACAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'PLACAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'EXAUSTORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'EXAUSTORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MÁQUINAS DE LOIÇA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MLL_ENCASTRE')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'COMBINADOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'FRIGORIFICOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'FRIGORIFICOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'FRIGORIFICOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MAQ.LAVAR/SECAR ROUPA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MLSR')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        CASE 'CONGELADORES VERTICAIS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'CONGELADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MAQ.LAVAR ROUPA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MLR')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'GARRAFEIRAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'GARRAFEIRAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MISTURADORAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MISTURADORAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'LAVA LOUÇAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'LAVA_LOUÇAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MAQ.SECAR ROUPA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MSR')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;
                    }
                //CLIMATIZAÇÃO
                case 'AQUECIMENTO':
                    switch ($subfamilia) {
                        case 'SALAMANDRAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'SALAMANDRAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'EMISSORES TÉRMICOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'EMISSORES_TERMICOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'RECUPERADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'RECUPERADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'CONVECTORES/TERMOVENT':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'TERMOVENTILADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'RADIADORES A OLEO':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'RADIADORES_OLEO')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }
                case 'TRATAMENTO DE AR':
                    switch ($subfamilia){
                        case 'DESUMIDIFICADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'DESUMIDIFICADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'HUMIDIFICADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'HUMIDIFICADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }

                case 'AR CONDICIONADO':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'AC')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'SISTEMAS AQUEC.SOLAR':
                    switch ($subfamilia) {
                        case 'PAINEIS SOLARES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'PAINEIS_SOLARES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'ACUM.DE ÁGUA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'ACUMULADORES_AGUA')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;
                    }
                case 'VENTILAÇÃO':
                    switch ($subfamilia) {
                        case 'VENTOINHAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'VENTOINHAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;
                    }
                //CAR AUDIO
                case 'AUTO-RADIOS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'AUTO_RADIOS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'ALTIFALANTES':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'AUTO_ALTIFALANTES')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'SISTEMAS NAVEGAÇÃO':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'AUTO_SISTEMAS NAVEGACAO')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'AMPLIFICADORES':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'AUTO_AMPLIFICADORES')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                default:
                    return 4;
        }
        }catch (\Exception $ex){
            /*
            $entityTypeCode = 'catalog_product';
            $entityType     = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $defaultSetId   = $entityType->getDefaultAttributeSetId();

            $attributeSet = $this->attributeSetFactory->create();
            $data = [
                'attribute_set_name'    => $familia,
                'entity_type_id'        => $entityType->getId(),
                'sort_order'            => 200,
            ];
            $attributeSet->setData($data);

            $newAttribute = $this->attributeSetManagement->create($entityTypeCode, $attributeSet, $defaultSetId);

            return $newAttribute->getAttributeSetId();
            */
            return 4;

        }
        return 4;

    }

    /**
     * @param $gama
     * @param $familia
     * @param $subfamilia
     * @param $description
     * @param $name
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSpecialAttributes($gama, $familia, $subfamilia, $description, $name)
    {
        $attributes = [];
        switch ($gama) {
            case 'IMAGEM E SOM':
                switch ($familia) {
                    case 'AUSCULTADORES':
                        $attribute['code'] = 'conectividade_auscultadores';
                        if (preg_match('/BT/', $name) || preg_match('/BLUETOOHT/', $name)) {
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('conectividade_auscultadores', 'bluetooth');
                        } else {
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('conectividade_auscultadores', 'cabo jack');
                        }
                }
            case 'GRANDES DOMÉSTICOS':
                if (preg_match('/Largura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $mLargura) == 1) {
                    $getLargura = $this->getLargura($mLargura[1]);
                    $largura['code'] = 'largura';
                    $largura['value'] = $this->dataAttributeOptions->createOrGetId('largura', $getLargura);
                    array_push($attributes, $largura);
                }
                if (preg_match('/Profundidade:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $mProfundidade) == 1) {
                    $getProfundidade = $this->getProfundidade($mProfundidade[1]);
                    $profundidade['code'] = 'profundidade';
                    $profundidade['value'] = $this->dataAttributeOptions->createOrGetId('profundidade', $getProfundidade);
                    array_push($attributes, $profundidade);
                }
                if (preg_match('/Altura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $mAltura) == 1) {
                    $getAltura = $this->getAltura($mAltura[1]);
                    $altura['code'] = 'altura';
                    $altura['value'] = $this->dataAttributeOptions->createOrGetId('altura', $getAltura);
                    array_push($attributes, $altura);
                }
                if (preg_match('/Cor: (\w+)/ui', strip_tags($description), $mCor) == 1) {
                    $cor['code'] = 'color';
                    $cor['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($mCor[1]));
                    array_push($attributes, $cor);
                }
                switch ($familia) {
                    case 'ENCASTRE - MESAS':
                        $attribute['code'] = 'tipo_placa_encastre';
                        switch ($subfamilia) {
                            case 'CONVENCIONAIS C/GÁS':
                                $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Gás');
                                array_push($attributes, $attribute);
                                return $attributes;
                            case 'DE INDUÇÃO':
                                $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Indução');
                                array_push($attributes, $attribute);
                                return $attributes;
                            case 'VITROCERÂMICAS C/GÁS':
                                $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Vitrocerâmicas Gás');
                                array_push($attributes, $attribute);
                                return $attributes;
                            case 'DOMINÓS C/GÁS':
                                $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Dominós Gás');
                                array_push($attributes, $attribute);
                                return $attributes;
                            case 'VITROCERÂMICAS - ELÉCTRICAS':
                                $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Vitrocerâmicas');
                                array_push($attributes, $attribute);
                                return $attributes;
                            case 'DOMINÓS - ELÉCTRICOS':
                                $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Dominós Eléctricos');
                                array_push($attributes, $attribute);
                                return $attributes;
                            default:
                                $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Outras');
                                array_push($attributes, $attribute);
                                return $attributes;
                        }
                    case 'ENCASTRE - MAQ.L.ROUPA':
                    case 'MAQUINAS LAVAR ROUPA':
                        /* if (preg_match('/(\d+)R\./', $name, $matches) == 1) {
                            if ((int)$matches[1] > 600) {
                                $attribute1['code'] = 'rotacao_mlr';
                                $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('rotacao_mlr', (int)$matches[1]);
                                array_push($attributes, $attribute1);
                            }
                        }
                        if (preg_match('/R.(\d+)K/', $name, $matches1) == 1) {
                            if ((int)$matches1[1] > 1) {
                                $attribute2['code'] = 'capacidade_kg';
                                $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('capacidade_kg', (int)$matches1[1]);
                                array_push($attributes, $attribute2);
                            }

                        } */
                        if (preg_match('/(A\+{1,3})/', $name, $matches2) == 1) {
                            $attribute3['code'] = 'eficiencia_energetica';
                            $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches2[1]));
                            array_push($attributes, $attribute3);
                        }
                        if (preg_match('/Cor: (\w+)\s/', strip_tags($description), $matches3) == 1) {
                            print_r("- " . $matches3[1] . " - ");
                            $attribute4['code'] = 'color';
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches3[1]));
                            array_push($attributes, $attribute4);
                        }
                        return $attributes;
                    case 'ENCASTRE - MAQ.LOUÇA':
                    case 'MAQUINAS LAVAR LOUÇA':
                        if (preg_match('/Cor: (\w+)\s/', strip_tags($description), $matches1) == 1) {
                            $attribute1['code'] = 'color';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches1[1]));
                            array_push($attributes, $attribute1);
                        }
                        if (preg_match('/(A\+{1,3})/', $name, $matches2) == 1) {
                            $attribute2['code'] = 'eficiencia_energetica';
                            $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches2[1]));
                            array_push($attributes, $attribute2);
                        }
                        if (preg_match('/(\d+)TA/', $name, $matches3) == 1) {
                            $attribute3['code'] = 'capacidade_mll';
                            $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('capacidade_mll', $matches3[1] . " Conjuntos");
                            array_push($attributes, $attribute3);
                        }
                        if (preg_match('/(\d)P/', $name, $matches4) == 1) {
                            print_r(" - programas: " . $matches4[1] . " - ");
                            $attribute4['code'] = 'programas_mll';
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('programas_mll', $matches4[1] . " Programas");
                            array_push($attributes, $attribute4);
                        }
                        return $attributes;
                    case 'MAQUINAS SECAR ROUPA':
                        if (preg_match('/Cor: (\w+)/ui', strip_tags($description), $matches1) == 1) {
                            $attribute1['code'] = 'color';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches1[1]));
                            array_push($attributes, $attribute1);
                        }
                        if (preg_match('/(A\+{1,3})/', $name, $matches2) == 1) {
                            $attribute2['code'] = 'eficiencia_energetica';
                            $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches2[1]));
                            array_push($attributes, $attribute2);
                            if (preg_match('/(A\+{1,3})/', $name, $matches2) == 1) {
                                $attribute2['code'] = 'eficiencia_energetica';
                                $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches2[1]));
                                array_push($attributes, $attribute2);
                            }
                        } elseif (preg_match('/Classe Energética: (\w)/', html_entity_decode(strip_tags($description)), $matches3) == 1) {
                            $attribute3['code'] = 'eficiencia_energetica';
                            $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches3[1]));
                            array_push($attributes, $attribute3);
                        }
                        if (preg_match('/(\d+)K/', $name, $matches4) == 1) {
                            $attribute4['code'] = 'capacidade_kg';
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('capacidade_kg', $matches4[1] . " kg");
                            array_push($attributes, $attribute4);
                        }
                        return $attributes;
                    case 'FOGÕES':
                        if (preg_match('/(\d+x\d+)/', $name, $matches1) == 1) {
                            $attribute1['code'] = 'medidas_fogao';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('medidas_fogao', trim($matches1[1]));
                            array_push($attributes, $attribute1);
                        }
                        if (preg_match('/Forno: (\w+)/ui', strip_tags($description), $matches2) == 1) {
                            $attribute2['code'] = 'tipo_forno';
                            $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('tipo_forno', trim($matches2[1]));
                            array_push($attributes, $attribute2);
                        }
                        if (preg_match('/Cor: (\w+)/ui', strip_tags($description), $matches3) == 1) {
                            $attribute3['code'] = 'color';
                            $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches3[1]));
                            array_push($attributes, $attribute3);
                        }
                        if (preg_match('/Largura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches4) == 1) {
                            $attribute4['code'] = 'largura';
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('largura', $matches4[1]);
                            array_push($attributes, $attribute4);
                        }
                        return $attributes;
                    case 'CONGELADORES':
                        if (preg_match('/Cor:\s*(\w+\s*\w*)/ui', strip_tags($description), $matches4) == 1) {
                            $attribute4['code'] = 'color';
                            $cor = $this->getCor($matches4[1]);
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('color', $cor);
                            array_push($attributes, $attribute4);
                        }
                        if (preg_match('/(A\+{1,3})/', $name, $matches5) == 1) {
                            $attribute5['code'] = 'eficiencia_energetica';
                            $attribute5['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches5[1]));
                            array_push($attributes, $attribute5);
                        } elseif (preg_match('/Classe Energética: (\w\S*\s*\S*)/', html_entity_decode(strip_tags($description)), $matches6) == 1) {
                            $eficiencia = $this->getClasseEnergetica($matches6[1]);
                            $attribute6['code'] = 'eficiencia_energetica';
                            $attribute6['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', $eficiencia);
                            array_push($attributes, $attribute6);
                        }
                        if (preg_match('/Congelador:\s*(\d+\s*Litros)/i', $description, $matches7) == 1) {
                            $attribute7['code'] = 'capacidade_congelador';
                            $litragem = $this->getLitragem($matches7[1]);
                            $attribute7['value'] = $this->dataAttributeOptions->createOrGetId('capacidade_congelador', $litragem);
                            array_push($attributes, $attribute7);
                        }
                        return $attributes;
                    case 'ENCASTRE - FRIO':
                    case 'FRIGORIFICOS/COMBINADOS':
                        if (preg_match('/Largura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches1) == 1) {
                            $attribute1['code'] = 'largura';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('largura', $matches1[1]);
                            array_push($attributes, $attribute1);
                        }
                        if (preg_match('/Profundidade:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches2) == 1) {
                            $attribute2['code'] = 'profundidade';
                            $profundidade = $this->getProfundidade($matches2[1]);
                            $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('profundidade', $profundidade);
                            array_push($attributes, $attribute2);
                        }
                        if (preg_match('/Altura:\s*(\d*,*\d*\s*)cm/', strip_tags($description), $matches3) == 1) {
                            $attribute3['code'] = 'altura';
                            $altura = $this->getAltura($matches3[1]);
                            $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('altura', $altura);
                            array_push($attributes, $attribute3);
                        }
                        if (preg_match('/Cor:\s*(\w+\s*\w*)/ui', strip_tags($description), $matches4) == 1) {
                            $attribute4['code'] = 'color';
                            $cor = $this->getCor($matches4[1]);
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('color', $cor);
                            array_push($attributes, $attribute4);
                        }
                        if (preg_match('/(A\+{1,3})/', $name, $matches5) == 1) {
                            $attribute5['code'] = 'eficiencia_energetica';
                            $attribute5['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches5[1]));
                            array_push($attributes, $attribute5);
                        } elseif (preg_match('/Classe Energética:\s*(\w\W*\d*\s*\W*\d*\s\W*\d*).*Con/', strip_tags($description), $matches6) == 1) {
                            $eficiencia = $this->getClasseEnergetica($matches6[1]);
                            $attribute6['code'] = 'eficiencia_energetica';
                            $attribute6['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', $eficiencia);
                            array_push($attributes, $attribute6);
                        }
                        return $attributes;
                    case 'ENCASTRE - FORNOS':
                        if (preg_match('/(A\+{1,3})/', $name, $matches1) == 1) {
                            $attribute1['code'] = 'eficiencia_energetica';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches1[1]));
                            array_push($attributes, $attribute1);
                        } elseif (preg_match('/nergética:\s*(\w\W*\d*\s*\W*\d*\s\W*\d*).*Con/', strip_tags($description), $matches2) == 1) {
                            $eficiencia = $this->getClasseEnergetica($matches2[1]);
                            $attribute2['code'] = 'eficiencia_energetica';
                            $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', $eficiencia);
                            array_push($attributes, $attribute2);
                        }
                        if (preg_match('/Cor:\s*(\w+)/ui', strip_tags($description), $matches3) == 1) {
                            $attribute3['code'] = 'color';
                            $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches3[1]));
                            array_push($attributes, $attribute3);
                        }
                        if (preg_match('/Forno:\s*(\d+ Litros)/ui', strip_tags($description), $matches4) == 1) {
                            $attribute4['code'] = 'capacidade_forno';
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('capacidade_forno', trim($matches4[1]));
                            array_push($attributes, $attribute4);
                        }
                        if (preg_match('/Forno: (\w+)/ui', strip_tags($description), $matches5) == 1) {
                            $attribute5['code'] = 'tipo_forno';
                            $attribute5['value'] = $this->dataAttributeOptions->createOrGetId('tipo_forno', trim($matches5[1]));
                            array_push($attributes, $attribute5);
                        }
                        if (preg_match('/Largura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches6) == 1) {
                            $attribute6['code'] = 'largura';
                            $attribute6['value'] = $this->dataAttributeOptions->createOrGetId('largura', $matches6[1]);
                            array_push($attributes, $attribute6);
                        }
                        if (preg_match('/Profundidade:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches7) == 1) {
                            $attribute7['code'] = 'profundidade';
                            $attribute7['value'] = $this->dataAttributeOptions->createOrGetId('profundidade', $matches7[1]);
                            array_push($attributes, $attribute7);
                        }
                        if (preg_match('/Altura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches8) == 1) {
                            $attribute8['code'] = 'altura';
                            $attribute8['value'] = $this->dataAttributeOptions->createOrGetId('altura', $matches8[1]);
                            array_push($attributes, $attribute8);
                        }
                        return $attributes;
                    case 'ENCASTRE - EXAUSTOR/EXTRATORES':
                        if (strcmp($subfamilia, 'EXAUST.DE CHAMINÉ') == 0) {
                            $attribute1['code'] = 'tipo_exaustor';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('tipo_exaustor', 'chaminé');
                            array_push($attributes, $attribute1);
                        } elseif (strcmp($subfamilia, 'EXAUST.TELESCÓPICOS') == 0) {
                            $attribute1['code'] = 'tipo_exaustor';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('tipo_exaustor', 'telescópio');
                            array_push($attributes, $attribute1);
                        } elseif (strcmp($subfamilia, 'EXAUST.CONVENCIONAIS') == 0) {
                            $attribute1['code'] = 'tipo_exaustor';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('tipo_exaustor', 'tradicional');
                            array_push($attributes, $attribute1);
                        }
                        if (preg_match('/Extracção:\s*(\d+)\s*m3/', $description, $mExtracao) == 1) {
                            $capExtracao = $this->getExtracao($mExtracao[1]);
                            $extracao['code'] = 'potencia_exaustor';
                            $extracao['value'] = $this->dataAttributeOptions->createOrGetId('potencia_exaustor', $capExtracao);
                            array_push($attributes, $extracao);
                        }
                        if (preg_match('/Cor: (\w+)/ui', strip_tags($description), $mCor) == 3) {
                            $corValue = $this->getCor($mCor[1]);
                            $corAtr['code'] = 'color';
                            $corAtr['value'] = $this->dataAttributeOptions->createOrGetId('color', $corValue);
                            array_push($attributes, $corAtr);
                        }
                        if (preg_match('/Largura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $mLargura) == 1) {
                            $largura = $this->getLarguraExaustor($mLargura[1]);
                            $attribute4['code'] = 'largura';
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('largura', $largura);
                            array_push($attributes, $attribute4);
                        }
                        return $attributes;
                    case 'MICROONDAS':
                        //print_r("description: ".$description."\n");
                        if (preg_match('/(\d{2})L\./', $name, $matches1) == 1) {
                            $attribute1['code'] = 'mo_litros';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('mo_litros', trim($matches1[1]));
                            array_push($attributes, $attribute1);
                        }
                        if (preg_match('/(\d{3,4})\s*W/', $name, $matches2) == 1) {
                            $attribute2['code'] = 'mo_potencia';
                            $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('mo_potencia', trim($matches2[1]));
                            array_push($attributes, $attribute2);
                        }
                        if (preg_match('/Cor: (\w+)/ui', strip_tags($description), $matches3) == 3) {
                            $attribute3['code'] = 'color';
                            $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches3[1]));
                            array_push($attributes, $attribute3);
                        }
                        if (preg_match('/Largura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches4) == 1) {
                            $attribute4['code'] = 'largura';
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('largura', $matches4[1]);
                            array_push($attributes, $attribute4);
                        }
                        if (preg_match('/Profundidade:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches5) == 1) {
                            $attribute5['code'] = 'profundidade';
                            $attribute5['value'] = $this->dataAttributeOptions->createOrGetId('profundidade', $matches5[1]);
                            array_push($attributes, $attribute5);
                        }
                        if (preg_match('/Altura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches6) == 1) {
                            $attribute6['code'] = 'altura';
                            $attribute6['value'] = $this->dataAttributeOptions->createOrGetId('altura', $matches6[1]);
                            array_push($attributes, $attribute6);
                        }
                        return $attributes;
                    case 'ESQUENTADORES/CALDEIRAS':
                        switch ($subfamilia) {
                            case  'ESQUENTADORES C/GÁS':
                                if (preg_match('/Capacidade:\s*(\d+)\s*Li/', $description, $matches) == 1) {
                                    $attribute1['code'] = 'esquentador_capacidade';
                                    $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('esquentador_capacidade', $matches[1]);
                                    array_push($attributes, $attribute1);
                                }
                                if (preg_match('/Ignição:\s*(\w+)/', $description, $matches) == 1) {
                                    $attribute1['code'] = 'esquentador_ignicao';
                                    $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('esquentador_ignicao', $matches[1]);
                                    array_push($attributes, $attribute1);
                                }
                                return $attributes;

                        }
                    case 'TERMOACUMULADORES':
                        if (preg_match('/Capacidade:\s*(\d+)\s*Li/', $description, $mCapacidade) == 1) {
                            $getCapacidade = $this->getLitragem($mCapacidade[1]);
                            $capacidade['code'] = 'termoacumulador_capacidade';
                            $capacidade['value'] = $this->dataAttributeOptions->createOrGetId('termoacumulador_capacidade', $getCapacidade);
                            array_push($attributes, $capacidade);
                        }
                        if (preg_match('/Potencia:\s*(\d+)\s*W/', $description, $mPotencia) == 1) {
                            $potencia['code'] = 'termoacumulador_potencia';
                            $potencia['value'] = $this->dataAttributeOptions->createOrGetId('termoacumulador_potencia', $mPotencia[1]);
                            array_push($attributes, $potencia);
                        }
                        return $attributes;
                    case 'IMAGEM E SOM':
                    case 'CLIMATIZAÇÃO':
                        switch ($familia) {
                            case 'AR CONDICIONADO':
                                switch ($subfamilia) {
                                    case 'AR COND.INVERTER':
                                    case 'AR COND.MULTI-SPLIT':
                                        //$attribute1['code'] = 'tipo_ac';
                                        if (preg_match('/UNID.INT/', $name) == 1) {
                                            if (preg_match('/Arrefecimento: (\d+)./', $description, $matches) == 1) {
                                                $potencia = $this->getPotencia((int)$matches[1]);
                                                if ($potencia != null) {
                                                    $attribute2['code'] = 'potencia_ac_int';
                                                    $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('potencia_ac_int',
                                                        $potencia);
                                                    array_push($attributes, $attribute2);
                                                }

                                            }
                                            return $attributes;
                                        } elseif (preg_match('/UNID.EXT/', $name) == 1) {
                                            if (preg_match('/Arrefecimento: (\d+)./', $description, $matches) == 1) {
                                                $potencia = $this->getPotencia((int)$matches[1]);
                                                if ($potencia != null) {
                                                    $attribute2['code'] = 'potencia_ac_ext';
                                                    $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('potencia_ac_ext',
                                                        $potencia);
                                                    array_push($attributes, $attribute2);
                                                }

                                            }
                                            return $attributes;
                                        } else {
                                            if (preg_match('/Arrefecimento: (\d+)./', $description, $matches) == 1) {
                                                $potencia = $this->getPotencia((int)$matches[1]);
                                                if ($potencia != null) {
                                                    $attribute2['code'] = 'potencia_ac_conj';
                                                    $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('potencia_ac_conj',
                                                        $potencia);
                                                    array_push($attributes, $attribute2);
                                                }

                                            }
                                            return $attributes;
                                        }
                                }
                        }
                }
        }
    }
    
    public function getPotencia($pot){
        return $pot;
    }
    public function getSpecialAttributesOrima($gama, $familia, $subfamilia, $description, $name){

    }
    private function getClasseEnergetica($text){
        if (preg_match('/A&#43 &#43  &#43/', $text) == 1){
            return 'A+++';
        }
        if (preg_match('/A&#43 &#43/', $text) == 1){
            return 'A++';
        }
        if (preg_match('/A&#43/', $text) == 1){
            return 'A+';
        }
        if (preg_match('/A/', $text) == 1){
            return 'A+';
        }
        if (preg_match('/(\w)/', $text, $matches) == 1){
            return $matches[1];
        }

    }

    private function getAltura($text){
        $altura = intval($text);
        if ($altura <= 10){
            return 'Até 10 cm';
        }
        if ($altura <= 20){
            return '10.1 a 20 cm';
        }
        if ($altura <= 30){
            return '20.1 a 30 cm';
        }
        if ($altura <= 40){
            return '30.1 a 40 cm';
        }
        if ($altura <= 50){
            return '40.1 a 50 cm';
        }
        if ($altura <= 60){
            return '50.1 a 60 cm';
        }
        if ($altura <= 70){
            return '70.1 a 80 cm';
        }
        if ($altura <= 80){
            return '70.1 a 80 cm';
        }
        if ($altura <= 90){
            return '80.1 a 90 cm';
        }
        if ($altura <= 100){
            return '90.1 a 100 cm';
        }
        if ($altura <= 110){
            return '100.1 a 110 cm';
        }
        if ($altura <= 120){
            return '110.1 a 120 cm';
        }
        if ($altura <= 130){
            return '120.1 a 130 cm';
        }
        if ($altura <= 140){
            return '130.1 a 140 cm';
        }
        if ($altura <= 150){
            return '140.1 a 150 cm';
        }
        if ($altura <= 160){
            return '150.1 a 160 cm';
        }
        if ($altura <= 170){
            return '160.1 a 170 cm';
        }
        if ($altura <= 180){
            return '170.1 a 180 cm';
        }
        if ($altura <= 190){
            return '180.1 a 190 cm';
        }
        if ($altura <= 200){
            return '190.1 a 200 cm';
        }
        else{
            return 'Superior a 200 cm';
        }
    }
    private function getProfundidade($text){
        $profundidade = intval($text);
        if ($profundidade <= 5){
            return 'Inferior a 5 cm';
        }
        if ($profundidade <= 10){
            return '5.1 a 10 cm';
        }
        if ($profundidade <= 15){
            return '10.1 a 15 cm';
        }
        if ($profundidade <= 20){
            return '15.1 a 20 cm';
        }
        if ($profundidade <= 25){
            return '20.1 a 25 cm';
        }
        if ($profundidade <= 30){
            return '25.1 a 30 cm';
        }
        if ($profundidade <= 35){
            return '30.1 a 35 cm';
        }
        if ($profundidade <= 40){
            return '35.1 a 40 cm';
        }
        if ($profundidade <= 45){
            return '40.1 a 45 cm';
        }
        if ($profundidade <= 50){
            return '45.1 a 50 cm';
        }
        if ($profundidade <= 55){
            return '50.1 a 55 cm';
        }
        if ($profundidade <= 60){
            return '55.1 a 60 cm';
        }
        if ($profundidade <= 65){
            return '60.1 a 65 cm';
        }
        if ($profundidade <= 70){
            return '65.1 a 70 cm';
        }
        else {
            return 'superior a 70 cm';
        }
    }

    private function getLargura($text)
    {
        $largura = intval($text);
        if ($largura <= 5) {
            return 'Inferior a 5 cm';
        }
        if ($largura <= 10) {
            return '5.1 a 10 cm';
        }
        if ($largura <= 15) {
            return '10.1 a 15 cm';
        }
        if ($largura <= 20) {
            return '15.1 a 20 cm';
        }
        if ($largura <= 25) {
            return '20.1 a 25 cm';
        }
        if ($largura <= 30) {
            return '25.1 a 30 cm';
        }
        if ($largura <= 35) {
            return '30.1 a 35 cm';
        }
        if ($largura <= 40) {
            return '35.1 a 40 cm';
        }
        if ($largura <= 45) {
            return '40.1 a 45 cm';
        }
        if ($largura <= 50) {
            return '45.1 a 50 cm';
        }
        if ($largura <= 55) {
            return '50.1 a 55 cm';
        }
        if ($largura <= 60) {
            return '55.1 a 60 cm';
        }
        if ($largura <= 65) {
            return '60.1 a 65 cm';
        }
        if ($largura <= 70) {
            return '65.1 a 70 cm';
        }
        if ($largura <= 75) {
            return '70.1 a 75 cm';
        }
        if ($largura <= 80) {
            return '75.1 a 80 cm';
        }
        if ($largura <= 85) {
            return '80.1 a 85 cm';
        }
        if ($largura <= 90) {
            return '85.1 a 90 cm';
        }
        else {
            return 'superior a 90 cm';
        }
    }

    private function getCor($text)
    {
        if (preg_match('/Branco/i', $text, $matches) == 1) {
            print_r("Branco");
            return 'Branco';
        }
        if (preg_match('/Inox/i', $text, $matches) == 1) {
            print_r("Inox");
            return 'Inox';

        }
        if (preg_match('/Silver/i', $text, $matches) == 1) {
            print_r("Silver");
            return 'Preto';

        }
        if (preg_match('/Preto/i', $text, $matches) == 1) {
            print_r("Preto");
            return 'Preto';

        }else {
            return 'Outras';
        }

    }

    private function getLitragem($text)
    {
        $litragem = intval($text);
        if ($litragem < 20){
            return 'Inferior a 20 Litros';
        }
        if ($litragem <= 50){
            return '20.1 a 50 Litros';
        }
        if ($litragem <= 100){
            return '50.1 a 100 Litros';
        }
        if ($litragem <= 150){
            return '100.1 a 150 Litros';
        }
        if ($litragem <= 200){
            return '150.1 a 200 Litros';
        }
        if ($litragem <= 250){
            return '200.1 a 250 Litros';
        }
        if ($litragem <= 300){
            return '250.1 a 300 Litros';
        }
        if ($litragem <= 350){
            return '300.1 a 350 Litros';
        }
        if ($litragem <= 400){
            return '350.1 a 400 Litros';
        }
        if ($litragem <= 450){
            return '400.1 a 450 Litros';
        }
        if ($litragem <= 500){
            return '450.1 a 500 Litros';
        }else {
            return 'Superior a 500 Litros';
        }
    }

    private function getExtracao($capExtracao)
    {
        if ( $capExtracao <= 400) {
            return 'Até 400 m3/h';
        }elseif ($capExtracao <= 500){
            return '400 a 500 m3/h';
        }elseif ($capExtracao <= 600){
            return '500 a 600 m3/h';
        }elseif ($capExtracao <= 700){
            return '600 a 700 m3/h';
        }elseif($capExtracao <=800){
            return '700 a 800 m3/h';
        }else{
            return 'Superior a 800 m3/h';
        }
    }

    private function getLarguraExaustor($largura)
    {
        if($largura <= 55){
            return 'Até 55 cm';
        }elseif ($largura <=65){
            return '55 a 65 cm';
        }elseif ($largura <= 75) {
            return '65 a 75 cm';
        }elseif ($largura <= 85) {
            return '75 a 85 cm';
        }elseif ($largura <= 95) {
            return '85 a 95 cm';
        }else{
            return 'Superior a 95 cm';
        }
    }


}
