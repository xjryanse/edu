<?php

namespace xjryanse\edu\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\edu\service\EduGradeService;
use xjryanse\edu\service\EduClassesStudentService;
use xjryanse\logic\Cachex;
use xjryanse\logic\Number;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Arrays;
use think\Db; 
use Exception;
/**
 * 
 */
class EduClassesService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\edu\\model\\EduClasses';

    use \xjryanse\edu\service\classes\FieldTraits;
    use \xjryanse\edu\service\classes\TriggerTraits;
    use \xjryanse\edu\service\classes\PaginateTraits;
    use \xjryanse\edu\service\classes\DoTraits;
    use \xjryanse\edu\service\classes\DimTraits;
    use \xjryanse\edu\service\classes\CalTraits;
    use \xjryanse\edu\service\classes\SqlTraits;
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    foreach($lists as &$v){
                        $v['hasStudent'] = Arrays::value($v,'uniEduClassesStudentCount') ? 1 : 0 ;
                    }
            
                    return $lists;
                },true);
    }
    
    /**
     * 批量添加班级
     * @param type $schoolId    学校
     * @param type $gradeId     年级
     * @param type $yearId      学期
     * @param type $count       班级数
     * @param type $withAfter   是否同时生成后续年段
     * @return bool
     * @throws Exception
     */
    public static function addClassessBatch($schoolId, $gradeId, $yearId, $count, $withAfter = false){
        if($count > 20){
            throw new Exception('最多支持20个班级');
        }
        $data               = [];
        $data['school_id']  = $schoolId;
        $data['grade_id']   = $gradeId;

        $yearIds = [$yearId];
        if($withAfter){
            // 如果需要同时生成后续年段
            // 提取后续的学年列表
            $afterYearIds = EduGradeService::getInstance($gradeId)->calAfterYearIds($yearId);
            $yearIds = array_merge($yearIds, $afterYearIds);
        }
        // 学年循环
        foreach($yearIds as $yId){
            // 班级循环
            for($i=1; $i<=$count; $i++){
                $data['year_id']    = $yId;
                $data['classes_no'] = $i;

                self::saveRam($data);
            }
        }
        return true;
    }
    /*
     * 学校每学年的年级和班级情况
     * @param type $schoolId    年级
     * @param type $yearId      班级
     */
    public static function schoolYearlyGradeClassesList($schoolId, $yearId){
        // 提取全部的入学数组
        $grades = EduGradeService::schoolYearGradesArr($schoolId, $yearId);
        // 提取年份
        $conL   = [];
        $conL[] = ['school_id','=',$schoolId];
        $conL[] = ['year_id','=',$yearId];
        $lists  = self::where($conL)->select();
        $listsArr = $lists ? $lists->toArray() : [];

        // 班级学生统计
        $classesIds = array_column($listsArr, 'id');
        $studentsCountArr = EduClassesStudentService::groupBatchCount('classes_id', $classesIds);
        // 【3】提取班级
        $classes = self::schoolYearlyClassesArr($schoolId, $yearId);

        $arrList = [];
        foreach($grades as $v){
            $tmp = [];
            // 20231015:前端显示用
            $tmp['status']      = 1;
            $tmp['school_id']   = $schoolId;
            $tmp['cate_id']     = $v['cate_id'];
            $tmp['grade_id']    = $v['id'];
            $tmp['grade_no']    = $v['grade_no'];
            // 年级大写：一二三四五六
            $tmp['gradeStr']    = Number::toChinese($v['grade_no']);
            $tmp['year_id']     = $yearId;
            // $tmp['grade']       = $v;
            // $tmp['yearName']    = Arrays::value($v, 'name');
            // 班级数
            $cone    = [];
            $cone[]  = ['grade_id','=',$v['id']];
            $tmp['classesCount']    = Arrays2d::listCount($listsArr, $cone);
            $tmp['hasClasses']      = $tmp['classesCount'] ? 1 : 0;
            // 学生总数
            $studentCountAll        = 0;
            foreach($classes as $ve){
                
                $conm    = [];
                //$conm[]  = ['grade_no','=',$v['grade_no']];
                $conm[]  = ['classes_no','=',$ve];
                $conm[]  = ['grade_id','=',$v['id']];
                // 单条
                $tObj = Arrays2d::listFind($listsArr, $conm);
                // 显示班级名称的
                // $tmp['cl'.$ve] = $tObj ? $tObj['name'] : '';
                // 20231015-显示班级人数的
                $tmp['cl'.$ve] = $tObj ? Arrays::value($studentsCountArr, $tObj['id'], 0) : '';
                // 20231020 未检人数
                $tmp['clNT'.$ve] = $tObj ? Arrays::value($studentsCountArr, $tObj['id'], 0) : '';
                // 20231020 报告人数
                $tmp['clRP'.$ve] = $tObj ? Arrays::value($studentsCountArr, $tObj['id'], 0) : '';
                
                // 班级id
                $tmp['clId'.$ve] = $tObj ? Arrays::value($tObj, 'id') : '';

                $studentCountAll += ($tmp['cl'.$ve] ? : 0);
            }
            $tmp['studentCount'] = $studentCountAll;
            $tmp['hasStudent']   = $tmp['studentCount'] ? 1 : 0;

            $arrList[] = $tmp;
        }
        return $arrList;
    }
    
    /*
     * 学校每学年的班级数组
     * 取全部年级，最多的班级数
     * @param type $schoolId    年级
     * @param type $yearId      班级
     */
    public static function schoolYearlyClassesArr($schoolId, $yearId){
        $key = __METHOD__.$schoolId.'_'.$yearId;
        return Cachex::funcGet($key, function() use($schoolId, $yearId){
            // 提取年份
            $conL   = [];
            $conL[] = ['school_id','=',$schoolId];
            $conL[] = ['year_id','=',$yearId];
            $arr  = self::where($conL)->column('distinct classes_no');
            // 小到大排序
            sort($arr);
            return $arr;
        },true,1);
    }
    
    /**
     * 学校年度班级数组
     * @param type $conArr  查询条件[['school_id'=>'id','year_id'=>'id'],[]]
     */
    public static function schoolYearlyClassesCountArr($conArr = []){

        $schoolYearIds = [];
        foreach($conArr as $v){
            $schoolYearIds[] = $v['school_id'].$v['year_id'];
        }
        $fieldName = 'concat(school_id,year_id)';
        $con[] = [$fieldName,'in',$schoolYearIds];
        
        $res = self::where($con)->group($fieldName)->column('count(1)',$fieldName);

        return $res;
    }
    
    /**
     * 学校年度年级数
     * @param type $conArr  查询条件[['school_id'=>'id','year_id'=>'id'],[]]
     */
    public static function schoolYearlyGradeCountArr($conArr = []){

        $schoolYearIds = [];
        foreach($conArr as $v){
            $schoolYearIds[] = $v['school_id'].$v['year_id'];
        }
        $fieldName = 'concat(school_id,year_id)';
        $con[] = [$fieldName,'in',$schoolYearIds];
        
        $res = self::where($con)->group($fieldName)->column('count(distinct grade_id)',$fieldName);

        return $res;
    }
    
    /*
     * 导入学生信息
     */
    public function studentImport(){
        
        
        
    }
    
}
