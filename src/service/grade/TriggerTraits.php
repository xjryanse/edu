<?php

namespace xjryanse\edu\service\grade;

use xjryanse\logic\Arrays;
use xjryanse\logic\DataCheck;
use xjryanse\edu\service\EduCateService;
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
        // 传了一个入学年
        if(Arrays::value($data, 'innYear')){
            $data['start_date'] = $data['innYear'] .'-09-01';
        }
        // 校验必填参数
        $keys       = ['cate_id','start_date'];
        DataCheck::must($data, $keys);
        $cateId       = Arrays::value($data, 'cate_id');
        $startDate  = Arrays::value($data, 'start_date');

        $data['years'] = EduCateService::getInstance($cateId)->fYears();
        if(!Arrays::value($data, 'name')){
            $data['name'] = self::calName($cateId, $startDate);
        }
        //年
        if(!Arrays::value($data, 'end_date')){
            $plusYearStr = '+'.$data['years'].' years';
            $times = strtotime($data['start_date']);
            $data['end_date'] = date('Y-07-01', strtotime($plusYearStr, $times));
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
