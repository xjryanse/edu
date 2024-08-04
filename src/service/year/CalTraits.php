<?php

namespace xjryanse\edu\service\year;

use xjryanse\logic\Arrays;
use xjryanse\edu\service\EduGradeService;
/**
 * 字段复用列表
 */
trait CalTraits{

    public static function calEduYearId($datetime){
        $date = date('Y-m-d',strtotime($datetime));
        $con = [];
        $con[] = ['start_date','<=',$date];
        $con[] = ['end_date','>=',$date];
        $info  = self::staticConFind($con);
        return Arrays::value($info, 'id');
    }
    /**
     * 开始年份(秋季)，计算学年id
     * @param type $year
     * @return type
     */
    public static function calEduYearIdByStartYear($year){
        // 学年一般是跨年，取当年最后一天
        $date   = $year.'-12-31';

        $con    = [];
        $con[]  = ['start_date','<=',$date];
        $con[]  = ['end_date','>=',$date];
        $info   = self::staticConFind($con);
        if(!$info){
            //学年初始化
            $info = self::initByYear($year);
        }

        return Arrays::value($info, 'id');
    }
    /**
     * 20231025：计算学年所在年份
     */
    public function calYear(){
        $yearInfo = $this->get();
        return $yearInfo['start_date'] ? date('Y',strtotime($yearInfo['start_date'])) : '';
    }
    /**
     * 前序学年id
     */
    public function calPreYearId(){
        $year       = $this->calYear();
        $preYear    = $year - 1;
        $preYearId  = self::calEduYearIdByStartYear($preYear);
        return $preYearId;
    }
    /**
     * 带入学年份校验，如果是首年，则没有前序学年
     * @return type
     */
    public function calPreYearIdWithGradeId($gradeId){
        $year       = $this->calYear();
        $gradeYear = EduGradeService::getInstance($gradeId)->calYear();
        if($year == $gradeYear){
            return '';
        }
        return $this->calPreYearId();
    }
    
}
