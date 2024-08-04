<?php
namespace xjryanse\edu\model;

use think\Db;
use xjryanse\edu\service\EduGradeService;
use xjryanse\edu\service\EduClassesService;
/**
 * 
 */
class EduClassesStudent extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'classes_id',
            // 去除prefix的表名
            'uni_name'  =>'edu_classes',
            'uni_field' =>'id',
            'del_check'=> true,
            'in_statics'=> true,
            'del_msg'   => '该班级有绑定学生，请先清理'
        ],
        [
            'field'     =>'student_id',
            // 去除prefix的表名
            'uni_name'  =>'edu_student',
            'uni_field' =>'id',
            'in_statics'=> true,
        ]
    ];

    /**
     * 班级学生带冗余sql
     */
    public static function sqlClassesStudentRedund($con = []){
        $classesTable = 'w_edu_classes';
        $fields     = ['a.id','a.company_id','a.classes_id','a.student_id','b.school_id','b.grade_id','b.year_id'];
        $sql = Db::name('edu_classes_student')
                ->alias('a')
                ->join($classesTable.' b','a.classes_id = b.id')
                ->where($con)
                ->field(implode(',',$fields))
                ->buildSql();
        return $sql;
    }
    /**
     * 学生id和分类，提取年级id，
     */
    public static function sqlGradeIdByStudentIdCateId($studentId,$cateId){
        $con = [];
        $con[] = ['d.student_id','in',$studentId];
        $con[] = ['a.cate_id','in',$cateId];

        $gradeTable     = EduGradeService::getTable();
        $classesTable   = EduClassesService::getTable();
        $thisTable      = self::getTable();
        
        $sql = Db::table($gradeTable)->alias('a')
                ->join($classesTable.' b','a.id = b.grade_id')
                ->join($thisTable.' d','b.id = d.classes_id')
                ->field('a.cate_id,b.grade_id,b.id as classes_id,d.student_id')
                ->where($con)
                ->buildSql();
        return $sql;
    }
    
}