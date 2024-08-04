<?php

namespace xjryanse\edu\service\catePhyexamItem;

use xjryanse\phyexam\service\PhyexamItemService;
/**
 * 
 */
trait DimTraits{

    /*
     * 根据cateId，提取应检项目列表
     * page_id维度列表
     */
    public static function dimItemIdsByCateId($cateId,$con=[]){
        $con[]  = ['cate_id','in',$cateId];
        // return self::column('phyexam_item_id',$con);
        return self::staticConColumn('phyexam_item_id',$con);
    }
    /*
     * 根据cateId，提取应检项目列表(只提取最终填写的项目)
     * page_id维度列表
     */
    public static function dimFinalItemIdsByCateId($cateId, $con = []){
        //【1】提取最终项目
        $finalItems = PhyexamItemService::dimFinalIds();
        //【2】:提取项目
        $con[]  = ['phyexam_item_id','in',$finalItems];
        $con[]  = ['cate_id','in',$cateId];
        // return self::column('phyexam_item_id',$con);
        return self::staticConColumn('phyexam_item_id',$con);
    }
    
    /*
     * 根据cateId，提取应检项目列表(只提取最终填写的项目)
     * page_id维度列表
     */
    public static function dimReportItemIdsByCateId($cateId, $con = []){
        //【1】提取报告项目：直检+衍生
        $finalItems = PhyexamItemService::dimReportIds();
        //【2】:提取项目
        $con[]  = ['phyexam_item_id','in',$finalItems];
        $con[]  = ['cate_id','in',$cateId];
        // return self::column('phyexam_item_id',$con);
        return self::staticConColumn('phyexam_item_id',$con);
    }
}
