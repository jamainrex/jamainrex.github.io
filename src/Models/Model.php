<?php namespace DirectoryCrawler\Models;

use DirectoryCrawler\Database\Connection;

Abstract Class Model{
    protected $tablename;
    protected $connection;
    public $config = [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'db' => 'directorycrawlerdb'
    ];

    public function __construct()
    {
        $this->connection = mysqli_connect($this->config['host'], $this->config['user'], $this->config['pass'], $this->config['db']);
    }

    abstract public function insert(array $data);

    public function getTable()
    {
        return $this->tablename;
    }

    public function getAll($limit=1000,$offset=0,$fields="*",$order="ASC",$orderby="id",$where_clause=" ")
    {
        $fields = is_array($fields) ? implode(',',array_map('tickify',$fields)):$fields;
        if(!empty($order)&&!empty($orderby)) {
            $orderby = 'ORDER BY `' . $orderby . '` '. $order;
        } else {
            $orderby = '';
        }
        $limit = (empty($limit))?'': 'LIMIT ' . $offset . ', ' . $limit ;
        //echo $this->connection->uber()->resolve_pipes( "SELECT $fields FROM `$this->tablename`$where_clause $limit $orderby" );
        //exit();
            $sql = 'SELECT ' . $fields . ' FROM `' . $this->tablename . '`'.$where_clause.' '.$orderby.' '.$limit;

        return $this->returnAssoc($sql);
    }

    public function getByField( $fieldname, $field )
    {
        $sql = 'SELECT * FROM `' . $this->tablename . '` where `' . $fieldname . '` = \'' . $field . '\'';
        return $this->returnAssoc($sql);
    }

    public function getCount($constraints)
    {
        $sql = 'SELECT count(*) FROM `' . $this->tablename . '` where ' . $constraints;
        $query = mysqli_query($this->connection, $sql);
        $count_arr = mysqli_fetch_row($query);
        return $count_arr[0];
    }


    public function getById( $id )
    {
        return $this->getByField('id', $id);
    }

    public function getByHref( $href )
    {
        return $this->getByField('href', $href);
    }

    public function insertMultipleValues( $values = array() )
    {
        $return_array = array();
        if (!empty($values)) {
            foreach ($values as $value) {
                $return_array[] = $this->insert( $value);
            }
        }
        return $return_array;
    }

    protected function tickify($string)
    {
        return '`'.$string.'`';
    }

    protected function returnAssoc($sql)
    {
        $result = mysqli_query($this->connection, $sql);
        if ($result!==TRUE && $result!==FALSE) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            return $result;
        }
    }
}