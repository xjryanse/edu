<?php

namespace xjryanse\edu\service\student;

use xjryanse\logic\IdNo;
use xjryanse\logic\Arrays;
use xjryanse\edu\service\EduClassesStudentService;
/**
 * 字段复用列表
 */
trait TriggerTraits{
    /**
     * 钩子-保存前
     */
    public static function extraPreSave(&$data, $uuid) {
        self::stopUse(__METHOD__);
    }

    public static function extraPreUpdate(&$data, $uuid) {
        self::stopUse(__METHOD__);
    }
    
    public function extraPreDelete() {
        self::stopUse(__METHOD__);
    }
    
    /**
     * 钩子-保存前
     */
    public static function ramPreSave(&$data, $uuid) {
        $idNo = Arrays::value($data,'id_no');
        // 标准身份证号才进行处理
        if(IdNo::isIdNo($idNo)){
            $data['birthday']   = IdNo::getBirthday($idNo);
            $data['sex']        = IdNo::getSex($idNo);
        }
    }

    /**
     * 钩子-保存后
     */
    public static function ramAfterSave(&$data, $uuid) {
        
    }

    /**
     * 钩子-更新前
     */
    public static function ramPreUpdate(&$data, $uuid) {
        $idNo = Arrays::value($data,'id_no');
        // 标准身份证号才进行处理
        if(IdNo::isIdNo($idNo)){
            $data['birthday']   = IdNo::getBirthday($idNo);
            $data['sex']        = IdNo::getSex($idNo);
        }        
    }

    /**
     * 钩子-更新后
     */
    public static function ramAfterUpdate(&$data, $uuid) {
        
    }

    /**
     * 钩子-删除前
     */
    public function ramPreDelete() {

    }

    /**
     * 钩子-删除后
     */
    public function ramAfterDelete() {
        
        // 解绑学生的班级
        EduClassesStudentService::unbindByStudentId($this->uuid);
    }

    
    
}
