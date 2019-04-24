<?php
use Models\xyftModel;
class xyftModelTest extends PHPUnit_Framework_TestCase {
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:测试新增一条记录
     */
    public function testaddOne()
    {
        $model = new xyftModel();
        $model->termNum = 10;
        $model->addTime = "2019-02-12";
        $model->one = 10;
        $model->two = 10;
        $model->three = 10;
        $model->four = 10;
        $model->five = 10;
        $model->six = 10;
        $model->seven = 10;
        $model->eight = 10;
        $model->night = 10;
        $model->ten = 10;
        $model->isDeleted = 0;
        $res = $model->addOne();
        var_dump($res);
        #./vendor/bin/phpunit --filter testaddOne ./test/xyftModelTest.php
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:测试删除记录的方法，不进回收站，直接删数据库的记录
     */
    public function testdeleteOne()
    {
        $record = xyftModel::getOneById(10);
        if(empty($record)){
            echo "搜索无数据";
            exit;
        }
        $record = $record[0];
        $res = $record->deleteOne();
        var_dump($res);
        #./vendor/bin/phpunit --filter testdeleteOne ./test/xyftModelTest.php
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:根据ID获取一条记录
     */
    public function testgetOneById()
    {
        $record = xyftModel::getOneById(1);
        var_dump($record);
        #./vendor/bin/phpunit --filter testgetOneById ./test/xyftModelTest.php
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:IN查询，根据ID获取一条记录
     */
    public function testgetAllUndeletedByIdUseIn()
    {
        $record = xyftModel::getAllUndeletedByIdUseIn('1,2,3,4');
        var_dump($record);
        #./vendor/bin/phpunit --filter testgetAllUndeletedByIdUseIn ./test/xyftModelTest.php
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:根据ID获取一条记录
     */
    public function testgetOneByIdForUpdate()
    {
        $record = xyftModel::getOneByIdForUpdate(1);
        var_dump($record);
        #./vendor/bin/phpunit --filter testgetOneByIdForUpdate ./test/xyftModelTest.php
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:删除记录的方法，进回收站，不是直接删数据库的记录
     */
    public function testdeleteUpdateOne()
    {
        $record = xyftModel::getOneById(1);
        if(empty($record)){
            echo "搜索无数据";
            exit;
        }
        $record = $record[0];
        $res= $record->deleteUpdateOne();
        var_dump($res);
        #./vendor/bin/phpunit --filter testdeleteUpdateOne ./test/xyftModelTest.php
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:不带limit的获取所有的没被删除的数据
     */
    public function testgetAllUndeletedWithOutLimit()
    {
        $record = xyftModel::getAllUndeletedWithOutLimit();
        var_dump($record);
        #./vendor/bin/phpunit --filter testgetAllUndeletedWithOutLimit ./test/xyftModelTest.php
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:带limit的获取所有的没被删除的数据
     */
    public function testgetAllUndeletedWithLimit()
    {
        $record = xyftModel::getAllUndeletedWithLimit("limit 0,1");
        var_dump($record);
        #./vendor/bin/phpunit --filter testgetAllUndeletedWithLimit ./test/xyftModelTest.php
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:不带limit的获取所有的被删除的数据
     */
    public function testgetAllDeletedWithOutLimit()
    {
        $record = xyftModel::getAllDeletedWithOutLimit();
        var_dump($record);
        #./vendor/bin/phpunit --filter testgetAllDeletedWithOutLimit ./test/xyftModelTest.php
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:带limit的获取所有的被删除的数据
     */
    public function testgetAllDeletedWithLimit()
    {
        $record = xyftModel::getAllDeletedWithLimit("limit 0,1");
        var_dump($record);
        #./vendor/bin/phpunit --filter testgetAllDeletedWithLimit ./test/xyftModelTest.php
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:测试编辑一条记录
     */
    public function testeditOne()
    {
        $record = xyftModel::getOneById(1);
        if(empty($record)){
            echo "搜索无数据";
            exit;
        }
        $record = $record[0];
        $record->termNum = 10;
        $record->addTime = "2019-02-12";
        $record->one = 10;
        $record->two = 10;
        $record->three = 10;
        $record->four = 10;
        $record->five = 10;
        $record->six = 10;
        $record->seven = 10;
        $record->eight = 10;
        $record->night = 10;
        $record->ten = 10;
        $record->isDeleted = 10;
        $res = $record->editOne();
        var_dump($res);
        #./vendor/bin/phpunit --filter testeditOne ./test/xyftModelTest.php
    }
}
