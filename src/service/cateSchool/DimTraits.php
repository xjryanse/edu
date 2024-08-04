<?php

namespace xjryanse\edu\service\cateSchool;

/**
 * 
 */
trait DimTraits{

    /*
     * 分类id
     */
    public static function dimCateIdsBySchoolId($schoolId){
        $con    = [];
        $con[]  = ['school_id','in',$schoolId];
        return self::column('cate_id',$con);
    }

}
