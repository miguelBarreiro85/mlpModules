<?php 

namespace Mlp\Cli\Helper\Auferma;

use Mlp\Cli\Helper\CategoriesConstants as Cat;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

class AufermaCategories {

    public static function getCategories($gama,$familia,$subFamilia,$logger,$sku)
    {
        switch ($gama) {
            case 'ACESSÓRIOS E BATERIAS':
                $gama = Cat::ELECTRICIDADE;
                switch ($familia) {
                    case 'ACESSÓRIOS E BATERIAS':
                        switch ($subFamilia) {
                            case 'ACESSÓRIOS':
                                $familia = Cat::OUTROS_ACESSORIOS;
                                $subFamilia = null;
                                $logger->info(Cat::VERIFICAR_CATEGORIAS.$sku);
                                return [$gama,$familia,$subFamilia];
                            case 'BATERIAS':
                                $familia = Cat::PILHAS_BATERIAS;
                                $subFamilia = Cat::BATERIAS;
                                $logger->info(Cat::VERIFICAR_CATEGORIAS.$sku);
                                return [$gama,$familia,$subFamilia];
                            case 'LAMPADAS':
                                $familia = Cat::ILUMINACAO;
                                $subFamilia = Cat::LAMPADAS;
                                return [$gama,$familia,$subFamilia];
                            case 'PROD. P/MAQ.ROUPA E LOUÇA':
                                $gama = Cat::GRANDES_DOMESTICOS;
                                $familia = Cat::ACESSORIOS_GRANDES_DOMESTICOS;
                                $subFamilia = null;
                                return [$gama,$familia,$subFamilia];
                            case 'PROD. P/QUEIMA':
                                $gama = Cat::GRANDES_DOMESTICOS;
                                $familia = Cat::ACESSORIOS_GRANDES_DOMESTICOS;
                                $subFamilia = null;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    default:
                        return [$gama,$familia,$subFamilia];
                }           
            case 'GRANDES DOMÉSTICOS':
                $gama = Cat::GRANDES_DOMESTICOS;
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
                        $familia = Cat::FRIO_ENC;
                        switch($subFamilia){
                            case 'COMBINADOS':
                                $subFamilia = Cat::COMBINADOS_ENC;
                                return [$gama,$familia,$subFamilia];
                            case 'CONGELADORES VERTICAIS':
                                $subFamilia = Cat::CONGELADORES_ENC;
                                return [$gama,$familia,$subFamilia];
                            case 'FRIGORIFICOS':
                                $subFamilia = Cat::FRIGORIFICOS_ENC;
                                return [$gama,$familia,$subFamilia];
                            case 'GARRAFEIRAS':
                                $subFamilia = Cat::GARRAFEIRAS_ENC;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];    
                        }
                    case 'ENCASTRE - MAQ.LOUÇA':
                        $gama = Cat::ENCASTRE;
                        $familia = Cat::MAQ_DE_LOUCA_ENC;
                        $subFamilia = null;
                        return [$gama,$familia,$subFamilia];
                    case 'ENCASTRE - MAQ.L.ROUPA':
                        $gama = Cat::ENCASTRE;
                        $familia = Cat::MAQ_ROUPA_ENC;
                        switch($subFamilia){
                            case 'MAQ.LAVAR/SECAR ROUPA':
                                $subFamilia = Cat::MAQ_LAVAR_SECAR_ROUPA_ENC;
                                return [$gama,$familia,$subFamilia];
                            case 'MAQ.LAVAR ROUPA':
                                $subFamilia = Cat::MAQ_LAVAR_ROUPA_ENC;
                                return [$gama,$familia,$subFamilia];
                            case 'MAQ.SECAR ROUPA':
                                $subFamilia = Cat::MAQ_SECAR_ROUPA_ENC;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    case 'ENCASTRE - MICROONDAS':
                        $gama = Cat::ENCASTRE;
                        $familia = Cat::MICROONDAS_ENC;
                        $subFamilia = null;
                        return [$gama,$familia,$subFamilia];
                    case 'ENCASTRE - OUTRAS':
                        $gama = Cat::ENCASTRE;
                        $subFamilia = Cat::OUTRO_ENC;
                        return [$gama,$familia,$subFamilia];
                    case 'MAQUINAS LAVAR ROUPA':
                        $familia = Cat::GRANDES_DOMESTICOS;
                        $familia = Cat::MAQ_ROUPA;
                        $subFamilia = Cat::MAQ_LAVAR_ROUPA;
                        return [$gama,$familia,$subFamilia];
                    case 'MAQUINAS SECAR ROUPA':
                        $gama = Cat::GRANDES_DOMESTICOS;
                        $familia = Cat::MAQ_ROUPA;
                        switch ($subFamilia) {
                            case 'MSR POR EXAUSTÃO':
                                $subFamilia = Cat::MAQ_SECAR_ROUPA_VENT;
                                return [$gama,$familia,$subFamilia];
                            case 'MSR POR CONDENSAÇÃO':
                                $subFamilia = Cat::MAQ_SECAR_ROUPA_COND;
                                return [$gama,$familia,$subFamilia];
                            case 'MSR POR CONDENSAÇÃO BOMBA CALOR':
                                $subFamilia = Cat::MAQ_SECAR_ROUPA_BC;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                            }         
                    case 'MAQUINAS LAVAR SECAR ROUPA':
                        $familia = Cat::MAQ_ROUPA;
                        $subFamilia = Cat::MAQ_LAVAR_SECAR_ROUPA;
                        return [$gama,$familia,$subFamilia];
                    case 'CONGELADORES':
                        $familia = Cat::FRIO;
                        switch ($subFamilia) {
                            case 'VERTICAIS':
                                $subFamilia = Cat::CONGELADORES_VERTICAIS;
                                return [$gama,$familia,$subFamilia];
                            case 'HORIZONTAIS':
                                $subFamilia = Cat::CONGELADORES_HORIZONTAIS;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    case 'FRIGORIFICOS/COMBINADOS':
                        $familia = Cat::FRIO;
                        switch ($subFamilia) {
                            case 'GARRAFEIRA':
                                $subFamilia = Cat::GARRAFEIRAS;
                                return [$gama,$familia,$subFamilia];
                                break;
                            case 'FRIGORIF.2 PORTAS':
                                $subFamilia = Cat::FRIGORIF_2_PORTAS;
                                return [$gama,$familia,$subFamilia];
                            case 'FRIGORIF.2P NO FROST':
                                $subFamilia = Cat::FRIGORIF_2P_NO_FROST;
                                return [$gama,$familia,$subFamilia];
                            case 'COMB.CONVENCIONAIS':
                                $subFamilia = Cat::COMBINADOS_CONVENCIONAIS;
                                return [$gama,$familia,$subFamilia];
                            case 'FRIGORIF.AMERICANOS':
                                $subFamilia = Cat::FRIGORIF_AMERICANOS;
                                return [$gama,$familia,$subFamilia];
                            case 'COMBINADOS NO FROST':
                                $subFamilia = Cat::COMBINADOS_NO_FROST;
                                return [$gama,$familia,$subFamilia];
                            case 'FRIGORIF.1 PORTA':
                                $subFamilia = Cat::FRIGORIF_1_PORTA;
                                return [$gama,$familia,$subFamilia];
                            case 'FRIGORIF.1P NO FROST':
                                $subFamilia = Cat::FRIGORIF_1_PORTA_NF;
                                return [$gama,$familia,$subFamilia];
                            case 'FRIGOBAR':
                                $subFamilia = Cat::FRIGOBAR;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    case 'MAQUINAS LAVAR LOUÇA':
                        $familia = Cat::MAQ_DE_LOUCA;
                        switch ($subFamilia) {
                            case 'MLL DE 60 Cm':
                                $subFamilia = Cat::MLL_DE_60;
                                return [$gama,$familia,$subFamilia];
                            case 'MLL DE 45 Cm':
                                $subFamilia = Cat::MLL_DE_45;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }    
                    case 'ENCASTRE - CONJUNTOS':
                        $gama = Cat::ENCASTRE;
                        $familia = Cat::CONJUNTOS_ENC;
                        $subFamilia = null;
                        return [$gama,$familia,$subFamilia];        
                    default:
                        return [$gama,$familia,$subFamilia];        
                }
            case 'IMAGEM E SOM':
                $gama = Cat::IMAGEM_E_SOM;
                switch ($familia) {
                    case 'CÂMARAS':
                        switch ($subFamilia) {
                            case 'VIDEO CARTÃO MEMÓRIA':
                                $subFamilia = Cat::OUTROS_ACESSORIOS_IMAGEM_SOM;
                                $familia = Cat::ACESSORIOS_IMAGEM_E_SOM;
                                return [$gama,$familia,$subFamilia];
                            case 'FOTOS DIGITAL COMPACTA':
                                $familia = Cat::CAMARAS_FOTOGRAFICAS;
                                $subFamilia = Cat::FOTOS_DIGITAL_COMPACTA;
                                return [$gama,$familia,$subFamilia];
                            case 'FOTOS DIGITAL REFLEX':
                            case 'FOTOS DIGITAL REFLEX':
                                $familia = Cat::CAMARAS_FOTOGRAFICAS;
                                $subFamilia = Cat::CAMARAS_REFLEX;
                                return [$gama,$familia,$subFamilia];
                            case 'VIDEO HDD':
                                $familia = Cat::CAMARAS_VIDEO;
                                $subFamilia = Cat::CAMARAS_VIDEO_HD;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }                
                    case 'TELEVISÃO':
                        $familia = Cat::TELEVISAO;
                        switch ($subFamilia) {
                            case 'TV LED+46"':
                                $subFamilia = Cat::TVS_GRANDES;
                                return [$gama,$familia,$subFamilia];
                            case 'TV LED 27"':
                            case 'TV LED 32"':
                            case 'TV LCD 19"':
                            case 'TV LED 19"':
                            case 'TV LED 23"':
                            case 'TV LED 24"':
                            case 'TV LED 20"':
                            case 'TV LED 22"':
                                $subFamilia = Cat::TVS_PEQUENAS;
                                return [$gama,$familia,$subFamilia];
                            case 'TV LED 40"':
                            case 'TV LED 42"':
                                $subFamilia = Cat::TVS_MEDIAS;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                            }
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
                        return [$gama,$familia,$subFamilia];
                }
                
            case 'CLIMATIZAÇÃO':
                switch ($familia) {
                    case 'AQUECIMENTO':
                        if (preg_match('/^CLIMATIZADOR/', $name) == 1) {
                            $gama = Cat::CLIMATIZACAO;
                            $familia = Cat::AR_CONDICIONADO;
                            $subFamilia = Cat::CLIMATIZADORES;
                            return [$gama,$familia,$subFamilia];
                        }
                    case 'AR CONDICIONADO':
                        switch ($subFamilia) {
                            case 'AR COND.INVERTER':
                            case 'AR COND.MULTI-SPLIT':
                                    $subFamilia = Cat::AC_FIXO;
                                    return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
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
        
            default:
                $logger->info(Cat::VERIFICAR_CATEGORIAS.$sku);
        
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
        $categories = $this->getCategories($gama,$familia,$subfamilia,$logger,$sku);
        //$categories['gama'] = $this -> setGamaSorefoz($gama);
        //$categories['familia'] = $this -> setFamiliaSorefoz($familia);
        //$categories['subfamilia'] = $this -> setSubFamiliaSorefoz($familia,$subfamilia);
        return $categories;
    }
}