<?php


namespace Mlp\Cli\Console\Command;


class Telefac
{


    protected function addTelefacProdCSV(){
        /*
         * 0 - Nome
         * 1 - marca
         * 2 - codigo
         * 3 - sku
         * 4 - descrição
         * 5 - pvp
         * 6 - stock
         * 7 - preço custo
         * 8 - gama
         * 9 - familia
         * 10 - sub familia
         */
        $categories = $this->categoryManager->getCategoriesArray();
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Telefac.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Telefac products" . "\n");
        $row = 0;
        if (($handle = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/Console/Command/telefac_interno.csv", "r")) !== FALSE) {
            print_r("abri ficheiro\n");
            while (!feof($handle)) {
                if (($data = fgetcsv($handle, 4000, ",", '"')) !== FALSE) {
                    $row++;
                    $sku = trim($data[3]);
                    if (strlen($sku) == 13) {
                        try {
                            $product = $this->productRepository->get($sku, true, null, true);
                            if ($product->getStatus() == 2) {
                                print_r($sku . "\n");
                                continue;
                            }
                        } catch (NoSuchEntityException $exception) {
                            $name = trim($data[0]);
                            $gama = trim($data[8]);
                            $familia = trim($data[9]);
                            $subfamilia = trim($data[10]);
                            $description = trim($data[4]);
                            $meta_description = "";
                            $manufacter = trim($data[1]);
                            $length = 0;
                            $width = 0;
                            $height = 0;
                            $weight = trim($data[4]);
                            $price = (int)trim($data[7]) * 1.23 * 1.20;

                            $productInterno = new \Mlp\Cli\Helper\Product($sku, $name, $gama, $familia, $subfamilia, $description,
                                $meta_description, $manufacter, $length, $width, $height, $weight, $price,
                                $this->productRepository, $this->productFactory, $this->categoryManager,
                                $this->dataAttributeOptions, $this->attributeManager, $this->stockRegistry,
                                $this->config, $this->optionFactory, $this->productRepositoryInterface);

                            $productInterno->add_product($categories, $logger, $data[3]);
                        }
                        $this->updateStock($sku, $data[6]);

                    }
                }
            }
            fclose($handle);
        }else {
            print_r("Não abriu o ficheiro");
        }
    }
}
