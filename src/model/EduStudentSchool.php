<?php
namespace xjryanse\edu\model;

/**
 * 
 */
class EduStudentSchool extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'cate_id',
            // 去除prefix的表名
            'uni_name'  =>'edu_cate',
            'uni_field' =>'id',
            'del_check' => true,
            'del_msg'   => '该分类有{$count}条学生学籍数据，请先清理'
        ],        
        [
            'field'     =>'school_id',
            // 去除prefix的表名
            'uni_name'  =>'customer',
            'uni_field' =>'id',
            'del_check' => true,
            'del_msg'   => '该学校有设置{$count}个班级，请先清理'
        ],
        [
            'field'     =>'grade_id',
            // 去除prefix的表名
            'uni_name'  =>'edu_grade',
            'uni_field' =>'id',
            'del_check' => true,
            'del_msg'   => '该年级有设置{$count}个班级，请先清理'
        ],
        [
            'field'     =>'student_id',
            // 去除prefix的表名
            'uni_name'  =>'edu_student',
            'uni_field' =>'id',
            'in_statics'=> true,
            'in_list'   => false,
        ]
    ];
}