<?php 

namespace Mlp\Cli\Helper\Sorefoz;

use Mlp\Cli\Helper\CategoriesConstants as Cat;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

class SorefozCategories {

    public function getCategoriesSorefoz($gama,$familia,$subFamilia,$logger,$sku)
    {
        switch ($gama) {
            case 'ACESSÓRIOS E BATERIAS':
                switch ($familia) {
                    case 'ACESSÓRIOS E BATERIAS':
                        switch ($subFamilia) {
                            case 'ACESSÓRIOS':
                                $gama = Cat::ELECTRICIDADE;
                                $familia = Cat::OUTROS_ACESSORIOS;
                                $subFamilia = null;
                                $logger->info(Cat::VERIFICAR_SUBFAMILIA.$sku);
                                return [$gama,$familia,$subFamilia];
                            case 'BATERIAS':
                                $gama = Cat::ELECTRICIDADE;
                                $familia = Cat::PILHAS_BATERIAS;
                                $subFamilia = Cat::BATERIAS;
                                $logger->info(Cat::VERIFICAR_SUBFAMILIA.$sku);
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    default:
                        return [$gama,$familia,$subFamilia];
                }
            case 'TELEFONES E TELEMÓVEIS':
            case 'SERVIÇOS TV/INTERNET/OUTROS':
                $gama = Cat::COMUNICACOES;
                return [$gama,$familia,$subFamilia];
            case 'GRANDES DOMÉSTICOS':
                switch ($familia) {
                    case 'ENCASTRE - FORNOS':
                        $gama = Cat::ENCASTRE;
                        switch ($subFamilia) {
                            case 'INDEPENDENTES - ELÉCTRICOS':
                            case 'PIROLITICOS':
                            case 'INDEPENDENTES C/GÁS':
                            case 'POLIVALENTES':
                                $familia = Cat::FORNOS;
                                $subfamilia = null;
                                return [$gama, $familia, $subfamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    case 'ENCASTRE - MESAS':
                        $gama = Cat::ENCASTRE;
                        switch ($subFamilia) {
                            case 'CONVENCIONAIS C/GÁS':
                            case 'DE INDUÇÃO':
                            case 'VITROCERÂMICAS C/GÁS':
                            case 'DOMINÓS C/GÁS':
                            case 'VITROCERÂMICAS - ELÉCTRICAS':
                            case 'DOMINÓS - ELÉCTRICOS':                                
                            case 'CONVENCIONAIS - ELÉCTRICAS':
                                $familia = Cat::PLACAS;
                                $subFamilia = null;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    case 'ENCASTRE - EXAUSTOR/EXTRATORES':
                        $gama = Cat::ENCASTRE;
                        switch($subFamilia){
                            case 'EXAUST.DE CHAMINÉ':
                            case 'EXAUST.TELESCÓPICOS':
                            case 'EXAUST.CONVENCIONAIS':
                            case 'EXTRACTORES':
                                $familia = Cat::EXAUSTORES;
                                $subFamilia = null;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    case 'ENCASTRE - FRIO':
                        $gama = Cat::ENCASTRE;
                        $familia = Cat::FRIO_ENCASTRE;
                        switch($subFamilia){
                            case 'COMBINADOS':
                                $subFamilia = Cat::COMBINADOS_ENCASTRE;
                                return [$gama,$familia,$subFamilia];
                            case 'CONGELADORES VERTICAIS':
                                $subFamilia = Cat::CONGELADORES_ENCASTRE;
                                return [$gama,$familia,$subFamilia];
                            case 'FRIGORIFICOS':
                                $subFamilia = Cat::FRIGORIFICOS_ENCASTRE;
                                return [$gama,$familia,$subFamilia];
                            case 'GARRAFEIRAS':
                                $subFamilia = Cat::GARRAFEIRAS_ENCASTRE;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];    
                        }
                    case 'ENCASTRE - MAQ.LOUÇA':
                        $gama = Cat::ENCASTRE;
                        $familia = Cat::MAQUINAS_DE_LOUCA_ENCASTRE;
                        $subFamilia = null;
                        return [$gama,$familia,$subFamilia];
                    case 'ENCASTRE - MAQ.L.ROUPA':
                        $gama = Cat::ENCASTRE;
                        $familia = Cat::MAQ_ROUPA_ENCASTRE;
                        switch($subFamilia){
                            case 'MAQ.LAVAR/SECAR ROUPA':
                                $subFamilia = Cat::MAQ_LAVAR_SECAR_ROUPA_ENCASTRE;
                                return [$gama,$familia,$subFamilia];
                            case 'MAQ.LAVAR ROUPA':
                                $subFamilia = Cat::MAQ_LAVAR_ROUPA_ENCASTRE;
                                return [$gama,$familia,$subFamilia];
                            case 'MAQ.SECAR ROUPA':
                                $subFamilia = Cat::MAQ_SECAR_ROUPA_ENCASTRE;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    case 'ENCASTRE - MICROONDAS':
                        $gama = Cat::ENCASTRE;
                        $familia = Cat::MICROONDAS_ENCASTRE;
                        $subFamilia = null;
                        return [$gama,$familia,$subFamilia];
                    case 'ENCASTRE - OUTRAS':
                        $gama = Cat::ENCASTRE;
                        $subFamilia = Cat::OUTRO_ENCASTRE;
                        return [$gama,$familia,$subFamilia];
                    case 'MAQUINAS LAVAR ROUPA':
                        $familia 
                    default:
                        return [$gama,$familia,$subFamilia];
                }        
            case 'IMAGEM E SOM':
                switch ($subFamilia) {
                    case 'TV LED 46"':
                        $subFamilia = Cat::TV_LED_M46;
                        return [$gama,$familia,$subFamilia];
                    case 'TV LED 27"':
                        $subFamilia = Cat::TV_LED_28;
                        return [$gama,$familia,$subFamilia];
                    case 'TV LED 42"':
                        $subFamilia = Cat::TV_LED_M42;
                        return [$gama,$familia,$subFamilia];
                    default:
                        return [$gama,$familia,$subFamilia];
                    
                }
            case 'INFORMÁTICA':
                $gama = Cat::INFORMATICA;
                switch ($familia) {
                    case 'ACESSÓRIOS':
                        $familia = Cat::ACESSORIOS_INFORMATICA;    
                        return [$gama,$familia,$subFamilia];
                    case "COMPUTADORES E TABLET'S ":
                        switch ($subFamilia) {
                            case 'DE SECRETÁRIA':
                                $subFamilia = Cat::DESKTOPS;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    default:
                        # code...
                        break;
                }
                
            case 'CLIMATIZAÇÃO':
                switch ($subFamilia) {
                    case 'AR COND.INVERTER':
                    case 'AR COND.MULTI-SPLIT':
                         $subFamilia = Cat::AC_FIXO;
                         return [$gama,$familia,$subFamilia];
                    default:
                        return [$gama,$familia,$subFamilia];
                }
            case 'PEQUENOS DOMÉSTICOS':
                switch ($familia) {
                    case 'APARELHOS DE COZINHA':
                        switch ($subFamilia) {
                            case 'FORNOS':
                                $subFamilia = Cat::FORNOS_DE_BANCADA;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    default:
                        return [$gama,$familia,$subFamilia];
                }
            case 'CAR AUDIO':
                switch ($familia) {
                    case 'AUTO-RADIOS':
                        $gama = Cat::IMAGEM_E_SOM;
                        $familia = Cat::CAR_AUDIO;
                        $subFamilia = Cat::AUTO_RADIOS;
                        Return [$gama,$familia,$subFamilia];
                    case 'COLUNAS':
                        $gama = Cat::IMAGEM_E_SOM;
                        $familia = Cat::CAR_AUDIO;
                        $subFamilia = Cat::COLUNAS_AUTO;
                        Return [$gama,$familia,$subFamilia];
                    default:
                        # code...
                        break;
                }
            default:
                return [$gama,$familia,$subFamilia];
        }
    }

    public function setCategories($gama, $familia, $subfamilia, $name,$logger,$sku)
    {
        $categories = [];
        //Especifico para alguns artigos que tem categorias totalmente diferentes
        //Informatica Acessorios Acessorios de som
        if (preg_match('/^COLUNA/', $name) == 1) {
            $categories['gama'] = 'IMAGEM E SOM';
            $categories['familia'] = 'COLUNAS';
            $categories['subfamilia'] = null;
            return $categories;
        }
        if (preg_match('/^AUSC/', $name) == 1) {
            $categories['gama'] = 'IMAGEM E SOM';
            $categories['familia'] = 'AUSCULTADORES';
            $categories['subfamilia'] = null;
            return $categories;
        }
        if (preg_match('/^CLIMATIZADOR/', $name) == 1) {
            $categories['gama'] = 'CLIMATIZAÇÃO';
            $categories['familia'] = 'AR CONDICIONADO';
            $categories['subfamilia'] = 'CLIMATIZADORES';
            return $categories;
        }
        $categories = $this->getCategoriesSorefoz($gama,$familia,$subfamilia,$logger,$sku);
        //$categories['gama'] = $this -> setGamaSorefoz($gama);
        //$categories['familia'] = $this -> setFamiliaSorefoz($familia);
        //$categories['subfamilia'] = $this -> setSubFamiliaSorefoz($familia,$subfamilia);
        return $categories;
    }
}