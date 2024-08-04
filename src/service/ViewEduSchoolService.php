<?php

namespace xjryanse\edu\service;

use xjryanse\system\interfaces\MainModelInterface;
use think\Db; 
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Arrays;
use xjryanse\edu\service\EduCateService;
/**
 * 
 * create view w_view_edu_school as 
 * select * from w_customer where customer_type = 'school'
 */
class ViewEduSchoolService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    // protected static $mainModelClass = '\\xjryanse\\edu\\model\\ViewEduSchool';
    // 20230915:TODO，不是很科学，尽量不用
    protected static $mainModelClass = '\\xjryanse\\customer\\model\\Customer';

    use \xjryanse\edu\service\viewSchool\TriggerTraits;
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {

                    $sql    = EduClassesService::sqlStaticsSchool();

                    $staticsArr  = Db::query($sql);
                    $staticsObj  = Arrays2d::fieldSetKey($staticsArr, 'school_id');

                    $cateArr = EduCateSchoolService::groupBatchSelect('school_id', $ids, 'school_id,cate_id');

                    foreach($lists as &$v){

                        $v['cateIds']       = array_column(Arrays::value($cateArr, $v['id'], []), 'cate_id') ?: [];
                        
                        $v['eduCate']       = EduCateService::calCateStr($v['cateIds']);
                        // 学校数
                        // $v['schoolCount']   = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'schoolCount', 0) ;
                        // 入学数
                        $v['gradeCount']    = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'gradeCount', 0) ;
                        // 学年数
                        $v['yearCount']     = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'yearCount', 0) ;
                        // 班级数
                        $v['classesCount']  = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'classesCount', 0) ;
                        // 学生数
                        $v['studentCount']  = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'studentCount', 0) ;
                    }

                    return $lists;
                });
    }
}
