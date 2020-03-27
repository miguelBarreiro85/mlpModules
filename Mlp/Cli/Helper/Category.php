<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 31-08-2018
 * Time: 10:21
 */

namespace Mlp\Cli\Helper;


class Category
{
    const ENCASTRE = 'ENCASTRE' ;
    const FRIGORIFICOS_COMBINADOS = 'FRIGORIFICOS/COMBINADOS';
    const MAQUINAS_LAVAR_ROUPA = 'MAQUINAS LAVAR ROUPA';
    const MAQUINAS_SECAR_ROUPA = 'MAQUINAS SECAR ROUPA' ;
    const PEQUENOS_DOMESTICOS = 'PEQUENOS DOMESTICOS' ;
    const CUIDADO_DE_ROUPA = 'CUIDADO DE ROUPA';
    const APARELHOS_DE_COZINHA = 'APARELHOS DE COZINHA';
    const ESPREMEDORES = 'ESPREMEDORES';
    const CONGELADORES = 'CONGELADORES';
    const CONGELADORES_VERTICAIS = 'VERTICAIS';
    const MOVEIS_SUPORTES = 'MÓVEIS / SUPORTES';
    const ACESSORIOS_IMAGEM_E_SOM = 'ACESSÓRIOS IMAGEM E SOM';
    const IMAGEM_E_SOM = 'IMAGEM E SOM';
    const FOGOES = 'FOGÕES';
    const FOGÕES_C_GÁS = 'FOGÕES C/GÁS';
    const FOGOES_ELECTRICOS = 'FOGÕES - ELÉCTRICOS';
    const FORNOS_DE_BANCADA = 'FORNOS DE BANCADA';
    const FORNOS = 'FORNOS';
    const PLACAS = 'PLACAS';
    const INDUSTRIAL = 'INDUSTRIAL';
    const GRELHADORES = 'GRELHADORES';
    const MQUINAS_COZINHA = 'MAQUINAS DE COZINHA';
    const AQUECEDORES_WC_PAREDE = 'AQUECEDORES WC PAREDE';
    const BRASEIRAS = 'BRASEIRAS';
    const ESCALFETAS = 'ESCALFETAS';
    const MICROONDAS = 'MICROONDAS';
    const MICROONDAS_ENCASTRE = 'MICROONDAS ENCASTRE';
    const ESQUENTADORES_CALDEIRAS = 'ESQUENTADORES/CALDEIRAS';
    const ESQUENTADORES_ELECTRICOS = 'ESQUENTADORES - ELÉCTRICOS';
    const ESQUENTADORES_C_GAS = 'ESQUENTADORES C/GÁS';
    const TERMOACUMULADORES = 'TERMOACUMULADORES';
    const TERMOACUMULADORES_ELECTRICOS = 'TERMOACUMULADORES - ELÉCTRICOS';
    const CLIMATIZACAO = 'CLIMATIZAÇÃO';
    const AQUECIMENTO = 'AQUECIMENTO';
    const TRATAMENTO_DE_AR = 'TRATAMENTO DE AR';
    const DESUMIDIFICADORES = 'DESUMIDIFICADORES';
    const AR_CONDICIONADO = 'AR CONDICIONADO';
    const AC_FIXO = 'FIXO';
    const AC_PORTATIL = 'AR COND.PORTATIL';
    const EXAUSTORES = 'EXAUSTORES';
    const COMBINADOS_ENCASTRE = 'COMBINADOS';
    const CONGELADORES_ENCASTRE = 'CONGELADORES VERTICAIS';
    const MAQUINAS_CAFE_ENCASTRE = 'MAQUINAS CAFE ENCASTRE';
    const FRIGORIFICOS_ENCASTRE = 'FRIGORIFICOS';
    const MAQUINAS_DE_LOUCA_ENCASTRE = 'MÁQUINAS DE LOIÇA';
    const MAQ_LAVAR_ROUPA_ENCASTRE = 'MAQ.LAVAR ROUPA';
    const FERROS_A_VAPOR = 'FERROS A VAPOR';
    const MAQ_LAVAR_SECAR_ROUPA_ENCASTRE = 'MAQ.LAVAR/SECAR ROUPA';
    const ACESSORIOS_ENCASTRE = 'ACESSORIOS ENCASTRE';
    const CONGELADORES_HORIZONTAIS = 'HORIZONTAIS';
    const FRIGORIF_AMERICANOS = 'FRIGORIF.AMERICANOS';
    const FRIGORIF_2P_NO_FROST = 'FRIGORIF.2P NO FROST';
    const FRIGORIF_2_PORTAS = 'FRIGORIF.2 PORTAS';
    const FRIGORIF_1_PORTA = 'FRIGORIF.1 PORTA';
    const COMBINADOS_NO_FROST = 'COMBINADOS NO FROST';
    const COMBINADOS_CONVENCIONAIS = 'COMB.CONVENCIONAIS';
    const FRIO_INDUSTRIAL = 'FRIO INDUSTRIAL';
    const ARREFECEDORES_HORIZONTAIS_INDUSTRIAIS = 'ARREFECEDORES HORIZONTAIS IND.';
    const CONGELADORES_HORIZONTAIS_INDUSTRIAIS = 'CONGELADORES HORIZONTAIS IND.';
    const CONGELADORES_ILHA_INDUSTRIAIS = 'CONGELADORES ILHA IND.';
    const ELECTROCUTORES_INSECTOS = 'ELECTROCUTORES DE INSECTOS IND.';
    const FOGOES_INDUSTRIAIS = 'FOGOES IND.';
    const EQUIPAMENTOS_COZINHA_INDUSTRIAIS = 'EQUIPAMENTOS COZINHA IND.';
    const VARINHAS_INDUSTRIAIS = 'VARINHAS INDUSTRIAIS';
    const ARREFECEDORES_VERTICAIS_INDUSTRIAIS = 'ARREFECEDORES VERTICAIS IND.';
    const CONGELADORES_VERTICAIS_INDUSTRIAIS = 'CONGELADORES VERTICAIS IND.';
    const MAQUINAS_DE_LOUCA = 'MAQUINAS LAVAR LOUÇA';
    const MLL_DE_45 = 'MLL DE 45 Cm';
    const MLL_DE_60 = 'MLL DE 60 Cm';
    const MLL_COMPACTAS = 'MLL COMPACTAS';
    const MAQUINAS_LAVAR_ROUPA_CARGA_FRONTAL = 'MLR CARGA FRONTAL';
    const MAQUINAS_LAVAR_ROUPA_CARGA_SUPERIOR = 'MLR CARGA SUPERIOR';
    const MAQUINAS_LAVAR_SECAR_ROUPA = 'MLR LAVAR E SECAR ROUPA';
    const MAQUINAS_SECAR_ROUPA_COND = 'MSR POR CONDENSAÇÃO';
    const MAQUINAS_SECAR_ROUPA_BC = 'MSR POR CONDENSAÇÃO BOMBA CALOR';
    const MAQUINAS_SECAR_ROUPA_VENT = 'MSR POR EXAUSTÃO';
    const ASSEIO_PESSOAL = 'ASSEIO PESSOAL';
    const APARADORES = 'APARADORES';
    const DEPILADORAS = 'DEPILADORAS';
    const ASPIRADOR_COM_SACO = 'ASPIRADOR COM SACO';
    const MINI_ASPIRADORES = 'MINI ASPIRADORES';
    const ASPIRADORES_ROBOT = 'ROBÔS ASPIRADORES';
    const ASPIRADOR_SEM_SACO = 'ASPIRADOR SEM SACO';
    const ASPIRADOR_VERTICAL = 'ASPIRADOR VERTICAL';
    const COFRES = 'COFRES';
    const APARELHOS_DE_LIMPEZA = 'APARELHOS DE LIMPEZA';
    const SACOS_ASPIRADOR = 'SACOS PARA ASPIRADOR';
    const ARTIGOS_DE_MENAGE = 'ARTIGOS DE MENAGE';
    const PEQ_APARELHOS_COZINHA = 'PEQ.APARELHOS COZINHA';
    const BATEDEIRAS = 'BATEDEIRAS';
    const CACAROLAS = 'CAÇAROLAS';
    const CAIXA_HERMETICA = 'CAIXA HERMETICA';
    const CENTRIFUGADORAS ='CENTRIFUGADORAS' ;
    const MAQUINAS_DE_COZINHA = 'MAQUINAS DE COZINHA';
    const ABRE_LATAS_FACAS = 'ABRE-LATAS E FACAS';
    const FIAMBREIRAS = 'FIAMBREIRAS';
    const FRIGIDEIRAS = 'FRIGIDEIRAS';
    const FRITADEIRAS = 'FRITADEIRAS';
    const LIQUIDIFICADORAS = 'LIQUIDIFICADORAS';
    const PANELAS_DE_PRESSAO = 'PANELAS DE PRESSÃO';
    const ROBOTS_DE_COZINHA = 'ROBOT DE COZINHA';
    const TACHOS = 'TACHOS';
    const TRENS_COZINHA = 'TRENS DE COZINHA';
    const VARINHAS_MAGICAS = 'VARINHAS MAGICAS';
    const BALANÇAS_DE_WC = 'BALANÇAS DE W.C.';
    const MODELADORES = 'MODELADORES';
    const SECADORES_DE_CABELO = 'SECADORES DE CABELO';
    const MO_COM_GRILL = 'MO - COM GRILL';
    const MO_SEM_GRILL = 'MO - SEM GRILL';
    const CAFETEIRAS = 'CAFETEIRAS';
    const JARROS_E_FERV_PURIF_ÁGUA = 'JARROS E FERV./PURIF. ÁGUA ';
    const MAQUINAS_CAFE = 'MAQ.CAFE EXPRESSO';


    private $storeManager;
    private $state;
    private $categoryFactory;
    private $categoryRepositoryInterface;
    private $categoryLinkManagement;

    const GRANDES_DOMESTICOS = 'GRANDES DOMÉSTICOS';


    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\App\State $state,
                                \Magento\Catalog\Model\CategoryFactory $categoryFactory,
                                \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepositoryInterface,
                                \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement )
    {
        $this->storeManager = $storeManager;
        $this->state = $state;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->categoryLinkManagement = $categoryLinkManagement;
    }

    public function createCategory($gama,$familia,$subfamlia = null,$categorias)
    {
        try{
            $gamaId = $categorias[$gama];
        }catch (\Exception $ex){
            $newGama = $this->categoryFactory->create();
            $newGama->setName($gama);
            $newGama->setParentId(2);
            $newGama->setIsActive(true);
            $newGama->setIsAnchor(false);
            $gamaId = $this->categoryRepositoryInterface->save($newGama)->getId();
        }
        try{
            $familiaId = $categorias[$familia];
        }catch (\Exception $ex){
            $newFamilia = $this->categoryFactory->create();
            $newFamilia->setName($familia);
            $newFamilia->setParentId($gamaId);
            $newFamilia->setIsActive(true);
            $newFamilia->setIsAnchor(false);
            $familiaId = $this->categoryRepositoryInterface->save($newFamilia)->getId();
        }
        //Se deu erro é porque este tem de ser adicionado
        if ($subfamlia != null){
            try{
                $newSubFamilia = $this->categoryFactory->create();
                $newSubFamilia->setName($subfamlia);
                $newSubFamilia->setParentId($familiaId);
                $newSubFamilia->setIsActive(true);
                $newSubFamilia->setIsAnchor(true);
                $subfamliaId = $this->categoryRepositoryInterface->save($newSubFamilia)->getId();
            } catch (\Exception $ex){
                print_r($ex->getMessage());
            }
        }
    }

    public function getCategoriesArray(){
        $categories = [];
        $categoriesCollection = $this->categoryFactory->create()->getCollection();
        $categoriesCollection->addFieldToSelect('*');
        foreach ($categoriesCollection as $cat){
            $categories[$cat->getName()] = $cat->getId();
        }
        return $categories;
    }


    public function setTelefacCategories($subFamilia,$sku,$logger){
        $categoryId = [];
        $subFamilia = $this->categoryFactory->create()->getCollection()->addAttributeToFilter('name',$subFamilia)->setPageSize(1);
        if ($subFamilia->getSize()) {
            array_push($categoryId,$subFamilia->getFirstItem()->getId());
        }
        try{
            $this->categoryLinkManagement->assignProductToCategories($sku,$categoryId);
        }catch (\Exception $exception){
            $logger->info("Category Exception: ".$sku);
        }
    }

    public function setGamaSorefoz($gama){
        switch ($gama){
            case 'TELEFONES E TELEMÓVEIS':
                return 'COMUNICAÇÕES';
                break;
            case 'SERVIÇOS TV/INTERNET/OUTROS':
                return 'COMUNICAÇÕES';
                break;
            default:
                return $gama;
        }
    }

    public function setFamiliaSorefoz($familia)
    {
        switch ($familia) {
            case 'ENCASTRE - FORNOS':
                return "ENCASTRE";
            case 'ENCASTRE - MESAS':
                return "ENCASTRE";
            case 'ENCASTRE - EXAUSTOR/EXTRATORES':
                return "ENCASTRE";
            case 'ENCASTRE - FRIO':
                return "ENCASTRE";
            case 'ENCASTRE - MAQ.LOUÇA':
                return "ENCASTRE";
            case 'ENCASTRE - MAQ.L.ROUPA':
                return "ENCASTRE";
            case 'ENCASTRE - MICROONDAS':
                return "ENCASTRE";
            case 'ENCASTRE - OUTRAS':
                return "ENCASTRE";
            default:
                return $familia;
        }
    }

    public function setSubFamiliaSorefoz($subFamilia){
            switch ($subFamilia){
                case 'INDEPENDENTES - ELÉCTRICOS':
                    return 'FORNOS';
                case 'PIROLITICOS':
                    return 'FORNOS';
                case 'INDEPENDENTES C/GÁS':
                    return 'FORNOS';
                case 'POLIVALENTES':
                    return 'FORNOS';

                case 'CONVENCIONAIS C/GÁS':
                    return 'PLACAS';
                case 'DE INDUÇÃO':
                    return 'PLACAS';
                case 'VITROCERÂMICAS C/GÁS':
                    return 'PLACAS';
                case 'DOMINÓS C/GÁS':
                    return 'PLACAS';
                case 'VITROCERÂMICAS - ELÉCTRICAS':
                    return 'PLACAS';
                case 'DOMINÓS - ELÉCTRICOS':
                    return 'PLACAS';
                case 'CONVENCIONAIS - ELÉCTRICAS':
                    return 'PLACAS';

                case 'EXAUST.DE CHAMINÉ':
                    return 'EXAUSTORES';
                case 'EXAUST.TELESCÓPICOS':
                    return 'EXAUSTORES';
                case 'EXAUST.CONVENCIONAIS':
                    return 'EXAUSTORES';
                case 'EXTRACTORES':
                    return 'EXAUSTORES';

                case 'MAQ.LAVAR LOUÇA 60 Cm':
                    return 'MÁQUINAS DE LOIÇA';
                case 'MAQ.LAVAR LOUÇA 45 Cm':
                    return 'MÁQUINAS DE LOIÇA';

                case 'TV LED 46"':
                    return 'TV LED+46"';
                case 'TV LED 27"':
                    return 'TV LED 28"';
                case 'TV LED 42"':
                    return 'TV LED+42"';
                case 'MICROONDAS':
                    return 'MICROONDAS ENCASTRE';
                case 'DE SECRETÁRIA':
                    return 'DESKTOPS';

                case 'AR COND.INVERTER':
                case 'AR COND.MULTI-SPLIT':
                    return 'FIXO';
                default:
                    return $subFamilia;
            }
        }
    public function setCategories($gama, $familia, $subfamilia, $name){
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
        $categories['gama'] = $this->setGamaSorefoz($gama);
        $categories['familia'] = $this->setFamiliaSorefoz($familia);
        $categories['subfamilia'] = $this->setSubFamiliaSorefoz($subfamilia);
        return $categories;
    }

    public  function setGamaOrima($gama){
        switch ($gama){
            case 'QUEIMA':
            case 'ENCASTRE':
            case 'FRIO':
            case 'AGUAS QUENTES':
            case 'MAQUINAS LOUÇA':
                return self::GRANDES_DOMESTICOS;

            case 'PEQUENOS DOMESTICOS':
                return 'PEQUENOS DOMÉSTICOS';

            case 'CLIMATIZAÇAO':
                return 'CLIMATIZAÇÃO';

            case 'SOM & IMAGEM':
                return 'IMAGEM E SOM';


        }
    }

    public function setFamiliaOrima($gama,$familia){
        switch ($familia){
            case 'FOGOES':
                return 'FOGÕES';
            case 'FORNOS':
                return 'ENCASTRE';
            case 'FRIGORIFICOS 2 PORTAS':
                return 'FRIGORIFICOS/COMBINADOS';
            case 'MAQUINAS LAVAR ROUPA':
                return 'MAQUINAS LAVAR ROUPA';
            case 'CONGELADORES HORIZONTAIS':
                return 'CONGELADORES':

        }
    }

    public function setCategoriesOrima($gama, $familia, $subFamilia){
        switch ($gama){
            case 'QUEIMA':
                $gama = self::GRANDES_DOMESTICOS;
                switch ($familia){
                    case ''
                }

            case 'MAQUINAS LAVAR ROUPA':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::MAQUINAS_LAVAR_ROUPA;
                $subFamilia = self::MAQUINAS_LAVAR_ROUPA_CARGA_FRONTAL;
                switch ($familia) {
                    case 'MAQUINAS LAVAR ROUPA':
                        switch ($subFamilia) {
                            case 'MAQUINAS LAVAR ROUPA CARGA SUPERIOR':
                                $subFamilia = self::MAQUINAS_LAVAR_ROUPA_CARGA_SUPERIOR;

                        }
                    case 'MAQUINAS LAVAR SECAR ROUPA':
                        switch ($subFamilia) {
                            case 'MAQUINAS LAVAR SECAR':
                                $subFamilia = self::MAQUINAS_LAVAR_SECAR_ROUPA;
                        }
                    case 'SECADORES ROUPA BOMBA CALOR':
                        $familia = self::MAQUINAS_SECAR_ROUPA;
                        $subFamilia = self::MAQUINAS_SECAR_ROUPA_BC;
                    case 'SECADORES ROUPA CONDENSAÇAO':
                        $familia = self::MAQUINAS_SECAR_ROUPA;
                        $subFamilia = self::MAQUINAS_SECAR_ROUPA_COND;
                    case 'SECADORES ROUPA VENTILAÇAO':
                        $familia = self::MAQUINAS_SECAR_ROUPA;
                        $subFamilia = self::MAQUINAS_SECAR_ROUPA_VENT;


                }

            case 'ENCASTRE':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::ENCASTRE;

                switch ($familia) {
                    case 'FRIGORIFICOS 1 PORTA ENCASTRE':
                    case 'FRIGORIFICOS 2 PORTAS ENCASTRE':
                        $subFamilia = self::FRIGORIFICOS_ENCASTRE;

                    case 'FORNOS':
                        $subFamilia = self::FORNOS;

                    case 'MAQUINAS LAVAR LOUÇA ENCASTRE':
                        $subFamilia = self::MAQUINAS_DE_LOUCA_ENCASTRE;

                    case 'MAQUINAS LAVAR ROUPA ENCASTRE':
                        $subFamilia = self::MAQ_LAVAR_ROUPA_ENCASTRE;

                    case 'MAQUINAS LAVAR SECAR ENCASTRE':
                        $subFamilia = self::MAQ_LAVAR_SECAR_ROUPA_ENCASTRE;

                    case 'MICRO ONDAS ENCASTRE':
                        $subFamilia = self::MICROONDAS_ENCASTRE;

                    case 'PLACAS A GAS':
                    case 'PLACAS CRISTAL GAS':
                    case 'PLACAS DOMINO':
                    case 'PLACAS DOMINO':
                    case 'PLACAS MISTAS':
                    case 'PLACAS VITROCERAMICAS':
                        $subFamilia = self::PLACAS;

                    case 'TAMPOS':
                        $subFamilia = self::ACESSORIOS_ENCASTRE;



                }


                switch ($subFamilia) {
                    case 'CHAMINES':
                        $subFamilia = self::EXAUSTORES;
                    case 'COMBINADOS ENCASTRE':
                        $subFamilia = self::COMBINADOS_ENCASTRE;
                    case 'CONGELADORES VERTICAIS ENCASTRE':
                        $subFamilia = self::CONGELADORES_ENCASTRE;
                    case 'MAQUINAS DE CAFE ENCASTRE':
                        $subFamilia = self::MAQUINAS_CAFE_ENCASTRE;




                }


            case 'FRIO':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::FRIGORIFICOS_COMBINADOS;
                switch ($familia){
                    case 'COMBINADOS':
                        $result = preg_match("NF", $subFamilia);
                        if($result == 1) {
                            $subFamilia = self::COMBINADOS_NO_FROST;
                        }elseif ($result == 0) {
                            $subFamilia = self::COMBINADOS_CONVENCIONAIS;
                        }else {
                            $subFamilia = '';
                        }
                    case 'FRIGORIFICOS 1 PORTA':

                        $subFamilia = self::FRIGORIF_1_PORTA;

                    case 'FRIGORIFICOS 2 PORTAS':

                        $result = preg_match("NF",$subFamilia);
                        if($result == 1) {
                            $subFamilia = self::FRIGORIF_2P_NO_FROST;
                        }elseif ($result == 0){
                            $subFamilia = self::FRIGORIF_2_PORTAS;
                        }else{
                            //ERRO
                            $subFamilia = '';
                        }

                    case 'FRIGORIFICOS SIDE BY SIDE':
                        $subFamilia = self::FRIGORIF_AMERICANOS;
                    case 'CONGELADORES HORIZONTAIS':
                        $familia = self::CONGELADORES;
                        $subFamilia = self::CONGELADORES_HORIZONTAIS;
                    case 'CONGELADORES VERTICAIS':
                        $familia = self::CONGELADORES;
                        $subFamilia = self::CONGELADORES_VERTICAIS;



                }


            case 'AGUAS QUENTES':
                $gama = self::GRANDES_DOMESTICOS;
                    switch ($familia) {

                        case 'TERMOACUMULADORES':
                            $familia = self::TERMOACUMULADORES;
                            switch ($subFamilia) {
                                case 'TERMOACUMULADORES':
                                case 'TERMOACUMULADORES HORIZONTAIS':
                                    $subFamilia = self::TERMOACUMULADORES_ELECTRICOS;
                            }

                        case 'ESQUENTADORES':
                            $familia = self::ESQUENTADORES_CALDEIRAS;
                            switch ($subFamilia) {
                                case 'ESQUENTADORES ELETRICOS':
                                    $subFamilia = self::ESQUENTADORES_ELECTRICOS;
                                case  'ESQUENTADORES ESTANQUES':
                                case 'ESQUENTADORES IGNIÇAO MANUAL':
                                case 'ESQUENTADORES INTELIGENTES':
                                case 'ESQUENTADORES VENTILADOS':
                                    $subFamilia = self::ESQUENTADORES_C_GAS;

                            }
                    }

            case 'MAQUINAS LOUÇA':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::MAQUINAS_DE_LOUCA;
                switch ($subFamilia) {
                    case 'MAQUINAS LAVAR LOUÇA 45CM':
                        $subFamilia = self::MLL_DE_45;
                    case 'MAQUINAS LAVAR LOUÇA BRANCAS':
                    case 'MAQUINAS LAVAR LOUÇA COMPACTAS':
                    case 'MAQUINAS LAVAR LOUÇA OUTRAS CORES':
                        $subFamilia = self::MLL_DE_60;
                    case 'MAQUINAS LAVAR LOUÇA COMPACTAS':
                        $subFamilia = self::MLL_COMPACTAS;
                }


            case 'PEQUENOS DOMESTICOS':
                $gama = self::PEQUENOS_DOMESTICOS;
                switch ($familia) {
                    case 'BELEZA & HIGIENE':
                        $familia = self::ASSEIO_PESSOAL;
                        switch ($subFamilia) {
                            case 'APARADORES DE BARBA E BIGODE':
                            case 'APARADORES DE CABELO':
                                $subFamilia = self::APARADORES;
                            case 'DEPILADORAS':
                                $subFamilia = self::DEPILADORAS;
                        }
                    case 'CASA':
                        $familia = self::APARELHOS_DE_LIMPEZA;
                        switch ($subFamilia) {
                            case 'ASPIRADORES COM SACO':
                                $subFamilia = self::ASPIRADOR_COM_SACO;
                            case 'ASPIRADORES MINI':
                                $subFamilia = self::MINI_ASPIRADORES;
                            case 'ASPIRADORES MISTOS':
                                $subFamilia = '';
                            case 'ASPIRADORES ROBOT':
                                $subFamilia = self::ASPIRADORES_ROBOT;
                            case 'ASPIRADORES SEM SACO':
                                $subFamilia = self::ASPIRADOR_SEM_SACO;
                            case 'ASPIRADORES VERTICAIS':
                                $subFamilia = self::ASPIRADOR_VERTICAL;
                            case 'COFRES':
                                $familia = self::COFRES;
                                $subFamilia = '';
                            case 'SACOS PARA ASPIRADOR':
                                $subFamilia = self::SACOS_ASPIRADOR;


                        }

                    case 'COZINHA':
                        $familia = self::APARELHOS_DE_COZINHA;
                        switch ($subFamilia) {
                            case 'ARTIGOS MANUAIS':
                                $familia = self::ARTIGOS_DE_MENAGE;
                                $subFamilia = self::PEQ_APARELHOS_COZINHA;
                            case 'BALANÇAS COZINHA':
                                $subFamilia = self::BALANÇAS_DE_COZINHA;
                            case 'BATEDEIRAS':
                                $subFamilia = self::BATEDEIRAS;
                            case 'CAÇAROLAS':
                                $familia = self::ARTIGOS_DE_MENAGE;
                                $subFamilia = self::CACAROLAS;
                            case 'CAIXA HERMETICA PLASTICO':
                            case 'CAIXA HERMETICA VIDRO':
                                $familia = self::ARTIGOS_DE_MENAGE;
                                $subFamilia = self::CAIXA_HERMETICA;
                            case 'CENTRIFUGADORAS':
                                $subFamilia = self::CENTRIFUGADORAS;
                            case 'COZEDURA A VAPOR':
                                $subFamilia = self::MAQUINAS_DE_COZINHA;
                            case 'ESPREMEDORES DE CITRINOS':
                                $subFamilia = self::ESPREMEDORES;
                            case 'FACAS ELECTRICAS':
                                $subFamilia = self::ABRE_LATAS_FACAS;
                            case 'FIAMBREIRAS':
                                $subFamilia = self::FIAMBREIRAS;
                            case 'FORNOS ELETRICOS DE BANCADA':
                                $subFamilia = self::FORNOS_DE_BANCADA;
                            case 'FRIGIDEIRAS':
                                $familia = self::ARTIGOS_DE_MENAGE;
                                $subFamilia = self::FRIGIDEIRAS;
                            case 'FRITADEIRAS':
                                $subFamilia = self::FRITADEIRAS;
                            case 'GRELHADORES':
                            case 'GRELHADORES DE PLACAS':
                                $subFamilia = self::GRELHADORES;
                            case 'LIQUIDIFICADORES':
                                $subFamilia = self::LIQUIDIFICADORAS;
                            case 'MAQUINAS DE MOER CARNE':
                                $subFamilia = self::MAQUINAS_DE_COZINHA;
                            case 'PANELAS DE PRESSAO':
                                $familia = self::ARTIGOS_DE_MENAGE;
                                $subFamilia = self::PANELAS_DE_PRESSAO;
                            case 'PICADORAS':
                                $subFamilia = self::MAQUINAS_DE_COZINHA;
                            case 'QUEIMADORES LEITE CREME':
                                $subFamilia = self::MAQUINAS_DE_COZINHA;
                            case 'ROBOTS DE COZINHA':
                                $subFamilia = self::ROBOTS_DE_COZINHA;
                            case 'SELADORES DE SACOS':
                                $subFamilia = self::MAQUINAS_DE_COZINHA;
                            case 'TACHOS':
                                $familia = self::ARTIGOS_DE_MENAGE;
                                $subFamilia = self::TACHOS;
                            case 'TREM DE COZINHA':
                                $familia = self::ARTIGOS_DE_MENAGE;
                                $subFamilia = self::TRENS_COZINHA;
                            case 'VARINHAS':
                                $subFamilia = self::VARINHAS_MAGICAS;



                        }

                    case 'CUIDADOS PESSOAIS':
                        $familia = self::ASSEIO_PESSOAL;
                        switch ($subFamilia) {
                            case 'BALANÇAS WC':
                                $subFamilia = self::BALANÇAS_DE_WC;
                            case 'MODELADORES DE CABELO':
                                $subFamilia = self::MODELADORES;
                            case 'SECADORES DE CABELO':
                                $subFamilia = self::SECADORES_DE_CABELO;
                        }

                    case 'MICRO ONDAS':
                        $gama = self::GRANDES_DOMESTICOS;
                        $familia = self::MICROONDAS;
                       switch ($subFamilia) {
                           case 'MICRO ONDAS C/GRILL 21 A 29L':
                           case 'MICRO ONDAS C/GRILL = > 30L':
                           case 'MICRO ONDAS C/GRILL ATÉ 20L':
                           case 'MICRO ONDAS C/GRILL ATÉ 20L':
                               $subFamilia = self::MO_COM_GRILL;
                           case 'MICRO ONDAS S/GRILL 21 A 29L':
                           case 'MICRO ONDAS S/GRILL ATÉ 20L':
                               $subFamilia = self::MO_SEM_GRILL;

                       }

                    case 'PEQUENO-ALMOÇO':
                        $familia = self::APARELHOS_DE_COZINHA;
                        switch ($subFamilia) {
                            case 'CAFETEIRAS':
                                $subFamilia = self::CAFETEIRAS;
                            case 'JARROS ELECTRICOS':
                                $subFamilia = self::JARROS_E_FERV_PURIF_ÁGUA;
                            case 'JARROS TERMICOS':
                                $familia = self::ARTIGOS_DE_MENAGE;
                                $subFamilia = self::PEQ_APARELHOS_COZINHA

                            case 'MAQUINAS DE CAFE':
                                $subFamilia = self::MAQUINAS_CAFE;
                            case 'MOINHOS DE CAFE':

                        }



                }
                return 'PEQUENOS DOMÉSTICOS';

            case 'CLIMATIZAÇAO':
                $gama = self::CLIMATIZACAO;

                switch ($familia) {
                    case 'AR CONDICIONADO':
                        $familia = self::AR_CONDICIONADO;
                        switch ($subFamilia) {
                            case 'AR CONDICIONADO UNIDADES EXTERIORES':
                            case 'AR CONDICIONADO UNIDADES INTERIORES':
                            case 'AR CONDICIONADO INVERTER':
                                $subFamilia = self::AC_FIXO;
                            case 'AR CONDICIONADO PORTATIL':
                                $subFamilia = self::AC_PORTATIL;
                        }

                }
                // GRANDE CONFUSAO NA ORIMA TEMOS DE IR PRIMEIRO A SUBFAMILIA
                switch ($subFamilia) {
                    case 'AQUECEDORES SILICAS':
                    case 'AQUECEDORES WC PAREDE':
                    case 'AQUECEDORES WC PAREDE':
                    case 'COLUNAS DE AR':
                    case 'CONVECTORES':
                    case 'EMISSORES TERMICOS':
                    case 'ESCALFETAS':
                    case 'IRRADIADORES A OLEO':
                    case 'IRRADIADORES DE MICA':
                    case 'TERMOVENTILADORES':
                    case 'TOALHEIRO':
                        $familia = self::AQUECIMENTO;

                    case 'FOGOES LENHA LINHA ESMALTADA/INOX':
                    case 'FOGOES LENHA TRADICIONAIS':
                    case 'IRRADIADORES DE MICA':
                    case 'SALAMANDRA A LENHA FUNDIÇÃO':
                    case 'SALAMANDRAS LENHA C/ VENTILAÇAO':
                    case 'SALAMANDRAS LENHA REDONDAS':
                    case 'SALAMANDRAS LENHA S/ VENTILAÇAO':
                    case 'SALAMANDRAS PELLETS AR QUENTE':

                    case 'VENTOINHAS MESA':
                    case: 'VENTOINHAS MESA':



                    case 'DESUMIDIFICADORES':
                        $familia = self::TRATAMENTO_DE_AR;
                        $subFamilia = self::DESUMIDIFICADORES;


                }

            case 'SOM & IMAGEM':
                return 'IMAGEM E SOM';

            case 'INDUSTRIAL':
                $gama = self::INDUSTRIAL;
                $familia = self::FRIO_INDUSTRIAL;
                switch ($subFamilia) {
                    case 'ARREFECEDORES HORIZONTAIS':
                        $subFamilia = self::ARREFECEDORES_HORIZONTAIS_INDUSTRIAIS;
                    case 'CONGELADORES HORIZONTAIS INDUSTRIAIS':
                        $subFamilia = self::CONGELADORES_HORIZONTAIS_INDUSTRIAIS;
                    case 'CONGELADORES ILHA':
                        $subFamilia = self::CONGELADORES_ILHA_INDUSTRIAIS;
                    case 'VITRINES CONGELADORAS':
                        $subFamilia = self::CONGELADORES_VERTICAIS_INDUSTRIAIS;
                    case 'VITRINES ARREFECEDORAS':
                    case 'VITRINES ARREFECEDORAS ENCASTRE':
                        $subFamilia = self::ARREFECEDORES_VERTICAIS_INDUSTRIAIS;
                    case 'ELECTROCUTORES DE INSETOS':
                        $familia = self::ELECTROCUTORES_INSECTOS;
                        $subFamilia = '';
                    case 'FOGOES GAMA INDUSTRIAL':
                    case 'TREMPES':
                        $familia = self::FOGOES_INDUSTRIAIS;
                        $subFamilia = '';
                    case 'VARINHAS GAMA HOTELEIRA':
                        $familia = self::EQUIPAMENTOS_COZINHA_INDUSTRIAIS;
                        $subFamilia = self::VARINHAS_INDUSTRIAIS;



                }


        }

        switch ($subFamilia){
            case 'FOGOES MONOBLOCO 60CM':
            case 'FOGOES MONOBLOCO 50/55CM':
                $Familia = 'FOGÕES';
                $gama = self::GRANDES_DOMESTICOS;

            case 'FORNOS ESTATICOS':
                $familia = self::ENCASTRE;
                $gama = self::GRANDES_DOMESTICOS;

            case 'FRIGORIFICOS 2P 140-149CM':
            case 'FRIGORIFICOS 2P 160-169CM':
            case 'FRIGORIFICOS 1P COOLER 140-149CM':
            case 'FRIGORIFICOS 1P COOLER 160-169CM':
            case 'FRIGORIFICOS 1P C/CONG <120CM':
            case 'FRIGORIFICOS MINI-BAR':
            case 'COMBINADOS NF 180-189CM':
            case 'COMBINADOS 170-179CM':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::FRIGORIFICOS_COMBINADOS;

            case 'CONGELADORES VERTICAIS 140-149CM':
            case 'CONGELADORES VERTICAIS 160-169CM':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::CONGELADORES;
                $subFamilia = self::CONGELADORES_VERTICAIS;

            case 'MAQUINAS LAVAR ROUPA 7KG':
            case 'MAQUINAS LAVAR ROUPA 8KG':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::MAQUINAS_LAVAR_ROUPA;

            case 'SECADORES ROUPA VENTILAÇAO 7KG':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::MAQUINAS_SECAR_ROUPA;

            case 'FERROS DE ENGOMAR A VAPOR':
                $gama = self::PEQUENOS_DOMESTICOS;
                $Familia = self::CUIDADO_DE_ROUPA;
                $subFamilia = self::FERROS_A_VAPOR;

            case 'ESPREMEDORES DE CITRINOS':
                $gama = self::PEQUENOS_DOMESTICOS;
                $familia = self::APARELHOS_DE_COZINHA;
                $subFamilia = self::ESPREMEDORES;

            case 'SUPORTES TV/LED/PLASMA':
                $gama = self::IMAGEM_E_SOM;
                $familia = self::ACESSORIOS_IMAGEM_E_SOM;
                $subFamilia = self::MOVEIS_SUPORTES;

            case 'FOGOES VITROCERAMICOS':
            case 'FOGOES PORTA GARRAFA':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::FOGOES;
                $subFamilia = self::FOGÕES_C_GÁS;

            case 'FOGOES VITROCERAMICOS':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::FOGOES;
                $subFamilia = self::FOGOES_ELECTRICOS;


            case 'FORNOS ELETRICOS DE BANCADA':
                $gama  = self::PEQUENOS_DOMESTICOS;
                $familia = self::APARELHOS_DE_COZINHA;
                $subFamilia = self::FORNOS_DE_BANCADA;

            case 'FORNOS MULTIFUNÇOES':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::ENCASTRE;
                $subFamilia = self::FORNOS;

            case 'PLACAS INDUÇAO 60CM':
            case 'PLACAS A GAS 70CM':
            case 'PLACAS A GAS 60CM':
            case 'PLACAS CRISTAL GAS 60CM':
                $gama = self::GRANDES_DOMESTICOS;
                $familia = self::ENCASTRE;
                $subFamilia = self::PLACAS;

            case 'VARINHAS GAMA HOTELEIRA':
                $gama = self::INDUSTRIAL;

            case 'GRELHADORES':
                $gama = self::PEQUENOS_DOMESTICOS;
                $familia = self::APARELHOS_DE_COZINHA;
                $subFamilia = self::GRELHADORES;

            case 'QUEIMADORES LEITE CREME':
                $gama = self::PEQUENOS_DOMESTICOS;
                $familia = self::APARELHOS_DE_COZINHA;
                $subFamilia = self::MQUINAS_COZINHA;

            case 'TORRADEIRAS':
                $gama = self::PEQUENOS_DOMESTICOS;
                $familia = self::APARELHOS_DE_COZINHA;
                $subFamilia = self::MQUINAS_COZINHA;

            case 'AQUECEDORES WC PAREDE':
                $subFamilia = self::AQUECEDORES_WC_PAREDE;

            case 'BRASEIRAS':
                $subFamilia = self::BRASEIRAS;

            case 'ESCALFETAS':
                $subFamilia = self::ESCALFETAS;

            case 'MICRO ONDAS C/GRILL 21 A 29L':
            case 'MICRO ONDAS S/GRILL ATÉ 20L':
                $subFamilia = self::MICROONDAS;

            case 'MICRO ONDAS ENCASTRE C/GRILL ATÉ 20L':
                $subFamilia = self::MICROONDAS_ENCASTRE;

            case 'MAQUINAS DE CAFE';

        }

    }
}