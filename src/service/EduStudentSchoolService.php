<?php

namespace xjryanse\edu\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\edu\service\EduClassesService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;

/**
 * 学生学籍表
 */
class EduStudentSchoolService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\edu\\model\\EduStudentSchool';

    use \xjryanse\edu\service\studentSchool\DimTraits;
//    use \xjryanse\edu\service\student\CalTraits;
//    use \xjryanse\edu\service\student\TriggerTraits;

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                return $lists;
            },true);
    }
    
    /**
     * 
     * @param type $cate        小学、初中、高中
     * @param type $schoolId    学校
     * @param type $studentId   学生
     * @param type $gradeId     年级
     */
    public static function getStudentSchoolIdWithGenerate($cate, $schoolId, $studentId, $gradeId, $data = []){
        $con    = [];
        $con[]  = ['cate_id','=',$cate];
        $con[]  = ['school_id','=',$schoolId];
        $con[]  = ['student_id','=',$studentId];
        $con[]  = ['grade_id','=',$gradeId];

        $id = self::where($con)->value('id');
        if(!$id){
            $data = [];
            $data['cate_id']    = $cate;
            $data['school_id']  = $schoolId;
            $data['student_id'] = $studentId;
            $data['grade_id']   = $gradeId;
            $id = self::saveGetIdRam($data);
        }
        return $id;
    }
    /**
     * 
     * @param type $classesId   班级id
     * @param type $studentIds  学生id
     */
    public static function bindBatch($classesId, $studentIds, $data = []){
        // 导入数据：身份证号匹配学号
        // 以身份证号设为唯一识别
        $dataObj = Arrays2d::fieldSetKey($data, 'id_no');
        
        // 班级id提取分类，学校，年级
        $classInfo  = EduClassesService::getInstance($classesId)->get();

        $cateId     = EduClassesService::getInstance($classesId)->calCateId();

        $con        = [];
        $con[]      = ['cate_id','=',$cateId];
        $con[]      = ['school_id','=',Arrays::value($classInfo,'school_id')];
        $con[]      = ['grade_id','=',Arrays::value($classInfo,'grade_id')];
        $con[]      = ['student_id','in',$studentIds];

        $lists      = self::where($con)->select();
        $listsArr   = $lists ? $lists->toArray() : [];
        // 有的部分
        $hasIds = array_column($listsArr, 'student_id');

        // 没有的部分, 写入学籍
        $noIds = array_diff($studentIds, $hasIds);
        self::batchBindAdd($classesId, $noIds, $dataObj);
    }
    /**
     * 没有学籍的，调用这个方法写入（请外部判断）
     * @param type $classesId   
     * @param type $studentIds  
     * @param type $dataObj     身份证号作键
     */
    private static function batchBindAdd($classesId, $studentIds, $dataObj){
        $classInfo = EduClassesService::getInstance($classesId)->get();

        $cateId = EduClassesService::getInstance($classesId)->calCateId();

        foreach($studentIds as $stuId){
            $studentInfo = EduStudentService::getInstance($stuId)->get();
            $idNo = Arrays::value($studentInfo, 'id_no');
            // 导入数据，单条
            $impObj = Arrays::value($dataObj, $idNo) ? : [];

            $data = [];
            $data['cate_id']    = $cateId;
            $data['school_id']  = Arrays::value($classInfo, 'school_id');
            $data['student_id'] = $stuId;
            $data['grade_id']   = Arrays::value($classInfo, 'grade_id');
            $data['student_no'] = Arrays::value($impObj, 'student_no');
            self::saveGetIdRam($data);
        }
    }
    
}
