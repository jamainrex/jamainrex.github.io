<?php namespace DirectoryCrawler\Models;

class Location extends Model{

    protected $tablename = 'locaations';

    /*
     *   $data['directory_id'], $category_id, $data['href'], $data['name'], $data['tag'], $data['include']='n', $data['id']=-1

     */

    public function insert( array $data )
    {
        $doInsert = (empty($data['id']) || intval($data['id'])==-1);
        $directory_id = $data['directory_id'];
        $category_id = $data['category_id'];
        $href = $data['href'];
        $name = $data['name'];
        $tag = $data['tag'];
        $include = $data['include'];

        if ($doInsert) {
            $sql =  "INSERT INTO `$this->tablename` (`directory_id`,`category_id`,`href`,`name`,`tag`,`include`) values( $directory_id, $category_id, $href, $name, $tag, $include ) ON DUPLICATE KEY UPDATE
directory_id='$directory_id', category_id='$category_id', href='$href', name='$name', tag='$tag', include='$include'";
        } else {
            $id = $data['id'];
            $sql =  "INSERT INTO `$this->tablename` (`id`,`directory_id`,`category_id`,`href`,`name`,`tag`,`include`) values($id,  $directory_id, $category_id, $href, $name, $tag, $include ) ON DUPLICATE KEY UPDATE
id='$id', directory_id='$directory_id', category_id='$category_id', href='$href', name='$name', tag='$tag', include='$include'";

        }
    }
}
