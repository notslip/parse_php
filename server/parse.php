<?php

require '../vendor/autoload.php';

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');


use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;





class Parse
{
    private string $file;
    private object $sheet;
    private string $highestColumn;
    private int $highestRow;
    private int $highestColumnIndex;
    private array $arrTitle;
    private array $arrData;

    function __construct(string $filepath){
        $this->file = $filepath;
        $this->sheet = IOFactory::load($this->file)->getActiveSheet();
        $this->highestColumn = $this->sheet->getHighestColumn();
        $this->highestRow = $this->sheet->getHighestRow();
        $this->highestColumnIndex = Coordinate::columnIndexFromString($this->highestColumn);
        $this->arrTitle = [];
        $this->arrData = [];
    }

    function name_col():array{
    //    creates array contains names for title columns
        for ($col = 1; $col <= $this->highestColumnIndex; ++$col) {
            $value = $this->sheet->getCellByColumnAndRow($col, 1)->getValue();
            if ($value != "") {
                $this->arrTitle[] = $value;
            }
        }
        return $this->arrTitle;
    }

    function get_Data():array{
//        creates array contains data, for example: [number row =>[title column => value data]]
        $this->arrTitle = $this->name_col();
        for ($row = 2; $row <= $this->highestRow; ++$row) {
            $arrTemp=[];
            for ($col = 1; $col <= $this->highestColumnIndex; ++$col) {
                $value = $this->sheet->getCellByColumnAndRow($col, $row)->getValue();
                if(mb_strtolower($value) == "стоимость"){
                    unset($arrTemp);
                    break;
                }
                elseif($value != "") {
                    $arrTemp+=[$this->arrTitle[$col-1] => $value];
                }
            }
            if (empty($arrTemp)){
                continue;
            }
            $this->arrData+=[$row-1 => $arrTemp];
            unset($arrTemp);
        }
        return $this->arrData;
    }
}
//$file_parse = "pricelist.xls";
//$a = new Parse($file_parse);
//
//var_dump($a->get_Data());

