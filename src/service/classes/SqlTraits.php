<?php

namespace xjryanse\edu\service\classes;

use xjryanse\edu\service\EduClassesStudentService;
use xjryanse\edu\service\EduGradeService;


/**
 * 
 */
trait SqlTraits{
    /**
     * 20230918:学校每学年学生人数统计
     * 班级表，按学校和学年聚合，
     * 班级表，关联学生。
     * 下钻到学年
     */
    public static function sqlSchoolYearStudentStatics($con = []){
        $fields     = [];
        $fields[]   = 'count(distinct b.student_id) as allStuCount';
        $fields[]   = 'concat(a.school_id,a.year_id) as schoolYear';
        
        $groups     = [];
        $groups[]   = 'a.school_id';
        $groups[]   = 'a.year_id';
        return self::sqlDown($con, $fields, $groups);
    }
    /**
     * 下钻到年级
     * @param type $con
     * @return type
     */
    public static function sqlSchoolYearGradeStudentStatics($con = []){
        $fields     = [];
        $fields[]   = 'count(distinct b.student_id) as allStuCount';
        $fields[]   = 'concat(a.school_id,a.year_id,a.grade_id) as schoolYearGrade';
        
        $groups     = [];
        $groups[]   = 'a.school_id';
        $groups[]   = 'a.year_id';
        $groups[]   = 'a.grade_id';
        
        return self::sqlDown($con, $fields, $groups);
    }
    
    /**
     * 下钻到年级
     * @param type $con
     * @return type
     */
    public static function sqlSchoolYearClassesStudentStatics($con = []){
        $fields     = [];
        $fields[]   = 'count(distinct b.student_id) as allStuCount';
        $fields[]   = 'a.id as classes_id';
        
        $groups     = [];
        $groups[]   = 'a.school_id';
        $groups[]   = 'a.year_id';
        $groups[]   = 'a.grade_id';
        $groups[]   = 'a.id';
        
        return self::sqlDown($con, $fields, $groups);
    }
    /**
     * 下钻sql
     * @param type $con
     * @param type $fields  结果字段
     * @param type $groups  聚合字段
     */
    private static function sqlDown($con = [], $fields = [], $groups = []){
        $con[]      = ['a.company_id','=',session(SESSION_COMPANY_ID)];
        $classStudentTable = EduClassesStudentService::getTable();

        // 聚合字段必须返回
        $fieldsN = array_merge($fields, $groups);

        $sql = self::mainModel()->where($con)
                ->alias('a')
                ->join($classStudentTable.' b','a.id=b.classes_id')
                ->field(implode(',',$fieldsN))
                ->group(implode(',',$groups))
                ->buildSql();
        return $sql;
    }
    
    
    /***********/
    /**
     * 统计数量的sql(TODO:性能问题？？)
     */
    public static function sqlStaticsCate($con = []){
        $fields     = self::sqlStaticsFields();
        $groups     = [];
        $groups[]   = 'b.cate_id';
        
        return self::sqlStatics($con, $fields, $groups);
    }
    
    public static function sqlStaticsSchool($con = []){
        $fields     = self::sqlStaticsFields();
        $groups     = [];
        $groups[]   = 'a.school_id';
        
        return self::sqlStatics($con, $fields, $groups);
    }
    
    public static function sqlStaticsYear($con = []){
        $fields     = self::sqlStaticsFields();
        $groups     = [];
        $groups[]   = 'a.year_id';
        
        return self::sqlStatics($con, $fields, $groups);
    }
    
    public static function sqlStaticsStudent($con = []){
        $fields     = self::sqlStaticsFields();
        $groups     = [];
        $groups[]   = 'c.student_id';
        
        return self::sqlStatics($con, $fields, $groups);
    }

    public static function sqlStaticsGrade($con = []){
        $fields     = self::sqlStaticsFields();
        $groups     = [];
        $groups[]   = 'a.grade_id';
        
        return self::sqlStatics($con, $fields, $groups);
    }

    /**
     * 下钻的统计
     * @param type $con
     * @param type $fields
     * @param type $groups
     * @return type
     */
    private static function sqlStatics($con = [], $fields = [], $groups = []){
        $classStudentTable  = EduClassesStudentService::getTable();        
        $gradeTable         = EduGradeService::getTable();        

        $fieldsN = array_merge($fields, $groups);
        
        $sql = self::mainModel()->where($con)
            ->alias('a')
            ->join($gradeTable.' b','a.grade_id=b.id')
            ->leftJoin($classStudentTable.' c','c.classes_id=a.id')
            ->field(implode(',',$fieldsN))
            ->group(implode(',',$groups))
            ->buildSql();
        return $sql;
    }
    /**
     * 字段，因为是通用的，所以封装
     */
    private static function sqlStaticsFields(){
        $fields     = [];
        $fields[]   = 'count(distinct b.cate_id) as cateCount';
        $fields[]   = 'count(distinct a.school_id) as schoolCount';
        $fields[]   = 'count(distinct a.grade_id) as gradeCount';
        $fields[]   = 'count(distinct a.id) as classesCount';
        $fields[]   = 'count(distinct a.year_id) as yearCount';
        $fields[]   = 'count(distinct c.student_id) as studentCount';
        return $fields;
    }
}
