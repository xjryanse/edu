<?php

namespace xjryanse\edu\service\student;

/**
 * 字段复用列表
 */
trait FieldTraits{
    /**
     * 姓名
     * @return type
     */
    public function fRealname() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fIdNo() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fBirthday() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fSex() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
