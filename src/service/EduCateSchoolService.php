<?php

namespace xjryanse\edu\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\customer\service\CustomerService;
use xjryanse\edu\service\EduCateService;

/**
 * 教育分类与体检系统关联
 */
class EduCateSchoolService extends Base implements MainModelInterface {

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
    protected static $mainModelClass = '\\xjryanse\\edu\\model\\EduCateSchool';

//    use \xjryanse\edu\service\cateSchool\FieldTraits;
//    use \xjryanse\edu\service\cateSchool\TriggerTraits;
//    use \xjryanse\edu\service\cateSchool\DoTraits;
//    use \xjryanse\edu\service\cateSchool\CalTraits;
//    use \xjryanse\edu\service\cateSchool\PaginateTraits;
//    use \xjryanse\edu\service\cateSchool\ListTraits;
    use \xjryanse\edu\service\cateSchool\DimTraits;

    /**
     * 分类保存学校
     * 
     * @param type $cateId      分类id
     * @param type $schoolIds   权限id
     */
    public static function saveCateSchools( $cateId, $schoolIds ){
        $lists = EduCateService::getInstance($cateId)->objAttrsList('eduCateSchool');
        foreach($lists as $v){
            self::getInstance($v['id'])->deleteRam();
        }

        $tempArr = [];
        foreach( $schoolIds as &$itemId ){
            $tempArr[] = ['cate_id'=>$cateId,'school_id'=>$itemId];
        }

        return self::saveAllRam($tempArr);
    }

    /**
     * 学校保存分类
     * 
     * @param type $schoolId      
     * @param type $cateIds   
     */
    public static function saveSchoolCatess( $schoolId, $cateIds ){
        $lists = CustomerService::getInstance($schoolId)->objAttrsList('eduCateSchool');
        foreach($lists as $v){
            self::getInstance($v['id'])->deleteRam();
        }

        $tempArr = [];
        foreach( $cateIds as &$itemId ){
            $tempArr[] = ['cate_id'=>$itemId,'school_id'=>$schoolId];
        }

        return self::saveAllRam($tempArr);
    }
    
}
