<?php

namespace xjryanse\edu\service\classes;

use xjryanse\logic\DataCheck;
use xjryanse\logic\Arrays;
use xjryanse\edu\service\EduGradeService;
/**
 * 
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
        $keys = ['school_id','grade_id','year_id','classes_no'];
        DataCheck::must($data, $keys);
        // 年级：根据grade_id和year_id计算得出
        $gradeId    = Arrays::value($data, 'grade_id');
        $yearId     = Arrays::value($data, 'year_id');

        $data['grade_no']   = EduGradeService::getInstance($gradeId)->calGradeNoByYearId($yearId);
        $data['name']       = self::calName($data['school_id'], $gradeId, $yearId, $data['classes_no']);
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
        
    }

}
