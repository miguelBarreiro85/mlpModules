<?php


namespace Mlp\Cli\Helper;

class CategoriesConstants {
    const WARN_DIDNT_FOUND_PRODUCTS = "WARNING : DID NOT FOUND PRODUCT : ";
    const WARN_DELETING_PRODUCT = "WARNING : DELETING PRODUCT : ";
    const WARN_PRODUCT_ADDED = "WARNING : PRODUCT ADDED : ";
    const WARN_OLD_PRODUCT = "WARNING : OLD PRODUCT : ";
    const WARN_FOUND_PRODUCT_SKU = "WARNING : FOUND PRODUCT SKU : ";
    const WARN_DISABLING_PRODUCT = "WARNING : DISABLING PRODUCT : ";

    
    const ERROR_DISABLING_PRODUCT  = "ERROR : DISABLING PRODUCT : ";
    const ERROR_OPEN_FILE = "ERROR : OPEN FILE : ";
    const ERROR_RENAMING_CSV = "ERRO : RENAMING CSV : ";
    const ERROR_DOWNLOAD_CSV="ERRO : DOWNLOAD CSV : ";
    const ERROR_SET_STOCK_ZERO_TO_REMOVE = "ERRO : SET STOCK TO ZERO TO REMOVE PRODUCT : ";
    const ERROR_SET_PRODUCT_DATA = "ERRO : SET PRODUCT DATA :";
    const ERROR_UPDATE_STOCK = "ERRO : UPDATE SOURCE ITEM : ";
    const ERROR_UPDATE_PRICE = "ERRO : UPDATE PRICE :";
    const ERROR_WRONG_SKU = "ERRO : WRONG SKU : ";
    const ERROR_PRICE_ZERO = "ERRO : PRODUTO COM PRECO A 0 : ";
    const ERROR_GET_CATEGORIAS = "ERRO : GET CATEGORIES : ";
    const ERROR_VERIFICAR_MANUFACTURER = "ERRO : VERIFICAR MANUFACTURER : ";
    const ERROR_SAVE_PRODUCT = "ERRO : SAVE PRODUCT : ";
    const ERROR_ADD_PRODUCT_OPTIONS = "ERRO : ADD PRODUCT OPTIONS : ";
    const ERROR_ADD_EAN_TO_OLD_EANFILE = "ERRO : ADD EAN TO OLD EAN FILE : ";


    const CLIMATIZADORES = "CLIMATIZADORES";
    const ACESSORIOS_GRANDES_DOMESTICOS = "ACESSÓRIOS GRANDES DOMÉSTICOS";
    const ACESSORIOS_QUEIMA = 'ACESSÓRIOS QUEIMA';
    const CONJUNTOS_ENC = 'PLACA E FORNO';
    const SERVICOS_COMUNICACOES = 'SERVIÇOS DE COMUNICAÇÕES';
    const TVS_GRANDES = 'TVS GRANDES > 46"';
    const TVS_PEQUENAS = 'TVS PEQUENAS ATÉ 40"';
    const TVS_MEDIAS = 'TVS MEDIAS 40" A 46"';
    const ACESSORIOS_LEITORES_GRAVADORES = "ACESSORIOS LEITORES GRAVADORES";
    const FRIGORIF_1_PORTA_NF = "FRIGORIF.1P NO FROST";
    
    const ACESSORIOS_POS = "ACESSORIOS POS";
    const NOT_FUND = " :NOT FOUND";
    const PENS_USB = "PENS USB";
    const MICROFONES = "MICROFONES";
    const TAPETES_RATOS = "TAPETES RATOS";
    const TECLADOS = "TECLADOS";
    const APRESENTADORES = "APRESENTADORES";
    const HOSPITALITY_TV = "HOSPITALITY TV";
    const HUBS_USB_LEITORES_CARTOES = "HUBS USB LEITORES CARTÕES";
    const SCANNERS = "SCANNERS";
    const IMPRESSORAS_GRANDE_FORMATO = "IMPRESSORAS GRANDE FORMATO";
    const IMPRESSORAS_TERMICAS = "IMPRESSORAS TERMICAS";
    const IMPRESSORAS_SIMPLES = "IMPRESSORAS SIMPLES";
    const IMPRESSORAS_MULTI_FUNC = "IMPRESSORAS MULTIFUNÇÕES";
    const CONSUMIVEIS_IMPRESSORAS = "CONSUMIVEIS IMPRESSORAS";
    const SEGURANCA = "SEGURANÇA";
    const VIDEOVIGILANCIA = "VIDEOVIGILANCIA";
    const ACESSORIOS_SMARTPHONES = "ACESSORIOS SMARTPHONE";
    const DISCOS_SSD = "DISCOS SSD";
    const DISCOS_HDD = "DISCO HDD";
    const SMART_HOME = "SMART HOME";
    const INTERRUPTORES_SMART = "INTERRUPTORES SMART";
    const CAMPAINHAS_SMART = "CAMPAINHAS SMART";
    const DISCOS_EXTERNOS = "DISCOS EXTERNOS";
    const CAMARAS_IP = "CAMARAS IP";
    const OUTRO_ENC = "OUTROS EQUIPAMENTOS ENC";
    const GARRAFEIRAS_ENC = "GARRAFEIRAS ENC";
    const MAQ_ROUPA_ENC = "MAQ DE ROUPA ENC";
    const OUTROS_ACESSORIOS = "OUTROS ACESSÓRIOS";
    const IMAGEM_E_SOM = "IMAGEM E SOM";
    
        const TELEVISAO =  "TELEVISÃO";
            const ACESSORIOS_TV = 'ACESSORIOS TV';
            const CABOS_TV = 'CABOS TV';
            const TV_LED_50_60 = 'TV LED 50 A 60"';
            const TV_LED_M65 = 'TV LED +65"' ;
            const TV_HOTELARIA = 'TV HOTELARIA';
        
        const PROJECTORES = 'PROJECTORES VIDEO';
            const PROJECTORES_MESA = 'PROJECTORES MESA';
            const PROJECTORES_PORTATEIS = 'PROJECTORES PORTATEIS';

        const SIST_HOME_CINEMA = 'SIST.HOME CINEMA';
            const RECEPTORES_AV = 'RECEPTORES AV';
            const ACESSORIOS_PROJECTORES = 'ACESSORIOS PROJECTORES';
            const LEITOR_DVD = 'LEITOR DE DVD';
            const KIT_COLUNAS = 'KIT COLUNAS';
            const SOUND_BARS = 'SOUND BAR';
            const OUTRO_HIFI = 'OUTRO HI-FI';
            const AMPLIFICADORES_HIFI = 'AMPLIFICADORES HIFI';
        
        //COLUNAS
        const COLUNAS = 'COLUNAS';
        const COLUNAS_AUTO = "COLUNAS AUTO";
        //CAR AUDIO
            const CAR_AUDIO = 'CAR AUDIO';
            const AUTO_RADIOS = 'AUTO RADIOS';
            const CAR_KITS = 'CAR KITS';

        //CAMARAS DE VIDEO
            const ACESSORIOS_CAM_VIDEO = 'ACESSORIOS DE VIDEO';
            const CAMARAS_VIDEO_STANDARD = 'CAMARAS VIDEO STANDARD';

        const AUDIO_PORTATIL = 'AUDIO PORTATIL';
            const RADIOS_PORTATEIS = 'RADIOS PORTATEIS';
            const RADIO_CDS = 'RADIOS C/CD';
            
        

        const EQUIPAMENTOS_AUDIO = 'EQUIPAMENTOS AUDIO';
            const BARRAS_SOM = 'BARRAS DE SOM';
            const APARELHAGENS_MICROS = 'APARELHAGENS MICROS';
            const AUSCULTADORES = 'AUSCULTADORES';
        
        const DVD_BLURAY_TDT = 'DVD /BLURAY /TDT';
    
    
    const ELECTRICIDADE = 'ELECTRICIDADE E ACESSÓRIOS';

        const PILHAS_BATERIAS = 'PILHAS E BATERIAS';
            const PILHAS = 'PILHAS';
            const PILHAS_COMANDO = 'PILHAS COMANDO';
            const BATERIAS = 'BATERIAS';
            const PILHAS_RECARREGAVEIS = 'PILHAS RECARREGAVEIS';
            
            
        const FICHAS_TOMADAS = 'FICHAS E TOMADAS';
            const TOMADAS = 'TOMADAS';
            
        const EXTENSOES_CABOS = 'EXTENSÕES E CABOS';
            const EXTENSOES_MULTIPLAS = 'EXTENSÕES MULTIPLAS';
        
        CONST DIVERSOS_ILUMINACAO = 'DIVERSOS ILUMINAÇÃO';
        
        const ACESSORIOS_ILUMINACAO = 'ACESSÓRIOS ILUMINAÇÃO';


    //COMUNICACOES
        const SMARTWATCHES = 'SMARTWATCHES';
    
    
    
    
    
    

    const TEXTIL = 'TEXTIL';
    
   
    

    
    
    
    const GRANDES_DOMESTICOS = 'GRANDES DOMÉSTICOS';
        //MAQ LOUCA
            const ACESSORIOS_MLL = 'ACESSÓRIOS MAQ LOUÇA';
        
        //ENC
            const FRIO_ENC = 'FRIO ENC';
        
        //FOGOES
        const FOGOES_GAS = 'FOGÕES C/GÁS';
        const MAQ_SECAR_ROUPA_ENC = 'MAQ SECAR ROUPA ENC';
        const ACESSORIOS_FRIO = 'ACESSÓRIOS FRIO';
        const MAQ_ROUPA = 'MAQ DE ROUPA';
        const GARRAFEIRAS = 'GARRAFEIRAS';
    
    //INFORMATICA
        const COMPUTADORES_E_TABLETS = "COMPUTADORES E TABLET'S";
            const TABLETS = "TABLET'S";
            const PORTATEIS_NOTEBOOKS = 'PORTÁTEIS';
            const ACESSORIOS_TABLETS = 'ACESSÓRIOS TABLETS';
            const ACESSORIOS_NOTEBOOKS = 'ACESSÓRIOS PORTATEIS';
        const MONITORES = 'MONITORES';
            const MONITORES_PC = 'MONITORES PC';
            const MONITORES_PC_CURVO = 'MONITORES CURVOS';
            const MONITORES_TACTEIS = 'MONITORES TACTEIS';
            const MONITORES_C_TV = 'MONITORES COM TV';
        
        
        const IMPRESSORAS = 'IMPRESSORAS';
            const IMPRESSORAS_FOTOS = 'IMPRESSORAS DE FOTOS';
            const ACESSORIOS_IMPRESSORAS = 'ACESSÓRIOS IMPRESSORAS';
            const IMPRESSORAS_JACTO_DE_TINTA = 'MULTIFUNÇÕES J.TINTA';
        
        const ACESSORIOS_INFORMATICA = 'ACESSÓRIOS INFORMATICA';
            const UPS = 'UPS';
            const WEB_CAMS = 'WEB CAMS';
            const LEITOR_CARTOES = 'LEITOR DE CARTÕES';
            const MALAS_BOLSAS_INFORMATICA = 'MALAS E BOLSAS';
            const OUTROS_ACESSORIOS_INFORMATICA = 'OUTROS ACESSÓRIOS INFORMÁTICA';
            
        const REDES_CABOS = 'REDES E CABOS';
        
        const INFORMATICA_SOFTWARE = 'SOFTWARE';
            const INFORMATICA_SOFTWARE_POS = 'SOFTWARE POS';
            const INFORMATICA_SOFTWARE_SEGURANCA = 'SOFTWARE SEGURANÇA';
        
        const GAMING = 'GAMING';
            const ACESSORIOS_PLAYSTATION = 'ACESSÓRIOS PLAYSTATION';
            const CONSOLAS_PLAYSTATION = 'CONSOLAS PLAYSTATION';
            const JOGOS_PLAYSTATION = 'JOGOS PLAYSTATION';
            const ACESSORIOS_PC_GAMING = 'ACESSÓRIOS PC GAMING';
        
        //MEMORIAS
            const CARTOES_MEMORIA = 'CARTÕES DE MEMÓRIA';
    const CAMARAS_VIGILANCIA = 'CAMARAS DE VIGILANCIA';
    const BOLSAS_PROTECCOES = 'BOLSAS E PROTECÇÕES';
    const TELEMOVEIS = 'TELEMÓVEIS';
    const TELEMOVEIS_CARTOES = 'TELEMÓVEIS / CARTÕES';
    const AURICULARES = 'AURICULARES';
    const OUTROS_ACESSORIOS_COMUNICACOES = 'OUTROS ACESSÓRIOS COMUNICAÇÕES';
    const COMUNICACOES_FIXAS = 'COMUNICAÇÕES FIXAS';
    const TELEFONES_FIXOS = 'TELEFONES DOMÉSTICOS';
    const VERIFICAR_CATEGORIAS = 'VERIFICAR CATEGORIAS: ';   
    const CAMARAS_FOTOGRAFICAS = 'CAMARAS FOTOGRAFICAS';
    const ACESSORIOS_CAMARAS_FOTOGRAFICAS = 'ACESSÓRIOS FOTOGRAFIA';
    const OBJECTIVAS_CAMARAS = 'OBJECTIVAS CAMARAS';
    const DRONES = 'DRONES';
    const BOLSAS = 'BOLSAS';
    const CAMARAS_REFLEX = 'CAMARAS REFLEX';
    const CAMARAS_VIDEO = 'CAMARAS VIDEO';
    const CAMARAS_VIDEO_HD = 'CAMARAS VIDEO HD';
    const OUTROS_ACESSORIOS_IMAGEM_SOM = 'OUTROS ACESSÓRIOS IMAGEM E SOM';
    const INFORMATICA = 'INFORMÁTICA';
    const MEMORIAS = 'MEMÓRIAS';
    const LAMPADAS = 'LAMPADAS';
    const CAMARAS = 'CÂMARAS';
    const LANTERNAS = 'LANTERNAS';
    const ILUMINACAO = 'ILUMINAÇÃO';
    const ILUMINACAO_BATERIAS = 'ILUMINAÇÃO E BATERIAS';
    const ACESSORIOS_COMUNICACOES = 'ACESSÓRIOS DE COMUNICAÇÕES';
    const ALIMENTACAO_COMUNICACOES = 'ALIMENTAÇÃO COMUNICAÇÕES';
    const FOTOS_DIGITAL_COMPACTA = 'FOTOS DIGITAL COMPACTA';
    const DESKTOPS =  'DESKTOPS';
    const TV_LED_28 = 'TV LED 28"';
    const TV_LED_M42 = 'TV LED+42"';
    const TV_LED_M46 = 'TV LED+46"';
    const COMUNICACOES = 'COMUNICAÇÕES';
    const ENCASTRE = 'ENCASTRE';
    const FRIGORIFICOS_COMBINADOS = 'FRIGORIFICOS/COMBINADOS';
    const MAQ_LAVAR_ROUPA = 'MAQ LAVAR ROUPA';
    const MAQ_SECAR_ROUPA = 'MAQ SECAR ROUPA';
    const PEQUENOS_DOMESTICOS = 'PEQUENOS DOMÉSTICOS';
    const CUIDADO_DE_ROUPA = 'CUIDADO DE ROUPA';
    const APARELHOS_DE_COZINHA = 'APARELHOS DE COZINHA';
    const ESPREMEDORES = 'ESPREMEDORES';
    const CONGELADORES = 'CONGELADORES';
    const CONGELADORES_VERTICAIS = 'CONGELADORES VERTICAIS';
    const MOVEIS_SUPORTES = 'MÓVEIS / SUPORTES';
    const ACESSORIOS_IMAGEM_E_SOM = 'ACESSÓRIOS IMAGEM E SOM';   
    const FOGOES = 'FOGÕES';
    const FOGÕES_C_GÁS = 'FOGÕES C/GÁS';
    const FOGOES_ELECTRICOS = 'FOGÕES - ELÉCTRICOS';
    const FORNOS_DE_BANCADA = 'FORNOS DE BANCADA';
    const FORNOS = 'FORNOS';
    const PLACAS = 'PLACAS';
    const INDUSTRIAL = 'INDUSTRIAL';
    const GRELHADORES = 'GRELHADORES';
    const MQUINAS_COZINHA = 'MAQ DE COZINHA';
    const AQUECEDORES_WC_PAREDE = 'AQUECEDORES WC PAREDE';
    const BRASEIRAS = 'BRASEIRAS';
    const ESCALFETAS = 'ESCALFETAS';
    const MICROONDAS = 'MICROONDAS';
    const MICROONDAS_ENC = 'MICROONDAS ENC';
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
    const COMBINADOS_ENC = 'COMBINADOS ENC';
    const CONGELADORES_ENC = 'CONGELADORES VERTICAIS ENC';
    const MAQ_CAFE_ENC = 'MAQ CAFE ENC';
    const FRIGORIFICOS_ENC = 'FRIGORIFICOS ENC';
    const MAQ_DE_LOUCA_ENC = 'MAQ DE LOUÇA ENC';
    const MAQ_LAVAR_ROUPA_ENC = 'MAQ LAVAR ROUPA ENC';
    const MAQ_LAVAR_SECAR_ROUPA_ENC = 'MAQ LAVAR/SECAR ROUPA ENC';
    const ACESSORIOS_ENC = 'ACESSÓRIOS ENC';
    const CONGELADORES_HORIZONTAIS = 'CONGELADORES HORIZONTAIS';
    const FRIGORIF_AMERICANOS = 'FRIGORIF.AMERICANOS';
    const FRIGORIF_2P_NO_FROST = 'FRIGORIF.2P NO FROST';
    const FRIGORIF_2_PORTAS = 'FRIGORIF.2 PORTAS';
    const FRIGORIF_1_PORTA = 'FRIGORIF.1 PORTA';
    const COMBINADOS_NO_FROST = 'COMBINADOS NO FROST';
    const COMBINADOS_CONVENCIONAIS = 'COMB.CONVENCIONAIS';
    const FRIO_INDUSTRIAL = 'FRIO INDUSTRIAL';
    const ARREFECEDORES_HORIZONTAIS_INDUSTRIAIS = 'ARREFECEDORES HORIZONTAIS IND';
    const CONGELADORES_HORIZONTAIS_INDUSTRIAIS = 'CONGELADORES HORIZONTAIS IND';
    const CONGELADORES_ILHA_INDUSTRIAIS = 'CONGELADORES ILHA IND';
    const ELECTROCUTORES_INSECTOS = 'ELECTROCUTORES DE INSECTOS IND';
    const FOGOES_INDUSTRIAIS = 'FOGOES IND';
    const EQUIPAMENTOS_COZINHA_INDUSTRIAIS = 'EQUIPAMENTOS COZINHA IND';
    const VARINHAS_INDUSTRIAIS = 'VARINHAS INDUSTRIAIS';
    const ARREFECEDORES_VERTICAIS_INDUSTRIAIS = 'ARREFECEDORES VERTICAIS IND';
    const CONGELADORES_VERTICAIS_INDUSTRIAIS = 'CONGELADORES VERTICAIS IND';
    const MAQ_DE_LOUCA = 'MAQ LAVAR LOUÇA';
    const MLL_DE_45 = 'MAQ LOUÇA 45 Cm';
    const MLL_DE_60 = 'MAQ LOUÇA 60 Cm';
    const MLL_COMPACTAS = 'MAQ LOUÇA COMPACTAS';
    const MAQ_LAVAR_ROUPA_CARGA_FRONTAL = 'MAQ LAVAR CARGA FRONTAL';
    const MAQ_LAVAR_ROUPA_CARGA_SUPERIOR = 'MAQ LAVAR CARGA SUPERIOR';
    const MAQ_LAVAR_SECAR_ROUPA = 'MAQ LAVAR E SECAR ROUPA';
    const MAQ_SECAR_ROUPA_COND = 'MAQ SECAR CONDENSAÇÃO';
    const MAQ_SECAR_ROUPA_BC = 'MAQ SECAR BOMBA CALOR';
    const MAQ_SECAR_ROUPA_VENT = 'MAQ SECAR EXAUSTÃO';
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
    const CENTRIFUGADORAS = 'CENTRIFUGADORAS';
    const MAQ_DE_COZINHA = 'MAQ DE COZINHA';
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
    const JARROS_E_FERV_PURIF_ÁGUA = 'JARROS E FERV./PURIF. ÁGUA';
    const MAQ_CAFE = 'MAQ CAFE EXPRESSO';
    const MOINHOS_DE_CAFE = 'MOINHOS DE CAFE';
    const SANDWICHEIRAS = 'SANDWICHEIRAS';
    const TERMOS = 'TERMOS';
    const TORRADEIRAS = 'TORRADEIRAS';
    const FERROS_CALDEIRA = 'FERROS COM CALDEIRA';
    const FERROS_A_SECO = 'FERROS SECOS';
    const FERROS_A_VAPOR = 'FERROS A VAPOR';
    const TABUAS_PASSAR_FERRO = 'TÁBUAS DE PASSAR';
    const FERRO_VIAGEM = 'FERRO DE VIAGEM';
    const FOGAREIROS = 'FOGAREIROS';
    const RADIADORES_A_OLEO = 'RADIADORES A OLEO';
    const CONVECTORES_TERMOVENT = 'CONVECTORES/TERMOVENT.';
    const ELECTRICO = 'ELÉCTRICO';
    const EMISSORES_TERMICOS = 'EMISSORES TÉRMICOS';
    const VENTILACAO = 'VENTILAÇÃO';
    const VENTOINHAS = 'VENTOINHAS';
    const TOALHEIROS = 'TOALHEIROS';
    const RECUPERADORES = 'RECUPERADORES';
    const SALAMANDRAS = 'SALAMANDRAS';
    const FOGOES_LENHA = 'FOGÕES A LENHA';
    const FRIGOBAR = 'FRIGOBAR';
    const BALANÇAS_DE_COZINHA = 'BALANÇAS DE COZINHA';
    const FRIO = 'FRIO';
    const ACESSORIOS_MAQ_ROUPA = 'ACESSÓRIOS MAQ ROUPA';
    const ACESSORIOS_CLIMATIZACAO = 'ACESSÓRIOS CLIMATIZAÇÃO';
    const PURIFICADORES_AR = 'PURIFICADORES DE AR';
    const LAVA_LOUCAS = 'LAVA LOUÇAS';
    const MISTURADORAS = 'MISTURADORAS';
    const ACESSORIOS_COZINHA = 'ACESSÓRIOS APARELHOS COZINHA';
    const HUMIDIFICADORES = 'HUMIDIFICADORES';
    const OUTROS_EQUIPAMENTOS_COZINHA = 'OUTROS EQUIPAMENTOS COZINHA';
    const PUERICULTURA = 'PUERICULTURA';
    const ESCOVAS_DE_DENTES = 'ESCOVAS DE DENTES';
    CONST ACESSORIOS_ASSEIO = 'ACESSÓRIOS DE ASSEIO PESSOAL';
    const CUIDADOS_MASCULINOS = 'CUIDADOS_MASCULINOS';
    const SAUDE_BELEZA = 'SAUDE E BELEZA';
    const MAQ_COSTURA = 'MAQ COSTURA';
    const ACESSORIOS_CUIDADOS_ROUPA = 'ACESSÓRIOS CUIDADOS ROUPA';
    const TIRA_BORBOTOS = 'TIRA BORBOTOS';
    const MAQ_LIMPEZA_VAPOR = 'MAQ LIMPEZA A VAPOR';
    const MAQ_LAVAR_PRESSAO = 'MAQ LAVAR ALTA PRESSÃO';
    const ACESSORIOS_APARELHOS_LIMPEZA = 'ACESSÓRIOS APARELHOS LIMPEZA';
    const OUTROS_APARELHOS_LIMPEZA = 'OUTROS APARELHOS LIMPEZA';
    const ASPIRADORES = 'ASPIRADORES';
    const ACESSORIOS_MICROONDAS = 'ACESSÓRIOS MICROONDAS';
}