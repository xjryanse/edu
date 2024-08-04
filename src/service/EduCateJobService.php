<?php

namespace xjryanse\edu\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays2d;

/**
 * 教育分类与体检系统关联
 */
class EduCateJobService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\traits\ObjectAttrTrait;
    use \xjryanse\traits\MiddleModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\edu\\model\\EduCateJob';

    /**
     * 分类必过岗位
     */
    public static function cateMustJobs($cateId){
        $con    = [];
        $con[]  = ['cate_id','=',$cateId];
        $con[]  = ['is_must','=',1];

        $jobLists = self::staticConList($con);
        return Arrays2d::uniqueColumn($jobLists, 'job_id');
    }
    
    
}
