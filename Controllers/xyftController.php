<?php 
namespace Controllers;
use Libs\Controller;
use Libs\CTemplate;
use Libs\Page;
use Libs\Upload;
use Models\xyftModel;
use phpDocumentor\Reflection\Types\Array_;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class xyftController extends Controller
{

    public $firstIndex = array(-2, -2, -1, -1, 0, 0, 1, 1, 2, 2);
    public $secondIndex = array(-1, 0, 1, 2, 3, -3, -2, -1, 0 ,1);
    public $thirdIndex = array(-1, 0, 1, 2, 3, -3, -2, -1, 0 ,1);

    public $useThisToGetFifth = array(5,4,3,2,1,-1,-2,-3,-4,-5);
    public $forthIndex = array(5, 4, 3, 2, 1, -1, -2, -3, -4, -5);


    /**
     * User: tangwei
     * Date: 2019/4/25 16:55
     * Function:首页数据展示
     */
    public function index()
    {
        $xyftres = xyftModel::getLastFourUndeletedWithOutLimit();

        $yuceres = $this->getFifthStageByLastFourStage($xyftres);
        $rem = array();
        if(!empty($yuceres)){
            foreach($yuceres as $k=>$v){
                $rem[(int)$k] = !isset($rem[(int)$k]) || $rem[(int)$k] == "" ? $v : $rem[(int)$k]."|".$v;
            }
        }
        if(!empty($rem)){
            foreach($rem as $key=>$value){
                if($key == 0){
                    $rem["one"] = $value;
                }
                if($key == 1){
                    $rem["two"] = $value;
                }
                if($key == 2){
                    $rem["three"] = $value;
                }
                if($key == 3){
                    $rem["four"] = $value;
                }
                if($key == 4){
                    $rem["five"] = $value;
                }
                if($key == 5){
                    $rem["six"] = $value;
                }
                if($key == 6){
                    $rem["seven"] = $value;
                }
                if($key == 7){
                    $rem["eight"] = $value;
                }
                if($key == 8){
                    $rem["night"] = $value;
                }
                if($key == 9){
                    $rem["ten"] = $value;
                }
            }
        }
        $xyftres = array_reverse($xyftres);
        $ctemplate=new CTemplate("index.html","dafault",__DIR__."/../Template");//测试模板使用

        $ctemplate->render(array("xyftres"=>$xyftres, "yuce"=>$rem));
    }

    /**
     * User: tangwei
     * Date: 2019/4/25 17:27
     * Function:获取最新的一条开奖记录
     */
    public function getLastOne()
    {
        $lastfour = xyftModel::getLastFourUndeletedWithOutLimit();
        $lastone = $lastfour[0];
        $this->jsonSuccess($lastone->id);
    }

    /**
     * User: tangwei
     * Date: 2019/4/22 16:55
     * Function:导入数据的界面显示
     */
    public function importDate()
    {
        $ctemplate=new CTemplate("import.html","dafault",__DIR__."/../Template");
        $ctemplate->render(array());
    }

    public function importDate3()
    {
        $ctemplate=new CTemplate("import3.html","dafault",__DIR__."/../Template");
        $ctemplate->render(array());
    }

    public function importDate7()
    {
        $ctemplate=new CTemplate("import7.html","dafault",__DIR__."/../Template");
        $ctemplate->render(array());
    }



    /**
     * User: tangwei
     * Date: 2019/4/22 17:07
     * Function:获取导入数据的结果
     */
    public function getImportDateRes()
    {
        $upload = new Upload(12);//上传文件开始
        $upload->FileMaxSize = array('image' => 5*1024 * 1024, 'audio' => 2 * 1024 * 1024, 'video' => 20 * 1024 * 1024, "csv"=>5 * 1024 * 1024);
        $upload->FileType = array('text/csv', "application/octet-stream", "application/vnd.ms-execl"); // 允许上传的文件类型
        $upload->FileSavePath = './Upload/';
        $file_save_full_name = $upload->UploadFile();
        $str = "";
        if(is_array($file_save_full_name) && !empty($file_save_full_name)){
            foreach($file_save_full_name as $k=>$v){
                $str.=$v.";";
            }
            $str = substr($str,0,-1);
        }else{
            $str = $file_save_full_name;
        }
        $spreadsheet = IOFactory::load("./Upload/".$str); // 载入Excel表格
        # 这里和上面代码的效果都一样,好像就是只读区别吧 不是特别清楚,官网上也给出了很多读写的写法,应该用会少消耗资源
        # 官网地址 https://phpspreadsheet.readthedocs.io/en/develop/topics/reading-and-writing-to-file/
        //$reader = IOFactory::createReader('Xlsx');
        //$reader->setReadDataOnly(TRUE);
        //$spreadsheet = $reader->load('./file.xlsx'); //载入excel表格
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // 总行数
        $highestColumn = $worksheet->getHighestColumn(); // 总列数
        # 把列的索引字母转为数字 从1开始 这里返回的是最大列的索引
        # 我尝试了下不用这块代码用以前直接循环字母的方式,拿不到数据
        # 测试了下超过26个字母也是没有问题的
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        $data = [];
        for ($row = 1; $row <= $highestRow; ++$row) { // 从第一行开始
            $row_data = [];
            for ($column = 1; $column <= $highestColumnIndex; $column++) {
                $row_data[] = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
            }
            $data[] = $row_data;
        }
        $data = array_reverse($data);
        $totalNum = count($data);
        $totalNumQi = "";
        $totalNumQiArray = array();
//        for($nownum = 5; $nownum <= $totalNum +1; $nownum ++){
//            $totalNumQi .= "'第".$nownum."期',";
//        }
        $one = 0;
        $two = 1;
        $three = 2;
        $four = 3;
        $resArray = array();

        $arr = array(
            0,0,0,0,0,0,0,0,0,0
        );

        $errorarr = array(
            0,0,0,0,0,0,0,0,0,0
        );

        $kkk = array();

        $eee = array();

        $num = 0;

        $yingkui = array();

        $kkkuu = 0;

        while(true){
//            if($num > 10) break;
//            $num ++;
            $tmpArray = array();
            $tmpArray["mess"] = array();
            if(($totalNum - $one) < 5)break;
            $firstQi = $data[$one];
            $secondQi = $data[$two];
            $thirdQi = $data[$three];
            $forthQi = $data[$four];
            $matchQi = $data[(int)($four + 1)];
            $totalNumQi .= "'".($forthQi[1] + 1)."',";
            array_push($totalNumQiArray, $forthQi[1] + 1);
            $kkkuu ++;
            $yingkui[$kkkuu] = 0;



            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $forthQi[0]."第".$forthQi[1]."期";
            $tmpModel->one = $forthQi[2];
            $tmpModel->two = $forthQi[3];
            $tmpModel->three = $forthQi[4];
            $tmpModel->four = $forthQi[5];
            $tmpModel->five = $forthQi[6];
            $tmpModel->six = $forthQi[7];
            $tmpModel->seven = $forthQi[8];
            $tmpModel->eight = $forthQi[9];
            $tmpModel->night = $forthQi[10];
            $tmpModel->ten = $forthQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第四期放入到数组的第一位

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $thirdQi[0]."第".$thirdQi[1]."期";
            $tmpModel->one = $thirdQi[2];
            $tmpModel->two = $thirdQi[3];
            $tmpModel->three = $thirdQi[4];
            $tmpModel->four = $thirdQi[5];
            $tmpModel->five = $thirdQi[6];
            $tmpModel->six = $thirdQi[7];
            $tmpModel->seven = $thirdQi[8];
            $tmpModel->eight = $thirdQi[9];
            $tmpModel->night = $thirdQi[10];
            $tmpModel->ten = $thirdQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第三期放入到数组的第一位

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $secondQi[0]."第".$secondQi[1]."期";
            $tmpModel->one = $secondQi[2];
            $tmpModel->two = $secondQi[3];
            $tmpModel->three = $secondQi[4];
            $tmpModel->four = $secondQi[5];
            $tmpModel->five = $secondQi[6];
            $tmpModel->six = $secondQi[7];
            $tmpModel->seven = $secondQi[8];
            $tmpModel->eight = $secondQi[9];
            $tmpModel->night = $secondQi[10];
            $tmpModel->ten = $secondQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第二期放入到数组的第一位

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $firstQi[0]."第".$firstQi[1]."期";
            $tmpModel->one = $firstQi[2];
            $tmpModel->two = $firstQi[3];
            $tmpModel->three = $firstQi[4];
            $tmpModel->four = $firstQi[5];
            $tmpModel->five = $firstQi[6];
            $tmpModel->six = $firstQi[7];
            $tmpModel->seven = $firstQi[8];
            $tmpModel->eight = $firstQi[9];
            $tmpModel->night = $firstQi[10];
            $tmpModel->ten = $firstQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第二期放入到数组的第一位


//            echo "<br />-------------<br />";
//            var_dump($tmpArray["mess"]);
//            $k = array_reverse($tmpArray["mess"]);
            $yuceRes = $this->getFifthStageByLastFourStage($tmpArray["mess"]);
//            echo "<br />&&&&&&&&&&&&&&&&&&&&<br />";
//            var_dump($yuceRes);
//            echo "<br />***************<br />";
            $rem = array();
            if(!empty($yuceRes)){
                foreach($yuceRes as $k=>$v){
                    $rem[(int)$k] = !isset($rem[(int)$k]) || $rem[(int)$k] == "" ? $v : $rem[(int)$k]."|".$v;
                }
            }
            if(!empty($rem)){
                foreach($rem as $key=>$value){
                    if($key == 0){
                        $rem["one"] = $value;
                    }
                    if($key == 1){
                        $rem["two"] = $value;
                    }
                    if($key == 2){
                        $rem["three"] = $value;
                    }
                    if($key == 3){
                        $rem["four"] = $value;
                    }
                    if($key == 4){
                        $rem["five"] = $value;
                    }
                    if($key == 5){
                        $rem["six"] = $value;
                    }
                    if($key == 6){
                        $rem["seven"] = $value;
                    }
                    if($key == 7){
                        $rem["eight"] = $value;
                    }
                    if($key == 8){
                        $rem["night"] = $value;
                    }
                    if($key == 9){
                        $rem["ten"] = $value;
                    }
                }
            }

            $tmpArray["res"] = $rem;

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $matchQi[0]."第".$matchQi[1]."期";
            $tmpModel->one = $matchQi[2];
            $tmpModel->two = $matchQi[3];
            $tmpModel->three = $matchQi[4];
            $tmpModel->four = $matchQi[5];
            $tmpModel->five = $matchQi[6];
            $tmpModel->six = $matchQi[7];
            $tmpModel->seven = $matchQi[8];
            $tmpModel->eight = $matchQi[9];
            $tmpModel->night = $matchQi[10];
            $tmpModel->ten = $matchQi[11];
            $tmpArray["match"] = $tmpModel;

            array_push($resArray, $tmpArray);
            $one ++;
            $two ++;
            $three ++;
            $four ++;


            //每个数字的正确数量对比
            if(!empty($tmpArray["res"])){
//                var_dump($tmpArray["res"]);
                $match = $tmpArray["match"];
//                var_dump($match);
                foreach($tmpArray["res"] as $key=>$value){
//                    echo "<br />----".$key."-----".$value."-------<br/>";
                    if($key === "one"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

                                    if($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->one;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 5;
                                    }else{
//                                        if($val == 6){
//
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                    }
                                }
                            }
                        }else{
//                            echo $tmpArray["res"][$key] ."==". $match->one;
                            if($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                }
                            }
                        }
                    }

//                    var_dump($arr);

//                    echo "YYYYYYYYYYYYYYYYYYYY<br />";

                    if($key === "two"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
//


                                    if($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->two;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 5;

                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->one;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->one;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 5;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                    }
                                }
                            }
                        }else{






                            if($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;

                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                }
                            }
                        }
                    }

                    if($key === "three"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
                                    if($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->three;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                    }
                                }
                            }
                        }else{




                            if($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                }
                            }
                        }
                    }

                    if($key === "four"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
//                                    echo $val ."==". $match->four;

                                    if($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->four;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                    }
                                }
                            }
                        }else{



                            if($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                }
                            }
                        }
                    }

                    if($key === "five"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

                                    if($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->five;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                }
                            }
                        }
                    }

                    if($key === "six"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
                                    if($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->six;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                }
                            }
                        }
                    }

                    if($key === "seven"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){


                                    if($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->seven;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                    }
                                }
                            }
                        }else{




                            if($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                }
                            }
                        }
                    }

                    if($key === "eight"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){


                                    if($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->eight;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                }
                            }
                        }
                    }

                    if($key === "night"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

                                    if($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->night;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                }
                            }
                        }
                    }

                    if($key === "ten"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

                                    if($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->one;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                    $yingkui[$kkkuu] += 5;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                    }
                                }
                            }
                        }else{

                            if($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                    $yingkui[$kkkuu] += 5;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 5;
                                }
                            }
                        }
                    }
                }
            }
        }
//        exit;
//
////        var_dump($resArray);exit;
///
//        foreach($eee as $key=>$value){
//            echo "<br />".$value."<br />";
//        }

        $yingkuiStr = "";
        foreach($yingkui as $k=>$v){
            $yingkuiStr .= $v.",";
        }

        $diejiaRes = array();
        foreach($yingkui as $k=>$v){
            if($k == 1){
                $diejiaRes[$k] = $v;
            }else{
                $diejiaRes[$k] = $v + $diejiaRes[$k-1];
            }
        }
        $diejiaStr = "";
        foreach($diejiaRes as $k=>$v){
            $diejiaStr .= $v.",";
        }

        $ctemplate=new CTemplate("importRes.html","dafault",__DIR__."/../Template");//测试模板使用
        $ctemplate->render(array("resArray"=>$resArray, "arr"=>$arr, "errorarr"=>$errorarr, "totalNumQi"=>$totalNumQi, "totalNumQiArray"=>$totalNumQiArray, "yingkuiStr"=>$yingkuiStr, "diejiaStr"=>$diejiaStr));
    }

    public function getImportDateRes3()
    {
        $upload = new Upload(12);//上传文件开始
        $upload->FileMaxSize = array('image' => 5*1024 * 1024, 'audio' => 2 * 1024 * 1024, 'video' => 20 * 1024 * 1024, "csv"=>5 * 1024 * 1024);
        $upload->FileType = array('text/csv', "application/octet-stream", "application/vnd.ms-execl"); // 允许上传的文件类型
        $upload->FileSavePath = './Upload/';
        $file_save_full_name = $upload->UploadFile();
        $str = "";
        if(is_array($file_save_full_name) && !empty($file_save_full_name)){
            foreach($file_save_full_name as $k=>$v){
                $str.=$v.";";
            }
            $str = substr($str,0,-1);
        }else{
            $str = $file_save_full_name;
        }
        $spreadsheet = IOFactory::load("./Upload/".$str); // 载入Excel表格
        # 这里和上面代码的效果都一样,好像就是只读区别吧 不是特别清楚,官网上也给出了很多读写的写法,应该用会少消耗资源
        # 官网地址 https://phpspreadsheet.readthedocs.io/en/develop/topics/reading-and-writing-to-file/
        //$reader = IOFactory::createReader('Xlsx');
        //$reader->setReadDataOnly(TRUE);
        //$spreadsheet = $reader->load('./file.xlsx'); //载入excel表格
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // 总行数
        $highestColumn = $worksheet->getHighestColumn(); // 总列数
        # 把列的索引字母转为数字 从1开始 这里返回的是最大列的索引
        # 我尝试了下不用这块代码用以前直接循环字母的方式,拿不到数据
        # 测试了下超过26个字母也是没有问题的
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        $data = [];
        for ($row = 1; $row <= $highestRow; ++$row) { // 从第一行开始
            $row_data = [];
            for ($column = 1; $column <= $highestColumnIndex; $column++) {
                $row_data[] = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
            }
            $data[] = $row_data;
        }
        $data = array_reverse($data);
        $totalNum = count($data);
        $totalNumQi = "";
        $totalNumQiArray = array();
//        for($nownum = 5; $nownum <= $totalNum +1; $nownum ++){
//            $totalNumQi .= "'第".$nownum."期',";
//        }
        $one = 0;
        $two = 1;
        $three = 2;
        $four = 3;
        $resArray = array();

        $arr = array(
            0,0,0,0,0,0,0,0,0,0
        );

        $errorarr = array(
            0,0,0,0,0,0,0,0,0,0
        );

        $kkk = array();

        $eee = array();

        $num = 0;

        $yingkui = array();

        $kkkuu = 0;

        while(true){
//            if($num > 10) break;
//            $num ++;
            $tmpArray = array();
            $tmpArray["mess"] = array();
            if(($totalNum - $one) < 5)break;
            $firstQi = $data[$one];
            $secondQi = $data[$two];
            $thirdQi = $data[$three];
            $forthQi = $data[$four];
            $matchQi = $data[(int)($four + 1)];
            $totalNumQi .= "'".($forthQi[1] + 1)."',";
            array_push($totalNumQiArray, $forthQi[1] + 1);
            $kkkuu ++;
            $yingkui[$kkkuu] = 0;



            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $forthQi[0]."第".$forthQi[1]."期";
            $tmpModel->one = $forthQi[2];
            $tmpModel->two = $forthQi[3];
            $tmpModel->three = $forthQi[4];
            $tmpModel->four = $forthQi[5];
            $tmpModel->five = $forthQi[6];
            $tmpModel->six = $forthQi[7];
            $tmpModel->seven = $forthQi[8];
            $tmpModel->eight = $forthQi[9];
            $tmpModel->night = $forthQi[10];
            $tmpModel->ten = $forthQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第四期放入到数组的第一位

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $thirdQi[0]."第".$thirdQi[1]."期";
            $tmpModel->one = $thirdQi[2];
            $tmpModel->two = $thirdQi[3];
            $tmpModel->three = $thirdQi[4];
            $tmpModel->four = $thirdQi[5];
            $tmpModel->five = $thirdQi[6];
            $tmpModel->six = $thirdQi[7];
            $tmpModel->seven = $thirdQi[8];
            $tmpModel->eight = $thirdQi[9];
            $tmpModel->night = $thirdQi[10];
            $tmpModel->ten = $thirdQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第三期放入到数组的第一位

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $secondQi[0]."第".$secondQi[1]."期";
            $tmpModel->one = $secondQi[2];
            $tmpModel->two = $secondQi[3];
            $tmpModel->three = $secondQi[4];
            $tmpModel->four = $secondQi[5];
            $tmpModel->five = $secondQi[6];
            $tmpModel->six = $secondQi[7];
            $tmpModel->seven = $secondQi[8];
            $tmpModel->eight = $secondQi[9];
            $tmpModel->night = $secondQi[10];
            $tmpModel->ten = $secondQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第二期放入到数组的第一位

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $firstQi[0]."第".$firstQi[1]."期";
            $tmpModel->one = $firstQi[2];
            $tmpModel->two = $firstQi[3];
            $tmpModel->three = $firstQi[4];
            $tmpModel->four = $firstQi[5];
            $tmpModel->five = $firstQi[6];
            $tmpModel->six = $firstQi[7];
            $tmpModel->seven = $firstQi[8];
            $tmpModel->eight = $firstQi[9];
            $tmpModel->night = $firstQi[10];
            $tmpModel->ten = $firstQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第二期放入到数组的第一位


//            echo "<br />-------------<br />";
//            var_dump($tmpArray["mess"]);
//            $k = array_reverse($tmpArray["mess"]);
            $yuceRes = $this->getFifthStageByLastFourStage($tmpArray["mess"]);
//            echo "<br />&&&&&&&&&&&&&&&&&&&&<br />";
//            var_dump($yuceRes);
//            echo "<br />***************<br />";
            $rem = array();
            if(!empty($yuceRes)){
                foreach($yuceRes as $k=>$v){
                    $rem[(int)$k] = !isset($rem[(int)$k]) || $rem[(int)$k] == "" ? $v : $rem[(int)$k]."|".$v;
                }
            }
            if(!empty($rem)){
                foreach($rem as $key=>$value){
                    if($key == 0){
                        $rem["one"] = $value;
                    }
                    if($key == 1){
                        $rem["two"] = $value;
                    }
                    if($key == 2){
                        $rem["three"] = $value;
                    }
                    if($key == 3){
                        $rem["four"] = $value;
                    }
                    if($key == 4){
                        $rem["five"] = $value;
                    }
                    if($key == 5){
                        $rem["six"] = $value;
                    }
                    if($key == 6){
                        $rem["seven"] = $value;
                    }
                    if($key == 7){
                        $rem["eight"] = $value;
                    }
                    if($key == 8){
                        $rem["night"] = $value;
                    }
                    if($key == 9){
                        $rem["ten"] = $value;
                    }
                }
            }

            $tmpArray["res"] = $rem;

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $matchQi[0]."第".$matchQi[1]."期";
            $tmpModel->one = $matchQi[2];
            $tmpModel->two = $matchQi[3];
            $tmpModel->three = $matchQi[4];
            $tmpModel->four = $matchQi[5];
            $tmpModel->five = $matchQi[6];
            $tmpModel->six = $matchQi[7];
            $tmpModel->seven = $matchQi[8];
            $tmpModel->eight = $matchQi[9];
            $tmpModel->night = $matchQi[10];
            $tmpModel->ten = $matchQi[11];
            $tmpArray["match"] = $tmpModel;

            array_push($resArray, $tmpArray);
            $one ++;
            $two ++;
            $three ++;
            $four ++;

//            var_dump($tmpArray["res"]);
//            var_dump($tmpArray["match"]);exit;

            //每个数字的正确数量对比
            if(!empty($tmpArray["res"])){
//                var_dump($tmpArray["res"]);
                $match = $tmpArray["match"];
//                var_dump($match);
                foreach($tmpArray["res"] as $key=>$value){
//                    echo "<br />----".$key."-----".$value."-------<br/>";
                    if($key === "one"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

//                                    echo "******".$value."******<br />";

                                    if($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->one;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }else{
//                                        if($val == 6){
//
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 3;
                                    }
                                }
                            }
                        }else{
//                            echo $tmpArray["res"][$key] ."==". $match->one;
                            if($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 3;
                                }
                            }
                        }
                    }

//                    var_dump($arr);

//                    echo "YYYYYYYYYYYYYYYYYYYY<br />";

                    if($key === "two"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
//


                                    if($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->two;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->one;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 3;
                                    }
                                }
                            }
                        }else{






                            if($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 3;
                                }
                            }
                        }
                    }

                    if($key === "three"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
                                    if($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->three;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 3;
                                    }
                                }
                            }
                        }else{




                            if($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 3;
                                }
                            }
                        }
                    }

                    if($key === "four"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
//                                    echo $val ."==". $match->four;

                                    if($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->four;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 3;
                                    }
                                }
                            }
                        }else{



                            if($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 3;
                                }
                            }
                        }
                    }

                    if($key === "five"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

                                    if($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->five;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 3;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 3;
                                }
                            }
                        }
                    }

                    if($key === "six"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
                                    if($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->six;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 3;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 3;
                                }
                            }
                        }
                    }

                    if($key === "seven"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){


                                    if($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->seven;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 3;
                                    }
                                }
                            }
                        }else{




                            if($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 3;
                                }
                            }
                        }
                    }

                    if($key === "eight"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){


                                    if($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->eight;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 3;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 3;
                                }
                            }
                        }
                    }

                    if($key === "night"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

                                    if($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->night;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 3;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 3;
                                }
                            }
                        }
                    }

                    if($key === "ten"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

                                    if($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->one;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 7;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 3;
                                    }
                                }
                            }
                        }else{

                            if($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 7;
                            }else{
                                if($value != ""){
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 3;
                                }
                            }
                        }
                    }
                }
            }
        }
//        exit;
//
////        var_dump($resArray);exit;
///
//        foreach($eee as $key=>$value){
//            echo "<br />".$value."<br />";
//        }

        $yingkuiStr = "";
        foreach($yingkui as $k=>$v){
            $yingkuiStr .= $v.",";
        }

        $diejiaRes = array();
        foreach($yingkui as $k=>$v){
//            echo $k."********".$v."<br />";
            if($k == 1){
                $diejiaRes[$k] = $v;
            }else{
                $diejiaRes[$k] = $v + $diejiaRes[$k-1];
            }
        }
        $diejiaStr = "";
        foreach($diejiaRes as $k=>$v){
            $diejiaStr .= $v.",";
        }

        $ctemplate=new CTemplate("importRes3.html","dafault",__DIR__."/../Template");//测试模板使用
        $ctemplate->render(array("resArray"=>$resArray, "arr"=>$arr, "errorarr"=>$errorarr, "totalNumQi"=>$totalNumQi, "totalNumQiArray"=>$totalNumQiArray, "yingkuiStr"=>$yingkuiStr, "diejiaStr"=>$diejiaStr));
    }

    public function getImportDateRes7(){
        $upload = new Upload(12);//上传文件开始
        $upload->FileMaxSize = array('image' => 5*1024 * 1024, 'audio' => 2 * 1024 * 1024, 'video' => 20 * 1024 * 1024, "csv"=>5 * 1024 * 1024);
        $upload->FileType = array('text/csv', "application/octet-stream", "application/vnd.ms-execl"); // 允许上传的文件类型
        $upload->FileSavePath = './Upload/';
        $file_save_full_name = $upload->UploadFile();
        $str = "";
        if(is_array($file_save_full_name) && !empty($file_save_full_name)){
            foreach($file_save_full_name as $k=>$v){
                $str.=$v.";";
            }
            $str = substr($str,0,-1);
        }else{
            $str = $file_save_full_name;
        }
        $spreadsheet = IOFactory::load("./Upload/".$str); // 载入Excel表格
        # 这里和上面代码的效果都一样,好像就是只读区别吧 不是特别清楚,官网上也给出了很多读写的写法,应该用会少消耗资源
        # 官网地址 https://phpspreadsheet.readthedocs.io/en/develop/topics/reading-and-writing-to-file/
        //$reader = IOFactory::createReader('Xlsx');
        //$reader->setReadDataOnly(TRUE);
        //$spreadsheet = $reader->load('./file.xlsx'); //载入excel表格
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // 总行数
        $highestColumn = $worksheet->getHighestColumn(); // 总列数
        # 把列的索引字母转为数字 从1开始 这里返回的是最大列的索引
        # 我尝试了下不用这块代码用以前直接循环字母的方式,拿不到数据
        # 测试了下超过26个字母也是没有问题的
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        $data = [];
        for ($row = 1; $row <= $highestRow; ++$row) { // 从第一行开始
            $row_data = [];
            for ($column = 1; $column <= $highestColumnIndex; $column++) {
                $row_data[] = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
            }
            $data[] = $row_data;
        }
        $data = array_reverse($data);
        $totalNum = count($data);
        $totalNumQi = "";
//        for($nownum = 5; $nownum <= $totalNum +1; $nownum ++){
//            $totalNumQi .= "'第".$nownum."期',";
//        }
        $totalNumQiArray = array();
        $one = 0;
        $two = 1;
        $three = 2;
        $four = 3;
        $resArray = array();

        $arr = array(
            0,0,0,0,0,0,0,0,0,0
        );

        $errorarr = array(
            0,0,0,0,0,0,0,0,0,0
        );

        $kkk = array();

        $eee = array();

        $num = 0;

        $yingkui = array();

        $kkkuu = 0;

        while(true){
//            if($num > 10) break;
//            $num ++;
            $tmpArray = array();
            $tmpArray["mess"] = array();
            if(($totalNum - $one) < 5)break;
            $firstQi = $data[$one];
            $secondQi = $data[$two];
            $thirdQi = $data[$three];
            $forthQi = $data[$four];
            $matchQi = $data[(int)($four + 1)];
            $totalNumQi .= "'".($forthQi[1] + 1)."',";
            array_push($totalNumQiArray, $forthQi[1] + 1);
            $kkkuu ++;
            $yingkui[$kkkuu] = 0;



            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $forthQi[0]."第".$forthQi[1]."期";
            $tmpModel->one = $forthQi[2];
            $tmpModel->two = $forthQi[3];
            $tmpModel->three = $forthQi[4];
            $tmpModel->four = $forthQi[5];
            $tmpModel->five = $forthQi[6];
            $tmpModel->six = $forthQi[7];
            $tmpModel->seven = $forthQi[8];
            $tmpModel->eight = $forthQi[9];
            $tmpModel->night = $forthQi[10];
            $tmpModel->ten = $forthQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第四期放入到数组的第一位

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $thirdQi[0]."第".$thirdQi[1]."期";
            $tmpModel->one = $thirdQi[2];
            $tmpModel->two = $thirdQi[3];
            $tmpModel->three = $thirdQi[4];
            $tmpModel->four = $thirdQi[5];
            $tmpModel->five = $thirdQi[6];
            $tmpModel->six = $thirdQi[7];
            $tmpModel->seven = $thirdQi[8];
            $tmpModel->eight = $thirdQi[9];
            $tmpModel->night = $thirdQi[10];
            $tmpModel->ten = $thirdQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第三期放入到数组的第一位

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $secondQi[0]."第".$secondQi[1]."期";
            $tmpModel->one = $secondQi[2];
            $tmpModel->two = $secondQi[3];
            $tmpModel->three = $secondQi[4];
            $tmpModel->four = $secondQi[5];
            $tmpModel->five = $secondQi[6];
            $tmpModel->six = $secondQi[7];
            $tmpModel->seven = $secondQi[8];
            $tmpModel->eight = $secondQi[9];
            $tmpModel->night = $secondQi[10];
            $tmpModel->ten = $secondQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第二期放入到数组的第一位

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $firstQi[0]."第".$firstQi[1]."期";
            $tmpModel->one = $firstQi[2];
            $tmpModel->two = $firstQi[3];
            $tmpModel->three = $firstQi[4];
            $tmpModel->four = $firstQi[5];
            $tmpModel->five = $firstQi[6];
            $tmpModel->six = $firstQi[7];
            $tmpModel->seven = $firstQi[8];
            $tmpModel->eight = $firstQi[9];
            $tmpModel->night = $firstQi[10];
            $tmpModel->ten = $firstQi[11];
            array_push($tmpArray["mess"], $tmpModel);//将第二期放入到数组的第一位


//            echo "<br />-------------<br />";
//            var_dump($tmpArray["mess"]);
//            $k = array_reverse($tmpArray["mess"]);
            $yuceRes = $this->getFifthStageByLastFourStage($tmpArray["mess"]);
//            echo "<br />&&&&&&&&&&&&&&&&&&&&<br />";
//            var_dump($yuceRes);
//            echo "<br />***************<br />";
            $rem = array();
            if(!empty($yuceRes)){
                foreach($yuceRes as $k=>$v){
                    $rem[(int)$k] = !isset($rem[(int)$k]) || $rem[(int)$k] == "" ? $v : $rem[(int)$k]."|".$v;
                }
            }
            if(!empty($rem)){
                foreach($rem as $key=>$value){
                    if($key == 0){
                        $rem["one"] = $value;
                    }
                    if($key == 1){
                        $rem["two"] = $value;
                    }
                    if($key == 2){
                        $rem["three"] = $value;
                    }
                    if($key == 3){
                        $rem["four"] = $value;
                    }
                    if($key == 4){
                        $rem["five"] = $value;
                    }
                    if($key == 5){
                        $rem["six"] = $value;
                    }
                    if($key == 6){
                        $rem["seven"] = $value;
                    }
                    if($key == 7){
                        $rem["eight"] = $value;
                    }
                    if($key == 8){
                        $rem["night"] = $value;
                    }
                    if($key == 9){
                        $rem["ten"] = $value;
                    }
                }
            }

            $tmpArray["res"] = $rem;

            $tmpModel = new xyftModel();
            $tmpModel->tremNum = $matchQi[0]."第".$matchQi[1]."期";
            $tmpModel->one = $matchQi[2];
            $tmpModel->two = $matchQi[3];
            $tmpModel->three = $matchQi[4];
            $tmpModel->four = $matchQi[5];
            $tmpModel->five = $matchQi[6];
            $tmpModel->six = $matchQi[7];
            $tmpModel->seven = $matchQi[8];
            $tmpModel->eight = $matchQi[9];
            $tmpModel->night = $matchQi[10];
            $tmpModel->ten = $matchQi[11];
            $tmpArray["match"] = $tmpModel;

            array_push($resArray, $tmpArray);
            $one ++;
            $two ++;
            $three ++;
            $four ++;


            //每个数字的正确数量对比
            if(!empty($tmpArray["res"])){
//                var_dump($tmpArray["res"]);
                $match = $tmpArray["match"];
//                var_dump($match);
                foreach($tmpArray["res"] as $key=>$value){
//                    echo "<br />----".$key."-----".$value."-------<br/>";
                    if($key === "one"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

                                    if($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->one;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }else{
//                                        if($val == 6){
//
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 7;
                                    }
                                }
                            }
                        }else{
//                            echo $tmpArray["res"][$key] ."==". $match->one;
                            if($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 7;
                                }
                            }
                        }
                    }

//                    var_dump($arr);

//                    echo "YYYYYYYYYYYYYYYYYYYY<br />";

                    if($key === "two"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
//


                                    if($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->two;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->one;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->one;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->one;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val-1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 7;
                                    }
                                }
                            }
                        }else{






                            if($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;

                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;

                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 7;
                                }
                            }
                        }
                    }

                    if($key === "three"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
                                    if($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->three;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->two;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 7;
                                    }
                                }
                            }
                        }else{




                            if($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->two;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 7;
                                }
                            }
                        }
                    }

                    if($key === "four"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
//                                    echo $val ."==". $match->four;

                                    if($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->four;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->three;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 7;
                                    }
                                }
                            }
                        }else{



                            if($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->three;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 7;
                                }
                            }
                        }
                    }

                    if($key === "five"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

                                    if($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->five;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->four;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 7;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->four;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 7;
                                }
                            }
                        }
                    }

                    if($key === "six"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){
                                    if($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->six;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->five;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 7;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->five;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 7;
                                }
                            }
                        }
                    }

                    if($key === "seven"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){


                                    if($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->seven;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->four){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->six;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 7;
                                    }
                                }
                            }
                        }else{




                            if($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->four){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->six;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 7;
                                }
                            }
                        }
                    }

                    if($key === "eight"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){


                                    if($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->eight;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->five){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->seven;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 7;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->five){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->seven;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 7;
                                }
                            }
                        }
                    }

                    if($key === "night"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

                                    if($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->night;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->six){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->eight;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 7;
                                    }
                                }
                            }
                        }else{


                            if($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->six){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->eight;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 7;
                                }
                            }
                        }
                    }

                    if($key === "ten"){
                        if(strpos($value, "|")){
                            $ttarray= explode("|", $value);
                            if(!empty($ttarray)){
                                foreach($ttarray as $ke=>$val){

                                    if($val == $match->ten){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "1数字:".$val ."==第一位数字".$match->ten;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->night){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->eight){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->seven){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->one){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "3数字:".$val ."==第十位数字". $match->one;
//                                            echo "<br />*****************<br />";
//                                        }

                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->two){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }elseif($val == $match->three){
//                                        if($val == 6){
//                                            array_push($kkk, $match->tremNum);
//                                            echo "2数字:".$val ."==第二位数字". $match->night;
//                                            echo "<br />*****************<br />";
//                                        }
                                        $arr[$val - 1] ++;
                                        $yingkui[$kkkuu] += 3;
                                    }else{
//                                        if($val == 6){
//                                            echo "error";array_push($eee, $match->tremNum);
//                                            echo "<br />-------------------<br />";
//                                        }
                                        $errorarr[$val-1] ++;
                                        $yingkui[$kkkuu] -= 7;
                                    }
                                }
                            }
                        }else{

                            if($value == $match->ten){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "4数字:".$value ."==第一位数字".$match->ten;
//                                    echo "<br />*****************<br />";
//                                }
//                                echo "*****************<br />";
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->night){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->eight){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->seven){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "5数字:".$value ."==第一位数字".$match->night;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->one){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->two){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }elseif($value == $match->three){
//                                if($value == 6){
//                                    array_push($kkk, $match->tremNum);
//                                    echo "6数字:".$value ."==第十位数字".$match->one;
//                                    echo "<br />*****************<br />";
//                                }
                                $arr[$value-1] ++;
                                $yingkui[$kkkuu] += 3;
                            }else{
                                if($value != ""){
//                                    if($value == 6){
//                                        echo "error";array_push($eee, $match->tremNum);
//                                        echo "<br />-------------------<br />";
//                                    }
                                    $errorarr[$value - 1] ++;
                                    $yingkui[$kkkuu] -= 7;
                                }
                            }
                        }
                    }
                }
            }
        }
//        exit;
//
////        var_dump($resArray);exit;
///
//        foreach($eee as $key=>$value){
//            echo "<br />".$value."<br />";
//        }

        $yingkuiStr = "";
        foreach($yingkui as $k=>$v){
            $yingkuiStr .= $v.",";
        }

        $diejiaRes = array();
        foreach($yingkui as $k=>$v){
            if($k == 1){
                $diejiaRes[$k] = $v;
            }else{
                $diejiaRes[$k] = $v + $diejiaRes[$k-1];
            }
        }
        $diejiaStr = "";
        foreach($diejiaRes as $k=>$v){
            $diejiaStr .= $v.",";
        }

        $ctemplate=new CTemplate("importRes7.html","dafault",__DIR__."/../Template");//测试模板使用


        $ctemplate->render(array("resArray"=>$resArray, "arr"=>$arr, "errorarr"=>$errorarr, "totalNumQi"=>$totalNumQi, "totalNumQiArray"=>$totalNumQiArray, "yingkuiStr"=>$yingkuiStr, "diejiaStr"=>$diejiaStr));
    }

    /**
     * User: tangwei
     * Date: 2019/4/2 17:18
     * Function:显示界面
     */
    public function getFifthStageByLastFourStage($lastFourDate)//$lastFourDate
    {
//        $lastFourDate = xyftModel::getLastFourUndeletedWithOutLimit();//获取到最近四期的数据

//        var_dump($lastFourDate);

        if(count($lastFourDate) != 4){
            echo "数据库至少要有4期数据，才能开始推算";exit;
        }
        $this->thirdTmp = $this->getThirdTheoryByFirstAndSecondStage($lastFourDate[3], $lastFourDate[2]);//用第一期和第二期算出第三期的小范围理论
//        echo "<br />第三期预测如下&&&&&&&&&&&&&&&&&&&<br />";
//        var_dump($this->thirdTmp);
//        echo "<br />&&&&&&&&&&&&&&&&&&&<br />";
        $this->forthTmp = $this->getForthTeoryByFirstAndThirdStage($lastFourDate[3], $lastFourDate[1]);//用第一期和第三期和算出第四期的小范围理论
//        echo "<br />第三期预测如下&&&&&&&&&&&&&&&&&&&<br />";
//        var_dump($this->forthTmp);
//        echo "<br />&&&&&&&&&&&&&&&&&&&<br />";
        $this->fifthTmp = $this->getFifthTheoryBySecondAndThirdAndForthTheory($lastFourDate[2], $lastFourDate[1], $lastFourDate[0]);//用第二期和第三期和第四期开奖和第四期的理论 算出第五期的小范围理论
//        echo "<br />第五期预测如下***********************<br />";
//        var_dump($this->fifthTmp);
//        echo "<br />&&&&&&&&&&&&&&&&&&&<br />";
        return $this->fifthTmp;
    }

    /**
     * User: tangwei
     * Date: 2019/4/2 17:20
     * Function:根据第一期和第二期开奖结果，推算第三期的小范围理论
     */
    public function getThirdTheoryByFirstAndSecondStage($first, $second)
    {
        $firstQi = array();
        array_push($firstQi, $first->one);
        array_push($firstQi, $first->two);
        array_push($firstQi, $first->three);
        array_push($firstQi, $first->four);
        array_push($firstQi, $first->five);
        array_push($firstQi, $first->six);
        array_push($firstQi, $first->seven);
        array_push($firstQi, $first->eight);
        array_push($firstQi, $first->night);
        array_push($firstQi, $first->ten);

        $secondQi = array();
        array_push($secondQi, $second->one);
        array_push($secondQi, $second->two);
        array_push($secondQi, $second->three);
        array_push($secondQi, $second->four);
        array_push($secondQi, $second->five);
        array_push($secondQi, $second->six);
        array_push($secondQi, $second->seven);
        array_push($secondQi, $second->eight);
        array_push($secondQi, $second->night);
        array_push($secondQi, $second->ten);

        $newQi = array();//一个新数组 用于存放第三期理论记录
        if(!empty($secondQi)){
            foreach($secondQi as $key=>$value){
                $tmpIndex = 0;
                $str = "n";
                if($key<= 4){#如果第二期是前驱就置为后驱
                    $tmpIndex = 7;
                }else{#如果第二期是后驱就置为前驱
                    $tmpIndex = 2;
                }

//                echo "数字".$value."位置：".$key."前后互质为：".$tmpIndex."----";
                $pianyiSecond = $this->secondIndex[$key];//获取到第二期的下标
//                echo "第二期下标为：".$pianyiSecond;
                if(!empty($firstQi)){
                    foreach($firstQi as $k=>$v){
                        if($v == $value){
                            $pianyiFirst = $this->firstIndex[$k];//获取到第一期的下标
                        }
                    }
                }
//                echo "第一期下标为：".$pianyiFirst;
//                echo "***************";
                $pianyi = $pianyiFirst + $pianyiSecond;//下标相加，确定左移、右移
//                echo "确定偏移量：".$pianyi;
//                echo "-----------------";
                if($pianyi > 0){//如果大于0，确定是往左移

                    $tmpIndex = $tmpIndex - $pianyi;
                    if($tmpIndex < 0){
                        $tmpIndex = $tmpIndex + 10;
                        if(!array_key_exists($tmpIndex, $newQi)){
                            $newQi[$tmpIndex] = $value;
                        }else{
                            $tmpIndex = $tmpIndex.$str;
                            if(!array_key_exists($tmpIndex, $newQi)){
                                $newQi[$tmpIndex] = $value;
                            }else{
                                $tmpIndex = $tmpIndex.$str;
                                if(!array_key_exists($tmpIndex, $newQi)){
                                    $newQi[$tmpIndex] = $value;
                                }else{
                                    $tmpIndex = $tmpIndex.$str;
                                    if(!array_key_exists($tmpIndex, $newQi)){
                                        $newQi[$tmpIndex] = $value;
                                    }else{
                                        $tmpIndex = $tmpIndex.$str;
                                        if(!array_key_exists($tmpIndex, $newQi)){
                                            $newQi[$tmpIndex] = $value;
                                        }else{
                                            $tmpIndex = $tmpIndex.$str;
                                            $newQi[$tmpIndex] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        if(!array_key_exists($tmpIndex, $newQi)){
                            $newQi[$tmpIndex] = $value;
                        }else{
                            $tmpIndex = $tmpIndex.$str;
                            if(!array_key_exists($tmpIndex, $newQi)){
                                $newQi[$tmpIndex] = $value;
                            }else{
                                $tmpIndex = $tmpIndex.$str;
                                if(!array_key_exists($tmpIndex, $newQi)){
                                    $newQi[$tmpIndex] = $value;
                                }else{
                                    $tmpIndex = $tmpIndex.$str;
                                    if(!array_key_exists($tmpIndex, $newQi)){
                                        $newQi[$tmpIndex] = $value;
                                    }else{
                                        $tmpIndex = $tmpIndex.$str;
                                        if(!array_key_exists($tmpIndex, $newQi)){
                                            $newQi[$tmpIndex] = $value;
                                        }else{
                                            $tmpIndex = $tmpIndex.$str;
                                            $newQi[$tmpIndex] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }elseif($pianyi < 0){//如果小于0，确定是往右移动
                    $tmpIndex = $tmpIndex - $pianyi;
                    if($tmpIndex > 9){
                        $tmpIndex = $tmpIndex - 10;
                        if(!array_key_exists($tmpIndex, $newQi)){
                            $newQi[$tmpIndex] = $value;
                        }else{
                            $tmpIndex = $tmpIndex.$str;
                            if(!array_key_exists($tmpIndex, $newQi)){
                                $newQi[$tmpIndex] = $value;
                            }else{
                                $tmpIndex = $tmpIndex.$str;
                                if(!array_key_exists($tmpIndex, $newQi)){
                                    $newQi[$tmpIndex] = $value;
                                }else{
                                    $tmpIndex = $tmpIndex.$str;
                                    if(!array_key_exists($tmpIndex, $newQi)){
                                        $newQi[$tmpIndex] = $value;
                                    }else{
                                        $tmpIndex = $tmpIndex.$str;
                                        if(!array_key_exists($tmpIndex, $newQi)){
                                            $newQi[$tmpIndex] = $value;
                                        }else{
                                            $tmpIndex = $tmpIndex.$str;
                                            $newQi[$tmpIndex] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        if(!array_key_exists($tmpIndex, $newQi)){
                            $newQi[$tmpIndex] = $value;
                        }else{
                            $tmpIndex = $tmpIndex.$str;
                            if(!array_key_exists($tmpIndex, $newQi)){
                                $newQi[$tmpIndex] = $value;
                            }else{
                                $tmpIndex = $tmpIndex.$str;
                                if(!array_key_exists($tmpIndex, $newQi)){
                                    $newQi[$tmpIndex] = $value;
                                }else{
                                    $tmpIndex = $tmpIndex.$str;
                                    if(!array_key_exists($tmpIndex, $newQi)){
                                        $newQi[$tmpIndex] = $value;
                                    }else{
                                        $tmpIndex = $tmpIndex.$str;
                                        if(!array_key_exists($tmpIndex, $newQi)){
                                            $newQi[$tmpIndex] = $value;
                                        }else{
                                            $tmpIndex = $tmpIndex.$str;
                                            $newQi[$tmpIndex] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }else{
                    if(!array_key_exists($tmpIndex, $newQi)){
                        $newQi[$tmpIndex] = $value;
                    }else{
                        $tmpIndex = $tmpIndex.$str;
                        if(!array_key_exists($tmpIndex, $newQi)){
                            $newQi[$tmpIndex] = $value;
                        }else{
                            $tmpIndex = $tmpIndex.$str;
                            if(!array_key_exists($tmpIndex, $newQi)){
                                $newQi[$tmpIndex] = $value;
                            }else{
                                $tmpIndex = $tmpIndex.$str;
                                if(!array_key_exists($tmpIndex, $newQi)){
                                    $newQi[$tmpIndex] = $value;
                                }else{
                                    $tmpIndex = $tmpIndex.$str;
                                    if(!array_key_exists($tmpIndex, $newQi)){
                                        $newQi[$tmpIndex] = $value;
                                    }else{
                                        $tmpIndex = $tmpIndex.$str;
                                        $newQi[$tmpIndex] = $value;
                                    }
                                }
                            }
                        }
                    }
                }

//                echo "得到数字下标：".$tmpIndex."<br />";
            }
        }
        ksort($newQi);
//        echo "*************************************************************************<br /><br /><br /><br /><br />";
        return $newQi;
    }

    /**
     * User: tangwei
     * Date: 2019/4/2 17:27
     * @param $secondTheory
     * Function:根据第一期和第三期开奖，对比第三期的小范围理论结果，推算第四期的小范围理论
     */
    public function getForthTeoryByFirstAndThirdStage($first, $third)
    {

        $str = "n";
        $firstQi = array();
        array_push($firstQi, $first->one);
        array_push($firstQi, $first->two);
        array_push($firstQi, $first->three);
        array_push($firstQi, $first->four);
        array_push($firstQi, $first->five);
        array_push($firstQi, $first->six);
        array_push($firstQi, $first->seven);
        array_push($firstQi, $first->eight);
        array_push($firstQi, $first->night);
        array_push($firstQi, $first->ten);

        $thirdQi = array();
        array_push($thirdQi, $third->one);
        array_push($thirdQi, $third->two);
        array_push($thirdQi, $third->three);
        array_push($thirdQi, $third->four);
        array_push($thirdQi, $third->five);
        array_push($thirdQi, $third->six);
        array_push($thirdQi, $third->seven);
        array_push($thirdQi, $third->eight);
        array_push($thirdQi, $third->night);
        array_push($thirdQi, $third->ten);

        $newQi = array();//一个新数组 用于存放第四期理论记录
        if(!empty($thirdQi)){
            foreach($thirdQi as $key=>$value){
                $tmpIndex = 0;
                $str = "n";
                if($key<= 4){#如果第三期是前驱就置为后驱
                    $tmpIndex = 7;
                }else{#如果第三期是后驱就置为前驱
                    $tmpIndex = 2;
                }

//                echo "数字".$value."位置为：".$key."前后互质为：".$tmpIndex."----";
                $pianyiSecond = $this->thirdIndex[$key];//获取到第三期的下标
//                echo "第三期位置为：".$pianyiSecond;
                if(!empty($firstQi)){
                    foreach($firstQi as $k=>$v){
                        if($v == $value){
                            $pianyiFirst = $this->firstIndex[$k];//获取到第一期的下标
                        }
                    }
                }
//                echo "第一期位置为：".$pianyiFirst;
//                echo "-----------";
                $pianyi = $pianyiFirst + $pianyiSecond;//下标相加，确定左移、右移
//                echo "确定偏移量：".$pianyi."........";
                if($pianyi > 0){//如果大于0，确定是往左移

                    $tmpIndex = $tmpIndex - $pianyi;
                    if($tmpIndex < 0){
                        $tmpIndex = $tmpIndex + 10;
                        if(!array_key_exists($tmpIndex, $newQi)){
                            $newQi[$tmpIndex] = $value;
                        }else{
                            $tmpIndex = $tmpIndex.$str;
                            if(!array_key_exists($tmpIndex, $newQi)){
                                $newQi[$tmpIndex] = $value;
                            }else{
                                $tmpIndex = $tmpIndex.$str;
                                if(!array_key_exists($tmpIndex, $newQi)){
                                    $newQi[$tmpIndex] = $value;
                                }else{
                                    $tmpIndex = $tmpIndex.$str;
                                    if(!array_key_exists($tmpIndex, $newQi)){
                                        $newQi[$tmpIndex] = $value;
                                    }else{
                                        $tmpIndex = $tmpIndex.$str;
                                        if(!array_key_exists($tmpIndex, $newQi)){
                                            $newQi[$tmpIndex] = $value;
                                        }else{
                                            $tmpIndex = $tmpIndex.$str;
                                            $newQi[$tmpIndex] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        if(!array_key_exists($tmpIndex, $newQi)){
                            $newQi[$tmpIndex] = $value;
                        }else{
                            $tmpIndex = $tmpIndex.$str;
                            if(!array_key_exists($tmpIndex, $newQi)){
                                $newQi[$tmpIndex] = $value;
                            }else{
                                $tmpIndex = $tmpIndex.$str;
                                if(!array_key_exists($tmpIndex, $newQi)){
                                    $newQi[$tmpIndex] = $value;
                                }else{
                                    $tmpIndex = $tmpIndex.$str;
                                    if(!array_key_exists($tmpIndex, $newQi)){
                                        $newQi[$tmpIndex] = $value;
                                    }else{
                                        $tmpIndex = $tmpIndex.$str;
                                        if(!array_key_exists($tmpIndex, $newQi)){
                                            $newQi[$tmpIndex] = $value;
                                        }else{
                                            $tmpIndex = $tmpIndex.$str;
                                            $newQi[$tmpIndex] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }elseif($pianyi < 0){//如果小于0，确定是往右移动
                    $tmpIndex = $tmpIndex - $pianyi;
                    if($tmpIndex > 9){
                        $tmpIndex = $tmpIndex - 10;
                        if(!array_key_exists($tmpIndex, $newQi)){
                            $newQi[$tmpIndex] = $value;
                        }else{
                            $tmpIndex = $tmpIndex.$str;
                            if(!array_key_exists($tmpIndex, $newQi)){
                                $newQi[$tmpIndex] = $value;
                            }else{
                                $tmpIndex = $tmpIndex.$str;
                                if(!array_key_exists($tmpIndex, $newQi)){
                                    $newQi[$tmpIndex] = $value;
                                }else{
                                    $tmpIndex = $tmpIndex.$str;
                                    if(!array_key_exists($tmpIndex, $newQi)){
                                        $newQi[$tmpIndex] = $value;
                                    }else{
                                        $tmpIndex = $tmpIndex.$str;
                                        if(!array_key_exists($tmpIndex, $newQi)){
                                            $newQi[$tmpIndex] = $value;
                                        }else{
                                            $tmpIndex = $tmpIndex.$str;
                                            $newQi[$tmpIndex] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        if(!array_key_exists($tmpIndex, $newQi)){
                            $newQi[$tmpIndex] = $value;
                        }else{
                            $tmpIndex = $tmpIndex.$str;
                            if(!array_key_exists($tmpIndex, $newQi)){
                                $newQi[$tmpIndex] = $value;
                            }else{
                                $tmpIndex = $tmpIndex.$str;
                                if(!array_key_exists($tmpIndex, $newQi)){
                                    $newQi[$tmpIndex] = $value;
                                }else{
                                    $tmpIndex = $tmpIndex.$str;
                                    if(!array_key_exists($tmpIndex, $newQi)){
                                        $newQi[$tmpIndex] = $value;
                                    }else{
                                        $tmpIndex = $tmpIndex.$str;
                                        if(!array_key_exists($tmpIndex, $newQi)){
                                            $newQi[$tmpIndex] = $value;
                                        }else{
                                            $tmpIndex = $tmpIndex.$str;
                                            $newQi[$tmpIndex] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }else{
                    if(!array_key_exists($tmpIndex, $newQi)){
                        $newQi[$tmpIndex] = $value;
                    }else{
                        $tmpIndex = $tmpIndex.$str;
                        if(!array_key_exists($tmpIndex, $newQi)){
                            $newQi[$tmpIndex] = $value;
                        }else{
                            $tmpIndex = $tmpIndex.$str;
                            if(!array_key_exists($tmpIndex, $newQi)){
                                $newQi[$tmpIndex] = $value;
                            }else{
                                $tmpIndex = $tmpIndex.$str;
                                if(!array_key_exists($tmpIndex, $newQi)){
                                    $newQi[$tmpIndex] = $value;
                                }else{
                                    $tmpIndex = $tmpIndex.$str;
                                    if(!array_key_exists($tmpIndex, $newQi)){
                                        $newQi[$tmpIndex] = $value;
                                    }else{
                                        $tmpIndex = $tmpIndex.$str;
                                        $newQi[$tmpIndex] = $value;
                                    }
                                }
                            }
                        }
                    }
                }
//                echo "确定位置：".$tmpIndex."<br />";


            }
        }
//        echo "---------------------------------<br />";
        ksort($newQi);
        $notJoin = array();
        foreach($thirdQi as $key=>$value){//开始对比第三期的开奖与第三期的小范围理论
            foreach($this->thirdTmp as $k=>$v){
                if($v == $value){
//                    echo "第三期开奖数字：".$value."位置为：".$key."-----"."第三期小范围理论数字：".$v."位置为：".$k."<br />";
                    if(($key-(int)$k) > 2 || ($key-(int)$k) < -2){//如果间隔超过2位，该数字不参与第四期推算
//                        echo $key."&&&&&".(int)$k."<br />";
                        $key = $key + 10;
                        if(($key-$k) > 2){
                            array_push($notJoin, $value);
                        }

                    }
                }
            }
        }
        if(!empty($notJoin)){//去掉第三期开奖与小范围理论超过2个距离的数字
            foreach($notJoin as $key=>$value){
                foreach($newQi as $k=>$v){
                    if($value == $v){
                        unset($newQi[$k]);
                    }
                }
            }
        }
//        var_dump($newQi);echo "<br />";


        $yidong = array();
        foreach($thirdQi as $key=>$value){//开始对比第三期的开奖与第三期的小范围理论，这儿是为了确定平移的数据
            foreach($this->thirdTmp as $k=>$v){
                if($v == $value){
                    $tmp = array();
                    if(($key-(int)$k) > 2 || ($key-(int)$k) < -2){//如果间隔超过2位，该数字不参与第四期推算
                        $key = $key + 10;
                        if(($key-(int)$k) <= 2){//如果间隔没有超过2位
                            if((int)$k==$key){//如果位置重合了，那么就左移5位
                                $tmp["value"] = $value;
                                $tmp["pingyi"] = 5;
                            }else{
                                if((int)$k-$key > 0){//理论在实际的右边
                                    $tmp["value"] = $value;
                                    $tmp["pingyi"] = (int)$k - $key + 5;
                                }else{//理论在实际的左边
                                    $tmp["value"] = $value;
                                    $tmp["pingyi"] = (int)$k - $key - 5;
                                }
                            }
                            array_push($yidong, $tmp);
                        }
                    }else{//如果间隔没有超过2位
                        if((int)$k==$key){//如果位置重合了，那么就左移5位
                            $tmp["value"] = $value;
                            $tmp["pingyi"] = 5;
                        }else{//如果位置不重合，那么需要知道具体位置
                            if((int)$k-$key > 0){//理论在实际的右边
                                $tmp["value"] = $value;
                                $tmp["pingyi"] = (int)$k - $key + 5;
                            }else{//理论在实际的左边
                                $tmp["value"] = $value;
                                $tmp["pingyi"] = (int)$k - $key - 5;
                            }
                        }
                        array_push($yidong, $tmp);
                    }
                }
            }
        }
        $resQi = array();
        foreach($newQi as $key=>$value){//开始处理数据平移
            if(!empty($yidong)){
                foreach($yidong as $k=>$v){
                    if($v["value"] == $value){
//                        echo $v["value"] ."==". $value."<br />";
                        $newIndex = (int)$key - $v["pingyi"];
                        if($value == "09"){
//                            echo $newIndex;
                        }

                        if($newIndex < 0){
                            $newIndex = $newIndex + 10;
                            if(!array_key_exists($newIndex, $resQi)){
                                $resQi[$newIndex] = $value;
                            }else{
                                $newIndex = $newIndex.$str;
                                if(!array_key_exists($newIndex, $resQi)){
                                    $resQi[$newIndex] = $value;
                                }else{
                                    $newIndex = $newIndex.$str;
                                    if(!array_key_exists($newIndex, $resQi)){
                                        $resQi[$newIndex] = $value;
                                    }else{
                                        $newIndex = $newIndex.$str;
                                        $newIndex = $newIndex.$str;
                                        if(!array_key_exists($newIndex, $resQi)){
                                            $resQi[$newIndex] = $value;
                                        }else{
                                            $newIndex = $newIndex.$str;
                                            if(!array_key_exists($newIndex, $resQi)){
                                                $resQi[$newIndex] = $value;
                                            }else{
                                                $newIndex = $newIndex.$str;
                                                $resQi[$newIndex] = $value;
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif($newIndex > 9){
                            $newIndex = $newIndex - 10;
                            if(!array_key_exists($newIndex, $resQi)){
                                $resQi[$newIndex] = $value;
                            }else{
                                $newIndex = $newIndex.$str;
                                if(!array_key_exists($newIndex, $resQi)){
                                    $resQi[$newIndex] = $value;
                                }else{
                                    $newIndex = $newIndex.$str;
                                    if(!array_key_exists($newIndex, $resQi)){
                                        $resQi[$newIndex] = $value;
                                    }else{
                                        $newIndex = $newIndex.$str;
                                        $resQi[$newIndex] = $value;
                                    }
                                }
                            }
                        }else{
                            if(!array_key_exists($newIndex, $resQi)){
                                $resQi[$newIndex] = $value;
                            }else{
                                $newIndex = $newIndex.$str;
                                if(!array_key_exists($newIndex, $resQi)){
                                    $resQi[$newIndex] = $value;
                                }else{
                                    $newIndex = $newIndex.$str;
                                    if(!array_key_exists($newIndex, $resQi)){
                                        $resQi[$newIndex] = $value;
                                    }else{
                                        $newIndex = $newIndex.$str;
                                        if(!array_key_exists($newIndex, $resQi)){
                                            $resQi[$newIndex] = $value;
                                        }else{
                                            $newIndex = $newIndex.$str;
                                            if(!array_key_exists($newIndex, $resQi)){
                                                $resQi[$newIndex] = $value;
                                            }else{
                                                $newIndex = $newIndex.$str;
                                                $resQi[$newIndex] = $value;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        ksort($resQi);

//        var_dump($resQi);

        return $resQi;
    }

    /**
     * User: tangwei
     * Date: 2019/4/2 17:30
     * @param $forthTheory
     * Function:根据第二期的开奖、第三期的开奖、第四期的小范围理论结果，推算第五期的小范围理论
     */
    public function getFifthTheoryBySecondAndThirdAndForthTheory($second, $third, $fouth)
    {
//        echo "<br />";
        $str = "n";
        $secondQi = array();
        array_push($secondQi, $second->one);
        array_push($secondQi, $second->two);
        array_push($secondQi, $second->three);
        array_push($secondQi, $second->four);
        array_push($secondQi, $second->five);
        array_push($secondQi, $second->six);
        array_push($secondQi, $second->seven);
        array_push($secondQi, $second->eight);
        array_push($secondQi, $second->night);
        array_push($secondQi, $second->ten);

//        echo "第二期开奖：";
//        var_dump($secondQi);
//        echo "<br />---------------------------------------------<br />";

        $thirdQi = array();
        array_push($thirdQi, $third->one);
        array_push($thirdQi, $third->two);
        array_push($thirdQi, $third->three);
        array_push($thirdQi, $third->four);
        array_push($thirdQi, $third->five);
        array_push($thirdQi, $third->six);
        array_push($thirdQi, $third->seven);
        array_push($thirdQi, $third->eight);
        array_push($thirdQi, $third->night);
        array_push($thirdQi, $third->ten);
//        echo "第三期开奖：";
//        var_dump($thirdQi);
//        echo "<br />---------------------------------------------<br />";

        $fouthQi = array();
        array_push($fouthQi, $fouth->one);
        array_push($fouthQi, $fouth->two);
        array_push($fouthQi, $fouth->three);
        array_push($fouthQi, $fouth->four);
        array_push($fouthQi, $fouth->five);
        array_push($fouthQi, $fouth->six);
        array_push($fouthQi, $fouth->seven);
        array_push($fouthQi, $fouth->eight);
        array_push($fouthQi, $fouth->night);
        array_push($fouthQi, $fouth->ten);
//        echo "第四期开奖：";
//        var_dump($fouthQi);
//        echo "<br />---------------------------------------------<br />";

        $newQi = array();//一个新数组 用于存放第五期理论记录
        if(!empty($thirdQi)){
            foreach($thirdQi as $key=>$value){
                if(!empty($secondQi)){
                    foreach ($secondQi as $k=>$v){
                        if(!empty($this->forthTmp)){
                            foreach($this->forthTmp as $ke=>$val){
                                if($value == $v && $v == (int)$val){
                                    $tmpIndex = $this->useThisToGetFifth[$key] + $this->useThisToGetFifth[$k] + $this->useThisToGetFifth[(int)$ke];//将第二期开奖、第三期开奖、第四期的理论下标相加
//                                    echo "当前数字为：".$value."在第三期的下标得分为".$this->useThisToGetFifth[$key]."，在第二期的下标得分为：".$this->useThisToGetFifth[$k]."，在第四期的理论下标得分为：".$this->useThisToGetFifth[(int)$ke]."，所以得到的下标得分为：".$tmpIndex."<br />";
                                    if($tmpIndex != 0){//如果下标为0则改数字不参与推算，不等于0才参与推算
                                        $tmpIndex = -$tmpIndex;//互为倒置，比如 如果得到4 那么改为-4
//                                        echo "当前倒置为:".$tmpIndex."<br />";
                                        if($tmpIndex > 5){//如果下标大于5
                                            $tmpIndex = $tmpIndex - 10 -1;
                                        }elseif($tmpIndex < -5){//如果下标小于-5
                                            $tmpIndex = $tmpIndex + 10 + 1;
                                        }else{
                                            $tmpIndex = $tmpIndex;
                                        }
                                        $tmpIndex = array_search($tmpIndex, $this->useThisToGetFifth);
                                        if(!array_key_exists($tmpIndex, $newQi)){
                                            $newQi[$tmpIndex] = $value;
                                        }else{
                                            $newQi[$tmpIndex.$str] = $value;
                                        }
                                    }

                                }
                            }
                        }
                    }
                }
            }
        }
        ksort($newQi);
//        echo "<br />";
//        var_dump($newQi);
//        echo "<br />";
//        echo "-----------------------------------------------<br />";

        $yidong = array();
        foreach($fouthQi as $key=>$value){//开始对比第四期的开奖与第四期的小范围理论，这儿是为了确定平移的数据
            if(!empty($this->forthTmp)){
                foreach($this->forthTmp as $k=>$v){
                    if($v == $value){
//                        echo "第四期的数字为：".$value."下标为：".$key.";第四期的理论数字为：".$v."下标为：".$k."<br />";
                        $tmp = array();
                        if((int)$k > $key){//理论在实际的右边，那么需要判断是在什么范围
                            if((int)$k - $key > 5){
//                                echo "理论在实际的右边，需移动：".
                                    $tmp["pingyi"] = 5 - ((int)$k - $key);
                                $tmp["value"] = $value;
                            }elseif((int)$k - $key == 5){
//                                echo "理论在实际的右边，需移动：".
                                    $tmp["pingyi"] = 0;
                                $tmp["value"] = $value;
                            }else{

//                                echo "理论在实际的右边，需移动：".
                                    $tmp["pingyi"] = 5 - ((int)$k - $key);
                                $tmp["value"] = $value;
                            }
                        }else{//如果理论在实际的左边，那么也需要判断在什么范围
                            if((int)$k - $key < -5){
//                                echo "理论在实际的左边，需移动：".
                                    $tmp["pingyi"] = 5 - ((int)$k - $key);
                                $tmp["value"] = $value;
                            }elseif((int)$k - $key == -5){
//                                echo "理论在实际的左边，需移动：".
                                    $tmp["pingyi"] = 0;
                                $tmp["value"] = $value;
                            }else{
//                                echo "理论在实际的左边，需移动：".
                                    $tmp["pingyi"] = -5 - ((int)$k - $key);
                                $tmp["value"] = $value;
                            }
                        }
//                        echo "<br />";
                        array_push($yidong, $tmp);
                    }
                }
            }
        }
//        var_dump($newQi);
//        echo "<br />";
        $resQi = array();
        foreach($newQi as $key=>$value){//开始处理数据平移
            if(!empty($yidong)){
                foreach($yidong as $k=>$v){
                    if($v["value"] == $value){
//                        echo $v["value"] ."==". $value."<br />";
                        $newIndex = (int)$key - $v["pingyi"];
                        if($newIndex < 0){
                            $newIndex = $newIndex + 10;
                            if($newIndex < 0){
                                $newIndex = $newIndex + 10;
                            }
                            if(!array_key_exists($newIndex, $resQi)){
                                $resQi[$newIndex] = $value;
                            }else{
                                $resQi[$newIndex.$str] = $value;
                            }
                        } elseif($newIndex > 9){
                            $newIndex = $newIndex - 10;
                            if($newIndex > 9){
                                $newIndex = $newIndex - 10;
                            }
                            if(!array_key_exists($newIndex, $resQi)){
                                $resQi[$newIndex] = $value;
                            }else{
                                $resQi[$newIndex.$str] = $value;
                            }
                        }else{
                            if(!array_key_exists($newIndex, $resQi)){
                                $resQi[$newIndex] = $value;
                            }else{
                                $resQi[$newIndex.$str] = $value;
                            }
                        }
                    }
                }
            }
        }
        ksort($resQi);
//        echo "最终结果：:";
//        var_dump($resQi);
        return $resQi;
    }




    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:新增数据的界面显示
     */
    public function addxyft()
    {
        $ctemplate=new CTemplate("xyft/add.html","dafault",__DIR__."/../Template");
        $ctemplate->render(array());
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:新增数据的逻辑操作
     */
    public function doaddxyft()
    {
        if(!isset($_POST["termNum"]) || !is_numeric($_POST["termNum"]) || $_POST["termNum"] <= 0 )$this->errorBack("termNum correct type");
        $termNum = $this->filter($_POST["termNum"]);
        if(!isset($_POST["one"]) || !is_numeric($_POST["one"]) || $_POST["one"] <= 0 )$this->errorBack("one correct type");
        $one = $this->filter($_POST["one"]);
        if(!isset($_POST["two"]) || !is_numeric($_POST["two"]) || $_POST["two"] <= 0 )$this->errorBack("two correct type");
        $two = $this->filter($_POST["two"]);
        if(!isset($_POST["three"]) || !is_numeric($_POST["three"]) || $_POST["three"] <= 0 )$this->errorBack("three correct type");
        $three = $this->filter($_POST["three"]);
        if(!isset($_POST["four"]) || !is_numeric($_POST["four"]) || $_POST["four"] <= 0 )$this->errorBack("four correct type");
        $four = $this->filter($_POST["four"]);
        if(!isset($_POST["five"]) || !is_numeric($_POST["five"]) || $_POST["five"] <= 0 )$this->errorBack("five correct type");
        $five = $this->filter($_POST["five"]);
        if(!isset($_POST["six"]) || !is_numeric($_POST["six"]) || $_POST["six"] <= 0 )$this->errorBack("six correct type");
        $six = $this->filter($_POST["six"]);
        if(!isset($_POST["seven"]) || !is_numeric($_POST["seven"]) || $_POST["seven"] <= 0 )$this->errorBack("seven correct type");
        $seven = $this->filter($_POST["seven"]);
        if(!isset($_POST["eight"]) || !is_numeric($_POST["eight"]) || $_POST["eight"] <= 0 )$this->errorBack("eight correct type");
        $eight = $this->filter($_POST["eight"]);
        if(!isset($_POST["night"]) || !is_numeric($_POST["night"]) || $_POST["night"] <= 0 )$this->errorBack("night correct type");
        $night = $this->filter($_POST["night"]);
        if(!isset($_POST["ten"]) || !is_numeric($_POST["ten"]) || $_POST["ten"] <= 0 )$this->errorBack("ten correct type");
        $ten = $this->filter($_POST["ten"]);
        $xyftModel = new xyftModel();
        $xyftModel->termNum = $termNum;
        $xyftModel->addTime = date("Y-m-d H:i:s");
        $xyftModel->one = $one;
        $xyftModel->two = $two;
        $xyftModel->three = $three;
        $xyftModel->four = $four;
        $xyftModel->five = $five;
        $xyftModel->six = $six;
        $xyftModel->seven = $seven;
        $xyftModel->eight = $eight;
        $xyftModel->night = $night;
        $xyftModel->ten = $ten;
        $xyftModel->isDeleted = 0;
        $res = $xyftModel->addOne();
        if(!empty($res))$this->jump("新增成功", "/xyftList");
        $this->jump("新增失败", "/addxyft");
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:编辑数据的界面显示
     */
    public function editxyft()
    {
        if (!isset($_GET["id"]) || !is_numeric($_GET["id"]) || $_GET["id"] <= 0) $this->errorBack("参数错误");
        $id = $this->filter($_GET["id"]);
        $xyftDate = xyftModel::getOneById($id);
        if(empty($xyftDate))$this->errorBack("不存在该数据或已被删除");
        $xyftDate = $xyftDate[0];
        $ctemplate=new CTemplate("xyft/edit.html","dafault",__DIR__."/../Template");
        $ctemplate->render(array("xyftDate"=>$xyftDate));
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:编辑数据的逻辑操作
     */
    public function doeditxyft()
    {
        if (!isset($_GET["id"]) || !is_numeric($_GET["id"]) || $_GET["id"] <= 0) $this->errorBack("参数错误");
        $id = $this->filter($_GET["id"]);
        if(!isset($_POST["termNum"]) || !is_numeric($_POST["termNum"]) || $_POST["termNum"] <= 0 )$this->errorBack("termNum correct type");
        $termNum = $this->filter($_POST["termNum"]);
        if(!isset($_POST["one"]) || !is_numeric($_POST["one"]) || $_POST["one"] <= 0 )$this->errorBack("one correct type");
        $one = $this->filter($_POST["one"]);
        if(!isset($_POST["two"]) || !is_numeric($_POST["two"]) || $_POST["two"] <= 0 )$this->errorBack("two correct type");
        $two = $this->filter($_POST["two"]);
        if(!isset($_POST["three"]) || !is_numeric($_POST["three"]) || $_POST["three"] <= 0 )$this->errorBack("three correct type");
        $three = $this->filter($_POST["three"]);
        if(!isset($_POST["four"]) || !is_numeric($_POST["four"]) || $_POST["four"] <= 0 )$this->errorBack("four correct type");
        $four = $this->filter($_POST["four"]);
        if(!isset($_POST["five"]) || !is_numeric($_POST["five"]) || $_POST["five"] <= 0 )$this->errorBack("five correct type");
        $five = $this->filter($_POST["five"]);
        if(!isset($_POST["six"]) || !is_numeric($_POST["six"]) || $_POST["six"] <= 0 )$this->errorBack("six correct type");
        $six = $this->filter($_POST["six"]);
        if(!isset($_POST["seven"]) || !is_numeric($_POST["seven"]) || $_POST["seven"] <= 0 )$this->errorBack("seven correct type");
        $seven = $this->filter($_POST["seven"]);
        if(!isset($_POST["eight"]) || !is_numeric($_POST["eight"]) || $_POST["eight"] <= 0 )$this->errorBack("eight correct type");
        $eight = $this->filter($_POST["eight"]);
        if(!isset($_POST["night"]) || !is_numeric($_POST["night"]) || $_POST["night"] <= 0 )$this->errorBack("night correct type");
        $night = $this->filter($_POST["night"]);
        if(!isset($_POST["ten"]) || !is_numeric($_POST["ten"]) || $_POST["ten"] <= 0 )$this->errorBack("ten correct type");
        $ten = $this->filter($_POST["ten"]);
        $xyftDate = xyftModel::getOneById($id);
        if(empty($xyftDate))$this->errorBack("不存在该数据或已被删除");
        $xyftDate = $xyftDate[0];
        $xyftDate->termNum = $termNum;
        $xyftDate->one = $one;
        $xyftDate->two = $two;
        $xyftDate->three = $three;
        $xyftDate->four = $four;
        $xyftDate->five = $five;
        $xyftDate->six = $six;
        $xyftDate->seven = $seven;
        $xyftDate->eight = $eight;
        $xyftDate->night = $night;
        $xyftDate->ten = $ten;
        $res = $xyftDate->editOne();
        $this->jump("编辑成功", "/xyftList");
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:删除一条数据的逻辑操作
     */
    public function deletexyft()
    {
        if (!isset($_GET["id"]) || !is_numeric($_GET["id"]) || $_GET["id"] <= 0) $this->errorBack("参数错误");
        $id = $this->filter($_GET["id"]);
        $xyftDate = xyftModel::getOneById($id);
        if(empty($xyftDate))$this->errorBack("不存在该数据或已被删除");
        $xyftDate = $xyftDate[0];
        $res = $xyftDate->deleteUpdateOne();
        if(!empty($res))$this->jump("操作成功", "/xyftList");
        $this->jump("操作失败", "/xyftList");
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:显示列表页面
     */
    public function xyftList()
    {
        $allxyft = xyftModel::getAllUndeletedWithOutLimit();
        $allNum = count($allxyft);
        $page = new Page($allNum,self::$PAGESIZE);
        $allxyftDate = xyftModel::getAllUndeletedWithLimit($page->limit);
        $ctemplate=new CTemplate("articleCate/list.html","dafault",__DIR__."/../Template");
        $allxyftDate = $allxyftDate[0];
        $ctemplate->render(array("allxyftDate"=>$allxyftDate, "page"=>$page->showpage()));
    }

}