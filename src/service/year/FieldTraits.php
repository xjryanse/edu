<?php

namespace xjryanse\edu\service\year;

/**
 * 字段复用列表
 */
trait FieldTraits{
    public function fName() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
