<?php // callback.php

require __DIR__."/vendor/autoload.php";
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use \Statickidz\GoogleTranslate;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
$logger = new Logger('LineBot');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));

define("MLAB_API_KEY", '6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv');
define("LINE_MESSAGING_API_CHANNEL_SECRET", '82d7948950b54381bcbd0345be0d4a2c');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", 'BYnvAcR40qJk4fLopvVtVozF00iUqfUjoD33tIPcnjMoXEyG3fzYSE24XRKB5lnttxPePUIHPWdylLdkROwbOESi4rQE3+oSG3njcFj7yoQuaqU27effhhF4lz6lbOfhPjD9mLvHWYZlSbeigV4ETAdB04t89/1O/w1cDnyilFU=');

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
        $text = strtolower($text);
        $explodeText=explode(" ",$text);
	    
// check Profile ID
	$res = $bot->getProfile('user-id');
if ($res->isSucceeded()) {
    $profile = $res->getJSONDecodedBody();
    $displayName = $profile['displayName'];
    $statusMessage = $profile['statusMessage'];
    $pictureUrl = $profile['pictureUrl'];
}
	    // else exit;
	    print_r($profile);
	     echo $displayName; echo $statusMessage;
switch ($explodeText[0]) {

	 case '#เพิ่มรถ':
              $x_tra = str_replace("#เพิ่มรถ ","", $text);
              $pieces = explode("|", $x_tra);
              $_license_plate=$pieces[0];
              $_brand=$pieces[1];
              $_model=$pieces[2];
              $_color=$pieces[3];
              $_owner=$pieces[4];
              $_user=$pieces[5];
              $_note=$pieces[6];
              //Post New Data
              $newData = json_encode(array('license_plate' => $_license_plate,'brand'=> $_brand,'model'=> $_model,'color'=> $_color,'owner'=> $_owner,'user'=> $_user,'note'=> $_note,'status'=>'active') );
              $opts = array('http' => array( 'method' => "POST",
                                            'header' => "Content-type: application/json",
                                            'content' => $newData
                                             )
                                          );
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY;
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              if($returnValue){$replyText = 'เพิ่มรถสำเร็จแล้ว';
			           $img_url="https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";
			      }else {$replyText="ไม่สามารถเพิ่มรถได้";
			           $img_url="https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";}
              //$bot->replyText($reply_token, $text);

              break;

	 case '#ทะเบียน':
		         $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY.'&q={"license_plate":"'.$explodeText[1].'"}');
              $data = json_decode($json);
              $isData=sizeof($data);
              if($isData >0){
		          $replyText="";
		          $count=1;
                foreach($data as $rec){
                  $replyText= $replyText.$count.' '.$rec->license_plate.' '.$rec->brand.' '.$rec->model.' '.$rec->color."\n หมายเหตุ/ประวัติ ".$rec->note."\n\n\n ผู้ถือกรรมสิทธิ์ ".$rec->owner."\n ผู้ครอบครอง ".$rec->user;
                  $count++;
                }//end for each
		      $img_url = "https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";
	      }else{
		  $replyText= "ไม่พบข้อมูลทะเบียนรถ ".$explodeText[1];
		      $img_url = "https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";
	      }

                  //$bot->replyText($reply_token, $replyText);
                   break;
		
         case '#แก้ไขประวัติ':
         $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY.'&q={"license_plate":"'.$explodeText[1].'"}');
          $data = json_decode($json);
          $isData=sizeof($data);
          if($isData >0){
          $replyText="พบข้อมูลรถที่จะแก้ไข";
            foreach($data as &$rec){
              $carUpdateId = $rec->_id;
              foreach ($carUpdateId as $key=>$value){
                if ($key==='$oid'){
                  $updateId=$value;
                  }
                }//end foreach carupdateid
              }//end for each data from json
$replyText=$replyText.' id:'.$updateId.' with '.$explodeText[2]."\n";
     // update note
     $mlabURL='https://api.mlab.com/api/1/databases/hooqline/collections/carregister/'.$updateId.'?apiKey='.MLAB_API_KEY;
     $newNote = json_encode(
       array(
         '$set'=>array('note'=>$explodeText[2])
       )
     );

       // ใช้  '$set' เพื่อไม่ให้เปลี่ยนแปลงทั้งหมด ใน document

     $opts=array('http'=>
       array(
         'method'=>'PUT',
         'header'=>'Content-type: application/json',
         'content'=>$newNote
       )
     );
     $context= stream_context_create($opts);
     $returnVal = file_get_contents($mlabURL,false,$context);
     $replyText=$replyText."\n ผลลัพธ์คือ ".$returnVal;
     
   }else{ // ไม่พบข้อมูลทะเบียนรถ
                  $replyText= "ไม่พบข้อมูลทะเบียนรถ ".$explodeText[1];
                }
break;

        case '#ลบรถ':
$json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY.'&q={"license_plate":"'.$explodeText[1].'"}');
 $data = json_decode($json);
 $isData=sizeof($data);
 if($isData >0){
 $replyText="พบข้อมูลรถที่จะลบ\n";
   foreach($data as &$rec){
     $carDeleteId = $rec->_id;
     foreach ($carDeleteId as $key=>$value){
       if ($key==='$oid'){
         $deleteId=$value;
         }
       }//end foreach carupdateid
     }//end for each data from json

// delete
$mlabURL='https://api.mlab.com/api/1/databases/hooqline/collections/carregister/'.$deleteId.'?apiKey='.MLAB_API_KEY;
$opts=array('http'=>
  array(
    'method'=>'DELETE',
    'header'=>'Content-type: application/json'
      )
    );
$context= stream_context_create($opts);
$returnVal = file_get_contents($mlabURL,false,$context);
$replyText=$replyText."\n ทะเบียน ".$explodeText[1]."\n id:".$deleteId." DELETED \n รายละเอียด".$returnVal;

}else{ // ไม่พบข้อมูลทะเบียนรถ
         $replyText= "ไม่พบข้อมูลทะเบียนรถ ".$explodeText[1].' ที่จะลบ';
       }
break;
          default:
		 $replyText=$replyText.$displayName.$statusMessage;
		break;
            }//end switch
	    
	    $bot->replyText($reply_token, $replyText);
    }//end if text
}// end foreach event
?>
