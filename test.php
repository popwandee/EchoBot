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

$explodeText=explode(" ",$text);
$text_parameter='';
$count_element=count($explodeText);
print_r($explodeText);
echo $count_element;

for($i=1;$i<$count_element;$i++){
  $text_parameter=$text_parameter." ".$explodeText[$i];
  echo $i;
}

echo $text;
echo "<br>text_parameter is ".$text_parameter;

$text_parameter='What is it';
echo $text_parameter;
$lang_url="https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=th&dt=t&q=$text_parameter" ;
 $content = file_get_contents($lang_url); // อ่านข้อมูล JSON
 $json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
 echo "<br>".$lang_url."<br>";
print_r($json_arr);
?>
