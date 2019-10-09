<?php


namespace Mlp\Cli\Helper;


class splitFile
{

    public static function greet(){
        print_r("Hello World");
    }

    public static function split_file($file_url, $output_dir) {
        $inputFile = $file_url;
        $outputFile = 'output';

        $splitSize = 100;

        $in = fopen($inputFile, 'r');

        $rowCount = 0;
        $fileCount = 1;
        while (!feof($in)) {
            if (($rowCount % $splitSize) == 0) {
                if ($rowCount > 0) {
                    fclose($out);
                }
                $out = fopen($output_dir.$outputFile . $fileCount++ . '.csv', 'w');
            }
            $data = fgetcsv($in,4000,';');
            if ($data)
                fputcsv($out, $data);
            $rowCount++;
        }

        fclose($out);
        return $fileCount;
    }

}
