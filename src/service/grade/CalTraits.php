<?php

namespace xjryanse\edu\service\grade;

use xjryanse\edu\service\EduYearService;
use xjryanse\edu\service\EduCateService;
use xjryanse\logic\Datetime;
/**
 * 字段复用列表
 */
trait CalTraits{
    /**
     * 20230912:根据学年计算年级
     */
    public function calGradeNoByYearId($yearId){
        $yearInfo = EduYearService::getInstance($yearId)->get();
        return $this->calGradeNo($yearInfo['end_date']);
    }
    
    /**
     * 计算年级名称
     * 保存时自动生成的名称
     */
    public static function calName($cateId, $startDate){
        $year = date('Y',strtotime($startDate));
        $cateName = EduCateService::getInstance($cateId)->fName();
        return $year.'级'.$cateName;
    }

    /**
     * 计算几年级
     */
    public function calGradeNo($date){
        $info = $this->get();
        return ceil(Datetime::dayDiff($date,$info['start_date']) / 365);
    }
    
    /**
     * 计算开始年份，一般用于分表。
     * 
     */
    public function calYear(){
        $gradeInfo = $this->get();
        return $gradeInfo['start_date'] ? date('Y',strtotime($gradeInfo['start_date'])) : '';
    }
    /**
     * 20231025：提取后续的年份列表
     * @param type $yearId
     */
    public function calAfterYears($yearId){
        // 入学年份
        $gradeYear = $this->calYear();
        // 学制（年）
        $years = $this->fYears();
        // 当前入参学年年份
        $thisYear = EduYearService::getInstance($yearId)->calYear();
        // 剩余年份
        $remainYears = $years - ($thisYear - $gradeYear) - 1 ;

        $yearsArr = [] ;
        for($i=1;$i<=$remainYears;$i++){
            $yearsArr[] = $thisYear + $i;
        }
        return $yearsArr;
    }
    /**
     * 计算后续年份
     * @param type $yearId
     * @return string
     */
    public function calAfterYearIds($yearId){
        $years = $this->calAfterYears($yearId);

        $yearArr = [];
        foreach($years as $year){
            $yearArr[] = EduYearService::calEduYearIdByStartYear($year);
        }

        return $yearArr;
    }
    
}
