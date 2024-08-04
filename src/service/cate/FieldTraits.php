<?php

namespace xjryanse\edu\service\cate;

/**
 * 字段复用列表
 */
trait FieldTraits{

    public function fName() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fYears() {
        return $this->getFFieldValue(__FUNCTION__);
    }
}
