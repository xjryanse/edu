<?php

namespace xjryanse\edu\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\edu\service\EduClassesService;
use think\Db; 
use xjryanse\logic\Arrays2d;

/**
 * 
 */
class EduCateService extends Base implements MainModelInterface {

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
    protected static $mainModelClass = '\\xjryanse\\edu\\model\\EduCate';

    use \xjryanse\edu\service\cate\FieldTraits;
    use \xjryanse\edu\service\cate\TriggerTraits;
    use \xjryanse\edu\service\cate\CalTraits;
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $sql    = EduClassesService::sqlStaticsCate();

                    $staticsArr     = Db::query($sql);
                    $staticsObj     = Arrays2d::fieldSetKey($staticsArr, 'cate_id');

                    $itemObjs       = EduCatePhyexamItemService::mainModel()->where([['cate_id', 'in', $ids]])->select();
                    $itemIdArrs     = $itemObjs ? $itemObjs->toArray() : [];
                    
                    foreach($lists as &$v){
                        // 学校数
                        $v['schoolCount']   = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'schoolCount', 0) ;
                        // 入学数
                        $v['gradeCount']    = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'gradeCount', 0) ;
                        // 学年数
                        $v['yearCount']     = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'yearCount', 0) ;
                        // 班级数
                        $v['classesCount']  = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'classesCount', 0) ;
                        // 学生数
                        $v['studentCount']  = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'studentCount', 0) ;
                        
                        $con = [];
                        $con[] = ['cate_id', 'in', $v['id']];
                        $v['phyexamItemIds'] = array_column(Arrays2d::listFilter($itemIdArrs, $con), 'phyexam_item_id');
                    }

                    return $lists;
                },true);
    }
    /**
     * 提取最大年份
     * 用于导出报告时初始化数据
     */
    public static function maxYears(){
        return self::where()->max('years');
    }
}
