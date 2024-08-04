<?php

namespace xjryanse\edu\service\classesStudent;

/**
 * 
 */
trait DimTraits{
    /*
     * page_id维度列表
     */
    public static function dimClassesIdsByStudentId($studentId){
        $con    = [];
        $con[]  = ['student_id','in',$studentId];
        return self::column('classes_id',$con);
    }
    /*
     * 
     */
    public static function dimStudentIdsByClassesId($classesId){
        $con    = [];
        $con[]  = ['classes_id','in',$classesId];
        return self::column('student_id',$con);
    }
    
    
}
