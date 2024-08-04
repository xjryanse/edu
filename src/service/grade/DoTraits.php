<?php

namespace xjryanse\edu\service\grade;

use xjryanse\edu\service\EduCateService;
use xjryanse\logic\Arrays;
use Exception;

/**
 * 字段复用列表
 */
trait DoTraits{
    /**
     * 学年初始化
     */
    public static function doInit(){
        $cateIds = EduCateService::ids();
        foreach($cateIds as $cId){
            $con    = [];
            $con[]  = ['cate_id','in',$cId];
            $last   = self::where($con)->order('start_date desc')->find();
            $year = Arrays::value($last, 'start_date') 
                    ? date('Y',strtotime($last['start_date'])) 
                    : date('Y',strtotime('-10 year'));
            if($year >= date('Y')){
                continue;
            }
            self::initByYear($cId, $year + 1);
        }
        
        return true;
    }

    private static function initByYear($cateId, $year){
        $data['cate_id'] = $cateId;
        $data['innYear'] = $year;
        
        return self::saveRam($data);
    }
}
