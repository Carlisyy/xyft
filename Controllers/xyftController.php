<?php 
namespace Controllers;
use Libs\Controller;
use Libs\CTemplate;
use Libs\Page;
use Models\xyftModel;
use phpDocumentor\Reflection\Types\Array_;

class xyftController extends Controller
{

    public $firstIndex = array(-2, -2, -1, -1, 0, 0, 1, 1, 2, 2);
    public $secondIndex = array(-1, 0, 1, 2, 3, -3, -2, -1, 0 ,1);
    public $thirdIndex = array(-1, 0, 1, 2, 3, -3, -2, -1, 0 ,1);

    public $useThisToGetFifth = array(5,4,3,2,1,-1,-2,-3,-4,-5);
    public $forthIndex = array(5, 4, 3, 2, 1, -1, -2, -3, -4, -5);

    /**
     * User: tangwei
     * Date: 2019/4/22 16:55
     * Function:导入数据的界面显示
     */
    public function importDate()
    {

    }

    /**
     * User: tangwei
     * Date: 2019/4/22 17:07
     * Function:获取导入数据的结果
     */
    public function getImportDateRes()
    {

    }

    /**
     * User: tangwei
     * Date: 2019/4/2 17:18
     * Function:显示界面
     */
    public function getFifthStageByLastFourStage()
    {
        $lastFourDate = xyftModel::getLastFourUndeletedWithOutLimit();//获取到最近四期的数据
//        var_dump($lastFourDate);
        if(count($lastFourDate) != 4){
            echo "数据库至少要有4期数据，才能开始推算";exit;
        }
        $this->thirdTmp = $this->getThirdTheoryByFirstAndSecondStage($lastFourDate[3], $lastFourDate[2]);//用第一期和第二期算出第三期的小范围理论
//        var_dump($this->thirdTmp);

        $this->forthTmp = $this->getForthTeoryByFirstAndThirdStage($lastFourDate[3], $lastFourDate[1]);//用第一期和第三期和算出第四期的小范围理论

//        var_dump($this->forthTmp);

        $this->fifthTmp = $this->getFifthTheoryBySecondAndThirdAndForthTheory($lastFourDate[2], $lastFourDate[1], $lastFourDate[0]);//用第二期和第三期和第四期开奖和第四期的理论 算出第五期的小范围理论
        var_dump($this->fifthTmp);
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

        $newQi = array();//一个新数组 用于存放第五期理论记录
        if(!empty($thirdQi)){
            foreach($thirdQi as $key=>$value){
                if(!empty($secondQi)){
                    foreach ($secondQi as $k=>$v){
                        if(!empty($this->forthTmp)){
                            foreach($this->forthTmp as $ke=>$val){
                                if($value == $v && $v == (int)$val){
                                    $tmpIndex = $this->useThisToGetFifth[$key] + $this->useThisToGetFifth[$k] + $this->useThisToGetFifth[(int)$ke];//将第二期开奖、第三期开奖、第四期的理论下标相加
//                                    echo "当前数字为：".$value."在第三期的下标得分为".$this->useThisToGetFifth[$key]."在第二期的下标得分为：".$this->useThisToGetFifth[$k]."在第四期的理论下标得分为：".$this->useThisToGetFifth[(int)$ke]."，所以得到的下标得分为：".$tmpIndex."";
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
                                    }
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
//                        echo "第四期的数字为：".$value."下标为：".$key.";第四期的理论数字为：".$v."小标为：".$k."<br />";
                        $tmp = array();
                        if((int)$k > $key){//理论在实际的右边，那么需要判断是在什么范围
                            if((int)$k - $key > 5){
                                $tmp["pingyi"] = 5 - ((int)$k - $key);
                                $tmp["value"] = $value;
                            }elseif((int)$k - $key == 5){
                                $tmp["pingyi"] = 0;
                                $tmp["value"] = $value;
                            }else{

                                $tmp["pingyi"] = 5 - ((int)$k - $key);
                                $tmp["value"] = $value;
                            }
                        }else{//如果理论在实际的左边，那么也需要判断在什么范围
                            if((int)$k - $key < -5){
                                $tmp["pingyi"] = 5 - ((int)$k - $key);
                                $tmp["value"] = $value;
                            }elseif((int)$k - $key == -5){
                                $tmp["pingyi"] = 0;
                                $tmp["value"] = $value;
                            }else{
                                $tmp["pingyi"] = 5 + ((int)$k - $key);
                                $tmp["value"] = $value;
                            }
                        }
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
                            if(!array_key_exists($newIndex, $resQi)){
                                $resQi[$newIndex] = $value;
                            }else{
                                $resQi[$newIndex.$str] = $value;
                            }
                        } elseif($newIndex > 9){
                            $newIndex = $newIndex - 10;
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