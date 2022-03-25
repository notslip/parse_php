<?php

function rename_key($arr, $lang): ?array
{
    /**
     * перводит названия столбцов таблиц
     *что бы можно было записывать в базу
     *и обратно для представления
     *указывается массив и язык НА который нужно перевезти
    */

    $ru_en = [
        "Номер"=>"id",
        "Наименование товара" => "name_product",
        "Стоимость, руб" => "cost_rub",
        "Стоимость опт, руб" => "cost_wholesale",
        "Наличие на складе 1, шт" => "quantity_stock_1",
        "Наличие на складе 2, шт" => "quantity_stock_2",
        "Страна производства"=>"source_country"
    ];
    $en_ru = [
        "id"=>"Номер",
        "name_product" => "Наименование товара",
        "cost_rub" => "Стоимость, руб",
        "cost_wholesale" => "Стоимость опт, руб",
        "quantity_stock_1" => "Наличие на складе 1, шт",
        "quantity_stock_2" => "Наличие на складе 2, шт",
        "source_country" => "Страна производства"
    ];
    $arrtemp = [];

    if ($lang == "ru") {
        foreach (array_keys($arr) as $key) {
            if(isset($en_ru[$key])) {
                $arrtemp[$en_ru[$key]] = $arr[$key];
            }
            else{
                echo "Неправильный формат массива или неверно указан язык перевода";
                return null;
            }
        }
    }

    if ($lang == "en") {
        foreach (array_keys($arr) as $key) {
            if(isset($ru_en[$key])) {
                $arrtemp[$ru_en[$key]] = $arr[$key];
            }
            else{
                echo "Неправильный формат массива или неверно указан язык перевода";
                return null;
            }
        }
    }
    return $arrtemp;
}
