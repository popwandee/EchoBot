<?php
require __DIR__."/vendor/autoload.php";
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use \Statickidz\GoogleTranslate;
$logger = new Logger('LineBot');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));

echo "ok 2";

$bot = new \LINE\LINEBot(

    new \LINE\LINEBot\HTTPClient\CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN),

    ['channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET]

);
$signature = $_SERVER["HTTP_".\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];


try {
	$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);
} catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
	error_log('parseEventRequest failed. InvalidSignatureException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
	error_log('parseEventRequest failed. UnknownEventTypeException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
	error_log('parseEventRequest failed. UnknownMessageTypeException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
	error_log('parseEventRequest failed. InvalidEventRequestException => '.var_export($e, true));
}


$text="lang hello yes i do what do you do";

$text_parameter = str_replace("lang ","", $text);
$source = 'en';
$target = 'th';
$trans = new GoogleTranslate();
$result = $trans->translate($source, $target, $text_parameter);
//$lang_url="https://translation.googleapis.com/language/translate/v2";
print_r($result);
//test



$news_url='https://newsapi.org/v2/top-headlines?country=th&apiKey=dca7d30a57ec451cad6540a696a7f60a' ;
$content = file_get_contents($news_url); // อ่านข้อมูล JSON
$json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
$count_news=0;
$text='';
  while (list($key) = each($json_arr)) { // ทำการ list ค่า key ของ Array ทั้งหมดออกมา
    if($key=='articles'){
     $json_arr1 = $json_arr[$key]; //ส่งมอบคุณสมบัติ Array ระดับกลาง
     while (list($key) = each($json_arr1)) {
       ++$count_news;
          //$text_arr[$count_news]=$json_arr1[$key]['title'].$json_arr1[$key]['description'].$json_arr1[$key]['url'];
          $text=$text." ".$json_arr1[$key]['title'].$json_arr1[$key]['description'].$json_arr1[$key]['url'];
        }
    }
  }
  echo $text;
?>
