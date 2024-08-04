<?php

namespace xjryanse\edu\service\classes;

/**
 * 字段复用列表
 */
trait FieldTraits{

    public function fSchoolId() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fGradeId() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fGradeNo() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fYearId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fName() {
        return $this->getFFieldValue(__FUNCTION__);
    }
}
