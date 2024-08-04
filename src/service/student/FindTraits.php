<?php

namespace xjryanse\edu\service\student;

use xjryanse\logic\Datetime;
use xjryanse\logic\Arrays;
use Exception;
/**
 * 字段复用列表
 */
trait FindTraits{
    /**
     * 20240117:根据参数查询学生信息
     * @param type $param
     * @return type
     */
    public static function findByInfo($param){
        $idNo       = Arrays::value($param, 'id_no');
        $realname   = Arrays::value($param, 'realname');
        $con[]  = ['id_no','=',$idNo];
        $con[]  = ['realname','=',$realname];
        $info   = self::where($con)->find();
        if(!$info){
            throw new Exception('没有匹配的结果，请核对姓名和身份证号是否匹配');
        }
        return $info;
    }
    /**
     * 家长输入姓名和身份证号查询
     * @param type $param
     * @return type
     * @throws Exception
     */
    public static function findIdByInfo($param){
        $data       = Arrays::value($param, 'table_data') ? : [];
        $idNo       = Arrays::value($data, 'id_no');
        $realname   = Arrays::value($data, 'realname');
        $con[]  = ['id_no','=',$idNo];
        $con[]  = ['realname','=',$realname];
        $id     = self::where($con)->value('id');
        if(!$id){
            throw new Exception('没有匹配的结果，请核对姓名和身份证号是否匹配');
        }
        return $id;
    }

}
