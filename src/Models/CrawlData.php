<?php namespace DirectoryCrawler\Models;

class CrawlData extends Model{

    protected $tablename = 'crawl_data';

    /*
     * $data['crawl_id'], $data['categories'], $data['business_name'], $data['business_phone'], $data['business_address'], $data['business_website'], $data['business_emails'], $data['record_status'], $data['id']=-1
     */
    public function insert( array $data )
    {
        $doInsert = (empty($data['id']) || intval($data['id'])==-1);
        $crawl_id = $data['crawl_id'];
        $business_name = $data['business_name'];
        $business_phone = $data['business_phone'];
        $business_address = $data['business_address'];
        $business_website = $data['business_website'];
        $business_emails = $data['business_emails'];
        $record_status = $data['record_status'];
        $tablename=$this->tablename;

        if ($doInsert) {
            $sql = "INSERT INTO `$tablename` (`crawl_id`, `business_name`, `business_phone`, `business_address`, `business_website`, `business_emails`, `record_status`) values( '$crawl_id', '$business_name', '$business_phone', '$business_address', '$business_website', '$business_emails', '$record_status' ) ON DUPLICATE KEY UPDATE
crawl_id='$crawl_id', business_name='$business_name', business_phone='$business_phone', business_address='$business_address', business_website='$business_website', business_emails='$business_emails', record_status='$record_status'";
;
        } else {
            $id = $data['id'];
            $sql = "INSERT INTO `$tablename` (`crawl_id`, `business_name`, `business_phone`, `business_address`, `business_website`, `business_emails`, `record_status`, `id`) values( '$crawl_id', '$business_name', '$business_phone', '$business_address', '$business_website', '$business_emails', '$record_status', '$id') ON DUPLICATE KEY UPDATE
crawl_id='$crawl_id', business_name='$business_name', business_phone='$business_phone', business_address='$business_address', business_website='$business_website', business_emails='$business_emails', record_status='$record_status'";

        }
        return $this->returnAssoc($sql);
    }

    public function updateEmailAddress( $crawl_data_id, array $data )
    {
        $data = array_filter( $data );

        if( sizeof( $data ) == 0 ) return false;

        $id = $crawl_data_id;
        $business_emails = json_encode( $data );
        $emailAddresses = json_encode( $data );
        $tablename = $this->tablename;

        //echo $this->connection->uber()->resolve_pipes( "UPDATE `$this->tablename` SET `business_emails` = '$business_emails|' WHERE id=|id|" );
        //exit();
        $sql = "UPDATE `$tablename` SET `business_emails` = '$emailAddresses', `record_status` = 'complete' WHERE id=$id";
        return $this->returnAssoc($sql);

    }
}