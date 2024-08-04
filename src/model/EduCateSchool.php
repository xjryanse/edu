<?php
namespace xjryanse\edu\model;

/**
 * 
 */
class EduCateSchool extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'cate_id',
            // 去除prefix的表名
            'uni_name'  =>'edu_cate',
            'uni_field' =>'id',
            'del_check'=> true,
        ],
        [
            'field'     =>'school_id',
            // 去除prefix的表名
            'uni_name'  =>'customer',
            'uni_field' =>'id',
            'in_statics'=> true,
        ]
    ];

}