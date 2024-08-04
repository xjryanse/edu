<?php

namespace xjryanse\edu\service;

use xjryanse\system\interfaces\MainModelInterface;
/**
 * 教育分类与体检系统关联
 */
class EduCatePhyexamItemService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\edu\\model\\EduCatePhyexamItem';

    use \xjryanse\edu\service\catePhyexamItem\FieldTraits;
    use \xjryanse\edu\service\catePhyexamItem\TriggerTraits;
    use \xjryanse\edu\service\catePhyexamItem\DoTraits;
    use \xjryanse\edu\service\catePhyexamItem\CalTraits;
    use \xjryanse\edu\service\catePhyexamItem\PaginateTraits;
    use \xjryanse\edu\service\catePhyexamItem\ListTraits;
    use \xjryanse\edu\service\catePhyexamItem\DimTraits;

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                return $lists;
            },true);
    }

    /**
     * 保存检测项目
     * 
     * @param type $cateId      分类id
     * @param type $phyexamItemIds   权限id
     */
    public static function savePhyexamItems( $cateId, $phyexamItemIds ){
        $lists = EduCateService::getInstance($cateId)->objAttrsList('eduCatePhyexamItem');
        foreach($lists as $v){
            self::getInstance($v['id'])->deleteRam();
        }

        $tempArr = [];
        foreach( $phyexamItemIds as &$itemId ){
            $tempArr[] = ['cate_id'=>$cateId,'phyexam_item_id'=>$itemId];
        }

        return self::saveAllRam($tempArr);
    }
    
    /**
     * 20240214
     * @param type $cateId
     * @param type $phyexamItemId
     * @return bool
     */
    public static function deleteByCateAndItem($cateId, $phyexamItemId){
        $con[] = ['cate_id','=',$cateId];
        $con[] = ['phyexam_item_id','=',$phyexamItemId];
        
        $lists = self::where($con)->select();
        foreach($lists as $v){
            self::getInstance($v['id'])->deleteRam();
        }
        return true;
    }

    /**
     * 20240214
     * @param type $cateId
     * @param type $phyexamItemId
     * @return bool
     */
    public static function saveByCateAndItem($cateId, $phyexamItemId){
        $con[] = ['cate_id','=',$cateId];
        $con[] = ['phyexam_item_id','=',$phyexamItemId];
        
        $count = self::where($con)->count();
        if(!$count){
            $data                   = [];
            $data['cate_id']        = $cateId;
            $data['phyexam_item_id']= $phyexamItemId;
            $res = self::saveRam($data);
            return $res;
        }
        return true;
    }
    
    
}
