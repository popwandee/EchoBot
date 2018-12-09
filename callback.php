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
define("LINE_MESSAGING_API_CHANNEL_SECRET", '32af0f0d2540846576a6e5adb4415db8');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", 'Hf0leB8PvKkMKkKPYw+rujZPrIi9cz6b8SlAksk37KKm648O8AJcCOyexU1qbn6lq5UCfkhGf8gLrcB4PluHJ4ViBppUh5/6PllJ4xi7z+drBtODoy3uMPFNw+Y6gpamMB46BrtcbwL8oz+1sd71NAdB04t89/1O/w1cDnyilFU=');
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

$arrayHeader=array();
		 $arrayHeader[]="Content-Type:application/json";
		 $arrayHeader[]="Authorization: Bearer {".LINE_MESSAGING_API_CHANNEL_TOKEN."}";
                 $content=file_get_contents('php//input');
                 $arrayJson=json_decode($content,true);
                 // ส่วนตรวจสอบผู้ใช้
	    if(isset($arrayJson['events'][0]['source']['userId'])){
		    // ตรวจสอบ id สำหรับตอบ push message
	       $replyId=$arrayJson['events'][0]['source']['userId'];
		   $userId=$replyId;
	    }  //ตรวจสอบว่าเหตุการณ์เกิดขึ้นในกลุ่มหรือไม่ เพื่อขอ id การตอบให้กลุ่ม
	    else if(isset($arrayJson['events'][0]['source']['groupId'])){
	       $replyId=$arrayJson['events'][0]['source']['groupId'];
	       $userId=$arrayJson['events'][0]['source']['userId'];
	    }  //ตรวจสอบว่าเหตุการณ์เกิดขึ้นในห้องหรือไม่ เพื่อขอ id การตอบให้ห้อง
	    else if(isset($arrayJson['events'][0]['source']['roomId'])){
	       $replyId=$arrayJson['events'][0]['source']['roomId'];
	       $userId=$arrayJson['events'][0]['source']['userId'];
	    }
		 // ตรวจสอบชื่อผู้ถามเพื่อตรวจสอบสิทธิ์ และหรือบันทึกการใช้
	       $response = $bot->getProfile($userId);
                if ($response->isSucceeded()) {// ดึงค่าโดยแปลจาก JSON String .ให้อยู่ใรูปแบบโครงสร้าง ตัวแปร array 
                   $userData = $response->getJSONDecodedBody(); // return array     
                            // $userData['userId'] // $userData['displayName'] // $userData['pictureUrl']  // $userData['statusMessage']
                   $userDisplayName = $userData['displayName']; 
		}else{
		   $userDisplayName = $userId;
		}
		// จบส่วนการตรวจสอบผู้ใช้

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
        $replyToken = $event->getReplyToken();
	$replyData='No Data';
	$replyText="";
        $text = $event->getText();
        $text = strtolower($text);
        $explodeText=explode(" ",$text);
	$textReplyMessage="";
        $multiMessage =     new MultiMessageBuilder;
	    
switch ($explodeText[0]) {
	 case '#i':
              $x_tra = str_replace("#i ","", $text);
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
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/register_south?apiKey='.MLAB_API_KEY;
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              if($returnValue){$replyText = 'เพิ่มรถสำเร็จแล้ว';
			           $img_url="https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";
			      }else {$replyText="ไม่สามารถเพิ่มรถได้";
			           $img_url="https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";}
              //$bot->replyText($reply_token, $text);
              break;
	 case '#r':
		$replyText="";
		        /* ส่วนดึงข้อมูลจากฐานข้อมูล */
		if (!is_null($explodeText[1])){
		   $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/register_south?apiKey='.MLAB_API_KEY.'&q={"license_plate":"'.$explodeText[1].'"}');
                   $data = json_decode($json);
                   $isData=sizeof($data);
		      
                 if($isData >0){
		    $count=1;
		    
                    foreach($data as $rec){
			   $count++;
                           $textReplyMessage= '#ทะเบียน '.$rec->license_plate.' ยี่ห้อ'.$rec->brand.' รุ่น'.$rec->model.' สี'.$rec->color."\n\n#ผู้ครอบครอง ".$rec->user."\n#มีประวัติสงสัยว่าเป็น".$rec->note."\n\n#คำแนะนำ ควรตรวจสอบผู้ขับขี่, ยานพาหนะโดยละเอียด ถ่ายภาพและรายงานให้ ทราบโดยด่วน \n";
                           $textMessage = new TextMessageBuilder($textReplyMessage);
			   $multiMessage->add($textMessage);
			   //$textReplyMessage= "https://www.hooq.info/img/$rec->nationid.png";
                           //$textMessage = new TextMessageBuilder($textReplyMessage);
			   //$multiMessage->add($textMessage);
			   //$picFullSize = "https://www.hooq.info/img/$rec->nationid.png";
                           //$picThumbnail = "https://www.hooq.info/img/$rec->nationid.png";
			   //$imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			   //$multiMessage->add($imageMessage);
                           }//end for each
	            $replyData = $multiMessage;
			 
		   }else{ //$isData <0  ไม่พบข้อมูลที่ค้นหา
		          $textReplyMessage= "ไม่พบ ".$explodeText[1]."  ในฐานข้อมูลของหน่วย"; 
			  $textMessage = new TextMessageBuilder($textReplyMessage);
			  $multiMessage->add($textMessage);
			  //$picFullSize = 'https://s.isanook.com/sp/0/rp/r/w700/ya0xa0m1w0/aHR0cHM6Ly9zLmlzYW5vb2suY29tL3NwLzAvdWQvMTY2LzgzNDUzOS9sb3ZlcmppbmEuanBn.jpg';
                          //$picThumbnail = 'https://s.isanook.com/sp/0/rp/r/w700/ya0xa0m1w0/aHR0cHM6Ly9zLmlzYW5vb2suY29tL3NwLzAvdWQvMTY2LzgzNDUzOS9sb3ZlcmppbmEuanBn.jpg';
                          //$imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			  //$multiMessage->add($imageMessage);
			  $replyData = $multiMessage;
			 // กรณีจะตอบเฉพาะข้อความ
		      //$bot->replyText($replyToken, $textMessage);
		        } // end $isData>0
		   }else{ // no $explodeText[1]
	                $textReplyMessage= "คุณให้ข้อมูลในการสอบถามไม่ครบถ้วนค่ะ"; 
			$textMessage = new TextMessageBuilder($textReplyMessage);
			  $multiMessage->add($textMessage);
			  //$picFullSize = "https://s.isanook.com/sp/0/rp/r/w700/ya0xa0m1w0/aHR0cHM6Ly9zLmlzYW5vb2suY29tL3NwLzAvdWQvMTY2LzgzNDUzOS9sb3ZlcmppbmEuanBn.jpg";
                          //$picThumbnail = "https://s.isanook.com/sp/0/rp/r/w700/ya0xa0m1w0/aHR0cHM6Ly9zLmlzYW5vb2suY29tL3NwLzAvdWQvMTY2LzgzNDUzOS9sb3ZlcmppbmEuanBn.jpg";
                          //$imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			  //$multiMessage->add($imageMessage);
			  $replyData = $multiMessage;
			 // กรณีจะตอบเฉพาะข้อความ
		      //$bot->replyText($replyToken, $textMessage);
		   }// end !is_null($explodeText[1])
		/* จบส่วนดึงข้อมูลจากฐานข้อมูล */
		      $response = $bot->replyMessage($replyToken,$replyData);
                   break;
		case '#ra':// read all data with car owner
		         $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/register_south?apiKey='.MLAB_API_KEY.'&q={"license_plate":"'.$explodeText[1].'"}');
              $data = json_decode($json);
              $isData=sizeof($data);
              if($isData >0){
		          $replyText="";
		          $count=1;
                foreach($data as $rec){
                  $replyText= $replyText.'#ทะเบียน '.$rec->license_plate.' ยี่ห้อ'.$rec->brand.' รุ่น'.$rec->model.' สี'.$rec->color."\n\n#ผู้ครอบครอง ".$rec->user."\n\n#มีประวัติสงสัยว่าเป็น".$rec->note."\n\n#คำแนะนำ ควรตรวจสอบผู้ขับขี่, ยานพาหนะโดยละเอียด ถ่ายภาพและรายงานให้ ทราบโดยด่วน \n";
                  $count++;
                }//end for each
		      $img_url = "https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";
	      }else{
		  $replyText= "ไม่พบทะเบียนรถ ".$explodeText[1]."  ในฐานข้อมูลของหน่วย";
		      $img_url = "https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";
	      }
                  //$bot->replyText($reply_token, $replyText);
                   break;
         case '#e':
         $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/register_south?apiKey='.MLAB_API_KEY.'&q={"license_plate":"'.$explodeText[1].'"}');
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
     $mlabURL='https://api.mlab.com/api/1/databases/hooqline/collections/register_south/'.$updateId.'?apiKey='.MLAB_API_KEY;
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
	
       
        case '#d':
$json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/register_south?apiKey='.MLAB_API_KEY.'&q={"license_plate":"'.$explodeText[1].'"}');
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
$mlabURL='https://api.mlab.com/api/1/databases/hooqline/collections/register_south/'.$deleteId.'?apiKey='.MLAB_API_KEY;
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
		
/* ตรวจสอบบุคคล */
	 case '$i':
              $x_tra = str_replace('$i ',"", $text);
              $pieces = explode("|", $x_tra);
              $_nationid=$pieces[0];
              $_name=$pieces[1];
              $_address=$pieces[2];
              $_note=$pieces[3];
              //Post New Data
              $newData = json_encode(array('nationid' => $_nationid,'name'=> $_name,'address'=> $_address,'note'=> $_note) );
              $opts = array('http' => array( 'method' => "POST",
                                            'header' => "Content-type: application/json",
                                            'content' => $newData
                                             )
                                          );
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey='.MLAB_API_KEY;
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              if($returnValue){$replyText = 'เพิ่มข้อมูลบุคคลสำเร็จแล้ว';
			           $img_url="https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";
			      }else {$replyText="ไม่สามารถเพิ่มข้อมูลบุคคลได้";
			           $img_url="https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";}
              //$bot->replyText($reply_token, $text);
              break;
	 case '$': // เรียกอ่านข้อมูลบุคคล
		$replyText="";
		        /* ส่วนดึงข้อมูลจากฐานข้อมูล */
		if (!is_null($explodeText[1])){
		   $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey='.MLAB_API_KEY.'&q={"nationid":"'.$explodeText[1].'"}');
                   $data = json_decode($json);
                   $isData=sizeof($data);
		      
                 if($isData >0){
		    $count=1;
		    //$textReplyMessage = 'ตอบคุณ @'.$userDisplayName; 
                    //$textMessage = new TextMessageBuilder($textReplyMessage);
		    //$multiMessage->add($textMessage);
                    foreach($data as $rec){
			   $count++;
                           $textReplyMessage= "\nหมายเลข ปชช. ".$rec->nationid."\nชื่อ".$rec->name."\nที่อยู่".$rec->address."\nหมายเหตุ".$rec->note;
                           $textMessage = new TextMessageBuilder($textReplyMessage);
			   $multiMessage->add($textMessage);
			   $textReplyMessage= "https://www.hooq.info/img/$rec->nationid.png";
                           $textMessage = new TextMessageBuilder($textReplyMessage);
			   $multiMessage->add($textMessage);
			   $picFullSize = "https://www.hooq.info/img/$rec->nationid.png";
                           $picThumbnail = "https://www.hooq.info/img/$rec->nationid.png";
			   //$picFullSize = 'https://s.isanook.com/sp/0/rp/r/w700/ya0xa0m1w0/aHR0cHM6Ly9zLmlzYW5vb2suY29tL3NwLzAvdWQvMTY2LzgzNDUzOS9sb3ZlcmppbmEuanBn.jpg';
                           //$picThumbnail = 'https://s.isanook.com/sp/0/rp/r/w700/ya0xa0m1w0/aHR0cHM6Ly9zLmlzYW5vb2suY29tL3NwLzAvdWQvMTY2LzgzNDUzOS9sb3ZlcmppbmEuanBn.jpg';
                           $imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			   $multiMessage->add($imageMessage);
			    //$arrayPostData['to']=$replyId;
			    //$arrayPostData['messages'][0]['type']="text";
			    //$arrayPostData['messages'][0]['text']="hello";
			    //pushMsg($arrayHeader,$arrayPostData);
                           }//end for each
	            $replyData = $multiMessage;
			 
		   }else{ //$isData <0  ไม่พบข้อมูลที่ค้นหา
		          $textReplyMessage= "ตอบคุณ ".$userDisplayName."ไม่พบ ".$explodeText[1]."  ในฐานข้อมูลของหน่วย"; 
			  $textMessage = new TextMessageBuilder($textReplyMessage);
			  $multiMessage->add($textMessage);
			  //$picFullSize = 'https://s.isanook.com/sp/0/rp/r/w700/ya0xa0m1w0/aHR0cHM6Ly9zLmlzYW5vb2suY29tL3NwLzAvdWQvMTY2LzgzNDUzOS9sb3ZlcmppbmEuanBn.jpg';
                          //$picThumbnail = 'https://s.isanook.com/sp/0/rp/r/w700/ya0xa0m1w0/aHR0cHM6Ly9zLmlzYW5vb2suY29tL3NwLzAvdWQvMTY2LzgzNDUzOS9sb3ZlcmppbmEuanBn.jpg';
                          //$imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			  //$multiMessage->add($imageMessage);
			  $replyData = $multiMessage;
			 // กรณีจะตอบเฉพาะข้อความ
		      //$bot->replyText($replyToken, $textMessage);
		        } // end $isData>0
		   }else{ // no $explodeText[1]
	                $textReplyMessage= "ตอบคุณ ".$userDisplayName."คุณให้ข้อมูลในการสอบถามไม่ครบถ้วนค่ะ"; 
			$textMessage = new TextMessageBuilder($textReplyMessage);
			  $multiMessage->add($textMessage);
			  //$picFullSize = "https://s.isanook.com/sp/0/rp/r/w700/ya0xa0m1w0/aHR0cHM6Ly9zLmlzYW5vb2suY29tL3NwLzAvdWQvMTY2LzgzNDUzOS9sb3ZlcmppbmEuanBn.jpg";
                          //$picThumbnail = "https://s.isanook.com/sp/0/rp/r/w700/ya0xa0m1w0/aHR0cHM6Ly9zLmlzYW5vb2suY29tL3NwLzAvdWQvMTY2LzgzNDUzOS9sb3ZlcmppbmEuanBn.jpg";
                          //$imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			  //$multiMessage->add($imageMessage);
			  $replyData = $multiMessage;
			 // กรณีจะตอบเฉพาะข้อความ
		      //$bot->replyText($replyToken, $textMessage);
		   }// end !is_null($explodeText[1])
		/* จบส่วนดึงข้อมูลจากฐานข้อมูล */
		      $response = $bot->replyMessage($replyToken,$replyData);
                   break;
		
         case '$e':
         $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey='.MLAB_API_KEY.'&q={"nationid":"'.$explodeText[1].'"}');
          $data = json_decode($json);
          $isData=sizeof($data);
          if($isData >0){
          $replyText="พบข้อมูลบุคคลที่จะแก้ไข";
            foreach($data as &$rec){
              $peopleUpdateId = $rec->_id;
              foreach ($peopleUpdateId as $key=>$value){
                if ($key==='$oid'){
                  $updateId=$value;
                  }
                }//end foreach carupdateid
              }//end for each data from json
$replyText=$replyText.' id:'.$updateId.' with '.$explodeText[2]."\n";
     // update note
     $mlabURL='https://api.mlab.com/api/1/databases/hooqline/collections/people/'.$updateId.'?apiKey='.MLAB_API_KEY;
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
     
   }else{ // ไม่พบข้อมูลบุคคล
                  $replyText= "ไม่พบข้อมูลบุคคล ".$explodeText[1]." ในฐานข้อมูลของหน่วย";
                }
break;
	
       
        case '$d':
$json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey='.MLAB_API_KEY.'&q={"nationid":"'.$explodeText[1].'"}');
 $data = json_decode($json);
 $isData=sizeof($data);
 if($isData >0){
 $replyText="พบข้อมูลบุคคลที่จะลบ\n";
   foreach($data as &$rec){
     $carDeleteId = $rec->_id;
     foreach ($carDeleteId as $key=>$value){
       if ($key==='$oid'){
         $deleteId=$value;
         }
       }//end foreach carupdateid
     }//end for each data from json
// delete
$mlabURL='https://api.mlab.com/api/1/databases/hooqline/collections/people/'.$deleteId.'?apiKey='.MLAB_API_KEY;
$opts=array('http'=>
  array(
    'method'=>'DELETE',
    'header'=>'Content-type: application/json'
      )
    );
$context= stream_context_create($opts);
$returnVal = file_get_contents($mlabURL,false,$context);
$replyText=$replyText."\n หมายเลข ".$explodeText[1]."\n id:".$deleteId." DELETED \n รายละเอียด".$returnVal;
}else{ // ไม่พบข้อมูลทะเบียนรถ
         $replyText= "ไม่พบข้อมูลบุคคล ".$explodeText[1].' ที่จะลบ';
       }
break;
		/* จบส่วนตรวจสอบบุคคล */
		
          default:
		// $replyText=$replyText.$displayName.$statusMessage;
		break;
            }//end switch
	    
	    $bot->replyText($reply_token, $replyText);
    }//end if text
}// end foreach event
?>
