<?php namespace DirectoryCrawler\Models;

class MergeQuery extends Model{

    protected $tablename = 'mergequery';

    public function find( $mergecode ){
        $sql = "SELECT * FROM `$this->tablename` WHERE mergecode LIKE '$mergecode'";
        return $this->returnAssoc($sql);
    }

    public function delete( $mergecode ){
        $sql = "DELETE FROM `$this->tablename` WHERE mergecode = '$mergecode'";
        return $this->returnAssoc($sql);
    }

    public function update( $mergecode, $desc, $sqlstatement ){
        $sql = "UPDATE INTO `$this->tablename` set `description`='$desc', `sqlstatement`='$sqlstatement' WHERE `mergecode` = '$mergecode'";
        return $this->returnAssoc($sql);
    }

    public function create( $mergecode, $desc, $sqlstatement )
    {
        $sql = "INSERT IGNORE INTO `$this->tablename` (`mergecode`,`description`,`sqlstatement`, `created`) values( '$mergecode','$desc','$sqlstatement', now() )";
        return $this->returnAssoc($sql);
    }

    public function insert( array $data )
    {
        $name = $data['name'];
        $tag = $data['tag'];
        $url = $data['url'];
        $include = $data['include'];
        $sql =  "INSERT INTO `$this->tablename` (`name`,`tag`,`href`,`include`) values( '$name','$tag','$url','$include' ) ON DUPLICATE KEY UPDATE
name='$name', tag='$tag', href='$url', include='$include'";
        return $this->returnAssoc($sql);
    }
  }
