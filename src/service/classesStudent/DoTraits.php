<?php

namespace xjryanse\edu\service\classesStudent;

use xjryanse\edu\service\EduStudentService;
use xjryanse\edu\service\EduClassesService;
use xjryanse\edu\service\EduGradeService;
use xjryanse\edu\service\EduYearService;
use xjryanse\generate\service\GenerateTemplateService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Word;
use xjryanse\logic\FileTemp;
use Exception;

/**
 * 
 */
trait DoTraits{
    
    protected static $docBasePath = "/Uploads/Download/CanDelete/";
    /**
     * 按班级，批量添加学生
     * @param type $param
     */
    public static function doStudentBatchAddByClasses($param){
        $classesId = Arrays::value($param, 'classes_id');
        if(!$classesId){
            throw new Exception('未指定班级');
        }
        $studentArr = Arrays::value($param, 'table_data', []);
        $res = self::batchAddClassesStudent($classesId, $studentArr);
        return $res;
    }
    /**
     * 20240308:按班级，单个添加学生
     * @param type $param
     * @return type
     * @throws Exception
     */
    public static function doStudentAddByClasses($param){
        $classesId = Arrays::value($param, 'classes_id');
        if(!$classesId){
            throw new Exception('未指定班级');
        }
        
        $keys       = ['realname','id_no','nation'];
        $studentArr = [Arrays::getByKeys($param, $keys)];
        $res        = self::batchAddClassesStudent($classesId, $studentArr);
        return $res;
    }
    
    /**
     * 按年级导出学生数据
     * @createTime 2023-10-15
     * @param type $param
     * @throws Exception
     */
    public static function doStudentBatchAddByGradeYear($param){
        $gradeId = Arrays::value($param, 'grade_id');
        if(!$gradeId){
            throw new Exception('未指定入学年份');
        }

        $yearId = Arrays::value($param, 'year_id');
        if(!$yearId){
            throw new Exception('未指定学年');
        }
        
        $schoolId = Arrays::value($param, 'school_id');
        if(!$yearId){
            throw new Exception('未指定学校');
        }

        $studentArr = Arrays::value($param, 'table_data', []);
        // ['初一年1班','初一年2班','初一年3班','初一年4班']
        $classesNames = array_unique(array_column($studentArr,'classesName'));
        // 提取班级名匹配的班级id：根据”一1“表示 一年1班
        $classesIdArr = EduClassesService::calClassesMatchArr($schoolId, $gradeId, $yearId, $classesNames);
        // 循环每个班级，添加学生数据
        foreach($classesNames as $v){
            // 匹配数组中提取id
            $classesId = Arrays::value($classesIdArr, $v);
            $con    = [];
            $con[]  = ['classesName','=',$v];
            // 提取当班学生
            $thisStudentArr = Arrays2d::listFilter($studentArr, $con); 
            
            self::batchAddClassesStudent($classesId, $thisStudentArr);
        }
        return true;
    }
    
    /**
     * 学生二维码导出
     * @param type $classesId   班级编号
     */
    public static function doStudentQrExportByClasses($classesId){
        // 20230919：清理历史文件
        FileTemp::unlink();
        
        $studentIds = self::dimStudentIdsByClassesId($classesId);
        
        $classInfo = EduClassesService::getInstance($classesId)->get();

        $con[] = ['id','in',$studentIds];
        $studentList = EduStudentService::lists($con);
        foreach($studentList as &$v){
            $v['text'] = $classInfo['name'].'<w:br/>'.$v['realname'].'<w:br/>'.$v['id_no'];
        }
        //学生二维码
        $key = 'eduStudentQr';
        $id = GenerateTemplateService::keyToId($key);
        $info = GenerateTemplateService::getInstance($id)->get();
        
        // 测试生成学生体检二维码
        $PHPWord            = new Word();
        $PHPWord->loadTemplate('./'.$info['template_id']['rawPath']);
        $PHPWord->cloneRow('img1', count($studentList));
        // 二维码
        $keyReflect = [];
        $keyReflect['id_no'] = 'img';
        $PHPWord->setQrcodeArr($studentList, $keyReflect);
        // 文本
        $keyReflectTxt = [];
        $keyReflectTxt['text'] = 'name';
        $PHPWord->setDataArr($studentList, $keyReflectTxt);
        
        $filename = self::$docBasePath.time().'.docx';  // Word 文件的路径和文件名
        $PHPWord->save('.'.$filename);

        $rData['fileName'] = $classInfo['name'].'-二维码.docx';
        $rData['url'] = $filename;
        
        return $rData;
    }
    /**
     * 按年级升级学生数据
     * @param type $param
     */
    public static function doStudentUpgradeByGradeId($param){
        $gradeId    = Arrays::value($param, 'grade_id');
        $yearId     = Arrays::value($param, 'year_id');
        // 前一个年份
        $preYearId  = EduYearService::getInstance($yearId)->calPreYearIdWithGradeId($gradeId);
        if(!$preYearId){
            throw new Exception('没有前序学年');
        }
        $field = 'id,grade_id,year_id,classes_no';
        // 提取前序年份的班级数
        $preClasses  = EduClassesService::dimListByGradeIdYearId($gradeId, $preYearId,$field);
        // 提取当前年份的班级数
        $thisClasses = EduClassesService::dimListByGradeIdYearId($gradeId, $yearId,$field);
        if(count($preClasses) != count($thisClasses)){
            throw new Exception('当前学年与前序学年班级数不匹配'.count($preClasses).'-'.count($thisClasses));
        }
        $preClassesArr = array_column($preClasses, 'id','classes_no');
        //循环班级
        foreach($thisClasses as $v){
            // 班号匹配的前一学年班级编号
            $preClassesId = Arrays::value($preClassesArr, $v['classes_no']);
            // 复制学生
            self::studentCopyRam($preClassesId, $v['id']);
        }
        return true;
    }

}
