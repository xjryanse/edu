<?php
namespace xjryanse\edu\model;

/**
 * 
 */
class EduGrade extends Base
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
            'del_msg'   => '该分类有{$count}个学级记录，请先删除'
        ]
    ];

}