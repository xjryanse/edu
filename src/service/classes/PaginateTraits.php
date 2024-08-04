<?php

namespace xjryanse\edu\service\classes;

use xjryanse\logic\ModelQueryCon;
use xjryanse\edu\service\EduYearService;
use xjryanse\logic\DataList;
use xjryanse\logic\Arrays;
use Exception;

/**
 * 字段复用列表
 */
trait PaginateTraits{
    /**
     * 学校班级，按年度区分
     * @param type $con
     * @param type $order
     * @param type $perPage
     * @param type $having
     * @param type $field
     * @param type $withSum
     * @return string
     * @throws Exception
     */
    public static function schoolClassesYearlyPaginate($con, $order = '', $perPage = 10, $having = '', $field = "*", $withSum = false){
        // $fields = self::tableIdFieldsArr();
        $schoolId   = ModelQueryCon::parseValue($con, 'school_id');
        if(!$schoolId){
            throw new Exception('school_id必须');
        }
        // 学年
        $yearId   = ModelQueryCon::parseValue($con, 'year_id');
        if(!$yearId){
            // 20230913:没有的取最后一个
            $yearId = EduYearService::lastYearId();
        }
        // 学校，学年，年级，班级
        $arrList    = self::schoolYearlyGradeClassesList($schoolId, $yearId);
        // 封装分页
        $pgLists    = DataList::dataPackPaginate($arrList, false, [], $perPage);
        // 班级清单
        $classes    = self::schoolYearlyClassesArr($schoolId, $yearId);

        foreach ($classes as $ve) {
            $popParam = ['classes_id'=>'clId'.$ve];
            // 20230604:控制前端页面显示的动态站点:字段格式：universal_item_table表
            $pgLists['fdynFields'][] = ['id' => self::mainModel()->newId(), 'name' => 'cl'.$ve, 'label' => $ve.'班'
                , 'type' => 'text','pop_param'=>$popParam];
        }

        return $pgLists;
    }

}
