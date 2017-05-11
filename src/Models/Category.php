<?php namespace DirectoryCrawler\Models;

class Category extends Model{
        
    protected $tablename = 'categories';

    /*
     * $data['id'] set to -1 = append and create new id.
     * $data['id'] set to any other value updates the record for that value, appending if that value doesn't exist
     *
     * $data['directory_id'], $data['href'], $data['name'], $data['tag'], $data['include']='n', $data['id']=-1
     */

    public function getByNameAndDirId( $dir_id, $cat_name )
    {
        $sql = "SELECT * FROM `$this->tablename` WHERE directory_id = '$dir_id' AND name = '$cat_name' LIMIT 1";
        return mysqli_query($this->connection, $sql);
    }
    public function getByDirIdAndCatId( $dir_id, $cat_id )
    {
        $sql = "SELECT * FROM `$this->tablename` WHERE directory_id = '$dir_id' AND id = '$cat_id' LIMIT 1";
        return mysqli_query($this->connection, $sql);
    }

    public function getByDirIdAndCatIds( $dir_id, array $cat_ids )
    {
        $_cat_ids = implode( ", ", $cat_ids);
        $sql = "SELECT * FROM `$this->tablename` WHERE directory_id = '$dir_id' AND id = IN('$_cat_ids') LIMIT 1";
        return mysqli_query($this->connection, $sql);
    }

    public function insert( array $data)
    {
        $doInsert = (empty($data['id']) || intval($data['id'])==-1);
        $directory_id = $data['directory_id'];
        $href = $data['href'];
        $name = $data['name'];
        $tag = $data['tag'];
        $include = $data['include'];

        if ($doInsert) {
//            die( "INSERT INTO categories (`directory_id`, `href`, `name`, `tag`, `include`) values( '$directory_id', '$href', '$name', '$tag', '$include' ) ON DUPLICATE KEY UPDATE directory_id='$directory_id', href='$href', name='$name', tag='$tag', include='$include'");
            //$sql =  "INSERT INTO categories (`directory_id`, `href`, `name`, `tag`, `include`) values( '$directory_id', '$href', '$name', '$tag', '$include' ) ON DUPLICATE KEY UPDATE directory_id='$directory_id', href='$href', name='$name', tag='$tag', include='$include'");
            $sql = "INSERT INTO categories (`directory_id`, `href`, `name`, `tag`, `include`) values( '$directory_id', '$href', '$name', '$tag', '$include' ) ON DUPLICATE KEY UPDATE directory_id='$directory_id', href='$href', name='$name', tag='$tag', include='$include'";
            return mysqli_query($this->connection, $sql);
        } else {
            $id = $data['id'];
            $sql = "INSERT INTO categories (`directory_id`, `href`, `name`, `tag`, `include`, `id`) values( '$directory_id', '$href', '$name', '$tag', '$include', '$id') ON DUPLICATE KEY UPDATE directory_id='$directory_id', href='$href', name='$name', tag='$tag', include='$include', id='$id' ";
            return mysqli_query($this->connection, $sql);
        }
    }
}
