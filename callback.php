<?php // callback.php
define("MLAB_API_KEY", '6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv');
define("LINE_MESSAGING_API_CHANNEL_SECRET", '32af0f0d2540846576a6e5adb4415db8');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", 'Hf0leB8PvKkMKkKPYw+rujZPrIi9cz6b8SlAksk37KKm648O8AJcCOyexU1qbn6lq5UCfkhGf8gLrcB4PluHJ4ViBppUh5/6PllJ4xi7z+drBtODoy3uMPFNw+Y6gpamMB46BrtcbwL8oz+1sd71NAdB04t89/1O/w1cDnyilFU=');

require __DIR__."/vendor/autoload.php";
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use \Statickidz\GoogleTranslate;
$logger = new Logger('LineBot');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));



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
		$logger->info('Postback message has come');
		continue;
	}
	// Location Event
	if  ($event instanceof LINE\LINEBot\Event\MessageEvent\LocationMessage) {
		$logger->info("location -> ".$event->getLatitude().",".$event->getLongitude());
		continue;
	}


    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {

        $reply_token = $event->getReplyToken();

        $text = $event->getText();

        $explodeText=explode(" ",$text);


        switch ($explodeText[0]) {

         
	 case '#เพิ่มรถ':
              $x_tra = str_replace("#เพิ่มรถ ","", $text);
              $pieces = explode("|", $x_tra);
              $_licence_plate=$pieces[0];
              $_brand=$pieces[1];
              $_model=$pieces[2];
              $_color=$pieces[3];
              $_owner=$pieces[4];
              $_user=$pieces[5];
              $_note=$pieces[6];
              //Post New Data
              $newData = json_encode(array('licence_plate' => $_licence_plate,'brand'=> $_brand,'model'=> $_model,'color'=> $_color,'owner'=> $_owner,'user'=> $_user,'note'=> $_note) );
              $opts = array('http' => array( 'method' => "POST",
                                            'header' => "Content-type: application/json",
                                            'content' => $newData
                                             )
                                          );
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY;
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              if($returnValue)$text = 'เพิ่มรถสำเร็จแล้ว';
              else $text="ไม่สามารถเพิ่มรถได้";
              $bot->replyText($reply_token, $text);

              break;
	 case '#ทะเบียน':
		  $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY.'&q={"licence_plate":"'.$explodeText[1].'"}');
              $data = json_decode($json);
              $isData=sizeof($data);
              if($isData >0){
		   $text="";
		   $count=1;
                foreach($data as $rec){
                  $text= $text.$count.' '.$rec->licence_plate.' '.$rec->brand.' '.$rec->model.' '.$rec->color."\n ผู้ถือกรรมสิทธิ์ ".$rec->owner."\n ผู้ครอบครอง ".$rec->user."\n หมายเหตุ/ประวัติ ".$rec->note."\n\n";
                  $count++;
                }//end for each
	      }else{
		  $text= "ไม่พบข้อมูลทะเบียนรถ ".$explodeText[1];
	      }
			/*
my $multipleMessageBuilder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
$multipleMessageBuilder->add(new TextMessageBuilder('text1', 'text2'))
                       ->add(new AudioMessageBuilder('https://example.com/audio.mp4', 1000));
$res = $bot->replyMessage('your-reply-token', $multipleMessageBuilder);
*/
                  $bot->replyText($reply_token, $text);
                   break;
         
         
          default:
              
            }//end switch
    }//end if text
}// end foreach event
?>
