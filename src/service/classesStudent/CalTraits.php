<?php

namespace xjryanse\edu\service\classesStudent;

use think\Db;
use xjryanse\logic\Cachex;
/**
 * 字段复用列表
 */
trait CalTraits{
    /**
     * 根据学生和分类，计算年级
     * 
     * 20230912:根据学年计算年级
     */
    public function calGradeIdByStudentIdCateId($studentId, $cateId){
        $cacheKey = __METHOD__.$studentId.$cateId;
        return Cachex::funcGet($cacheKey, function() use($studentId, $cateId){
            $sql    = self::mainModel()::sqlGradeIdByStudentIdCateId($studentId, $cateId);
            $lists  = Db::query($sql);
            return $lists ? $lists[0]['grade_id'] : '';
        },true,300);
    }
}
