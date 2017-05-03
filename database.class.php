<?php 
namespace wsilence;

use \PDO;

class BeaconDatabase
{
    private $host   = DB_HOST;
    private $user   = DB_USER;
    private $pass   = DB_PASS;
    private $dbname = DB_NAME;
    
    public $dbh;
    public $error;
    
    public $stmt;
    
    public function __construct()
    {
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8';
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION/*,
            PDO::MYSQL_ATTR_INIT_COMMAND =>"SET time_zone = 'Asia/Yekaterinburg'"*/
        );
        //Create a new PDO instance
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        }
        // Catch any errors
        catch(PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function __destruct() {
       $this->dbh = null;
    }

    public function checkIsCreated($imei, $datetime)
    {
        $checker = $this->dbh->query('SELECT count(id) FROM beacon WHERE `date`="'.$datetime.'" and `imei` = "'.$imei.'"')->fetchColumn(0);
        switch($checker){
            case  0:
                return 0;
                break;

            case 1:
                return 1;
                break;
        }
        //return $this->dbh->query('SELECT name,value FROM setting')->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function insertRow($imei, $row)
    {
        $sth = $this->dbh->prepare('INSERT INTO `beacon`(`imei`, `date`, `timezone_type`, `timezone`, `priority`, `longitude`, `latitude`, `altitude`, `angle`, `satellites`, `speed`, `hasGpsFix`) VALUES ( :imei, :date, :timezone_type, :timezone , :priority, :longitude, :latitude, :altitude, :angle, :satellites, :speed, :hasGpsFix)');
        $sth->bindParam(':imei', $imei->imei, PDO::PARAM_STR, 20);
        $sth->bindParam(':date', $row->dateTime->date, PDO::PARAM_INT);
        $sth->bindParam(':timezone_type', $row->dateTime->timezone_type, PDO::PARAM_INT);
        $sth->bindParam(':timezone', $row->dateTime->timezone, PDO::PARAM_STR, 20);
        $sth->bindParam(':priority', $row->priority, PDO::PARAM_INT);
        $sth->bindParam(':longitude', $row->gpsData->longitude, PDO::PARAM_STR, 20);
        $sth->bindParam(':latitude', $row->gpsData->latitude, PDO::PARAM_STR, 20);
        $sth->bindParam(':altitude', $row->gpsData->altitude, PDO::PARAM_STR, 20);
        $sth->bindParam(':angle', $row->gpsData->angle, PDO::PARAM_STR, 20);
        $sth->bindParam(':satellites', $row->gpsData->satellites, PDO::PARAM_INT);
        $sth->bindParam(':speed', $row->gpsData->speed, PDO::PARAM_STR, 20);
        $sth->bindParam(':hasGpsFix', $row->gpsData->hasGpsFix, PDO::PARAM_INT);
        $sth->execute();
        $sth = null;
    }

    public function updateRow($row)
    {
        return true;
    }

    
}
?>