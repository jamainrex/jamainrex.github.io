<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 21/01/2016
 * Time: 17:03
 */

namespace DirectoryCrawler\Models;

class Page extends Model
{
    protected $tablename = 'pages';
    protected $id;
    protected $href;
    protected $content;
    protected $expiry = 30*24*60*60;

    public function insert( array $data )
    {
        $doInsert = (empty($data['id']) || intval($data['id'])==-1);
        $expiry= time()+$this->expiry;
        $content = mysqli_real_escape_string($this->connection, $data['content'] );
        $href = $data['href'];
        if ($doInsert) {
            $sql = "INSERT INTO `$this->tablename` (`href`,`expiry`,`content`) values( '$href','$expiry','$content' ) ON DUPLICATE KEY UPDATE
href='$href', expiry='$expiry', content='$content'";
            return mysqli_query($this->connection,$sql);
        } else {
            $id = $data['id'];
            $sql = "INSERT INTO `$this->tablename` (`id`,`href`,`expiry`,`content`) values( '$id','$href','$expiry','$content' ) ON DUPLICATE KEY UPDATE
id='$id', href='$href', expiry='$expiry', content='$content'";
            return mysqli_query($this->connection,$sql);
        }
    }

    public function getByHref($href)
    {
        $result = parent::getByHref($href); // TODO: Change the autogenerated stub
        if ($this->expiry > time()) { // cache freshness check - yes it's arbitrary but it's better than constantly pulling data off servers
            return $result;
        } else {
            return null;
        }
    }
    public function getById($id)
    {
        $result = parent::getById($id); // TODO: Change the autogenerated stub
        if ($this->expiry > time()) { // cache freshness check - yes it's arbitrary but it's better than constantly pulling data off servers
            return $result;
        } else {
            return null;
        }
    }
}