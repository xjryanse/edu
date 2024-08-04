<?php

namespace xjryanse\edu\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\edu\service\EduClassesService;
use xjryanse\logic\Arrays2d;
use think\Db; 
/**
 * 
 */
class EduYearService extends Base implements MainModelInterface {

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
    protected static $mainModelClass = '\\xjryanse\\edu\\model\\EduYear';

    use \xjryanse\edu\service\year\FieldTraits;
    use \xjryanse\edu\service\year\TriggerTraits;
    use \xjryanse\edu\service\year\ListTraits;
    use \xjryanse\edu\service\year\DoTraits;
    use \xjryanse\edu\service\year\CalTraits;
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
            
            
                $sql    = EduClassesService::sqlStaticsYear();

                $staticsArr  = Db::query($sql);
                $staticsObj  = Arrays2d::fieldSetKey($staticsArr, 'year_id');
                
                foreach($lists as &$v){
                    $v['eduCate']       = '小学、初中';
                    // 学校数
                    $v['schoolCount']   = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'schoolCount', 0) ;
                    // 入学数
                    $v['gradeCount']    = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'gradeCount', 0) ;
                    // 学年数
                    // $v['yearCount']     = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'yearCount', 0) ;
                    // 班级数
                    $v['classesCount']  = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'classesCount', 0) ;
                    // 学生数
                    $v['studentCount']  = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'studentCount', 0) ;
                }
                return $lists;
            },true);
    }
    /**
     * 最后一个年份
     */
    public static function lastYearId(){
        $all    = self::staticConList();
        $last   = Arrays2d::maxFind($all, 'start_date');
        return $last ? $last['id'] : '';

    }
}
