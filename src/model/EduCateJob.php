<?php
namespace xjryanse\edu\model;

/**
 * 
 */
class EduCateJob extends Base
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
            'field'     =>'job_id',
            // 去除prefix的表名
            'uni_name'  =>'system_company_job',
            'uni_field' =>'id',
            'in_statics'=> true,
        ]
    ];

}