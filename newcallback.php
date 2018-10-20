<?php // callback.php
require __DIR__."/vendor/autoload.php";

use LINE\LINEBot;

$logger = new Logger('LineBot');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));

define("MLAB_API_KEY", '6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv');
define("LINE_MESSAGING_API_CHANNEL_SECRET", 'db66a0aa1a057415832cfd97f6963cb3');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", '22hdP860hpFYokOIcmae6cdlKPpriZO3/XHhRWkLEp8YPkXjS8R36U7reDuvpliAtRKnkbKLNAh2/QByqEocSkrGx3yyz1T6dGdHu9nrSc3t5PejaraL26vuKjCppl3mQ7k/lqhZ4F3XaWH8/4tWiAdB04t89/1O/w1cDnyilFU=');



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

foreach ($events as $event) {
  // Postback Event
	if (($event instanceof \LINE\LINEBot\Event\PostbackEvent)) {
		$replyText='Postback message has come';
       		 $reply_token = $event->getReplyToken();
		$logger->info($replyText);
		$bot->replyText($reply_token, $replyText);
		continue;
	}
	// Location Event
	if  ($event instanceof LINE\LINEBot\Event\MessageEvent\LocationMessage) {
		$replyText='Postback location has come'."location -> ".$event->getLatitude().",".$event->getLongitude();
		$logger->info($replyText);
        	$reply_token = $event->getReplyToken();
		$bot->replyText($reply_token, $replyText);
		continue;
	}
    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
        $reply_token = $event->getReplyToken();
	$text = $event->getText();
	$text = strtolower($text);
        $msg=$text."Thank you";
	$image_url= "https://qph.fs.quoracdn.net/main-qimg-f93403f6d32bc43b40d85bd978e88bbf";
	$url_detail ="https://www.hooq.info";
	$messageBuilder = new TextMessageBuilder($msg);
	$imageBuilder = new ImageMessageBuilder($image_url, $image_url);
 
	$multiMessageBuilder = new MultiMessageBuilder();
	$multiMessageBuilder->add($messageBuilder)
                             ->add($imageBuilder);
 
	$bot->replyMessage($bot->replyToken, $multiMessageBuilder);
         
	
    }//end if text
}// end foreach event

?>
