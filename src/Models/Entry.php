<?php namespace DirectoryCrawler\Models;

class Entry extends Model{

    protected $tablename = 'entries';

    /*
     * structure of $data:
     * [
     * 'directory_id' => $data['directory_id'],
     * 'href'         => $data['href'],
     * 'name'         => $data['name'],
     * 'tag'          => $data['tag'],
     * 'include'      => $data['include'],
     * 'id'           => -1
     * ]
     */
    public function insert( array $data )
    {
        $doInsert = (empty($data['id']) || intval($data['id'])==-1);
        $directory_id = $data['directory_id'];
        $href = $data['href'];
        $name = $data['name'];
        $tag = $data['tag'];
        $include = $data['include'];

        if ($doInsert) {
            $sql =  "INSERT INTO `$this->tablename` (`directory_id`, `href`, `name`, `tag`, `include`) values( $directory_id, $href, $name, $tag, $include ) ON DUPLICATE KEY UPDATE
directory_id='$directory_id', href='$href', name='$name', tag='$tag', include='$include'";
        } else {
            $id = $data['id'];
            $sql =  "INSERT INTO `$this->tablename` (`directory_id`, `href`, `name`, `tag`, `include`, `id`) values( $directory_id, $href, $name, $tag, $include, $id) ON DUPLICATE KEY UPDATE directory_id='$directory_id', href='$href', name='$name', tag='$tag', include='$include', id='$id'";

        }
        return $this->returnAssoc($sql);
    }
}
