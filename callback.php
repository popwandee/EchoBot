<?php // callback.php
ob_start();
$raw = file_get_contents('php://input');
var_dump(json_decode($raw,1));
$raw = ob_get_clean();
file_put_contents('/tmp/dump.txt', $raw."\n=====================================\n", FILE_APPEND);

echo "Hooqs .. Dump temp OK";

define("LINE_MESSAGING_API_CHANNEL_SECRET", '558ab5cee72171faced07fe0113795c8');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", 'I2JgX3AxxDJISaIzkFJHgX0FClIpUiGd4J39jPXI2YMLoMq0bbQFYD4uxACCfDZie+8dTshHUeMXofpHEvBWBzqNWboCLF8J1ctCILzMsFs5ODOqeS5waFIB8jU81VO3ZG+UA/w0QONygohJ3MUhUwdB04t89/1O/w1cDnyilFU=
');
echo "ok 1";

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
/*
my $multipleMessageBuilder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
$multipleMessageBuilder->add(new TextMessageBuilder('text1', 'text2'))
                       ->add(new AudioMessageBuilder('https://example.com/audio.mp4', 1000));
$res = $bot->replyMessage('your-reply-token', $multipleMessageBuilder);
*/

echo "ok 3";

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


foreach ($events as $event) {
  // Postback Event
	if (($event instanceof \LINE\LINEBot\Event\PostbackEvent)) {
		$logger->info('Postback message has come');
		continue;
	}
	// Location Event
	if  ($event instanceof LINE\LINEBot\Event\MessageEvent\LocationMessage) {
		$logger->info("location -> ".$event->getLatitude().",".$event->getLongitude());
		continue;
	}

   if ($event  instanceof \LINE\LINEBot\Event\MessageEvent\ImageMessage){

     $reply_token = $event->getReplyToken();
      // $a = ['ว้าว ว้าว ว้าว', 'อุ๊ยตาย ว้ายกรีดดดด', 'ชอบๆ', 'ขอบคุณฮะ', 'OK', 'OK, I Like it.','เฮียไก่ชอบ','ป๊อบปี้ว่ามัน...ซิกเนเจอร์มาก','ตามว่าก็งั้นๆ','เชษไม่ปลื้ม',''];
   	//$text = $a[mt_rand(0, count($a) - 1)];//$a[min,max];
       //$bot->replyText($reply_token, $text);
   }

    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {

        $reply_token = $event->getReplyToken();

        $text = $event->getText();

        $explodeText=explode(" ",$text);

        switch ($explodeText[0]) {
          case 'News':
          case 'news': $bot->replyText($reply_token, "news ok");
             break;
          case 'Lang':
          case 'lang':$bot->replyText($reply_token, "lang ok");
             break;
          default:

              $api_key="6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv";

              $url = 'https://api.mlab.com/api/1/databases/hooqs46/collections/crma46phonebook?apiKey='.$api_key.'';

              $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqs46/collections/crma46phonebook?apiKey='.$api_key.'&q={"name":"'.$explodeText[0].'"}');

              $data = json_decode($json);

              $isData=sizeof($data);

              if($isData >0){

                foreach($data as $rec){

                  $text= $rec->answer;

                  $bot->replyText($reply_token, $text);

                  //-----------------------

                }//end for each

              }else{

                  $text='';

                  //$text= $explodeText[0];

                  //$bot->replyText($reply_token, $text);

              }//end no data from mlab

            }//end switch

    }//end if text

}// end foreach event



echo "OK4";
/*

*/
