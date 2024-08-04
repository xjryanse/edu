<?php

namespace xjryanse\edu\service\classes;

use xjryanse\logic\Number;
use xjryanse\edu\service\EduGradeService;
use xjryanse\edu\service\EduClassesStudentService;
use xjryanse\logic\Strings;
use xjryanse\logic\Arrays;
use xjryanse\logic\Cachex;
use Exception;
/**
 * 
 */
trait CalTraits{

    /**
     * 计算年级名称
     * 保存时自动生成的名称
     */
    public static function calName($schoolId, $gradeId, $yearId, $classesNo){
        // 计算几年级
        $gradeNo  = EduGradeService::getInstance($gradeId)->calGradeNoByYearId($yearId);

        return Number::toChinese($gradeNo).'年（'.$classesNo.'）班';
    }
    /*
     * 根据学年id和学生id，提取班级
     * @param type $yearId      
     * @param type $studentId
     */
    public static function calClassesIdByYearStudent($yearId, $studentId){
        $cacheKey = 'calClassesIdByYearStudent_'.$yearId.'_'.$studentId;
        return Cachex::funcGet($cacheKey, function() use ($yearId, $studentId){
            // 提取学生全部班级；
            $stuClassIds = EduClassesStudentService::dimClassesIdsByStudentId($studentId);
            // 提取学年全部班级；
            $yearClassIds = self::dimIdsByYearId($yearId);
            // 求交集
            $intSec = array_intersect($stuClassIds, $yearClassIds);

            return $intSec ? $intSec[0] : '';            
        });
    }
    /**
     * 根据班级，提取是小学，初中，高中……
     */
    public function calCateId(){
        $gradeId    = $this->fGradeId();
        return EduGradeService::getInstance($gradeId)->fCateId();
    }
    /**
     * 20231024：班级计算入学年份
     */
    public function calGradeYear(){
        $gradeId    = $this->fGradeId();
        return EduGradeService::getInstance($gradeId)->calYear();
    }
    /**
     * 计算班级的匹配数组
     * @createTime 2023-10-15
     * @param type $classesNames    导入的班级名称['初一年级1班','初一（2）班']
     */
    public function calClassesMatchArr($schoolId, $gradeId, $yearId, $classesNames){
        // 提取已有的班级
        $con = [];
        $con[] = ['school_id','=',$schoolId];
        $con[] = ['grade_id','=',$gradeId];
        $con[] = ['year_id','=',$yearId];
        
        $lists = self::where($con)->select();
        $listsArr = $lists ? $lists->toArray() : [];
        foreach($listsArr as &$ve){
            $ve['nKey'] = Strings::keepCNNumber($ve['name']);
        }
        // ["一1"] = "5525222707247554560"
        $arrN = array_column($listsArr, 'id','nKey');

        //导入数组
        $arrI = [];
        foreach($classesNames as $v){
            // $key格式：一3
            $key = Strings::keepCNNumber($v);
            $kId = Arrays::value($arrN, $key);
            if(!$kId){
                throw new Exception('系统找不到班级"'.$v.'"，请先添加');
            }

            $arrI[$v] = $kId;
        }
        // ["初一年1班"] = "5525222707247554560"
        return $arrI;
    }
}
