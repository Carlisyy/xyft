<?php 
namespace Models;
use Libs\CObject;
class xyftModel extends CObject
{
    static $table = "xyft"; 
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:新增一条记录
     */
    public function addOne() : array 
    {
        assert(isset($this->termNum) && is_numeric($this->termNum) && $this->termNum >= 0);
        assert(isset($this->addTime) && $this->addTime != '' );
        assert(isset($this->one) && is_numeric($this->one) && $this->one >= 0);
        assert(isset($this->two) && is_numeric($this->two) && $this->two >= 0);
        assert(isset($this->three) && is_numeric($this->three) && $this->three >= 0);
        assert(isset($this->four) && is_numeric($this->four) && $this->four >= 0);
        assert(isset($this->five) && is_numeric($this->five) && $this->five >= 0);
        assert(isset($this->six) && is_numeric($this->six) && $this->six >= 0);
        assert(isset($this->seven) && is_numeric($this->seven) && $this->seven >= 0);
        assert(isset($this->eight) && is_numeric($this->eight) && $this->eight >= 0);
        assert(isset($this->night) && is_numeric($this->night) && $this->night >= 0);
        assert(isset($this->ten) && is_numeric($this->ten) && $this->ten >= 0);
        assert(isset($this->isDeleted) && is_numeric($this->isDeleted) && $this->isDeleted >= 0);
        $sql = "INSERT INTO ".self::$table." (`termNum`,`addTime`,`one`,`two`,`three`,`four`,`five`,`six`,`seven`,`eight`,`night`,`ten`,`isDeleted`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $sqlParam = array(
            $this->termNum,
            $this->addTime,
            $this->one,
            $this->two,
            $this->three,
            $this->four,
            $this->five,
            $this->six,
            $this->seven,
            $this->eight,
            $this->night,
            $this->ten,
            $this->isDeleted,);
        return self::query($sql, $sqlParam, false);
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:删除记录的方法，不进回收站，直接删数据库的记录
     */
    public function deleteOne() : array 
    {
        $sql = "DELETE FROM ".self::$table." WHERE `id` = ?";
        $sqlParam = array($this->id);
        return self::query($sql, $sqlParam, false);
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:根据ID获取一条记录
     */
    public static function getOneById($id) : array
    {
        assert(is_numeric($id) && $id > 0);
        $sql = "SELECT * FROM ".self::$table." WHERE `id` = ? AND isDeleted = 0";
        $sqlParam = array($id);
        return self::query($sql, $sqlParam, true);
    }

    /**
     * User: tangwei
     * Date: 2019/4/24 09:26
     * Function:根据termNum获取一期数据
     */
    public static function getOneByTermNum($termNum):array
    {
        $sql = "SELECT * FROM ".self::$table." WHERE `termNum` = ? AND isDeleted = 0";
        $sqlParam = array($termNum);
        return self::query($sql, $sqlParam, true);
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:IN查询，根据ID获取所有的记录
     */
    public static function getAllUndeletedByIdUseIn($ids) : array
    {
        assert($ids != "");
        $sql = "SELECT * FROM ".self::$table." WHERE find_in_set(`id`, '$ids') AND isDeleted = 0";
        $sqlParam = array();
        return self::query($sql, $sqlParam, true);
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:根据ID获取一条没被删除的记录
     */
    public static function getOneByIdForUpdate($id) : array
    {
        assert(is_numeric($id) && $id > 0);
        $sql = "SELECT * FROM ".self::$table." WHERE `id` = ? AND isDeleted = 0 FOR UPDATE";
        $sqlParam = array($id);
        return self::query($sql, $sqlParam, true);
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:删除记录的方法，进回收站，不是直接删数据库的记录
     */
    public function deleteUpdateOne() : array
    {
        $sql = "UPDATE ".self::$table." SET `isDeleted` = 1 WHERE `id` = ?";
        $sqlParam = array($this->id);
        return self::query($sql, $sqlParam, false);
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:不带limit的获取所有的没被删除的数据
     */
    public static function getAllUndeletedWithOutLimit() : array
    {
        $sql = "SELECT * FROM ".self::$table." WHERE `isDeleted` = 0 order by id desc ";
        $sqlParam = array();
        return self::query($sql, $sqlParam, true);
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:带limit的获取所有的没被删除的数据
     */
    public static function getAllUndeletedWithLimit($limit) : array
    {
        assert($limit != "");
        $sql = "SELECT * FROM ".self::$table." WHERE `isDeleted` = 0  order by id desc ".$limit;
        $sqlParam = array();
        return self::query($sql, $sqlParam, true);
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:不带limit的获取所有的被删除的数据
     */
    public static function getAllDeletedWithOutLimit() : array
    {
        $sql = "SELECT * FROM ".self::$table." WHERE `isDeleted` = 1 order by id desc ";
        $sqlParam = array();
        return self::query($sql, $sqlParam, true);
    }

    /**
     * User: tangwei
     * Date: 2019/4/8 14:01
     * @return array|bool|int
     * Function:获取到最近的四期数据
     */
    public static function getLastFourUndeletedWithOutLimit() : array
    {
        $sql = "SELECT * FROM ".self::$table." WHERE `isDeleted` = 0 order by id desc limit 4";
        $sqlParam = array();
        return self::query($sql, $sqlParam, true);
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:带limit的获取所有的被删除的数据
     */
    public static function getAllDeletedWithLimit($limit) : array
    {
        assert($limit != "");
        $sql = "SELECT * FROM ".self::$table." WHERE `isDeleted` = 1 order by id desc ".$limit;
        $sqlParam = array();
        return self::query($sql, $sqlParam, true);
    }
    /**
     * User: tangwei
     * Date: 2019.04.02
     * @param 
     * @return
     * Function:编辑一条记录
     */
    public function editOne() : array
    {
        assert(isset($this->termNum) && is_numeric($this->termNum) && $this->termNum >= 0);
        assert(isset($this->one) && is_numeric($this->one) && $this->one >= 0);
        assert(isset($this->two) && is_numeric($this->two) && $this->two >= 0);
        assert(isset($this->three) && is_numeric($this->three) && $this->three >= 0);
        assert(isset($this->four) && is_numeric($this->four) && $this->four >= 0);
        assert(isset($this->five) && is_numeric($this->five) && $this->five >= 0);
        assert(isset($this->six) && is_numeric($this->six) && $this->six >= 0);
        assert(isset($this->seven) && is_numeric($this->seven) && $this->seven >= 0);
        assert(isset($this->eight) && is_numeric($this->eight) && $this->eight >= 0);
        assert(isset($this->night) && is_numeric($this->night) && $this->night >= 0);
        assert(isset($this->ten) && is_numeric($this->ten) && $this->ten >= 0);
        assert(isset($this->isDeleted) && is_numeric($this->isDeleted) && $this->isDeleted >= 0);
        $sql = "UPDATE ".self::$table." SET `termNum` = ?,`one` = ?,`two` = ?,`three` = ?,`four` = ?,`five` = ?,`six` = ?,`seven` = ?,`eight` = ?,`night` = ?,`ten` = ?,`isDeleted` = ? WHERE `id` = ? ";
        $sqlParam = array(
            $this->termNum,
            $this->one,
            $this->two,
            $this->three,
            $this->four,
            $this->five,
            $this->six,
            $this->seven,
            $this->eight,
            $this->night,
            $this->ten,
            $this->isDeleted,
            $this->id
        );
        return self::query($sql, $sqlParam, false);
    }

}