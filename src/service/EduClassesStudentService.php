<?php

namespace xjryanse\edu\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\edu\service\EduStudentService;
use xjryanse\edu\service\EduStudentSchoolService;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Arrays;
use xjryanse\logic\DbOperate;
use Exception;
/**
 * 
 */
class EduClassesStudentService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\MiddleModelTrait;
    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\edu\\model\\EduClassesStudent';

    use \xjryanse\edu\service\classesStudent\CalTraits;
    use \xjryanse\edu\service\classesStudent\FieldTraits;
    use \xjryanse\edu\service\classesStudent\TriggerTraits;
    use \xjryanse\edu\service\classesStudent\DoTraits;
    use \xjryanse\edu\service\classesStudent\DimTraits;


    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    return $lists;
                },true);
    }
    
    /**
     * 批量添加班级的学生
     * @param type $classesId
     * @param type $studentArr
     */
    public static function batchAddClassesStudent($classesId, $studentArr = []){
        $idNos  = array_column($studentArr, 'id_no');
        
        $con         = [];
        $con[]       = ['id_no','in',$idNos];
        $exiLists    = EduStudentService::where($con)->select();
        $exiListsArr = $exiLists ? $exiLists->toArray() : [];
        
        $ids = [];
        foreach($studentArr as $v){
            $exInfo = Arrays2d::listFindByField($exiListsArr, 'id_no', $v['id_no']);
            if($exInfo){
                // 已有的，更新
                $ids[] = $exInfo['id'];
                EduStudentService::getInstance($exInfo['id'])->updateRam($v);
            } else {
                // 没有的新增
                $ids[] = EduStudentService::saveGetIdRam($v);
            }
        }

        //20230923：批量学籍绑定
        EduStudentSchoolService::bindBatch($classesId, $ids, $studentArr);

        return self::middleBindRam('classes_id', $classesId, 'student_id', $ids);
    }
    /**
     * 
     * 20231020：带冗余数据的列表
     */
    public static function listsWithRedun($con){
        $lists      = self::where($con)->select();
        $listsArr   = $lists ? $lists->toArray() : [];
        
        $classesIds = Arrays2d::uniqueColumn($listsArr, 'classes_id');
        $classesObj = EduClassesService::groupBatchFind($classesIds);
        
        // 体检记录数
        foreach($listsArr as &$v){
            $obj = Arrays::value($classesObj, $v['classes_id']);

            $v['grade_id']  = Arrays::value($obj, 'grade_id');
            $v['school_id'] = Arrays::value($obj, 'school_id');
            $v['year_id']   = Arrays::value($obj, 'year_id');
        }

        return $listsArr;
    }
    /**
     * 按班级复制学生名单
     * @param type $rawClassesId    原班级
     * @param type $newClassesId    新班级
     */
    public static function studentCopyRam($rawClassesId,$newClassesId){
        // 新班级已有数据，不支持
        $con[] = ['classes_id','=',$newClassesId];
        $has = self::where($con)->count();
        if($has){
            throw new Exception('新班级已有学生数据，不支持复制');
        }
        // 原班级与新班级不同校，不支持。
        $schoolIdRaw = EduClassesService::getInstance($rawClassesId)->fSchoolId();
        $schoolIdNew = EduClassesService::getInstance($newClassesId)->fSchoolId();
        if($schoolIdRaw != $schoolIdNew){
            throw new Exception('不支持跨校复制');
        }
        // 原班级与新班级不同校，不支持。
        $yearIdRaw = EduClassesService::getInstance($rawClassesId)->fYearId();
        $yearIdNew = EduClassesService::getInstance($newClassesId)->fYearId();
        if($yearIdRaw == $yearIdNew){
            throw new Exception('不支持同学年复制');
        }
        // 提取学生列表
        $conRaw = [];
        $conRaw[] = ['classes_id','=',$rawClassesId];
        
        $studentIds = self::where($conRaw)->column('distinct student_id');
        
        return self::middleBindRam('classes_id', $newClassesId, 'student_id', $studentIds);
    }
    /**
     * 学生维度解绑
     */
    public static function unbindByStudentId($studentId){
        $con    = [];
        $con[]  = ['student_id','=',$studentId];
        $ids = self::where($con)->column('id');
        foreach($ids as $id){
            self::getInstance($id)->deleteRam();
        }
    }
    

}
