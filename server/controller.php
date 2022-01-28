<?php
header('Content-Type: application/json; charset=utf-8');
require_once "migration.php";
require_once "model.php";
require_once "utils.php";

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');

//$req  = [
//    "run"=>1,
////      "request"=>["price"=>"cost_wholesale",
////                  "min_price"=>"10",
////                  "max_price"=>"30000",
////                  "quantity"=>"<",
////                  "number_quantity"=>"20"]
//];



class Controller{
    private array $request;
    function __construct(array $req)
    {
        $this->request = $req;
    }
    function checkData(): array
    {
        if (!checkTable()){
            return array("check"=>"false");
        }
        else{
            return array("check"=>"true");
        }
    }
    function getResult(array $data): array
    {
        $result=[];
        $sum = 0;
        $max_cost = 0;
        $id_max_cost=0;
        $min_cost=100000000;
        $id_min_cost=0;
        $medium_cost=0;
        $medium_cost_w=0;
        $attention_arr=[];
        foreach ( $data as $i){
            if ($max_cost < (float)$i["cost_rub"]){
                $max_cost = (float)$i["cost_rub"];
                $id_max_cost = $i["id"];
            }
            $s = (int)$i["quantity_stock_1"]+(int)$i["quantity_stock_2"];
            $sum+=$s;
            if ($s<20){
                $attention_arr[]=$i["id"];
            }
            $medium_cost_w+=(float)$i["cost_wholesale"];
            $medium_cost+=(float)$i["cost_rub"];
            if ($min_cost>(float)$i["cost_wholesale"]){
                $min_cost = (float)$i["cost_wholesale"];
                $id_min_cost = $i["id"];
            }
        }
        $medium_cost_w = $medium_cost_w/count($data);
        $medium_cost = $medium_cost/count($data);
        $result["medium_cost_w"] = $medium_cost_w;
        $result["medium_cost"] = $medium_cost;
        $result["sum"] = $sum;
        $result["id_max_cost"] = $id_max_cost;
        $result["id_min_cost"] = $id_min_cost;
        $result["attention"] = $attention_arr;
        return $result;
    }
    function getData(array $data): array
    {
        $json_data=array();
        try {
            if($data[0]=="00000"){
                unset($data);
                $json_data["data"][0]=array("id"=>"-",
                    "name_product"=>"-",
                    "cost_rub"=>"-",
                    "cost_wholesale"=>"-",
                    "quantity_stock_1"=>"-",
                    "quantity_stock_2"=>"-",
                    "source_country"=>"-");
                $json_data["title"]=array_keys(rename_key($json_data["data"][0], "ru"));
                $json_data["result"]=array("medium_cost_w"=>"",
                    "medium_cost"=>"",
                    "sum"=>"",
                    "id_max_cost"=>"",
                    "id_min_cost"=>"",
                    "attention"=>[]
                );
            }else{
                $json_data["data"]=$data;
                $json_data["title"]= array_keys(rename_key($data[0], "ru"));
                $json_data["result"] = $this->getResult($data);
            }

        }
        catch (Exception $e){
            $json_data['data']= "json fail".$e;
            $json_data["title"]= [];
            $json_data["result"] = [];
        }
        return $json_data;
    }
    function routing(): void
    {
        switch (array_key_first($this->request))
        {
            case 'request':
                $data = getdbData($this->request['request']);
                echo json_encode($this->getData($data));
                break;
            case 'check':
                echo json_encode($this->checkData());
                break;
            case 'run':
                try {
                    createData();
                }
                catch (Exception $e){
                    echo json_encode(array("fail"=>$e));
                }
        }
    }
}

$req = json_decode(file_get_contents("php://input"),true);
$controler = new Controller($req);
$controler->routing();
exit;