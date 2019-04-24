<?php
/**
 * Created by PhpStorm.
 * User: tangwei
 * Date: 2019/4/23
 * Time: 14:38
 */
require_once "vendor/autoload.php";
/**
 * Class getXyftDaemon
 */
class getXyftDaemon{
    public $url = "https://www.guzaoapi.com/t?format=json&code=xyft&limit=5&token=968290ACF5964683";//临时的拉取数据的接口

    public $filename = "./daemonPid.txt";//记录所有进程pid的文件
    /**
     * User: tangwei
     * Date: 2019/4/23 14:55
     * Function:标准的输入输出重定向
     */
    public function inAndOutRedirect()
    {
        $path = "/dev/null";
        @fclose(STDOUT);//关闭掉标准输出
        @fclose(STDERR);//关闭掉标准错误输出
        $stdout = fopen($path, "a");
        $stderr = fopen($path, "a");
    }

    /**
     * User: tangwei
     * Date: 2019/4/24 08:38
     * Function:解析命令行参数
     */
    public function optionParse()
    {
        $argv = $_SERVER['argv'];
        if(!isset($argv[1]) || $argv[1] == ""){
            echo "命令使用不正确 仅支持 php xxx.php start/stop\n";
            exit;
        }
        return strtolower($argv[1]);
    }

    /**
     * User: tangwei
     * Date: 2019/4/23 15:09
     * Function:关闭进程并删除进程ID记录文件
     */
    public function openPidFileAndDelete()
    {
        if(file_exists($this->filename)){
            $opendFile = fopen($this->filename, "r+");//以只读方式打开pid记录文件
            $filesize = filesize($this->filename);//获取到文件大小，因为我们代码中只会fork两次
            $filesize = $filesize == 0 ? 32 : $filesize;//如果文件大小为0，为避免fread出错，则设置为32

            if(flock($opendFile, LOCK_EX)){
                $contents = fread($opendFile, $filesize);//读取到文件中的pid值
                $pidArray = explode(";", $contents);//以;号分隔，获取所有的现存的进程pid值
                if(!empty($pidArray)){
                    foreach($pidArray as $key=>$value){
                        if($value != ""){
                            posix_kill($value, 9);//给该进程号，发错强制退出的信号
                        }
                    }
                }
                flock($opendFile, LOCK_UN);
            }
            fclose($opendFile);
            unlink($this->filename);//如果其中一个进程fork失败，那么删掉文件
        }
    }

    /**
     * User: tangwei
     * Date: 2019/4/23 15:15
     * @param $pid
     * Function:写入进程号到进程号记录文件
     */
    public function openFileAndWritePid($pid)
    {
        $opendFile = fopen($this->filename, "a+");//以读写方式打开pid记录文件
        $filesize = filesize($this->filename);//获取到文件大小，因为我们代码中只会fork两次
        $filesize = $filesize == 0 ? 32 : $filesize;//如果文件大小为0，为避免fread出错，则设置为32
        if(flock($opendFile, LOCK_EX)){
            $contents = fread($opendFile, $filesize);//读取到文件中的pid值
            if($contents == ""){//如果目前文件为空，则直接写入文件
                fwrite($opendFile, $pid);
            }else{//如果目前文件不为空，那么追加写入（所谓追加写入就是多了个";"分隔符）
                $pidArray = explode(";", $contents);
                if(!in_array($pid, $pidArray)) fwrite($opendFile, ";".$pid);
            }
            flock($opendFile, LOCK_UN);
        }
        fclose($opendFile);
    }

    /**
     * User: tangwei
     * Date: 2019/4/23 15:45
     * @param $pid
     * Function:打开进程号记录文件，删除其中一个进程ID号
     */
    public function openFileAndRemoveOnePid($pid)
    {
        $opendFile = fopen($this->filename, "a+");//以读写方式打开pid记录文件
        $filesize = filesize($this->filename);//获取到文件大小，因为我们代码中只会fork两次
        $filesize = $filesize == 0 ? 32 : $filesize;//如果文件大小为0，为避免fread出错，则设置为32
        if(flock($opendFile, LOCK_EX)){
            $contents = fread($opendFile, $filesize);//读取到文件中的pid值
            flock($opendFile, LOCK_UN);
        }
        fclose($opendFile);
        $pidArray = explode(";", $contents);//获取到所有的pid
        $str = "";
        if(!empty($pidArray)){
            foreach($pidArray as $key=>$value){
                if($value != $pid){
                    $str .= $value.";";
                }
            }
        }
        $str = substr($str, 0, -1);
        $opendFile = fopen($this->filename, "w");//以读写方式打开pid记录文件
        if(flock($opendFile, LOCK_EX)){
            fwrite($opendFile, $str);
            flock($opendFile, LOCK_UN);
        }
        fclose($opendFile);
    }

    /**
     * User: tangwei
     * Date: 2019/4/24 09:02
     * @param $data
     * @param $url
     * @param int $second
     * @return bool|mixed
     * Function:发送请求到目标地址
     */
    public static function getCurl($url, $second = 30)
    {
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, FALSE);//get提交方式
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        if($data){
            return $data;//返回结果
        }else{

            return false;
        }
    }

    /**
     * User: tangwei
     * Date: 2019/4/23 14:43
     * Function:fork出子进程
     */
    public function forkFirstChild($parentProExit = true)
    {
//        $this->inAndOutRedirect();//输入输出重定向到/dev/null
        $pid = pcntl_fork();
        if($pid == -1){//fork子进程失败
            $this->openPidFileAndDelete();//子进程fork失败，那么我就退出所有的进程，并把进程文件删掉
            exit;
        }elseif($pid > 0){//父进程会得到子进程号，所以这里是父进程执行的逻辑
            if($parentProExit){//设置了父进程退出
                exit;
            }else{
                $pid = posix_getpid();
                $this->openFileAndWritePid($pid);//获取到守护进程的进程号，写入文件
                posix_setsid();//父进程成为sessiod leader进程，并在这儿等待子进程
                echo "\t\t\t||\t守护进程已启动成功，进程ID：".$pid."\t||\n";
                $childPid = pcntl_wait($status);
                $this->openFileAndRemoveOnePid($childPid);
                $this->forkFirstChild(false);
            }
        }else{//父进程会得到子进程号，所以这里是父进程执行的逻辑
            if($parentProExit == true){//父进程退出，子进程需要继续fork
                $this->forkFirstChild(false);
            }else{//父进程成为session leader之后，子进程需干活
                $pid = posix_getpid();
                echo "\t\t\t||\t工作进程已启动成功，进程ID：".$pid."\t||\n";
                sleep(1);//这儿确保父进程的pid先写入文件，子进程的pd后写入文件
                $this->openFileAndWritePid($pid);//获取到干活进程的进程号，写入文件
                while(true){
                    $res = $this->getCurl($this->url);// 获取地址返回的数据
                    $jsonData = json_decode($res);
                    if($jsonData->code == "xyft"){//确认类型为幸运飞艇
                        $allData = array_reverse($jsonData->data);//拿到所有的数据
                        if(!empty($allData)){
                            foreach($allData as $key=>$value){//循环所有的数据
                                $xyftInfo = \Models\xyftModel::getOneByTermNum($value->expect);
                                if(empty($xyftInfo)){//数据库中不存在本期数据，则自动存入数据
                                    $numArray = explode(",", $value->opencode);
                                    if(count($numArray) != 10)continue;//如果数据不为10个则不要这个数据
                                    $xyftModel = new \Models\xyftModel();
                                    $xyftModel->termNum = $value->expect;
                                    $xyftModel->addTime = date("Y-m-d");
                                    $xyftModel->one = $numArray[0];
                                    $xyftModel->two = $numArray[1];
                                    $xyftModel->three = $numArray[2];
                                    $xyftModel->four = $numArray[3];
                                    $xyftModel->five = $numArray[4];
                                    $xyftModel->six = $numArray[5];
                                    $xyftModel->seven = $numArray[6];
                                    $xyftModel->eight = $numArray[7];
                                    $xyftModel->night = $numArray[8];
                                    $xyftModel->ten = $numArray[9];
                                    $xyftModel->isDeleted = 0;
                                    $xyftModel->addOne();
                                }
                            }
                        }
                    }
                    sleep(4*60);
                }
            }
        }
    }
}

$daemon = new getXyftDaemon();
$action = $daemon->optionParse();
if($action == "stop"){
    $daemon->openPidFileAndDelete();
}elseif($action == "start"){
    echo "\t\t\t||\t@author:tangwei\t||\n";
    echo "\t\t\t||\t@Email:jsdy_tw1519@163.com\t||\n";
    $daemon->forkFirstChild();
}else{
    echo "命令使用不正确 仅支持 php xxx.php start/stop\n";
    exit;
}
