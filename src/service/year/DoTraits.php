<?php

namespace xjryanse\edu\service\year;

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
        $last = self::where()->order('start_date desc')->find();
        $year = Arrays::value($last, 'start_date') 
                ? date('Y',strtotime($last['start_date'])) 
                : date('Y',strtotime('-10 year'));
        if($year >= date('Y')){
            throw new Exception('已是最新学年'.$year);
        }
        // 初始化
        return self::initByYear($year + 1);
    }
    
    protected static function initByYear($year){
        $data['start_date'] = $year.'-09-01';
        $data['end_date']   = ($year + 1).'-07-01';

        $data['name']       = $year .'至'.($year + 1).'学年';
        return self::saveRam($data);
    }
}
