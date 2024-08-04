<?php

namespace xjryanse\edu\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\edu\service\EduYearService;
use think\Db; 
use xjryanse\logic\Arrays2d;

/**
 * 
 */
class EduGradeService extends Base implements MainModelInterface {

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
    protected static $mainModelClass = '\\xjryanse\\edu\\model\\EduGrade';

    use \xjryanse\edu\service\grade\CalTraits;
    use \xjryanse\edu\service\grade\FieldTraits;
    use \xjryanse\edu\service\grade\DoTraits;
    use \xjryanse\edu\service\grade\ListTraits;
    use \xjryanse\edu\service\grade\PaginateTraits;
    use \xjryanse\edu\service\grade\TriggerTraits;
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
            
            $sql    = EduClassesService::sqlStaticsGrade();

            $staticsArr  = Db::query($sql);
            $staticsObj  = Arrays2d::fieldSetKey($staticsArr, 'grade_id');

            foreach($lists as &$v){
                // 学校数
                $v['schoolCount']   = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'schoolCount', 0) ;
                // 入学数
                // $v['gradeCount']    = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'gradeCount', 0) ;
                // 学年数
                $v['yearCount']     = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'yearCount', 0) ;
                // 班级数
                $v['classesCount']  = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'classesCount', 0) ;
                // 学生数
                $v['studentCount']  = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'studentCount', 0) ;
            }
            return $lists;
        },true);
    }

    /**
     * 传一个学校id，学年id，提取全部grade数组
     * @param type $schoolId
     * @param type $yearId
     */
    public static function schoolYearGradesArr($schoolId, $yearId){

        $yearInfo = EduYearService::getInstance($yearId)->get();
        
        // 提取年份
        // 20230923:提取学校的cateId;
        $cateIds = EduCateSchoolService::dimCateIdsBySchoolId($schoolId);

        $con    = [];
        $con[]  = ['cate_id','in',$cateIds];
        $con[]  = ['end_date','>',$yearInfo['start_date']];
        $con[]  = ['start_date','<',$yearInfo['end_date']];
        // 取列表
        $lists = self::staticConList($con);

        //按年级
        Arrays2d::sort($lists, 'start_date', 'desc');
        //分小学，初中，高中
        Arrays2d::sort($lists, 'cate_id');

        foreach($lists as &$v){
            // 计算年级
            $v['grade_no']  = self::getInstance($v['id'])->calGradeNoByYearId($yearId);
        }
        return $lists;
    }
    
    
}
