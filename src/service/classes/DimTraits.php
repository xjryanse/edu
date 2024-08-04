<?php

namespace xjryanse\edu\service\classes;

use xjryanse\logic\DataCheck;
use xjryanse\logic\Arrays;
/**
 * 
 */
trait DimTraits{
    
    /*
     * page_id维度列表
     */
    public static function dimIdsByYearId($yearId){
        $con    = [];
        $con[]  = ['year_id','in',$yearId];
        return self::column('id',$con);
    }
    
    /*
     * page_id维度列表
     */
    public static function dimIdsByGradeIdYearId($gradeId, $yearId){
        $con    = [];
        $con[]  = ['grade_id','in',$gradeId];
        $con[]  = ['year_id','in',$yearId];
        return self::column('id',$con);
    }

    /**
     * 
     * @param type $gradeId 年级
     * @param type $yearId  学年
     * @param type $field   列表字段
     */
    public static function dimListByGradeIdYearId($gradeId, $yearId, $field='*'){
        $con    = [];
        $con[]  = ['grade_id','in',$gradeId];
        $con[]  = ['year_id','in',$yearId];
        $lists = self::where($con)->field($field)->select();
        return $lists ? $lists->toArray() : [];
    }

}
