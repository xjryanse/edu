<?php

namespace xjryanse\edu\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\edu\service\EduClassesService;
use xjryanse\logic\Arrays2d;
use think\Db; 
/**
 * 
 */
class EduStudentService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\edu\\model\\EduStudent';

    use \xjryanse\edu\service\student\FieldTraits;
    use \xjryanse\edu\service\student\CalTraits;
    use \xjryanse\edu\service\student\TriggerTraits;
    use \xjryanse\edu\service\student\FindTraits;
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {

            $sql    = EduClassesService::sqlStaticsStudent();

            $staticsArr  = Db::query($sql);
            $staticsObj  = Arrays2d::fieldSetKey($staticsArr, 'student_id');

            foreach($lists as &$v){
                $v['eduCate']       = '小学、初中';
                // 学校数
                $v['schoolCount']   = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'schoolCount', 0) ;
                // 入学数
                $v['gradeCount']    = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'gradeCount', 0) ;
                // 学年数
                $v['yearCount']     = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'yearCount', 0) ;
                // 班级数
                $v['classesCount']  = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'classesCount', 0) ;
                // 学生数
                // $v['studentCount']  = Arrays2d::objKeyFieldGet($staticsObj, $v['id'], 'studentCount', 0) ;
            }
            return $lists;
        },true);
    }
    /**
     * 身份证号取id
     * @param type $idNo
     * @return type
     */
    public static function getIdByIdNo($idNo){
        $con = [];
        $con[] = ['id_no','=',$idNo];
        return self::where($con)->value('id');
    }
    /**
     * 20240117
     * @param type $idNo
     * @return type
     */
    public static function getByIdNo($idNo){
        $con = [];
        $con[] = ['id_no','=',$idNo];
        return self::where($con)->find();
    }
    
    /**
     * 学生带班级信息
     */
    public function getInfoWithClasses($time = ''){
        $info = $this->get();
        // 
        if(!$time){
            $time = date('Y-m-d H:i:s');
        }
        $eduYearId  = EduYearService::calEduYearId($time);
        $studentId  = $this->uuid;

        // 根据学生和学年，提取班级
        $eduClassesId = EduClassesService::calClassesIdByYearStudent($eduYearId, $studentId);
        // 拼接班级数据
        $info['edu_classes_id'] = $eduClassesId;
        $info['edu_grade_id']   = EduClassesService::getInstance($eduClassesId)->fGradeId();
        // 20240118:分类
        $info['edu_cate_id']    = EduGradeService::getInstance($info['edu_grade_id'])->fCateId();;
        $info['edu_school_id']  = EduClassesService::getInstance($eduClassesId)->fSchoolId();
        $info['edu_year_id']    = $eduYearId;
        $info['student_id']     = $studentId;
        return $info;
    }
    
}
