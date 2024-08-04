<?php

namespace xjryanse\edu\service\catePhyexamItem;

use xjryanse\phyexam\service\PhyexamItemJobService;

/**
 * 
 */
trait ListTraits{
    /*
     * 根据cateId，提取应检项目列表
     * page_id维度列表
     */
    public static function listByCateId($cateId){
        $con    = [];
        $con[]  = ['cate_id','in',$cateId];
        // return self::column('phyexam_item_id',$con);
        return self::staticConList($con);
    }
    /**
     * 岗位的检测项目列表
     * @param type $jobId
     * @param type $cateId
     * @return type
     */
    public static function listByJobAndCate($jobId, $cateId){
        $cateList   = self::listByCateId($cateId);
        // 20240117：岗位的检测项目
        $jItemIds   = PhyexamItemJobService::dimItemIdsByJobId($jobId);
        
        $arr = [];
        foreach($cateList as $v){
            if(in_array($v['phyexam_item_id'], $jItemIds)){
                $arr[] = $v;
            }
        }
        return $arr;
    }
    
}
