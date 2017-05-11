<?php namespace DirectoryCrawler\Models;

class CrawlerLink extends Model{

    protected $tablename = 'crawlerlinks';

    /*
     * $data['name'], $data['tag'], $data['url']
     */

    public function insert( array $data )
    {
        $crawl_id = $data['crawl_id'];
        $tag = $data['tag'];
        $url = $data['url'];
        $tablename = $this->tablename;

        $sql = "INSERT INTO `$tablename` (`crawl_id`,`tag`,`href`) values( '$crawl_id', '$tag', '$url' ) ON DUPLICATE KEY UPDATE
crawl_id='$crawl_id', tag='$tag', href='$url' " ;
        return $this->returnAssoc($sql);
    }

    public function getByTargetLink( $tLink ){

        $sql = "SELECT * FROM `$this->tablename` WHERE target_link = '$tLink'" ;
        return $this->returnAssoc($sql);
    }
        
    public function insertLinks( $target_link, $href, $crawl_id, $tag, $text, $outerText, $innerText )
    {
        $sql = "INSERT INTO `$this->tablename` VALUES ('$target_link',  '$href',  '$crawl_id',  '$tag',  '$text', '$outerText', '$innerText', now()) ON DUPLICATE KEY UPDATE outertext='$outerText',  innertext='$innerText'";
        return $this->returnAssoc($sql);
    }

    public function insertMultipleValues( $values = array() )
    {
        $insert_array = array();
        if (!empty($values)) {
            $tablename = $this->tablename;
            $sql = "INSERT INTO `$tablename` (`crawl_id`,`tag`,`href`) values ";
            $firstone = true;
            $append="";
            foreach ($values as $data) {
                $append .= ($firstone) ? "": ", ";
                $crawl_id = $data['crawl_id'];
                $tag = $data['tag'];
                $url = $data['url'];
                $append .= "( '$crawl_id', '$tag', '$url' )";
                $firstone = false;
            }
            $append .= "ON DUPLICATE KEY UPDATE crawl_id='$crawl_id', tag='$tag' " ;
            $sql .= $append;
            return $this->returnAssoc($sql);
        } else {
            return false;
        }
    }
}
