<?php

namespace xjryanse\edu\service\cate;

use xjryanse\edu\service\EduCatePhyexamItemService;
use xjryanse\logic\Arrays;
/**
 * 字段复用列表
 */
trait TriggerTraits{
    
    /**
     * 钩子-保存前
     */
    public static function extraPreSave(&$data, $uuid) {
        self::stopUse(__METHOD__);
    }

    public static function extraPreUpdate(&$data, $uuid) {
        self::stopUse(__METHOD__);
    }
    
    public function extraPreDelete() {
        self::stopUse(__METHOD__);
    }
    /**
     * 钩子-保存前
     */
    public static function ramPreSave(&$data, $uuid) {
        //有传权限数据，则保存权限
        $itemIds = Arrays::value($data, 'phyexamItemIds', []);
        if ($itemIds) {
            EduCatePhyexamItemService::savePhyexamItems($uuid, $itemIds);
        }

    }

    /**
     * 钩子-保存前
     */
    public static function ramPreUpdate(&$data, $uuid) {
        //有传权限数据，则保存权限
        $itemIds = Arrays::value($data, 'phyexamItemIds', []);
        if ($itemIds) {
            EduCatePhyexamItemService::savePhyexamItems($uuid, $itemIds);
        }
    }
}
