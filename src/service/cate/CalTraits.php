<?php

namespace xjryanse\edu\service\cate;

/**
 * 
 */
trait CalTraits{
    /**
     * 20230923：计算分类字串
     */
    public static function calCateStr($cateIds){
        $con[] = ['id','in',$cateIds];
        $lists = self::staticConList($con);
        
        return implode(',',array_column($lists, 'name'));
    }
}
