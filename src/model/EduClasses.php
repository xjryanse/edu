<?php
namespace xjryanse\edu\model;

/**
 * 
 */
class EduClasses extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
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
            'field'     =>'year_id',
            // 去除prefix的表名
            'uni_name'  =>'edu_year',
            'uni_field' =>'id',
            'del_check' => true,
            'del_msg'   => '该学年有设置{$count}个班级，请先清理'
        ],
    ];

}