<?php  //callback.php
ob_start();
$raw = file_get_contents('php://input');
var_dump(json_decode($raw,1));
$raw = ob_get_clean();
file_put_contents('/tmp/dump.txt', $raw."\n=====================================\n", FILE_APPEND);
echo "Hooq .. Dump temp OK";
define("MLAB_API_KEY", '6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv');
echo 'define ok\n';
require DIR."/vendor/autoload.php";
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use \Statickidz\GoogleTranslate;
echo 'use ok\n'
$logger = new Logger('LineBot');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));
echo "logger ok <br>";

echo 'define valuable\n';
    $licenseplate= "1กฆ3977";
    $province= "กรุงเทพมหานคร";
    $username= "สีเย๊าะ";
    $usersurname= "แลนิ";
    $userid= "3-9501-00430-00-3";
   $ownername= "โตโยต้า ลีสซิ่ง (ประเทศไทย)";
    $ownersurname= "จำกัด";
    $ownerid= "105536113550";
    $cartype= "รถยนต์นั่งส่วนบุคคลไม่เกิน 7 คน (รย. 01)";
    $carbrand= "TOYOTA";
    $carcolor= "เทา";
echo 'ready for insert data\n';
 $newData = json_encode(array('licenseplate' => $licenseplate,'province'=> $province,'username'=> $username,'usersurname'=> $usersurname,'userid'=> $userid,'ownername'=> $ownername,'ownersurname'=> $ownersurname,'ownerid'=> $ownerid,'cartype'=> $cartype,'carbrand'=> $carbrand,'carcolor'=> $carcolor );
              $opts = array('http' => array( 'method' => "POST",
                                            'header' => "Content-type: application/json",
                                            'content' => $newData
                                             )
                                          );
             echo 'insert data\n';
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY;
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              if($returnValue) echo "เพิ่มรถ1สำเร็จแล้ว";
              else echo "ไม่สามารถเพิ่มรถ1ได้";
              
           ?>   

/*
    $licenseplate= "1กฎ340";
    $province=  "กรุงเทพมหานคร";
    $username="อาอีเส๊าะ";
    $usersurname=  "กาเดร์";
    $userid= "5-9605-99002-31-2";
    $ownername="ธนชาต";
    $ownersurname=  "จำกัด (มหาชน)";
    $ownerid= "107536001401";
    $cartype=  "รถยนต์นั่งส่วนบุคคลไม่เกิน 7 คน (รย. 01)";
    $carbrand= "MITSUBISHI";
    $carcolor= "ขาว";
 $newData = json_encode(array('licenseplate' => $licenseplate,'province'=> $province,'username'=> $username,'usersurname'=> $usersurname,'userid'=> $userid,'ownername'=> $ownername,'ownersurname'=> $ownersurname,'ownerid'=> $ownerid,'cartype'=> $cartype,'carbrand'=> $carbrand,'carcolor'=> $carcolor );
              $opts = array('http' => array( 'method' => "POST",
                                            'header' => "Content-type: application/json",
                                            'content' => $newData
                                             )
                                          );
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY;
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              if($returnValue) echo "เพิ่มรถ2สำเร็จแล้ว";
              else echo "ไม่สามารถเพิ่มรถ2ได้";
   $licenseplate= "1กศ6254";
    $province=  "กรุงเทพมหานคร";
    $username= "อุสมาน";
    $usersurname=  "เจ๊ะแว";
    $userid= "3-9604-00160-13-3";
    $ownername= "ทิสโก้",
    $ownersurname=  "จำกัด(มหาชน)";
    $ownerid= "107539000171";
    $cartype=  "รถยนต์นั่งส่วนบุคคลไม่เกิน 7 คน (รย. 01)";
    $carbrand= "FORD";
    $carcolor= "เทา";
 $newData = json_encode(array('licenseplate' => $licenseplate,'province'=> $province,'username'=> $username,'usersurname'=> $usersurname,'userid'=> $userid,'ownername'=> $ownername,'ownersurname'=> $ownersurname,'ownerid'=> $ownerid,'cartype'=> $cartype,'carbrand'=> $carbrand,'carcolor'=> $carcolor );
              $opts = array('http' => array( 'method' => "POST",
                                            'header' => "Content-type: application/json",
                                            'content' => $newData
                                             )
                                          );
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY;
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              if($returnValue) echo "เพิ่มรถ3สำเร็จแล้ว";
              else echo "ไม่สามารถเพิ่มรถ3ได้";
    $licenseplate= "1กฮ1631";
    $province=  "กรุงเทพมหานคร";
    $username= "มัสตูรา";
    $usersurname= "อูมา";
    $userid= "3-9609-00016-12-1";
    $ownername="ลีสซิ่งกสิกรไทย";
    $ownersurname=  "จำกัด";
    $ownerid= "105547166951";
    $cartype=  "รถยนต์นั่งส่วนบุคคลไม่เกิน 7 คน (รย. 01)";
    $carbrand= "HONDA";
    $carcolor= "เทา";
 $newData = json_encode(array('licenseplate' => $licenseplate,'province'=> $province,'username'=> $username,'usersurname'=> $usersurname,'userid'=> $userid,'ownername'=> $ownername,'ownersurname'=> $ownersurname,'ownerid'=> $ownerid,'cartype'=> $cartype,'carbrand'=> $carbrand,'carcolor'=> $carcolor );
              $opts = array('http' => array( 'method' => "POST",
                                            'header' => "Content-type: application/json",
                                            'content' => $newData
                                             )
                                          );
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY;
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              if($returnValue) echo "เพิ่มรถสำเร็จ4แล้ว";
              else echo "ไม่สามารถเพิ่มรถ4ได้";
*/
