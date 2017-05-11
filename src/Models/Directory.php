<?php namespace DirectoryCrawler\Models;

class Directory extends Model
{

    protected $tablename = 'directory';

    /*
     * $data['url'], $data['name'], $data['active']=1, $data['id']=-1
     */

    public function getByName($name)
    {
        $table = $this->tablename;
        $sql = "SELECT * FROM $table WHERE name = '$name' LIMIT 1";
        $query = (mysqli_query($this->connection, $sql));
        return mysqli_fetch_all($query,MYSQLI_ASSOC );
    }

    public function insert(array $data)
    {
        $table = $this->tablename;
        $doInsert = (empty($data['id']) || intval($data['id']) == -1);

        if ($doInsert) {
            $sql = "INSERT INTO $table (`name`,`url`,`active`) values( '".$data['name']."','".$data['url']."','".$data['active']."' ) ON DUPLICATE KEY UPDATE
url='" . $data['url'] . "', name='" . $data['name'] . "', active='" . $data['active'] . "'";

            $query = (mysqli_query($this->connection, $sql));

        return mysqli_fetch_all($query,MYSQLI_ASSOC );

        } else {

            $sql = "INSERT INTO $table (`id`, `name`,`url`,`active`) values( '".$data['id']."','".$data['name']."','".$data['url']."','".$data['active']."' ) ON DUPLICATE KEY UPDATE
id='" . $data['id'] . "', url='" . $data['url'] . "', name='" . $data['name'] . "', active='" . $data['active'] . "'";
            $query = (mysqli_query($this->connection, $sql));

            return mysqli_fetch_all($query,MYSQLI_ASSOC );

        }
    }

    public function getDirectories()
    {
        $directories = $this->getAll();
        if(is_array($directories)) {
            return $directories;
        } else {
            return (array)$directories;
        }
    }

    public function getDirectoryNames()
    {
        $names = array();

        $sql = "SELECT name FROM directory";
        $query = (mysqli_query($this->connection, $sql));

        foreach ($query as $arr) {
            $names[] = $arr['name'];
        }
        return $names;
    }
}
