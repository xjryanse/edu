<?php
namespace xjryanse\edu\model;

use xjryanse\edu\service\EduClassesService;
use xjryanse\edu\service\EduClassesStudentService;
use xjryanse\phyexam\service\PhyexamItemJobService;
use xjryanse\phyexam\service\PhyexamItemService;
use think\Db;
/**
 * 
 */
class EduCatePhyexamItem extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'cate_id',
            // 去除prefix的表名
            'uni_name'  =>'edu_cate',
            'uni_field' =>'id',
            'del_check'=> true,
        ],
        [
            'field'     =>'phyexam_item_id',
            // 去除prefix的表名
            'uni_name'  =>'phyexam_item',
            'uni_field' =>'id',
            'in_statics'=> true,
        ]
    ];

    /**
     * 分类的项目sql,(一般用于管理端跟踪遗漏)
     * @createTime 2023-10-21
     */
    public static function sqlCateItems($cateId, $con = []){
        $con[]      = ['cate_id','in',$cateId];
        $fields     = ['id','company_id','cate_id','phyexam_item_id'];
        $sql        = Db::name('edu_cate_phyexam_item')->where($con)->field(implode(',',$fields))->buildSql();
        return $sql;
    }
    /**
     * 班级的检测项目（所有学生所有项目可能情况）
     * @param type $classesId   班级
     * @param type $conc        班级条件
     * @param type $coni        项目条件
     * @return string
     */
    public static function sqlClassesPhyexamItem($classesId, $conc = [], $coni=[]){
        $cateId         = EduClassesService::getInstance($classesId)->calCateId();
        $coni[]         = ['cate_id','in',$cateId];
        // 检测项目
        $itemSql        = Db::name('edu_cate_phyexam_item')->where($coni)->field('phyexam_item_id')->buildSql();
        // 班级学生
        $classStuTable  = EduClassesStudentService::mainModel()->getTable();
        
        $conc[] = ['classes_id','in',$classesId];
        $classesStuSql = Db::table($classStuTable)->where($conc)->field('classes_id,student_id')->buildSql();
        
        $sqlAll = '(select * from '.$classesStuSql.'as classesStuSql join '.$itemSql.' as itemSql)';

        return $sqlAll;
    }
    /**
     * 班级学生的检测项目(单一学生所有项目)
     * @param type $classesId   班级
     * @param type $studentId   学生
     * @return type
     */
    public static function sqlClassesStudentPhyexamItem($classesId, $studentId){
        $conc   = [];
        $conc[] = ['student_id','in',$studentId];
        
        return self::sqlClassesPhyexamItem($classesId, $conc);
    }
    
    
    /**
     * 班级的检测岗位（所有学生所有岗位跟踪）
     * 
     * 班级提取cate,cate提取项目，项目提取岗位
     * @param type $classesId   班级
     * @param type $conc        班级条件
     * @param type $coni        项目条件
     * @return string
     */
    public static function sqlClassesPhyexamJob($classesId, $conc = [], $coni=[]){
        $cateId         = EduClassesService::getInstance($classesId)->calCateId();
        $coni[]         = ['a.cate_id','in',$cateId];
        $coni[]         = ['c.is_final','=',1];
        
        $itemJobTable   = PhyexamItemJobService::getTable();
        $itemTable      = PhyexamItemService::getTable();
        // 检测项目
        $jobItemSql        = Db::name('edu_cate_phyexam_item')
                ->where($coni)->alias('a')
                ->join($itemJobTable.' b','a.phyexam_item_id = b.item_id')
                ->join($itemTable.' c','b.item_id=c.id')
                ->field('b.job_id,count( DISTINCT b.item_id ) AS jobItemCount')
                ->group('b.job_id')
                ->buildSql();

        // 班级学生
        $classStuTable  = EduClassesStudentService::mainModel()->getTable();
        
        $conc[] = ['classes_id','in',$classesId];
        $classesStuSql = Db::table($classStuTable)->where($conc)->field('classes_id,student_id')->buildSql();
        
        $sqlAll = '(select * from '.$classesStuSql.'as classesStuSql join '.$jobItemSql.' as jobItemSql)';

        return $sqlAll;
    }
    
    /**
     * 班级学生的检测岗位(单一学生所有项目)
     * @param type $classesId   班级
     * @param type $studentId   学生
     * @return type
     */
    public static function sqlClassesStudentPhyexamJob($classesId, $studentId){
        $conc   = [];
        $conc[] = ['student_id','in',$studentId];
        
        return self::sqlClassesPhyexamJob($classesId, $conc);
    }
    
}