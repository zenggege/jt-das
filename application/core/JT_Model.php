<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * JT 数据库Model
 * 
 */
class JT_Model extends CI_Model{

  /**
    * 表名
    *
    * @var string
   */
    protected $table_name = '';

    /**
    * 表名前缀
    *
    * @var string
   */
    public  $db_tablepre = 't_jt_';

    /**
    * 记录数量
    *
    * @var int
    */
    public $number;

    /**
    * 记录数量
    * $number alias
    *
    * @var int
    */
    public $data_count;

    /**
    * 分页数量
    *
    * @var int
    */
    public $page_count;

    /**
    * 页码
    * 设置或获取页码
    *
    * @var int
    */
    public $page_index = 1;

    /**
    * 分页大小
    * 设置或获取分页大小
    *
    * @var int
    */
    public $page_size = DEFAULT_PAGE_SIZE;

    /**
    * 数据库
    *
    * @var object
    */
    public $db;

    public $is_db;
    
  /**
   * 构造器
   *
   * @return  void
   */
    function __construct(){
    
        $this->table_name = $this->db_tablepre.$this->table_name;
        parent::__construct();
        $this->set_db();
    }
    
  // ------------------------------------------------------------------------
    function set_db($db_config='')
    {
        $this->db= $this->load->database($db_config,TRUE);
    }

    
    /**
     *  设置表名
     */
    function set_table_name($tablename='',$tablepre='t_sys_')
    {
        $this->db_tablepre = $tablepre;
        $this->table_name = $this->db_tablepre.$this->table_name;
    }
    
    
    /**
     * 执行sql查询
     * @param $where        可填，查询条件[例array('name'=>$name)]
     * @param $fields   可填，需要查询的字段值[例"name,gender,birthday"]
     * @param $order        可填，排序方式 [默认按数据库默认方式排序]
     * @param $group        可填，分组方式 [默认为空]
     * @param $key      可填，返回数组按键名排序
     * @return array/null       可填，查询结果集数组
     */
    final public function select($where = '', $fields = '*',  $order_by = '', $group_by = '', $key='', $auto_count = true) {
        if ($auto_count) $this->data_count = intval($this->count($where,$group_by));
        
        $page = max(intval($this->page_index), 1);
        $pagesize = max(intval($this->page_size), 1);
        $offset = $pagesize*($page-1);

        //是否开启超出页面大小保护, 开启了就如果有100页， 传入101页，也会自动转入100页
        // if(SAFT_PAGE_INDEX){
        //     if($offset>$this->data_count)
        //     {
        //         $page=round($this->data_count/$pagesize);
        //         $offset = max($pagesize*($page-1),0);
        //     }
        // }
        
        //设置页数
        $this->page_count =  ceil($this->data_count / $pagesize);

        if (!empty($where)) $where = $this->db->where($where); 

        $fields = str_replace("，", ",", $fields);
        $this->db->select($fields);
        $this->db->limit($pagesize, $offset);
        
        if(!empty($order_by))$this->db->order_by($order_by); 
        if(!empty($group_by))$this->db->group_by($group_by);
        
        $this->db->from($this->table_name);
        $Q = $this->db->get();
        return $this->process_db_result($Q, $key);
    }

    /**
     * 处理sql查询结果
     * @return array        查询结果集数组
     */
    final public function process_db_result(&$Q, $key='') {
        
        $datalist = null;
        if ($Q->num_rows() > 0)
        {
          foreach ($Q->result_array() as $rs)
          {
            if($key) {
                if(isset($rs[$key])) 
                    $datalist[$rs[$key]] = $rs;
                else
                    $datalist[] = $rs;
            } else {
                    $datalist[] = $rs;
                }
          }
        }
        $Q->free_result();

        return $datalist;
    }
    
    /**
     * 直接执行sql查询(默认为写)
     * @param $sql      SQL
     * @param $key      返回数据主键
     * @return array        查询结果集数组
     */
    final public function query($sql, $key ='') {
        $Q = $this->db->query($sql);
        return $this->process_db_result($Q, $key);
    }

    /**
     * 获取单条记录查询
     * @param $where        可填，查询条件
     * @param $fields   可填，需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param $order        可填，排序方式 [默认按数据库默认方式排序]
     * @param $group        可填，分组方式 [默认为空]
     * @return array/null   数据查询结果集,如果不存在，则返回空
     */
    final public function one($where = '', $fields = '*', $order_by = '', $group_by = '') {

        $this->is_db = false;
        $r = $this->db->from($this->table_name)->select($fields)->limit(1);
        if($where){
            $r->where($where);
        }
        if($group_by){
            $r->group_by($group_by);
        }
        if($order_by){
            $r->order_by($order_by);
        }
        $result = $r->get()->row_array();

        return $result;
    }


    /**
     * 获得单个字段值
     * @param string/array $where 查询条件
     */
    final public function field_value($field, $where = '') {

        $this->is_db = false;
        if(is_array($field))throw new Exception("只能是一个字段", 1);
        
        $r = $this->db->from($this->table_name)->select($field)->limit(1);
        if($where){
            $r->where($where);
        }
        $result = $r->get()->row_array();

        return isset($result[$field])?$result[$field]:'';
    }
    
    /**
     * 执行添加记录操作
     * @param $data         要增加的数据，参数为数组。数组key为字段值，数组值为数据取值
     * @param $return_insert_id 是否返回新建ID号
     * @return boolean
     */
    final public function insert($data, $return_insert_id = true) {
        $this->is_db = true;
        $this->db->insert($this->table_name, $data);
        if($return_insert_id)return $this->db->insert_id();
    }

    /**
     * 批量新增
     * @param $data
     * @return boolean
     */
    final public function insert_batch($data)
    {
        $this->is_db = true;
        return $this->db->insert_batch($this->table_name, $data);
    }

    final public  function update_batch($data,$field){
        $this->is_db = true;
        return $this->db->update_batch($this->table_name, $data,$field);

    }
    
    /**
     * 获取最后一次添加记录的主键号
     * @return int 
     */
    final public function insert_id() {
        return $this->db->insert_id();
    }
    
    /**
     * 执行更新记录操作
     * @param $data         要更新的数据内容，参数可以为数组也可以为字符串，建议数组。
     *                      为数组时数组key为字段值，数组值为数据取值
     *                      为字符串时[例：`name`='phpcms',`hits`=`hits`+1]。
     *                      为数组时[例: array('name'=>'phpcms','password'=>'123456')]
     *                      数组的另一种使用array('name'=>'+=1', 'base'=>'-=1');程序会自动解析为`name` = `name` + 1, `base` = `base` - 1
     * @param $where        更新数据时的条件,可为数组或字符串
     * @param $return_affected_rows         是否返回影响行数
     * @return boolean
     */
    final public function update($data, $where = '',$return_affected_rows=true) {
        
        $this->is_db = true;
        $this->db->where($where);
    
        if(is_array($data))
            {
                foreach($data as $k=>$v)
                {
                    switch (substr($v, 0, 2)) {
                        case '+=':
                            $this->db->set($k, $k."+".str_replace("+=","",$v), false);
                            unset($data[$k]);
                            break;
                        case '-=':
                            $this->db->set($k, $k."-".str_replace("-=","",$v), false);
                            unset($data[$k]);
                            break;
                        case '<>':
                            $this->db->set($k, $k."<>".$v, false);
                            unset($data[$k]);
                            break;
                        case '<=':
                            $this->db->set($k, $k."<=".$v, false);
                            unset($data[$k]);
                            break;
                        case '>=':
                            $this->db->set($k, $k.">=".$v, false);
                            unset($data[$k]);
                            break;
                        case '^1':
                            $this->db->set($k, $k."^1", false);
                            unset($data[$k]);
                            break;
                        case 'in':
                            if(substr($v, 0, 3)=="in("){
                                $this->db->where_in($k, $v, false);
                                unset($data[$k]);
                                break;
                            }else{

                            }

                        default:
                            $this->db->set($k, $v, true);
                    }
                }
            }
        
        $this->db->update($this->table_name, $data);

        return $return_affected_rows? $this->db->affected_rows() : true;
    }
    
    /**
     * 返回最后运行的查询（是查询语句，不是查询结果）
     * @return int 
     */
    final public function last_query() {
        return $this->is_db?$this->db->last_query():$this->db->last_query();
    }
    
    /**
     * 执行删除记录操作
     * @param $where        删除数据条件,不充许为空。
     * @return boolean
     */
    final public function delete($where) {
        $this->is_db = true;
        return $this->db->delete($this->table_name, $where);
    }
    
    /**
     * 计算记录数
     * @param string/array $where 查询条件
     */
    final public function count($where = '',$group_by = '') {
        $this->is_db = false;
        $r = $this->db->from($this->table_name);
        if($where){
            $r->where($where);
        }
        if($group_by){
            $r->group_by($group_by);
        }
        $r = $r->count_all_results();
    
        return $r;
    }
    
    
    /**
     * 合计sum记录数
     * @param string/array $where 查询条件
     */
    final public function sum($field,$where = '') {
        $this->is_db = false;
        $r = $this->db->from($this->table_name)->select_sum($field, 's');
        if($where){
            $r->where($where);
        }
        $result = $r->get()->row_array();

        return isset($result['s'])?$result['s']:0;
    }
    
    /**
     * 求字段最大值
     * @param string/array $where 查询条件
     */
    final public function max($field,$where = '') {
        $this->is_db = false;
        $r = $this->db->from($this->table_name)->select_max($field,'m');
        if($where){
            $r->where($where);
        }
        $result = $r->get()->row_array();

        return isset($result['m'])?$result['m']:0;
    }

    
    /**
     * 拼写in语句
     * 生成sql语句，如果传入$in_cloumn 生成格式为 IN('a', 'b', 'c')
     * 
     * @param $data 条件数组或者字符串
     * @param $front 连接符
     * @param $in_column 字段名称
     * @return string
     */
    final public function spell_in($data, $front = ' AND ', $in_column = false, $is_digt=false) {
        if($in_column && is_array($data)) {

            $ids = '\''.implode('\',\'', $data).'\'';
            if($is_digt)$ids = implode(',', $data) ;
            $sql = "$in_column IN ($ids)";
            return $sql;
        
        }

        if ($front == '')  $front = ' AND ';
        if(is_array($data) && count($data) > 0) {
            $sql = '';
            foreach ($data as $key => $val) {
                $sql .= $sql ? " $front `$key` = '$val' " : " `$key` = '$val' ";    
            }
            return $sql;
        }

        return $data;
    }


        /**
     * 开启事务
     */
    final public function trans_begin() {
        $this->db->trans_begin();
    }

    /**
     * 提交事务
     */
    final public function trans_commit() {
        $this->db->trans_commit();
    }

    /**
     * 提交事务
     */
    final public function trans_rollback() {
        $this->db->trans_rollback();
    }

    final public function trans_status() {
        $this->db->trans_status();
    }
    /**
     * 计算记录数X锁
     * @param string/array $where 查询条件
     */
    final public function count_lock($where = '',$group_by = '') {

        $r = $this->db->from($this->table_name);
        if($where){
            $r->where($where);
        }

        if($group_by){
            $r->group_by($group_by);
        }
        $r->select('count(*) as c');

        $sql = $this->db->get_compiled_select();
        $query = $this->db->query($sql.' for update');
        $row = $query->row();
        $order_count = $row->c;
        return $order_count;
    }

    /**
     * 获取单条记录查询
     * @param $where        可填，查询条件
     * @param $fields   可填，需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param $order        可填，排序方式 [默认按数据库默认方式排序]
     * @param $group        可填，分组方式 [默认为空]
     * @return array/null   数据查询结果集,如果不存在，则返回空
     */
    final public function one_lock($where = '', $fields = '*', $order_by = '', $group_by = '') {

        $r = $this->db->from($this->table_name)->select($fields)->limit(1);
        if($where){
            $r->where($where);
        }
        if($group_by){
            $r->group_by($group_by);
        }
        if($order_by){
            $r->order_by($order_by);
        }

        $sql = $this->db->get_compiled_select();
        $result = $this->db->query($sql.' for update')->row_array();

        return $result;
    }

    /**
     * 执行sql查询
     * @param $where        可填，查询条件[例array('name'=>$name)]
     * @param $fields   可填，需要查询的字段值[例"name,gender,birthday"]
     * @param $order        可填，排序方式 [默认按数据库默认方式排序]
     * @param $group        可填，分组方式 [默认为空]
     * @param $key      可填，返回数组按键名排序
     * @return array/null       可填，查询结果集数组
     */

    final public function select_lock($where = '', $fields = '*',  $order_by = '', $group_by = '', $key='', $auto_count = true) {

        if ($auto_count) $this->data_count = intval($this->count($where,$group_by));

        
        $page = max(intval($this->page_index), 1);
        $pagesize = max(intval($this->page_size), 1);
        $offset = $pagesize*($page-1);
        
        //是否开启超出页面大小保护, 开启了就如果有100页， 传入101页，也会自动转入100页
        if(SAFT_PAGE_INDEX){
            if($offset>$this->data_count)
            {
                $page=round($this->data_count/$pagesize);
                $offset = max($pagesize*($page-1),0);
            }
        }
        
        //设置页数
        $this->page_count =  ceil($this->data_count / $pagesize);

        if (!empty($where)) $where = $this->db->where($where); 

        $fields = str_replace("，", ",", $fields);
        $this->db->select($fields);
        $this->db->limit($pagesize, $offset);
        
        if(!empty($order_by))$this->db->order_by($order_by); 
        if(!empty($group_by))$this->db->group_by($group_by);
        
        $this->db->from($this->table_name);

        $sql = $this->db->get_compiled_select();
        return $this->db->query($sql.' for update')->result_array();

    }
    
}
