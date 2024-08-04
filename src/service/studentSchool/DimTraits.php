<?php

namespace xjryanse\edu\service\studentSchool;

/**
 * 
 */
trait DimTraits{
    /*
     * page_id维度列表
     */
    public static function dimGradeIdsByStudentId($studentId){
        $con    = [];
        $con[]  = ['student_id','in',$studentId];
        return self::column('grade_id',$con);
    }

}
