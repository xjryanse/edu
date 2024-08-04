<?php

namespace xjryanse\edu\service\grade;

/**
 * 字段复用列表
 */
trait FieldTraits{

    public function fStartDate() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fEndDate() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fCateId() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fYears(){
        return $this->getFFieldValue(__FUNCTION__);
    }
}
