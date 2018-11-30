<?php
namespace database;
class DBHelper
{
    static $mysqli_con;
    private $conn;
    private $host;
    private $username;
    private $password;


    protected function __construct()
    {
        $this->host='127.0.0.1';
        $this->username='root';
        $this->password='root';
        $this->dbname='diary';
        $this->open();
    }

    static function getInstance(){
        if (!self::$mysqli_con){
            self::$mysqli_con = new self();
        }
        return self::$mysqli_con;
    }
    /**
     * 打开数据库连接
     */
    public function open()
    {
        $this->conn = mysqli_connect($this->host,$this->username,$this->password);
        mysqli_select_db($this->conn,$this->dbname);
        mysqli_query($this->conn,"SET CHARACTER SET utf8");
    }
    
    /**
     * 关闭数据连接
     */
    public function close()
    {
        mysqli_close($this->conn);
    }
    
    
    public function getOneColumnValue($sql)
    {
        $this->open();
        
        $rs = mysql_query($sql,$this->conn);
        $i = 0;
        
        $value = array();
        
        while($row  = mysql_fetch_array($rs))
        {
            foreach($row as $k => $v) {
                $value[$k] = $v;
            }
        }
        return $value;
    }
    
    public function getTotalSize($sql)
    {
        $totalSize = 0;
        $this->open();
        
        $rs = mysql_query($sql,$this->conn);
        
        while($row  = mysql_fetch_array($rs))
        {
           $totalSize=$row['count(*)'];
        }
        $this->close();
        return $totalSize;
    }
    /**
     * 通过sql语句获取数据
     * @return: array()
     */
    public function getObjListBySql($sql)
    {
        $objList = array();
        $this->open();
        $rs = mysql_query($sql,$this->conn);
        while($obj = mysql_fetch_array($rs))
        {
            if($obj)
            {
                $objList[] = $obj;
            }
        }
        $this->close();
        return $objList;
    }
    
    /**
     * 向数据库表中插入数据
     * @param unknown $sql
     */
    public function insertBySql($sql) {
        $this->open();
        mysql_query($sql,$this->conn);
        $id = mysql_insert_id($this->conn);
        $this->close();
        return $id;
    }
    
    /**
     * 向数据库表中插入数据
     * @param：$table,表名
     * @param：$columns,包含表中所有字段名的数组。默认空数组，则是全部有序字段名
     * @param：$values,包含对应所有字段的属性值的数组
     */
    public function insertData($table,$columns=array(),$values=array())
    {   
        $sql = 'insert into '.$table .'( ';
        for($i=0; $i <sizeof($columns);$i++)
        {
            $sql.= $columns[$i];
            if($i < sizeof($columns) - 1)
            {
                $sql .= ',';
            }
        }
        $sql .= ') values ( ';
        for($i = 0; $i<sizeof($values);$i++)
        {
            $sql.= "'".$values[$i]."'";
            if($i < sizeof($values) - 1)
            {
                $sql .= ',';
            }
        }
        $sql .= ' )';
        $this->open();
        mysql_query($sql,$this->conn);
        $id = mysql_insert_id($this->conn);
        $this->close();
        return $id;
    }
    /**
     * 通过表中的"id"，删除记录
     */
//     public function delete($tableName,$atrName,$atrValue){
//         $this->open();
//         $deleteResult = false;
//         if(mysql_query("DELETE FROM ".$tableName." WHERE $atrName = '$atrValue'")) $deleteResult = true;
//         $this->close();
//         if($deleteResult) return true;
//         else return false;
//     }
    
    public function delete($sql)
    {
        $updateResult = false;
        $this->open();
        
        if(mysql_query($sql))
        {
            $updateResult=true;
        }
        $this->close();
        return $updateResult;
    }
    
    public function  update($sql)
    {
        $updateResult = false;
        $this->open();
        
        if(mysql_query($sql))
        {
            $updateResult=true;
        }
        $this->close();
        return $updateResult;
    }
    
    /**
     * 更新表中的属性值
     */
    public function updateParamById($tableName,$atrName,$atrValue,$key,$value){
        $this->open();
        if(mysql_query("UPDATE ".$tableName." SET $key = '$value' WHERE $atrName = '$atrValue' ")){  //$key不要单引号
            $db->close();
            return true;
        }
        else{
            $db->close();
            return false;
        }
    }
    /*
     * @description: 取得一个table的所有属性名
     * @param: $tbName 表名
     * @return：字符串数组
     */
    public function fieldName($tbName){
        $resultName=array();
        $i=0;
        $this->open();
        $result = mysql_query("SELECT * FROM $tbName");
        while ($property = mysql_fetch_field($result)){
            $resultName[$i++]=$property->name;
        }
        $this->close();
        return $resultName;
    }



    /**
     * 执行查询 返回数据集
     * @access public
     * @param string $str  sql指令
     * @return mixed
     */
    public function query($str)
    {
        $this->open();
        $result = mysqli_query($this->conn,$str);
        //返回数据集
        $data = array();
        $i = 0;
        if ($result){
            while($row = mysqli_fetch_assoc($result)){
                if($row)
                {
                    $data[] = $row;
                }
            }
        }
        $this->close();
        return $data;
    }
    /**
     * 执行语句
     * @access public
     * @param string $str  sql指令
     * @return integer|false
     */
    public function execute($str)
    {
        $this->open();
        $result =  mysqli_query($this->conn,$str) ;
        if ( false === $result)
        {
            $data = mysqli_affected_rows($this->conn).'.'.mysqli_insert_id($this->conn);
            $this->close();
            return $data;
        } else
        {
            $data = mysqli_affected_rows($this->conn).'.'.mysqli_insert_id($this->conn);
            $this->close();
            return $data;
        }
    }
}
?>
