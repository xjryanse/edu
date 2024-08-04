<?php
namespace xjryanse\edu\model;

/**
 * 
 */
class EduCate extends Base
{
    public static $picFields = ['phyexam_tpl_id'];

    public function getPhyexamTplIdAttr($value) {
        return self::getImgVal($value);
    }

    /**
     * @param type $value
     * @throws \Exception
     */
    public function setPhyexamTplIdAttr($value) {
        return self::setImgVal($value);
    }

}