<?php

namespace xjryanse\edu\service\classes;

use xjryanse\logic\DataCheck;
use xjryanse\logic\Arrays;
/**
 * 
 */
trait DoTraits{
    /**
     * 班级批量添加
     * @param type $param
     */
    public static function doBatchAdd($param){
        $keys       = ['school_id','grade_id','year_id','classesCount'];
        DataCheck::must($param, $keys);
        $schoolId   = Arrays::value($param, 'school_id');
        $gradeId    = Arrays::value($param, 'grade_id');
        $yearId     = Arrays::value($param, 'year_id');
        $count      = Arrays::value($param, 'classesCount');

        // 20231025:是否同时生成后续年段（例如，一年级初始化16班，后续学年直接生成）
        $withAfter  = Arrays::value($param, 'withAfter');
        
        return self::addClassessBatch($schoolId, $gradeId, $yearId, $count, $withAfter);
    }


}
