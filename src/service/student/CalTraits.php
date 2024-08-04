<?php

namespace xjryanse\edu\service\student;

use xjryanse\logic\Datetime;
use xjryanse\edu\service\EduYearService;
use xjryanse\edu\service\EduClassesService;


/**
 * 字段复用列表
 */
trait CalTraits{

    /**
     * 计算幼儿园，小学，中学
     */
    public function calCate($time){
        // 时间提取学年；
        
        // 测试一下；
        return 2;

    }
    /**
     * 20231030：计算年龄，周岁
     */
    public function calAge($time = ''){
        if(!$time){
            $time = date('Y-m-d H:i:s');
        }
        $birthday = $this->fBirthday();
        return Datetime::calAge($birthday, $time);
    }
    /**
     * 20240308：计算月龄
     */
    public function calAgeMonth(){
        $age = $this->calAge();
        $ageMonth = intval($age * 12);
        return $ageMonth;
    }
    
    /**
     * 根据时间，计算学生的所在年级
     */
    public function calClassesIdByTime($time){
        // 时间提取学年
        $eduYearId  = EduYearService::calEduYearId($time);
        $studentId  = $this->uuid;
        // 根据学生和学年，提取班级
        $eduClassesId = EduClassesService::calClassesIdByYearStudent($eduYearId, $studentId);
        return $eduClassesId;
    }
    /**
     * 根据时间，计算学生的所在年级
     */
    public function calGradeIdByTime($time){
        // 根据学生和学年，提取班级
        $eduClassesId = $this->calClassesIdByTime($time);
        // 拼接班级数据
        $gradeId   = EduClassesService::getInstance($eduClassesId)->fGradeId();
        return $gradeId;
    }
    
}
