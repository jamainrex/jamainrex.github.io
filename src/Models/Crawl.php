<?php namespace DirectoryCrawler\Models;

class Crawl extends Model {
// $data['directory_id'], $data['name'], $data['id']=-1

    protected $tablename = 'crawl';

    public function insert( array $data)
    {
        $doInsert = (empty($data['id']) || intval($data['id'])==-1);
        $directory_id = $data['directory_id'];
        $name = $data['name'];
        $tablename = $this->tablename;

        if ($doInsert) {
            $sql = "INSERT INTO `$tablename` (`name`,`directory_id`) values( '$name','$directory_id' ) ON DUPLICATE KEY UPDATE
directory_id='$directory_id', name='$name'";
        } else {
            $id = $data['id'];
            $sql = "INSERT INTO `$tablename` (`id`, `name`,`directory_id`) values( '$id','$name','$directory_id' ) ON DUPLICATE KEY UPDATE
directory_id='$directory_id', name='$name'";
        }
        $result = mysqli_query($this->connection, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

