<?php

namespace xjryanse\edu\service\year;

use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use xjryanse\edu\service\EduGradeService;
use Exception;
/**
 * 字段复用列表
 */
trait ListTraits{

    /**
     * 当前入学年份，获取关联的学年
     */
    public function listGradeYears($param = []){
        $gradeId = Arrays::value($param, 'grade_id');
        if(!$gradeId){
            throw new Exception('grade_id必须');
        }
        
        // 提取入学年份的开始日期，结束日期。
        $startDate  = EduGradeService::getInstance($gradeId)->fStartDate();
        $endDate    = EduGradeService::getInstance($gradeId)->fEndDate();
        // 查询学年列表:
        // 结束时间大于学级开始时间
        // 开始时间小于学级结束时间
        $con    = [];
        $con[]  = ['end_date','>=',$startDate];
        $con[]  = ['start_date','<=',$endDate];
        // 取列表
        $lists = self::staticConList($con);
        foreach($lists as &$v){
            // 计算几年级
            $v['gradeNo']   = EduGradeService::getInstance($gradeId)->calGradeNo($v['end_date']);
            // $v['gradeStr']  = '一年级';
        }
        
        return Arrays2d::addData($lists, ['grade_id'=>$gradeId]);
    }
}
