<?php // callback.php
// กรณีต้องการตรวจสอบการแจ้ง error ให้เปิด 3 บรรทัดล่างนี้ให้ทำงาน กรณีไม่ ให้ comment ปิดไป
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__."/vendor/autoload.php";
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use \Statickidz\GoogleTranslate;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentIconSize;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentButtonHeight;
use LINE\LINEBot\Constant\Flex\ComponentSpaceSize;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
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
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\IconComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SpacerComponentBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
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
	// Message Event
 if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
  $rawText = $event->getText();$text = strtolower($rawText);$explodeText=explode(" ",$text);$textReplyMessage="";
	$log_note=$text;
	 $tz_object = new DateTimeZone('Asia/Bangkok');
         $datetime = new DateTime();
         $datetime->setTimezone($tz_object);
         $dateTimeNow = $datetime->format('Y\-m\-d\ H:i:s');
	$replyToken = $event->getReplyToken();	
        $multiMessage =     new MultiMessageBuilder;
	$replyData='No Data';
        $userId=$event->getUserId();
	$res = $bot->getProfile($userId);
         if ($res->isSucceeded()) {
              $profile = $res->getJSONDecodedBody();
              if(!is_null($profile['displayName'])){$displayName = $profile['displayName'];}else{$displayName ='';}
              if(!is_null($profile['statusMessage'])){$statusMessage = $profile['statusMessage'];}else{$statusMessage ='';}
              if(!is_null($profile['pictureUrl'])){$pictureUrl = $profile['pictureUrl'];}else{$pictureUrl ='';}
	      $textReplyMessage= "คุณ ".$displayName;
	      //$textMessage = new TextMessageBuilder($textReplyMessage);
	      //$multiMessage->add($textMessage);  
		 
		 if(($explodeText[0]=='#register') and (isset($explodeText[1]))){ // เก็บข้อมูลผู้สมัคร แต่ยังคงให้ status =0
			                $text_parameter = str_replace("#register ","", $text); 
					$newUserData = json_encode(array('userName' => $text_parameter,'displayName' => $displayName,
									 'userId'=> $userId,'statusMessage'=> $statusMessage,
									 'pictureUrl'=>$pictureUrl,'status'=>0) );
                                        $opts = array('http' => array( 'method' => "POST",
                                          'header' => "Content-type: application/json",
                                          'content' => $newUserData ) );
           
                                       $url = 'https://api.mlab.com/api/1/databases/crma51/collections/user_register?apiKey='.MLAB_API_KEY.'';
                                       $context = stream_context_create($opts);
                                       $returnValue = file_get_contents($url,false,$context);
			               if($returnValue){
		                           $textReplyMessage= "คุณ".$displayName." ได้ส่ง รหัสเครื่องให้ลิซ่าแล้วนะคะ\n\n รอการอนุมัติสักครู่นะคะ เพื่อให้การลงทะเบียนสมบูรณ์ (ปกติจะใช้เวลาไม่นานถ้าไม่ลืมนะคะ คริคริ) หลังจากที่ท่านลงทะเบียนแล้วถึงจะสามารถตรวจสอบข้อมูลกับลิซ่าได้นะค่ะ";
			                    $textReplyMessage= $textReplyMessage."\n\n เพื่อป้องกันการเข้ามาใช้งานโดยไม่ได้รับอนุญาต และปกป้องข้อมูลส่วนตัวของเพื่อนๆ ซึ่งเป็นเรื่องที่สำคัญ \n\n ผู้ใช้จำเป็นต้องลงทะเบียน เมื่อท่านพิมพ์ #register และข้อมูลส่วนตัวของท่าน แสดงว่าท่านยินยอมให้ลิซ่าเก็บรหัสของ LINE กับอุปกรณ์ที่ท่านใช้งาน เพื่อยืนยันตัวบุคคลก่อน";
			                   $textReplyMessage= $textReplyMessage."\n\n พิมพ์ #help เพื่อสอบถามวิธีการตั้งคำถามให้ลิซ่าช่วยตอบ";
                                            $textReplyMessage= $textReplyMessage."\n\n รหัสของคุณคือ ".$userId."\n\n รหัสของคุณจะใช้ได้จนกว่าคุณจะสมัคร LINE ใหม่ หรือเปลี่ยนเครื่อง \n\n ";
			                  $textMessage = new TextMessageBuilder($textReplyMessage);
			                   $multiMessage->add($textMessage);		                           
					   $textReplyMessage= $userId;
                                           $textMessage = new TextMessageBuilder($textReplyMessage);
			                   $multiMessage->add($textMessage);
					   //$textReplyMessage= "พิมพ์ #register ยศ ชื่อ นามสกุล ตำแหน่ง สังกัด หมายเลขโทรศัพท์ เพื่อลงทะเบียนขอใช้งานระบบ";
			                   //$textReplyMessage= $textReplyMessage."\n\nพิมพ์ #c ทะเบียนรถ (เช่น #c กก12345ยะลา) เพื่อตรวจสอบทะเบียนรถ";
			 	           
			                  // $textReplyMessage= $textReplyMessage."\n\nพิมพ์ #p หมายเลข ปชช. 13 หลัก (เช่น #p 1234567891234) เพื่อตรวจสอบประวัติบุคคลใน ทกร.";
			                  // $textReplyMessage= $textReplyMessage."\n\n พิมพ์ #f ชื่อ ตำแหน่ง สังกัด (เช่น #f ลิซ่า) เพื่อค้นหาข้อมูลการติดต่อเพื่อน จปร.51";
			                   //$textReplyMessage= $textReplyMessage."\n\n พิมพ์ #lisa คำถาม คำตอบ (เช่น #lisa ชื่ออะไร ลิซ่าค่ะ) เพื่อสอนคำใหม่ให้ลิซ่า";
			 	           //$textReplyMessage= $textReplyMessage."\n\n พิมพ์ #lisa คำถาม (เช่น #lisa ชื่ออะไร) เพื่อสอบถามข้อมูลจากลิซ่า";
			 	          
			                   //$textReplyMessage= $textReplyMessage."\n\n พิมพ์ #tran รหัสประเทศต้นทาง ปลายทาง คำที่ต้องการแปล (เช่น #tran ms th hello แปลคำว่า hello จากมาเลเซียเป็นไทย) เพื่อแปลภาษา";
				           //$textMessage = new TextMessageBuilder($textReplyMessage);
			                   //$multiMessage->add($textMessage);
			                   $replyData = $multiMessage;
			                   $response = $bot->replyMessage($replyToken,$replyData);
					   $userId = NULL;
				           }else{
					   $textReplyMessage= "คุณ".$displayName." ไม่สามารถลงทะเบียน ID ".$userId." ได้ค่ะ\n\n กรุณาลองใหม่อีกครั้งค่ะ \n\nหรือแจ้งผู้ดูแลระบบโดยตรงนะคะ";
                                           $textMessage = new TextMessageBuilder($textReplyMessage);
			                   $multiMessage->add($textMessage);
                                           $replyData = $multiMessage;
					   $userId = NULL;
				       }
		 } // end #register
		 
		 /*---- prove user by update status from 0 to 1---*/
		if(($explodeText[0]=='#prove') and ($userId=='U4acff231b87ace2fa827aea5b01baa6a')){ 
				$toProveUserId = str_replace("#prove ","", $rawText);  
			// get $_id
				$json = file_get_contents('https://api.mlab.com/api/1/databases/crma51/collections/user_register?apiKey='.MLAB_API_KEY.'&q={"userId":"'.$toProveUserId.'"}');
                                  $data = json_decode($json);
                                  $isGet_id=sizeof($data);
                                 if($isGet_id >0){
                                    foreach($data as &$rec){
                                       $documentId= $rec->_id;
					    foreach($documentId as $key => $value){
						    if($key === '$oid'){
							    $updateId=$value;
					                    $textReplyMessage="อนุมัติ Id ".$rec->userId." แล้วค่ะ";
					                    }
					             } // end for each $key=>$value
					    }//end for each
			  $updateUserData = json_encode(array('$set' => array('status' => '1')));
			  $opts = array('http' => array( 'method' => "PUT",
                                          'header' => "Content-type: application/json",
                                          'content' => $updateUserData
                                           )
                                        );
           
                                  $url = 'https://api.mlab.com/api/1/databases/crma51/collections/user_register/'.$updateId.'?apiKey='.MLAB_API_KEY;
                                  $context = stream_context_create($opts);
                                  $returnValue = file_get_contents($url,false,$context);
				 }else{// end isGet_id
					$textReplyMessage=$explodeText[1]." No User ID";
				 }// end isGet_id
				 $textMessage = new TextMessageBuilder($textReplyMessage);
			          $multiMessage->add($textMessage);
			          $replyData = $multiMessage;
			           $response = $bot->replyMessage($replyToken,$replyData);
			 } // end #prove
		 /*--------------------------*/
		 if($explodeText[0]=='#help'){
			 $textReplyMessage= "คุณ".$displayName."\n\n เพื่อป้องกันการเข้ามาใช้งานโดยไม่ได้รับอนุญาต และปกป้องข้อมูลส่วนตัวของเพื่อนๆ ซึ่งเป็นเรื่องที่สำคัญ \n\n ผู้ใช้จำเป็นต้องลงทะเบียน เมื่อท่านพิมพ์ #register และข้อมูลส่วนตัวของท่าน แสดงว่าท่านยินยอมให้ลิซ่าเก็บรหัสของ LINE กับอุปกรณ์ที่ท่านใช้งาน เพื่อยืนยันตัวบุคคลก่อน";
			 $textReplyMessage= $textReplyMessage."\n\n#register พ.ท.วิชญ์วิสิทธ์ เทียมจิตร บชร.2 0831098844 ชื่อเล่นบอม ฉายาหม่ำพันล้าน งานอดิเรก ช่วยเมียเลี้ยง KGB ";
			 $textReplyMessage= $textReplyMessage."\nพิมพ์ #register ยศ ชื่อ นามสกุล ตำแหน่ง สังกัด หมายเลขโทรศัพท์ หรือข้อมูลอื่นๆ ที่ท่านต้องการ เพื่อลงทะเบียนขอใช้งานระบบ หลังจากนั้นรอผู้ดูแลลิซ่า อนุมัติ ท่านจะใช้งานได้ค่ะ ปกติก็ใช้เวลาไม่นานนะคะ ถ้าไม่ลืม คริคริ";
			 $textReplyMessage= $textReplyMessage."\n\n#help ";
			 $textReplyMessage= $textReplyMessage."\n พิมพ์ #help เพื่อสอบถามวิธีการตั้งคำถามให้ลิซ่าช่วยตอบ";
			 //$textReplyMessage= $textReplyMessage."\n\n พิมพ์ #c ทะเบียนรถ (เช่น #c กก12345ยะลา) เพื่อตรวจสอบทะเบียนรถ"; 
			 //$textReplyMessage= $textReplyMessage."\n\n พิมพ์ #p หมายเลข ปชช. 13 หลัก (เช่น #p 1234567891234) เพื่อตรวจสอบประวัติบุคคลใน ทกร.";
			 $textReplyMessage= $textReplyMessage."\n\n#f บอม";
			 $textReplyMessage= $textReplyMessage."\n พิมพ์ #f ชื่อ/นามสกุล/ตำแหน่ง/สังกัด (เช่น #f ลิซ่า) เพื่อค้นหาข้อมูลการติดต่อเพื่อน จปร.51";
			 $textReplyMessage= $textReplyMessage."\n\n#lisa พี่บอม หม่ำไง";
			 $textReplyMessage= $textReplyMessage."\n พิมพ์ #lisa คำถาม คำตอบ (เช่น #lisa ชื่ออะไร ลิซ่าค่ะ) เพื่อสอนคำใหม่ให้ลิซ่า";
			 $textReplyMessage= $textReplyMessage."\n\n#lisa พี่บอม ";
			 $textReplyMessage= $textReplyMessage."\n พิมพ์ #lisa คำถาม  (เช่น #lisa ชื่ออะไร ) เพื่อสอบถามข้อมูลจากลิซ่า";
			 $textReplyMessage= $textReplyMessage."\n\n พิมพ์ #tran รหัสประเทศต้นทาง ปลายทาง คำที่ต้องการแปล (เช่น #tran ms th hello แปลคำว่า hello จากมาเลเซียเป็นไทย) เพื่อแปลภาษา";
			 $textReplyMessage= $textReplyMessage."\n\n th ไทย ms มาเลเซีย id อินโดนีเซีย zh-CN จีน en อังกฤษ";			 
			 $textReplyMessage= $textReplyMessage."\n\n อาจจะยุ่งยากนิดนึงนะคะ แต่เพื่อป้องกันไม่ให้ลิซ่าตอบเองโดยไม่ตั้งใจถาม จะเป็นการรบกวนพี่ๆ นะคะ ลิซ่าเกรงจายยยยยยยย";
				 $textMessage = new TextMessageBuilder($textReplyMessage);
			          $multiMessage->add($textMessage);
			          $replyData = $multiMessage;
			          $response = $bot->replyMessage($replyToken,$replyData);
		 }// end of help
		 
              }else{ // end get displayName succeed
		 /*-----------------  register by no data --*/
		  if(($explodeText[0]=='#register') and (isset($explodeText[1]))){ // เก็บข้อมูลผู้สมัคร แต่ยังคงให้ status =0
			  
			                $text_parameter = str_replace("#register ","", $text); 
			               $displayName ='';
                                       $statusMessage ='';
                                       $pictureUrl ='';
			                $text_parameter = str_replace("#register ","", $text); 
					$newUserData = json_encode(array('userName' => $text_parameter,'displayName' => $displayName,
									 'userId'=> $userId,'statusMessage'=> $statusMessage,
									 'pictureUrl'=>$pictureUrl,'status'=>0) );
                                        $opts = array('http' => array( 'method' => "POST",
                                          'header' => "Content-type: application/json",
                                          'content' => $newUserData ) );
           
                                       $url = 'https://api.mlab.com/api/1/databases/crma51/collections/user_register?apiKey='.MLAB_API_KEY.'';
                                       $context = stream_context_create($opts);
                                       $returnValue = file_get_contents($url,false,$context);
			               if($returnValue){
                                            $textReplyMessage= "คุณ".$displayName." ได้ส่ง รหัสเครื่องให้ลิซ่าแล้วนะคะ\n\n รอการอนุมัติสักครู่นะคะ เพื่อให้การลงทะเบียนสมบูรณ์ (ปกติจะใช้เวลาไม่นานถ้าไม่ลืมนะคะ คริคริ) หลังจากที่ท่านลงทะเบียนแล้วถึงจะสามารถตรวจสอบข้อมูลกับลิซ่าได้นะค่ะ";
			                    $textReplyMessage= $textReplyMessage."\n\n เพื่อป้องกันการเข้ามาใช้งานโดยไม่ได้รับอนุญาต และปกป้องข้อมูลส่วนตัวของเพื่อนๆ ซึ่งเป็นเรื่องที่สำคัญ \n\n ผู้ใช้จำเป็นต้องลงทะเบียน เมื่อท่านพิมพ์ #register และข้อมูลส่วนตัวของท่าน แสดงว่าท่านยินยอมให้ลิซ่าเก็บรหัสของ LINE กับอุปกรณ์ที่ท่านใช้งาน เพื่อยืนยันตัวบุคคลก่อน";
			                    $textReplyMessage= $textReplyMessage."\n\n พิมพ์ #help เพื่อสอบถามวิธีการตั้งคำถามให้ลิซ่าช่วยตอบ";
                                            $textReplyMessage= $textReplyMessage."\n\n รหัสของคุณคือ ".$userId."\n\n รหัสของคุณจะใช้ได้จนกว่าคุณจะสมัคร LINE ใหม่ หรือเปลี่ยนเครื่อง ";
			                  $textMessage = new TextMessageBuilder($textReplyMessage);
			                   $multiMessage->add($textMessage);		                           
					   $textReplyMessage= $userId;
                                           $textMessage = new TextMessageBuilder($textReplyMessage);
			                   $multiMessage->add($textMessage);
					   //$textReplyMessage= "พิมพ์ #register ยศ ชื่อ นามสกุล ตำแหน่ง สังกัด หมายเลขโทรศัพท์ เพื่อลงทะเบียนขอใช้งานระบบ";
			 	           
			                   //$textReplyMessage= "\n\n พิมพ์ #help เพื่อสอบถามวิธีการตั้งคำถามให้ลิซ่าช่วยตอบ";
			                   //$textReplyMessage= $textReplyMessage."\n\nพิมพ์ #c ทะเบียนรถ (เช่น #c กก12345ยะลา) เพื่อตรวจสอบทะเบียนรถ";
			 	           
			                   //$textReplyMessage= $textReplyMessage."\n\nพิมพ์ #p หมายเลข ปชช. 13 หลัก (เช่น #p 1234567891234) เพื่อตรวจสอบประวัติบุคคลใน ทกร.";
			                   //$textReplyMessage= $textReplyMessage."\n\n พิมพ์ #f ชื่อ ตำแหน่ง สังกัด (เช่น #f ลิซ่า) เพื่อค้นหาข้อมูลการติดต่อเพื่อน จปร.51";
			                   //$textReplyMessage= $textReplyMessage."\n\n พิมพ์ #lisa คำถาม คำตอบ (เช่น #lisa ชื่ออะไร ลิซ่าค่ะ) เพื่อสอนคำใหม่ให้ลิซ่า";
			 	           
			                   //$textReplyMessage= $textReplyMessage."\n\nพิมพ์ #tran รหัสประเทศต้นทาง ปลายทาง คำที่ต้องการแปล (เช่น #tran ms th hello แปลคำว่า hello จากมาเลเซียเป็นไทย) เพื่อแปลภาษา";
				           //$textMessage = new TextMessageBuilder($textReplyMessage);
			                   //$multiMessage->add($textMessage);
			                   $replyData = $multiMessage;
			                   $response = $bot->replyMessage($replyToken,$replyData);
					   $userId = NULL;
				           }else{
					   $textReplyMessage= "คุณ".$displayName." ไม่สามารถลงทะเบียน ID ".$userId." ได้ค่ะ\n\n กรุณาลองใหม่อีกครั้งค่ะ \n\nหรือแจ้งผู้ดูแลระบบโดยตรงนะคะ";
                                           $textMessage = new TextMessageBuilder($textReplyMessage);
			                   $multiMessage->add($textMessage);
                                           $replyData = $multiMessage;
					   $userId = NULL;
				       }
		 } // can not get displayName and //end of #register by userId 
	 }// end can not get displayName
	if(!is_null($userId)){
	    $json = file_get_contents('https://api.mlab.com/api/1/databases/crma51/collections/user_register?apiKey='.MLAB_API_KEY.'&q={"userId":"'.$userId.'"}');
            $data = json_decode($json);
            $isUserRegister=sizeof($data);
		if($isUserRegister <=0){
		           $notRegisterReplyMessage= "คุณ".$displayName." ยังไม่ได้ลงทะเบียน ID ".$userId." ไม่สามารถเข้าถึงฐานข้อมูลได้นะคะ\n กรุณาพิมพ์ #register ยศ ชื่อ นามสกุล ตำแหน่ง สังกัด หมายเลขโทรศัพท์ เพื่อลงทะเบียนค่ะ";
                          //$log_note = $log_note.$notRegisterReplyMessage;
	         }else{ // User registered
                    foreach($data as $rec){
			    $registerUserReplyMessage="From phone \nDisplayname ".$displayName."\n User Id ".$userId;
			    $userName=$rec->userName;
                           //$log_note = $log_note."From phone \nDisplayname ".$displayName."\n User Id ".$userId;
                           //$log_note= $log_note."\nFrom DB\nDisplayname ".$rec->displayName."\n Registered Id ".$rec->userId;
			     }//end for each
	if($rec->status==1){ // อนุมัติตัวบุคคลแล้ว
		//------------------------------------------
		
		$newPersonData='
 [
 {
   "nationid": 1560100019994,
   "name": "นายคอยรูลอานวารีจิ",
   "address": "๑/๒ ม.๑ บ.ป่าไหม้ ต.ดอนทราย อ.ไม้แก่น จว.ป.น.",
   "note": "หน.REGU",
   "picUrl": "1560100019994.jpg?alt=media&token=ec0f8d04-106d-4009-82d8-61a2622c46ed"
 },
 {
   "nationid": 1900600053938,
   "name": "นายบูคอรีหลำโสะ",
   "address": "๕/๒ ม.๒ บ.ควนหรัน ต.เปียน\nอ.สะบ้าย้อย จว.ส.ข.\n",
   "note": "หน.PLATONG \nต.ท่าเรือ, ต.บางโกร๊ะ,ต.โคกโพธิ์,ต.มะกรูด,ต.ป่าบอน, ต.ช้างไห้-ตก, ต.ทรายขาว",
   "picUrl": "1900600053938.jpg?alt=media&token=2354d9d6-f637-47a9-b7d8-393ebd87ca0d"
 },
 {
   "nationid": 1900600056716,
   "name": "นายอับดุลเลาะ บาราเฮง/",
   "address": "๒๗ บ.สวนนอก ม.๑ ต.ช้างให้ตก อ.โคกโพธิ์ จว.ป.น.",
   "note": "หน.REGU PLATONG ต.ท่าเรือ, ต.บางโกร๊ะ,ต.โคกโพธิ์,ต.มะกรูด,ต.ป่าบอน, ต.ช้างไห้-ตก, ต.ทรายขาว",
   "picUrl": "1900600056716.jpg?alt=media&token=e95ba43d-adb8-4add-aabf-0900ce6f54d3"
 },
 {
   "nationid": 1940100017487,
   "name": "นายอิสมาแอ แลแร/ป๊ะเงาะ",
   "address": "๑๐๕ ม.๓ ต.คลองมานิง อ.เมือง\nจว.ป.น.\n",
   "note": "ฝ่าย LOGISTIK\n(จัดหา/เก็บซ่อนอาวุธ)\n",
   "picUrl": "1940100017487.jpg?alt=media&token=913ba44d-e9e4-498e-9bda-dfa95f6406b1"
 },
 {
   "nationid": 1940100118569,
   "name": "นายมะนาเซไซร์ดี/นาเซ",
   "address": "๑๗๖/๑ ม.๑ บ.ดอนรัก \nต.ดอนรัก อ.หนองจิก จว.ป.น.\n",
   "note": "รอง หน.PLATONG\n(สั่งการแทน/ช่วงนายเสรี แวมามุ หลบหนี)\n",
   "picUrl": "1940100118869.jpg?alt=media&token=4d663f19-6df4-47ba-aa55-6aab10c3cba2"
 },
 {
   "nationid": 1940100148051,
   "name": "มูฮำหมัดกอซาฟีสาเมาะ",
   "address": "๖๐/๓ บ.ท่ากูโบ ม.๔\nต.ปูโละปูโย อ.หนองจิก จว.ป.น.",
   "note": "ฝ่าย LOGISTIK\n(จัดหา/เก็บซ่อนอาวุธ)",
   "picUrl": "1940100148051.jpg?alt=media&token=f333fb17-7023-4b2d-8b06-735f5458101b"
 },
 {
   "nationid": 1940200006401,
   "name": "นายสการียาวาจิ/เปาะยู",
   "address": "๕/๗ บ.วังกว้าง ม.๕ ต.ป่าไร่ อ.แม่ลาน จว.ป.น. เดิมอยู่ ๙๑บ.บาตูปูเต๊ะ ม.๖ ต.บ้านแหร อ.ธารโต จว.ย.ล.",
   "note": "หน.REGU",
   "picUrl": "1940200006401.jpg?alt=media&token=db6071a3-b697-48ab-9652-be03297dc11d"
 },
 {
   "nationid": 1940200009681,
   "name": "นายสุไฮหลีหนิจิบุลัด",
   "address": "๕๕/๒ ม.๔ บ.คลองช้าง\nต.นาเกตุ อ.โคกโพธิ์ จว.ป.น.",
   "note": "หน.REGU",
   "picUrl": "1940200009681.jpg?alt=media&token=a5858b3a-fc9f-42d6-84e4-1481bb2d5d72"
 },
 {
   "nationid": 1940200076230,
   "name": "นายสาบุดิงดอรอเสะ/เป๊าะเต๊ะ ",
   "address": "๖๒/๕ ม.๗ บ.คลองช้างออก \nต.นาเกตุ อ.โคกโพธิ์ จว.ป.น.",
   "note": "คุมตัว ๓๑ ต.ค.๕๗",
   "picUrl": "1940200076230.jpg?alt=media&token=00ee24e3-c4e4-4070-92d0-e50dfdfd2d58"
 },
 {
   "nationid": 1940200079361,
   "name": "นายอับดุลการิมสะตาปอ",
   "address": "๔๓/๑ ม.๗ บ.โผงโผง\nต.ปากล่อ อ.โคกโพธิ์ จว.ป.น.",
   "note": "ซถ.ฟาเดล เสาะหมาน",
   "picUrl": "1940200079361.jpg?alt=media&token=eaf2d4d5-73d5-44d1-8a3f-259b10e1e98c"
 },
 {
   "nationid": 1940300002599,
   "name": "นายอับดุลสตอปา สุหลง/ซะ",
   "address": "๒๙ บ.ปากาลือสง ม.๖ ต.ตุยงอ.หนองจิก จว.ป.น.",
   "note": "รอง หน.PLATONG\n(สั่งการแทน/ช่วงนายเสรี แวมามุ หลบหนี)\n",
   "picUrl": "1940300002599.jpg?alt=media&token=dc912d89-e0a3-4a6a-a2d1-b403af9d49aa"
 },
 {
   "nationid": 1940300012861,
   "name": "นายเมาลานาสาเมาะ",
   "address": "๗๒ ม.๑ ต.ท่ากำชำ อ.หนองจิก\nจว.ป.น.",
   "note": "หน.REGU PLATONG\nอ.หนองจิก, ต.ปะกา-ฮารัง อ.เมืองปัตตานี",
   "picUrl": "1940300012861.jpg?alt=media&token=979f080a-c8ea-4227-84c2-add6e2acf400"
 },
 {
   "nationid": 1940300021020,
   "name": "สการียา หมื่นรายา/แบลัง",
   "address": "๖๗ บ.บางไร่คลองขุด ม.๑ \nต.บางเขา อ.หนองจิก จว.ป.น.",
   "note": "รศ.ขกท.สน.จชต.\n-คุมตัว ๑๒ ต.ค.๕๓",
   "picUrl": "1940300021020.jpg?alt=media&token=67ae076e-effd-4dc9-81b8-4103c0a2c936"
 },
 {
   "nationid": 1940300106769,
   "name": "นายซุลกี๊ฟลีอาบู/จ่าหอย",
   "address": "๑๑๓ บ.แคนา ม.๗ ต.บางเขา อ.หนองจิก จว.ป.น. (ตามบัตร)\n-ปัจจุบันอยู่ ม.๔ บ.ปูลากาซิง ต.กอลำอ.ยะรัง จว.ป.น.\n",
   "note": "TL/SABOTAS\nแยลีมอ/ยะรัง\n",
   "picUrl": "1940300106769.jpg?alt=media&token=399b9da2-12da-4e32-a7de-97eba467daa5"
 },
 {
   "nationid": 1940300111207,
   "name": "นายอันวาสุหลง/ลี/มะวี",
   "address": "๙๘/๑ ม.๔ ต.ลิปะสะโง \nอ.หนองจิก จว.ป.น.",
   "note": "ฝ่าย LOGISTIK\n(จัดหา/เก็บซ่อนอาวุธ)",
   "picUrl": "1940300111207.jpg?alt=media&token=60f4e1d6-4b23-4c7f-a94b-41716d0f55c9"
 },
 {
   "nationid": 1940300113609,
   "name": "นายมะกอเซ็งอาแว/กอเซ็ง",
   "address": "๖๗/๔ ม.๔ บ.โคกคอแห้ง \nต.ปุโละปุโย อ.หนองจิก จว.ป.น.",
   "note": "ฝ่าย LOGISTIK\n(จัดหา/เก็บซ่อนอาวุธ)",
   "picUrl": "1940300113609.jpg?alt=media&token=6b9d5e96-d66d-4ef8-abd6-e4c0cfc03dc7"
 },
 {
   "nationid": 1940300119089,
   "name": "นายไซฟูสาหะ/ไซฟู",
   "address": "๘๒/๓ บ.เปี๊ยะ ม.๑ ต.ดาโต๊ะ\n อ.หนองจิก จว.ป.น.\n",
   "note": "ฝ่าย LOGISTIK ",
   "picUrl": "1940300119089.jpg?alt=media&token=e50de693-d999-41f2-b066-8f96e7edcbb4"
 },
 {
   "nationid": 1940300131046,
   "name": "นายอารีฟินยูนุ/ลี",
   "address": "๖๙ ม.๒ ต.บางเขา\nอ.หนองจิก",
   "note": "TL/SABOTAS \nโคกโพธิ์/หนองจิก\n",
   "picUrl": "1940300131046.jpg?alt=media&token=4fd06269-e230-4577-8f5c-f02aaaf779ad"
 },
 {
   "nationid": 1940400241354,
   "name": "นายมะดือเระแปะอิง",
   "address": "๑๖๖ ม.๔ บ.มะรวด\nต.คอกกระบือ อ.ปะนาเระ\nจว.ป.น.\n",
   "note": "อดีต ฝ่ายการข่าว/\nยทุธการ (INTOP)\n",
   "picUrl": "1940400241354.jpg?alt=media&token=00562bd5-c920-4bd9-89b0-2d12462cd798"
 },
 {
   "nationid": 1940500046361,
   "name": "นายอับดุลอาซิแอเสาะ",
   "address": "๔๗/๑ ม.๕ ต.ปะโด \nอ.มายอ จว.ป.น.\n",
   "note": "รศ.ขกท.สน.จชต.",
   "picUrl": "1940500046361.jpg?alt=media&token=e6bd90b8-e789-4d41-bac6-a461ae8d58c6"
 },
 {
   "nationid": 1940500069468,
   "name": "นายอะห์มัดรอมซีย์ ดาโอะ",
   "address": "๑๑๐ ม.๒ ต.ปานัน อ.มายอ\nจว.ป.น.\n",
   "note": "คุมตัว ๒๘ เม.ย.๕๖",
   "picUrl": "1940500069468.jpg?alt=media&token=5d7465cb-dbb5-4a03-97b9-ea199a0685e5"
 },
 {
   "nationid": 1940600001407,
   "name": "นายบือราเฮง มือสา/มะโอ๊ะ",
   "address": "๗๗/๑ บ.บือราแง ม.๒ ต.น้ำดำ \nอ.ทุ่งยางแดง จว.ป.น.\n",
   "note": "หน. LOGISTIK\nPLATONG ทุ่งยางแดง\n",
   "picUrl": "1940600001407.jpg?alt=media&token=fc5bf76d-d110-400a-bf8a-2ed72453257c"
 },
 {
   "nationid": 1940600032281,
   "name": "นายมะหดีกอเดร",
   "address": "๑๐๐ ม.๑ บ.บาลูกาลูวะ \nต.น้ำดำ อ.ทุ่งยางแดง จว.ป.น.",
   "note": "นายหามะสาเมาะ",
   "picUrl": "1940600032281.jpg?alt=media&token=1a50a22a-e73e-4070-825a-a2f062316ce9"
 },
 {
   "nationid": 1940600037991,
   "name": "นายคอยเดรย์ เจะโด",
   "address": "๗/๒ บ.เขาดิน ม.๓ ต.ปากู \nอ.ทุ่งยางแดง จว.ป.น.",
   "note": "นายหามะสาเมาะ\nนายอาซิ ดาโอง\nคุมตัว ๑๓ ธ.ค.๕๘",
   "picUrl": "1940600037991.jpg?alt=media&token=21e761ee-7052-490f-9ed4-ca650b955eac"
 },
 {
   "nationid": 1940600043568,
   "name": "นายสูเฟียนดือราโอ๊ะ/ยัง",
   "address": "๕๖ บ.บาลูกาลูวะ ม.๑ \nต.น้ำดำ อ.ทุ่งยางแดง\nจว.ป.น.",
   "note": "TL/SABOTAS ปาลัส",
   "picUrl": "1940600043568.jpg?alt=media&token=28cdb8e9-0654-46d6-9d11-de39e59448bd"
 },
 {
   "nationid": 1940600057623,
   "name": "นายอาชิโซะ/อายิ",
   "address": "๔๙ บ.ล่องควน ม.๖ ต.คูหา \nอ.สะบ้าย้อย \n-บ้านภรรยาเลขที่ ๓๓/๒ บ.เจ๊ะดา ม.๔ ต.บางโกระ อ.โคกโพธิ์\n",
   "note": "ซถ.ยาการียา กือโนะ\n-ไม่นับยอด \n-ปรับยอดห้วงต่อไป\n",
   "picUrl": "1940600057623.jpg?alt=media&token=c556cea5-d62e-4d5d-bb20-09c0b12db46a"
 },
 {
   "nationid": 1940700007598,
   "name": "นายฮามือรีตอกอ/บาซิ\n(นายซอบรีกอตอ)\n",
   "address": "๑๗ ม.๑ ต.กะดุนง อ.สายบุรี จว.ป.น.",
   "note": "รอง หน.KOMPI\n(ซถ.ขกท.สน.จชต.)\n",
   "picUrl": "1940700007598.jpg?alt=media&token=e9380b69-990a-429a-9fae-697f77e7dbd0"
 },
 {
   "nationid": 1940700010157,
   "name": "นายยาการียาบาโง",
   "address": "๔๘ ม.๔ บ.สะบือแร ต.บือเระ \nอ.สายบุรี จว.ป.น.\n",
   "note": "รับผิดชอบ\nอ.สายบุรี\n",
   "picUrl": "1940700010157.jpg?alt=media&token=1543b948-5c77-429f-8d9f-418f0fb8aa5a"
 },
 {
   "nationid": 1940700032142,
   "name": "ลุกมานสาและ/แซะ/มิ๊ก",
   "address": "๑๑๓/๑ บ.เจาะบาตู ม.๑ \nต.ทุ่งคล้า อ.สายบุรี จว.ป.น.",
   "note": "ชำนาญประกอบระเบิด\nTL/SABOTAS ตะลุบัน\n",
   "picUrl": "1940700032142.jpg?alt=media&token=7828fe81-1359-4d98-9fd6-b27747998a67"
 },
 {
   "nationid": 1940700035311,
   "name": "นายอับดุลเลาะ มูดอ/อายิ",
   "address": "๑๕๐/๒ ม.๓ บ.บาโงยือริง \nต.บือเระ อ.สายบุรี จว.ป.น.\n",
   "note": "ฝ่าย LOGISTIK\nจัดหา/เก็บซ่อนอาวุธ\n\n",
   "picUrl": "1940700035311.jpg?alt=media&token=775f2d65-3c26-4e46-83c4-43eda2ff583b"
 },
 {
   "nationid": 1940700042539,
   "name": "นายอัมมัรนิติมุง/บุสรอ",
   "address": "๑๓๒/๒ บ.ละอาร์ ม.๖ \nต.กะดุนง อ.สายบุรี\n",
   "note": "คุมตัว ๒๓ พ.ค.๕๖\n-คุมตัว ๒ ก.พ.๖๑\n",
   "picUrl": "1940700042539.jpg?alt=media&token=259aebc6-a459-47ca-a476-c97bb2193c06"
 },
 {
   "nationid": 1940900013923,
   "name": "นายอิสมาแออาแวกะจิ",
   "address": "๑๗๕/๑ บ.ไอร์โซ ม.๕ \nต.ช้างเผือก อ.จะแนะ จว.น.ธ. เดิมอยู่ ๒๗ บ.จะรัง ม.๗ ต.จะรัง อ.ยะหริ่ง จว.ป.น.\n",
   "note": "คุมตัว ๗ ก.ย.๕๙",
   "picUrl": "1940900013923.jpg?alt=media&token=1c19f96c-e139-4412-b424-58f2a2636b9a"
 },
 {
   "nationid": 1940900014130,
   "name": "นายมูฮามัดนอเจ๊ะเละ",
   "address": "๑๑๓ บ.บาโลย ม.๒\nต.บาโลย อ.ยะหริ่ง จว.ป.น.\n",
   "note": "TL/SABOTAS\nกือดา/เมือง\n",
   "picUrl": "1940900014130.jpg?alt=media&token=0ce8ef0f-126c-4f11-b82a-cd1fab2630c2"
 },
 {
   "nationid": 1940900089016,
   "name": "นายยาการียาตาเยะ/รอง",
   "address": "๑๓/๑ บ.บาโลย ม.๑ \nอ.ยะหริ่ง จว.ป.น.\n\n",
   "note": "ฝ่าย LOGISTIK\n(จัดหา/เก็บซ่อนอาวุธ)\n",
   "picUrl": "1940900089016.jpg?alt=media&token=991495d3-a3fc-4eca-ab44-7a79b9597755"
 },
 {
   "nationid": 1941000011330,
   "name": "นายมูหะมะซูไอมียามา/มะ",
   "address": "๓๒ บ.กอลำ ม.๑ ต.กอลำ\nอ.ยะรัง จว.ป.น.\n",
   "note": "รอง หน.REGU",
   "picUrl": "1941000011330.jpg?alt=media&token=e4b69b0d-b48a-4dff-b607-da71ed819150"
 },
 {
   "nationid": 1941000022528,
   "name": "นายมะสกรีโต๊ะเด็ง/ซูไฮ",
   "address": "๗๐/๕ ม.๕ ต.เมาะมาวี อ.ยะรัง จว.ป.น. เดิมอยู่ ๗๐/๑ ม.๕ \nต.เมาะมาวี อ.ยะรัง จว.ป.น. ",
   "note": "ฝ่าย LOGISTIK จัดหา/เก็บซ่อนอาวุธ\n\n",
   "picUrl": "1941000022528.jpg?alt=media&token=746974e2-d5ea-4fd1-a195-082ff919c098"
 },
 {
   "nationid": 1941000022595,
   "name": "รุสลัน บือซา/ปะจู/มะซัง",
   "address": "๒๓๑/๑ บ.ต้นมะขาม ม.๔\nต.เมาะมาวี อ.ยะรัง จว.ป.น.\n",
   "note": "คุมตัว ๕ ส.ค.๖๐",
   "picUrl": "1941000022595.jpg?alt=media&token=dc337bbb-d80e-4a13-a519-06039aea900c"
 },
 {
   "nationid": 1941000031161,
   "name": "นายอมานจารง",
   "address": "๓๗ ม.๒ บ.นัดกูโบร์ ต.เขาตูม อ.ยะรัง จว.ป.น.",
   "note": "TL/SABOTAS",
   "picUrl": "1941000031161.jpg?alt=media&token=47725f30-599b-4076-abe9-3374c7ea4173"
 },
 {
   "nationid": 1941000033104,
   "name": "นายอัสรีแวฮามะ",
   "address": "๑/๙ บ.โคกหญ้าคา ม.๖ \nต.คลองใหม่ อ.ยะรัง จว.ป.น.\n",
   "note": "คุมตัว ๑๕ พ.ย.๕๗\n-ปฏิเสธทุกกรณี\n",
   "picUrl": "1941000033104.jpg?alt=media&token=7e5faa5d-53cd-4d28-8e2e-304eed5fcc43"
 },
 {
   "nationid": 1941000087549,
   "name": "นายอับดุลเลาะห์กาซอ/เปาะยู",
   "address": "๔๕ บ.ซาไก ม.๓ ต.บ้านแหร\nอ.ธารโต จว.ย.ล.\n",
   "note": "หน.REGU",
   "picUrl": "1941000087549.jpg?alt=media&token=0eeef3c5-1952-4c1c-90aa-3ceb6281f43a"
 },
 {
   "nationid": 1941000088715,
   "name": "นายมูหัมมัด ยาสา",
   "address": "๓๖/๒ ม.๔ ต.ปิตุมุดี อ.ยะรัง จว.ป.น.",
   "note": "TL/SABOTAS\nแยลีมอ/ยะรัง\n\n",
   "picUrl": "1941000088715.jpg?alt=media&token=93f537eb-adcd-40a1-80b0-9afb6589931a"
 },
 {
   "nationid": 1941000089312,
   "name": "นายฟัครุดดีนกอและ/โก๊ะ",
   "address": "๒๓/๒ บ.อาฆง ม.๔ ต.สาคอใต้ อ.มายอ จว.ป.น. เดิมอยู่ ๓๑/๑บ.ปูโล๊ะสนิแย ม.๔ ต.บ้านแหร อ.ธารโต จว.ย.ล.",
   "note": "TL/SABOTAS\nธารโต/เบตง\n",
   "picUrl": "1941000089312.jpg?alt=media&token=31848e02-bd9b-4f0d-9e1e-433620df50fc"
 },
 {
   "nationid": 1941000093611,
   "name": "นายอับดุลเลาะบือแน/จูรง",
   "address": "๕๑/๒ บ.ปูลากาซิง ม.๔ \nต.กอลำ อ.ยะรัง จว.ป.น.\n",
   "note": "คุมตัว ๓๐ ส.ค.๖๐",
   "picUrl": "1941000093611.jpg?alt=media&token=4510fa3f-28e5-439f-92d2-a5bcfe5f9c7f"
 },
 {
   "nationid": 1941000103234,
   "name": "นายอิบรอฮิมอาแว/จิมะ",
   "address": "๓๗/๔ บ.โต๊ะทูวอ ม.๔ \nต.ปิตูมุดี อ.ยะรัง\n",
   "note": "TL/SABOTAS\nแยลีมอ/ยะรัง\n",
   "picUrl": "1941000103234.gif?alt=media&token=3afa425f-a362-45ab-8156-33260ec64957"
 },
 {
   "nationid": 1941000127192,
   "name": "นายรุสดีมะนอ",
   "address": "๑๘๕ ม.๕ บ.โฉล ต.คลองใหม่ อ.ยะรัง จว.ป.น.",
   "note": "TL/SABOTAS\nแยลีมอ/ยะรัง\n-ชำนาญประกอบระเบิด\n\n",
   "picUrl": "1941000127192.jpg?alt=media&token=f4c9d775-28cf-4444-bec1-e2c5a45243e0"
 },
 {
   "nationid": 1941000136434,
   "name": "นายซุลกิฟลี เตะแต/เดะเราะ",
   "address": "๑๔ ม.๔ บ.ปูลากาซิง ต.กอลำ อ.ยะรัง จว.ป.น.\n\n",
   "note": "TL/SABOTAS\nแยลีมอ/ยะรัง\n\n",
   "picUrl": "1941000136434.jpg?alt=media&token=f728094c-0899-433d-a8b1-7da50a061cd1"
 },
 {
   "nationid": 1941100001832,
   "name": "นายมูฮำมัดอาดีลัน สาและ",
   "address": "๒๓/๑ ม.๑ บ.บาโง ต.กะรุบี\nอ.กะพ้อ จว.ป.น.\n",
   "note": "หน.REGU",
   "picUrl": "1941100001832.jpg?alt=media&token=e9d239e8-21d0-495c-873c-f5ede403324a"
 },
 {
   "nationid": 1941100006711,
   "name": "นายมะนัศรีสาแม/ยะยู",
   "address": "๙ ม.๒ บ.มะแนดาแล ต.กะรุบี\nอ.กะพ้อ จว.ป.น.\n",
   "note": "พื้นที่ อ.กะพ้อ",
   "picUrl": "1941100006711.jpg?alt=media&token=be215675-cbf6-45cf-94d4-8098ce0aa7d0"
 },
 {
   "nationid": 1949800001964,
   "name": "นายสมันดีสนิ",
   "address": "๖๓/๔ ม.๔ บ.มะนังยง ต.ปากู \nอ.ทุ่งยางแดง จว.ป.น. เดิมอยู่ ๓๗/๒ ถ.ตะพา ต.ตะลุบัน \nอ.สายบุรี จว.ป.น. \n\n",
   "note": "หน.KOMPI\n(ซถ.ขกท.สน.จชต.)\n",
   "picUrl": "1949800001964.jpg?alt=media&token=b4ab3775-6d5b-4276-bc22-756797193af0"
 },
 {
   "nationid": 1949900023927,
   "name": "นายเสรีแวมามุ/ดิง",
   "address": "๑๒๓ ม.๖ บ.คลองขุด ต.ปากบาง\nอ.เทพา จว.ส.ข.\n",
   "note": "หน.PLATONG\nอ.หนองจิก, \nต.ปะกาฮารัง อ.เมืองปัตตานี",
   "picUrl": "1949900023927.jpg?alt=media&token=3252a918-e917-4565-a7c3-14bd771fdef9"
 },
 {
   "nationid": 1950100003362,
   "name": "นายอับดุลฮาเล็ม บือราเฮง",
   "address": "๖๑ ม.๕ บ.บาตันตะโล๊ะ ต.ลิดล\nอ.เมือง จว.ย.ล.\n",
   "note": "หน.PLATONG",
   "picUrl": "1950100003362.png?alt=media&token=63a576f4-1e44-4152-9e48-7cfcca866729"
 },
 {
   "nationid": 1950100009778,
   "name": "นายมะรอยี มะแอ/ยี/ฟาละห์",
   "address": "๑๑๕/๔ ม.๔ บ.โต๊ะนิ ต.ยุโป\nอ.เมือง จว.ย.ล. \n",
   "note": "หน.PLATONG\n (นอกเขตเทศบาล/\n ในเขตเทศบาล)\n",
   "picUrl": "1950100009778.jpg?alt=media&token=bae5ee3c-607d-4a8e-9040-d52f100bbb57"
 },
 {
   "nationid": 1950100009964,
   "name": "นายสการียาอะแซ\n(โกเบ/มะยาอาแซ)\n",
   "address": "๘/๑ บ.กูแปอิเต๊ะ ม.๓ \nต.หน้าถ้ำ อ.เมือง จว.ย.ล.\n",
   "note": "หน.REGU",
   "picUrl": "1950100009964.png?alt=media&token=8eacd647-bbcc-4ce6-91e1-966478cfa08b"
 },
 {
   "nationid": 1950100101585,
   "name": "นายอายุทธอิตำ",
   "address": "๗๕/๓ บ.น้ำเย็น (บ.ทุ่งคา) \nม.๒ ต.ลำใหม่ อ.เมือง\nจว.ย.ล.\n",
   "note": "หน.REGU \nลำใหม่, ลิดล\n-ชำนาญประกอบระเบิด\n",
   "picUrl": "1950100101585.jpg?alt=media&token=3bfb219a-0d76-4498-82a6-d84b475940aa"
 },
 {
   "nationid": 1950100111441,
   "name": "นายมะซูรีสาเมาะ/จูเป็ง",
   "address": "๕๕ บ.ลำดา ม.๓ ต.ยุโป \nอ.เมือง จว.ย.ล.\n",
   "note": "หน.REGU \nยุโป, ตาเซะ, หน้าถ้ำ,\nท่าสาป, บ้านเนียง\n",
   "picUrl": "1950100111441.png?alt=media&token=d6b10cca-d3b2-49cc-ba57-74d3bdc2f6dd"
 },
 {
   "nationid": 1950100123589,
   "name": "นายต่วนเป็งตาหยง/วา",
   "address": "๖๒/๑ บ.ยะลา ม.๑ ต.ยะลา\nอ.เมือง จว.ย.ล.\n",
   "note": "TL/SABOTAS ยะหา",
   "picUrl": "1950100123589.jpg?alt=media&token=06e0b64a-79f9-4728-9aaa-fc9d0187cefd"
 },
 {
   "nationid": 1950100123864,
   "name": "นายมาหาดีมะลี/มะเย๊ะ",
   "address": "๖๒ บ.เปาะเส้ง ม.๑ ต.เปาะเส้ง\nอ.เมือง จว.ย.ล. เดิมอยู่ ๔๗ บ.เปาะเส้ง ม.๑ ต.เปาะเส้ง\nอ.เมือง จว.ย.ล.",
   "note": "คุมตัว ๗ ก.ค.๕๗",
   "picUrl": "1950100123864.jpg?alt=media&token=b34a7dc9-6705-472e-a31e-94f57b6c60bb"
 },
 {
   "nationid": 1950300006730,
   "name": "นายนอร์ดินหะยีอาซา",
   "address": "๒๒๘/๒ ม.๔ ต.ตาเนาะปูเต๊ะ \nอ.บันนังสตา จว.ย.ล.\n",
   "note": "หน.KOMPI\nพงยือไร-กาสัง\n-TL/SABOTAS KAM ๑\n-ชำนาญประกอบระเบิด\n",
   "picUrl": "1950300006730.jpg?alt=media&token=f03f66e3-9948-4d9a-bf24-02b2482cda0b"
 },
 {
   "nationid": 1950400003333,
   "name": "นายอิสมาแอปูลา",
   "address": "๓๓ ม.๖ ต.บ้านแหร อ.ธารโต\nจว.ย.ล.\n",
   "note": "ฝ่าย PHA",
   "picUrl": "1950400003333.png?alt=media&token=700e51b8-5559-43a7-8085-721a9d512eeb"
 },
 {
   "nationid": 1950400006065,
   "name": "นายมะซอตีเจ๊ะโซะ/อูมา",
   "address": "๒๙/๕ ม.๒ ต.บ้านแหร\nอ.ธารโต จว.ย.ล.\n",
   "note": "ฝ่าย LOGISTIK\n-หน.REGU\n(SNIPER)\n",
   "picUrl": "1950400006065.png?alt=media&token=1e4517a6-e51c-4b24-8dd6-3d1ce67f1dc1"
 },
 {
   "nationid": 1950500071543,
   "name": "นายรอฟีกีบินดือเร๊ะ",
   "address": "๓๖/๑ บ.คลองน้ำใส ม.๒ \nต.บาละ อ.กาบัง จว.ย.ล.\n",
   "note": "TL/SABOTAS\nPLATONG ยะรัง\n\n",
   "picUrl": "1950500071543.jpg?alt=media&token=ed6a0b02-a973-4c56-a1e8-a30d359be558"
 },
 {
   "nationid": 1950600043258,
   "name": "นายซอพวันสะหมะ/วาดุ๊",
   "address": "๑๒๑/๓ ม.๑ บ.บีดิง ต.เนินงาม อ.รามัน จว.ย.ล.",
   "note": "หน.PLATONG\n\n",
   "picUrl": "1950600043258.png?alt=media&token=731a3496-879c-4e51-9d3e-1625922fbb81"
 },
 {
   "nationid": 1950600082768,
   "name": "นายหมัดมุลกี กาโฮง/คอเวะ",
   "address": "๑๖/๒ บ.จือแร ม.๔ \nต.กอตอตือร๊ะ อ.รามัน จว.ย.ล.\n",
   "note": "หน.REGU\n(ตรวจสอบพื้นที่)\n",
   "picUrl": "1950600082768.png?alt=media&token=4380736d-8891-45b1-98be-0e165ea74068"
 },
 {
   "nationid": 19599000458229,
   "name": "นายฟิตรีนาแว/กือเดะ",
   "address": "๒๐ บ.ปากู ม.๕ ต.ปากู \nอ.ทุ่งยางแดง จว.ป.น.\n",
   "note": "TL/SABOTAS ปาลัส",
   "picUrl": "1959900049229.jpg?alt=media&token=6648443b-4f26-4264-b5c8-e7caa7d84574"
 },
 {
   "nationid": 1959900090241,
   "name": "นายซัมรีตาเยะ",
   "address": "๔ ม.๔ บ.คลองช้าง ต.นาเกตุ \nอ.โคกโพธิ์ จว.ป.น.",
   "note": "คุมตัว ๗ มิ.ย.๕๗",
   "picUrl": "1959900090241.jpg?alt=media&token=474655a7-1fa6-4e0a-8b52-7e2ac07187a1"
 },
 {
   "nationid": 1960100084711,
   "name": "นายเตาฟิกโต๊ะเล๊าะ/ตอเพะ",
   "address": "๒/๑ บ.จือนา ม.๖ ต.บางปอ \nอ.เมือง จว.น.ธ.\n",
   "note": "หน.PLATONG\nต.กาลิซา, ต.บองอ\n-ฝ่าย TERITORI\n-ฝ่าย PERSONALIA\n",
   "picUrl": "1960100084711.jpg?alt=media&token=aa85639e-30cf-4813-92e4-2fff5e3a1334"
 },
 {
   "nationid": 1960200040171,
   "name": "มูหามัดฟาร์มีมูดอ/ฮาเซ็ม\n(ชื่อเดิม นายอิอายีม มูดอ)\n",
   "address": "๙๓ ม.๗ บ.จาแบปะ \nต.เกาะสะท้อน อ.ตากใบ จว.น.ธ.\n",
   "note": "ฝ่าย LOGISTIK\n\n",
   "picUrl": "1960200040171.jpg?alt=media&token=576e87dd-b7f1-4dd4-a6bd-f046529ff881"
 },
 {
   "nationid": 1960200050249,
   "name": "นายสุกิปลีมะสะ",
   "address": "๒๙ ม.๖ ต.เจ๊ะเห อ.ตากใบ จว.น.ธ.\n\n",
   "note": "TL/SABOTAS\n\n",
   "picUrl": "1960200050249.jpg?alt=media&token=f13d4d64-a572-4c99-acb3-ad17ff50f83d"
 },
 {
   "nationid": 1960300031319,
   "name": "นายบารูวัน กือจิ",
   "address": "๑๘๐/๒ ม.๗ บ.ดูกูสุเหร่า\nต./อ.บาเจาะ จว.น.ธ.\n",
   "note": "ฉก.นย.ทร.\n-หลบหนีประกัน\n",
   "picUrl": "1960300031319.jpg?alt=media&token=2abdcffd-1e00-4e41-a9df-299bd23121c9"
 },
 {
   "nationid": 1960400032007,
   "name": "นายคอเละเซ็ง",
   "address": "๑๐๒/๒ ม.๒ บ.บูเกะบากง \nต.ตะปอเยาะ อ.ยี่งอ จว.น.ธ.\n",
   "note": "หน.PLATONG",
   "picUrl": "1960400032007.jpg?alt=media&token=4e3adb4d-a176-4aa7-84e1-05d37734d9fe"
 },
 {
   "nationid": 1960500003700,
   "name": "นายอุสนีบาโด",
   "address": "๖๓ ม.๔ บ.บองอ ต.บองอ\nอ.ระแงะ จว.น.ธ.\n",
   "note": "หน.KOMPI\nเขตระแงะ (โซนภูเขา)\n",
   "picUrl": "1960500003700.jpg?alt=media&token=479c35cc-f47d-49a5-85de-a5f5322a9fb3"
 },
 {
   "nationid": 1960500026823,
   "name": "นายซาฮารี ปลูแว/ยี/เปาะยี",
   "address": "๓๔ บ.บละแต ม.๔ ต.บาโงสะโต\nอ.ระแงะ จว.น.ธ.\n",
   "note": "ฝ่าย LOGISTIK",
   "picUrl": "1960500026823.jpg?alt=media&token=fed2f0f4-19d7-45e4-9c17-657cfa08b5a8"
 },
 {
   "nationid": 1960500037914,
   "name": "นายอาดีมะมะแก/เด๊ะมะ",
   "address": "๑๑๔ บ.ฮูลู ม.๓ ต.กาลิซา\nอ.ระแงะ จว.น.ธ.\n",
   "note": "หน.REGU",
   "picUrl": "1960500037914.jpg?alt=media&token=ae2fbb4f-d7d5-42f5-9fb2-88c545fcd04c"
 },
 {
   "nationid": 1960500092184,
   "name": "นายอาหะมะสาอุ",
   "address": "๕๔ บ.ปูโง๊ะ ม.๑ ต.กาลิซา\nอ.ระแงะ จว.น.ธ.\n",
   "note": "คุมตัว ๒ พ.ค.๖๐",
   "picUrl": "1960500092184.jpg?alt=media&token=51763bc6-d712-4371-a89f-e8c02a73538b"
 },
 {
   "nationid": 1960500104433,
   "name": "นายซุลกิฟลีดาบู/แจปรี",
   "address": "๑๑๐ ม.๘ ต.บ้านกลาง \nอ.ปะนาเระ จว.ป.น. เดิมอยู่ ๖๑ ม.๑ บ.ไอร์กามาลาต.ช้างเผือก อ.จะแนะ จว.น.ธ.",
   "note": "นายฮาซันมะลี\nนายซอบรี สือแม \nคุมตัว ๓ พ.ย.๕๘\nยอมรับให้ข้อมูล",
   "picUrl": "1960500104433.jpg?alt=media&token=206d6146-2b4e-4baf-99b0-f6efb46c9b24"
 },
 {
   "nationid": 1960500112991,
   "name": "นายสือดีปูเตะ",
   "address": "๑๓๖ ม.๒ บ.บาโงแยะ \nต.ตันหยงลิมอ อ.ระแงะ จว.น.ธ.\n",
   "note": "รองหน.REGU",
   "picUrl": "1960500112991.jpg?alt=media&token=380b7e28-454e-4fac-97f7-d9f101a74836"
 },
 {
   "nationid": 1960500142050,
   "name": "นายอาแวสามะ/เด๊ะแว",
   "address": "๒๗๘/๑ ม.๑ ต.ตันหยงมัสอ.ระแงะ จว.น.ธ.",
   "note": "ฝ่าย PERSONALIA\n (ฝ่ายจัดการฝึก)\n",
   "picUrl": "1960500142050.jpg?alt=media&token=d930dcf3-eaab-4ea8-8b7b-7fdf4098b292"
 },
 {
   "nationid": 1960500146675,
   "name": "นายมาฮาดีลง/แมเราะ ",
   "address": "๑๒๖ ม.๓ ต.กาลิซา \nอ.ระแงะ จว.น.ธ.\n",
   "note": "ฝ่าย LOGISTIK",
   "picUrl": "1960500146675.jpg?alt=media&token=01073d81-2276-4a95-95f5-0825e9fe4584"
 },
 {
   "nationid": 1960500153116,
   "name": "นายอารือมันมามะ/เปาะยอ",
   "address": "๒๖๙/๔ บ.ฮูลูปาเระ ม.๑ \nต.ตันหยงมัส อ.ระแงะ จว.น.ธ.\n",
   "note": "ฝ่าย TERITORI",
   "picUrl": "1960500153116.png?alt=media&token=fdc69e00-fe74-410f-a311-27833401f68f"
 },
 {
   "nationid": 1960500165823,
   "name": "นายวีรศักดิ์ ลาบอ/ยะลา",
   "address": "๖๘/๑ บ.กูจิงรือปะ ม.๔ ต.เฉลิม อ.ระแงะ จว.น.ธ.",
   "note": "หน.REGU\nTL/SABOTAS\nระแงะ (โซนภูเขา\n",
   "picUrl": "1960500165823.jpg?alt=media&token=d4f6bd42-7425-42e1-9a6d-92512a34ca59"
 },
 {
   "nationid": 1960500185964,
   "name": "นายอับดุลเลาะเปาะมา",
   "address": "๒๔๖ ม.๑ ต.ตันหยงมัส อ.ระแงะ จว.น.ธ.",
   "note": "ฝ่าย PHA \n(ฮารีเมา)\n",
   "picUrl": "1960500185964.png?alt=media&token=a14aefb9-9ce0-4249-8dea-c53c5f5f9693"
 },
 {
   "nationid": 1960500201242,
   "name": "นายฮาพิเจ๊ะหะ ",
   "address": "๘๗/๑ ม.๔ ต.เฉลิม \nอ.ระแงะ จว.น.ธ.\n",
   "note": "ฝ่าย PHA \n(ฮารีเมา)\n",
   "picUrl": "1960500201242.jpg?alt=media&token=aae96ac8-faae-48ed-b630-16dbc92737ee"
 },
 {
   "nationid": 1960500237859,
   "name": "นายอัซรันอาแวบือซา",
   "address": "๖ ม.๕ ต.บาโงสะโต อ.ระแงะ จว.น.ธ.",
   "note": "หน.REGU",
   "picUrl": "1960500237859.jpg?alt=media&token=feae0f24-ad05-4861-977a-2457b3908600"
 },
 {
   "nationid": 1960600003159,
   "name": "นายอับดุลรอนิงสาและ",
   "address": "๓๘ ม.๓ ต.บาตง อ.รือเสาะ จว.น.ธ. เดิมอยู่ ๕๐/๒ บ.แยะ \nม.๒ ต.อาซ่อง อ.รามัน จว.ย.ล.\n",
   "note": "หน.KOMPI \nรามัน/กาบู\n",
   "picUrl": "1960600003159.png?alt=media&token=3d6f7c13-c30a-46bb-8dcc-c9a212a53e62"
 },
 {
   "nationid": 1960600018563,
   "name": "นายอาแดร์เจ๊ะมุ/อาดัม",
   "address": "๒๓ ม.๖ ต.สุวารี อ.รือเสาะ จว.น.ธ.",
   "note": "รอง ผบ.เขตทหารที่ ๒\n-หน.ฝ่ายทหาร\n(ส่วนปฏิบัติการ)\n-หน.ฝ่าย LOGISTIK\n",
   "picUrl": "1960600018563.png?alt=media&token=9da77f3b-64f7-43ee-8f34-7a2f23aef976"
 },
 {
   "nationid": 1960600058221,
   "name": "นายอาหะมะ เจ๊ะมุ/มะ",
   "address": "๒๓ ม.๖ บ.ปอเนาะ ต.สุวารี อ.รือเสาะ จว.น.ธ. (บ้านภรรยา ๑๔๕ ม.๒ บ.น้ำบ่อ อ.ปานาเระ จว.ป.น.) ",
   "note": "คุมตัว ๒๓ พ.ย.๕๗\n-คุมตัว ๔ ต.ค.๖๑\n-ไม่นับยอด \n-ปรับยอดห้วงต่อไป\n",
   "picUrl": "1960600058221.jpg?alt=media&token=03823340-db08-4063-857f-f65dcc73d654"
 },
 {
   "nationid": 1960600063542,
   "name": "นายอัสรีย์โต๊ะเย๊ะ",
   "address": "๕ บ.นาดา ม.๓ ต.รือเสาะ\nอ.รือเสาะ จว.น.ธ.",
   "note": "คุมตัว ๑๙ เม.ย.๖๐",
   "picUrl": "1960600063542.jpg?alt=media&token=eaa77105-33a2-414e-b8eb-eeb8afa40329"
 },
 {
   "nationid": 1961200031962,
   "name": "นายอาลียะห์ปูโย๊ะ/ยะห์",
   "address": "๑๒๓ ม.๗ บ.ตือกอ ต.จะแนะ อ.จะแนะ จว.น.ธ.",
   "note": "นายแวอุมามามุ\nนายอาซีซันมะดาแฮ",
   "picUrl": "1961200031962.jpg?alt=media&token=e4be6a1e-1ddb-4ae8-896b-bde6f4f2e000"
 },
 {
   "nationid": 1961200061853,
   "name": "นายมูฮัมหมัดซากีรีน สาแม",
   "address": "๕๒ ม.๕ บ.กาแย ต.ดุงซงญอ\nอ.จะแนะ จว.น.ธ.\n",
   "note": "หน.PLATONG\nต.จะแนะ \n",
   "picUrl": "1961200061853.jpg?alt=media&token=ae03fc84-8746-4bf9-8e7c-17215fd8fbc7"
 },
 {
   "nationid": 1969900029968,
   "name": "นายนิรอมลีนิสุหลง",
   "address": "๑๕ ม.๕ บ.โคกกูแว ต.โฆษิต\nอ.ตากใบ จว.น.ธ.\n",
   "note": "หน.KOMPI \nเขตตาบา (โซนทะเล)\n",
   "picUrl": "1969900029968.jpg?alt=media&token=c40adf81-065c-40ed-b930-e69bc2c728f0"
 },
 {
   "nationid": 2940200018033,
   "name": "นายอาดังซาแม ",
   "address": "๗๖/๘ ม.๔ บ.บ่อหว้า\nต.ปากล่อ อ.โคกโพธิ์ จว.ป.น.",
   "note": "คุมตัว ๑๒ ธ.ค.๕๘",
   "picUrl": "2940200018033.jpg?alt=media&token=b95975dd-858f-42c8-9511-8632040709d7"
 },
 {
   "nationid": 2940300010808,
   "name": "นายมะซอรีอามะ",
   "address": "๙๑ ม.๔ ต.บางเขา อ.หนองจิก\nจว.ป.น.",
   "note": "คุมตัว ๑๓ ต.ค.๖๐",
   "picUrl": "2940300010808.jpg?alt=media&token=4b09b09d-d824-458e-bebd-bab79f745152"
 },
 {
   "nationid": 2940500017237,
   "name": "นายอาหามะ สามอ/มือรัง",
   "address": "๒๑/๖ ม.๓ ต.ลางา อ.มายอ จว.ป.น. เดิมอยู่ ๘๓ ม.๒ ต.ปานัน \nอ.มายอ จว.ป.น. \n",
   "note": "คุมตัว ๒๘ มี.ค.๕๗",
   "picUrl": "2940500017237.jpg?alt=media&token=c6acea3c-5493-4e6c-905b-020c4d6ef677"
 },
 {
   "nationid": 2940600008540,
   "name": "นายมะนาเซยูโซะ",
   "address": "๑๒๗ บ.บาลูกาลูวะ ม.๑ \nต.น้ำดำ อ.ทุ่งยางแดง\nจว.ป.น.",
   "note": "นายมารีดัน มูซอ\nนายหามะสาเมาะ\nนายอัซมัน กาซา",
   "picUrl": "2940600008540.jpg?alt=media&token=9e7a36d4-6544-497d-a835-b4e024dafd48"
 },
 {
   "nationid": 2950500009706,
   "name": "นายนัซดานยะลาแป/ดัน",
   "address": "๙๔/๕ ม.๔ บ.สาคอ ต.ท่าสาป\nอ.เมือง จว.ย.ล.\n",
   "note": "หน.PLATONG",
   "picUrl": "2950500009706.jpg?alt=media&token=516250f5-d81b-44b3-87bb-7cba79fc10f8"
 },
 {
   "nationid": 2960300011111,
   "name": "นายมะสะกรีอูแล",
   "address": "๓๓๘ ม.๗ บ.ดูกูสุเหร่า \nต.บาเจาะ อ.บาเจาะ จว.น.ธ.\n",
   "note": "TL/SABOTAS\nบาเจาะ\n",
   "picUrl": "2960300011111.jpg?alt=media&token=9670913d-2c39-4b81-bd4f-c53f9bbb7460"
 },
 {
   "nationid": 2960300011120,
   "name": "นายอับดุลเลาะอูแล/เตาเพ็ค",
   "address": "๓๓๘ ม.๔ บ.สะแต ต.บาเจาะ อ.บาเจาะ จว.น.ธ.",
   "note": "TL/SABOTAS\nบาเจาะ\n",
   "picUrl": "2960300011120.jpg?alt=media&token=0561e1be-0003-4a59-adbd-be3d23405efc"
 },
 {
   "nationid": 2960400008139,
   "name": "นายรอวี หะยีดิง/อาซัน",
   "address": "๖๑ บ.บาโงอาแซ ม.๖ต.ตันหยงมัส อ.ระแงะ จว.น.ธ. เดิมอยู่ ๕๙/๒ บ.บาโงอาแซม.๖ ต.ตันหยงมัส อ.ระแงะ ฯ ",
   "note": "TL/SABOTAS\nระแงะ (โซนทะเล)\nTL/SABOTAS\nระแงะ (โซนภูเขา)\n",
   "picUrl": "2960400008139.jpg?alt=media&token=5e1644c6-0873-472e-a69b-a33c4bbef41b"
 },
 {
   "nationid": 2960500007096,
   "name": "นายอูเซ็งมามะ",
   "address": "๓๑/๑ ม.๑๔ ต.บูกิต \nอ.เจาะไอร้อง จว.น.ธ.\n",
   "note": "ฝ่าย PHA \n(ฮารีเมา\n",
   "picUrl": "2960500007096.jpg?alt=media&token=50583306-ebd0-4a0b-91db-ecac7ea65089"
 },
 {
   "nationid": 2960500015561,
   "name": "นายไฟมี เจ๊ะดือเระ/ไฟมี",
   "address": "๑๙/๑ บ้านกือรง ม.๓ ต.จวบ อ.เจาะไอร้อง จว.น.ธ. เดิมอยู่ ๑๙ บ้านกือรง ม.๓ ต.จวบอ.เจาะไอร้อง จว.น.ธ.",
   "note": "TL/SABOTAS\n\n",
   "picUrl": "2960500015561.jpg?alt=media&token=ef2f7a16-9e11-45f3-9498-e5d9341c4850"
 },
 {
   "nationid": 2960500019388,
   "name": "นายสาบีลามะเด็ง",
   "address": "๑๒๙/๔ บ.ลาแป ม.๒ \nต.บองอ อ.ระแงะ จว.น.ธ.\n\n",
   "note": "TL/SABOTAS\nระแงะ (โซนภูเขา)\n",
   "picUrl": "2960500019388.jpg?alt=media&token=12106453-0639-40b4-b145-3cccec1a1b01"
 },
 {
   "nationid": 2960500021617,
   "name": "นายอายิยามา/โต๊ะแว/ยิ",
   "address": "๑๗ ม.๓ บ.สุแฆ ต.ดุงซงญอ\nอ.จะแนะ จว.น.ธ.\n",
   "note": "หน.PLATONG\nต.ดุซงญอ\n",
   "picUrl": "2960500021617.jpg?alt=media&token=44affa45-fae1-4e5d-8d6e-76333c8646cb"
 },
 {
   "nationid": 2960500035928,
   "name": "มะตอเพะมาลายอ/ซูโก๊ะ",
   "address": "๑๒๘/๑ ม.๔ ต.เฉลิม \nอ.ระแงะ จว.น.ธ.\n",
   "note": "หน.REGU",
   "picUrl": "2960500035928.jpg?alt=media&token=f908041f-38a4-4192-8f46-4c616b9a8a80"
 },
 {
   "nationid": 2960600016551,
   "name": "นายอัมดันแมเร๊าะ/แบดัน",
   "address": "๔๑ ม.๓ บ.นาดา ต./อ.รือเสาะ\nจว.น.ธ.\n",
   "note": "หน.KOMPI\n เขตรือเสาะ/ยะบะ\n -หน.PLATONG\n ต.รือเสาะ (ฝั่งตะวันตก\n",
   "picUrl": "2960600016551.jpg?alt=media&token=1e227137-a58c-40f7-8b0b-26ca3d60141c"
 },
 {
   "nationid": 2961100010392,
   "name": "นายอาดือนันอาแว",
   "address": "๙๖/๑ ม.๗ ต.ปะลุรู\nอ.สุไหงปาดี จว.น.ธ.\n",
   "note": "TL/SABOTAS\n\n",
   "picUrl": "2961100010392.jpg?alt=media&token=7e48f623-4bd2-40b5-b2e1-e260e08296c9"
 },
 {
   "nationid": 2961100014819,
   "name": "นายราฟีมามะรอยาลี",
   "address": "๔ บ.บาลูกา ม.๔ ต.ริโก๋ \nอ.สุไหงปาดี จว.น.ธ.\n",
   "note": "หน.REGU",
   "picUrl": "2961100014819.png?alt=media&token=de243f11-69df-4159-99f6-f3fbb3b91e3d"
 },
 {
   "nationid": 2961200006664,
   "name": "นายอาสือรีเจ๊ะตู/จะมะ",
   "address": "๗๓ ม.๒ บ.กูมุง ต.ช้างเผือก \nอ.จะแนะ จว.น.ธ.\n",
   "note": "หน.PLATONG",
   "picUrl": "2961200006664.jpg?alt=media&token=f659760f-414b-4ceb-bbe4-046947c50f93"
 },
 {
   "nationid": 2961200010696,
   "name": "นายรุสดีเจ๊ะหลง",
   "address": "๔๑/๑ ม.๒ บ.บือจะ \nต.ผดุงมาตร อ.จะแนะ\nจว.น.ธ.",
   "note": "TL/SABOTAS\nจะแนะ\n",
   "picUrl": "2961200010696.jpg?alt=media&token=50dc7785-dce9-481b-a5fc-f9d7d3f37798"
 },
 {
   "nationid": 2961200010823,
   "name": "นายพามิงเจ๊ะแต",
   "address": "๑๑๕ ม.๗ บ.ตือกอ \nต./อ.จะแนะ จว.น.ธ.\n",
   "note": "หน.PLATONG\nต.ช้างเผือก\n",
   "picUrl": "2961200010823.jpg?alt=media&token=2defd53b-1721-4283-8a4f-4bf7d559dc0a"
 },
 {
   "nationid": 3900300487696,
   "name": "นายรุสลันใบมะ",
   "address": "๕๒ ม.๕ บ.ป่ากอ ต./อ.เทพา จว.ส.ข. ย้ายไปอยู่ ต.บ้านนาอ.จะนะ จว.ส.ข.",
   "note": "หน.REGU PLATONG\nอ.หนองจิก, ต.ปะกา-ฮารัง อ.เมืองปัตตานี",
   "picUrl": "3900300487696.jpg?alt=media&token=d10e9f2d-a00d-4f42-948a-b00b1fc680a1"
 },
 {
   "nationid": 3900300599745,
   "name": "นายสูรีฮันอาแว",
   "address": "๔๐/๓ ม.๗ บ.ลางา ต.บ้านนา \nอ.จะนะ จว.ส.ข. มีภรรยา \nบ.บ่อหิน ต.บ้านแหร อ.ธารโต\nจว.ย.ล.\n",
   "note": "หน.REGU อัยเยอร์เวง ๒",
   "picUrl": "3900300599745.jpg?alt=media&token=7ed9e12c-7789-4df5-9d98-2657d62e6f9b"
 },
 {
   "nationid": 3900300600182,
   "name": "นายอามีนูดีนกะจิ\n(นายชวลิตรุ่งโรจน์จิต)",
   "address": "๔๘ ม.๗ บ.ลางา ต.บ้านนาอ.จะนะ จว.ส.ข.",
   "note": "หน.REGU",
   "picUrl": "3900300600182.jpg?alt=media&token=887e940d-e068-40db-8795-2c23f55f709f"
 },
 {
   "nationid": 3900500097107,
   "name": "นายจำเริญ อูมาสะ/\nปะดอ คลองขุด",
   "address": "๗๗ ม.๑ บ.บ่อเตย ต.ปากบาง อ.เทพา จว.ส.ข.",
   "note": "อ.เทพา",
   "picUrl": "3900500097107.jpg?alt=media&token=e1a19b23-db4f-42f3-a8ef-2b147c1c974f"
 },
 {
   "nationid": 3900500101261,
   "name": "นายมะรอพีบาเห็ม/รอฟัค",
   "address": "๖/๒ บ.ปาแน ม.๔ ต.ศรีสาคร\nอ.ศรีสาคร จว.น.ธ.",
   "note": "คุมตัว ๖ เม.ย.๕๔",
   "picUrl": "3900500101261.jpg?alt=media&token=73053cd2-15b0-4c3d-bf1a-a20ac08e3fa4"
 },
 {
   "nationid": 3900600020211,
   "name": "นายไซนูรอาบาเซร์ สุหลงเส็น",
   "address": "๖๗ ม.๓ บ.ลำยะ ต.บาโหย \nอ.สะบ้าย้อย จว.ส.ข.",
   "note": "(ข้อมูลเดิม)",
   "picUrl": "3900600020211.jpg?alt=media&token=d0633c1b-943f-4f1b-8072-bc510c87eff7"
 },
 {
   "nationid": 3900600331730,
   "name": "นายอุสมานเจ๊ะอุมง/มัง",
   "address": "๘๓ ม.๓ ต.ลิปะสะโง \nอ.หนองจิก จว.ป.น. (เดิมอยู่ ๒๓/๑ ม.๔\nต.จะแหน อ.สะบ้าย้อย จว.ส.ข.)\n",
   "note": "อดีต ผบ.ทหารเขตที่ ๑\n- เคยถูกจับปี ๕๑ (ตามหมาย พรก.) ปล่อยตัว/หลบหนี\n- (ถูก จนท.มซ. จับกุม ๑๕ ",
   "picUrl": "3900600331730.jpg?alt=media&token=e8d8e996-7bf3-4590-81fb-d61cf76b6daa"
 },
 {
   "nationid": 3940100174422,
   "name": "นายมะซอเรดือรามะ",
   "address": "๓/๓ ม.๖ บ.กอแลบิและ \nต.ปะกาฮารัง อ.เมือง จว.ป.น.\n",
   "note": "TL/SABOTAS\nกือดา/เมือง\n-ชำนาญประกอบระเบิด\n\n",
   "picUrl": "3940100174422.jpg?alt=media&token=a3107faf-793c-4f78-9151-8cce85380b6b"
 },
 {
   "nationid": 3940100205794,
   "name": "นายแวอาแซ แวอาลี/มะจัง",
   "address": "๓๐ ม.๔ ต.บาราเฮาะ \nอ.เมือง จว.ป.น.\n",
   "note": "คุมตัว ๑ มี.ค.๖๐",
   "picUrl": "3940100205794.jpg?alt=media&token=518c9fe7-4969-40a6-b68f-931df212d980"
 },
 {
   "nationid": 3940100334870,
   "name": "นายแวมะยูกีสือเมาะ/มัง",
   "address": "๔๕/๑๑ บ.บานา ม.๒ ต.บานา\nอ.เมือง จว.ป.น.\n",
   "note": "TL/SABOTAS\nธารโต/เบตง\n",
   "picUrl": "3940100334870.jpg?alt=media&token=5c7ab7bf-3c06-4ac5-9cfd-785097ce184c"
 },
 {
   "nationid": 3940100402743,
   "name": "นายบือราเฮง มามะ/อิบรอเฮม",
   "address": "๒๗/๓ ม.๕ บ.สะนิง ต.บาราเฮาะ อ.เมือง จว.ป.น.",
   "note": "หน.REGU",
   "picUrl": "3940100402743.jpg?alt=media&token=778cd8fd-c859-4488-8374-6c52ff85e2c0"
 },
 {
   "nationid": 3940100408563,
   "name": "นายมูฮำมัดกาซอ",
   "address": "๗๙/๔ ม.๕ บ.สะนิง \nต.บาราเฮาะ อ.เมือง จว.ป.น.\n",
   "note": "TL/SABOTAS\nกือดา/เมือง\n-ชำนาญประกอบระเบิด\n\n\n",
   "picUrl": "3940100408563.jpg?alt=media&token=f273a651-93cc-4d2f-ba3a-8a8c4cfd4979"
 },
 {
   "nationid": 3940200023970,
   "name": "นายไซฟูดดิน หะยีปูเต๊ะ/ดิง ",
   "address": "๖๐/๒ ม.๑ ต.ป่าบอน \nอ.โคกโพธิ์ จว.ป.น.",
   "note": "ชุด TL KOMPI \nโคกโพธิ์ (เฉพาะ ต.บางโกระ, ต.โคกโพธิ์)",
   "picUrl": "3940200023970.jpg?alt=media&token=f4b84984-00b2-4885-ae4c-9101a5bdc4aa"
 },
 {
   "nationid": 3940200194026,
   "name": "นายอดุลย์สาและ/\nมะซังสามยอด/อาบู\n",
   "address": "๑๔๒/๓ บ.บ่อหินตก ม.๕\nต.บ้านแหร อ.ธารโต จว.ย.ล. เดิมอยู่ ๓๔/๑ บ.พรุ ม.๑๒ ต.โคกโพธิ์\nจว.ป.น.\n",
   "note": "หน.REGU",
   "picUrl": "3940200194026.png?alt=media&token=165d30d9-c952-41d7-aecc-43cc1bcca029"
 },
 {
   "nationid": 3940200241423,
   "name": "นายวินัยกาแบ/เปาะฮะห์",
   "address": "๘๐/๓ ม.๗ ต.แม่ลาน อ.แม่ลาน จว.ป.น. ",
   "note": "หน.REGU",
   "picUrl": "3940200241423.jpg?alt=media&token=03caf243-6779-4d9f-a993-b87ba21160fa"
 },
 {
   "nationid": 3940200254436,
   "name": "นายมะดอเฮมูน๊ะ",
   "address": "๓๙/๒ บ.คูระ ม.๒ ต.ม่วงเตี้ย\nอ.แม่ลาน จว.ป.น.\n",
   "note": "ฝ่าย PERSONALIA",
   "picUrl": "3940200254436.jpg?alt=media&token=7219e38b-49c5-432f-9a4b-90e6e35429fc"
 },
 {
   "nationid": 3940200321371,
   "name": "นายสะกือรีลาเต๊ะ/กือรี",
   "address": "๑๓ บ.สลาม ม.๘ ต.นาประดู่\nอ.โคกโพธิ์ จว.ป.น.",
   "note": "คุมตัว",
   "picUrl": "3940200321371.jpg?alt=media&token=1a8ed953-f84a-43fa-8ab7-834baaedf101"
 },
 {
   "nationid": 3940200357006,
   "name": "นายฮาวารีมะเด็ง",
   "address": "๑๒๔/๓ บ.คลองช้างออก ม.๗",
   "note": "นายฟาเดล เสาะหมาน",
   "picUrl": "3940200357006.jpg?alt=media&token=d02d814c-fa76-45af-9686-c6464ecc95fa"
 },
 {
   "nationid": 3940200387878,
   "name": "นายอันวาร์สาลำ",
   "address": "๖๔/๑ ม.๕ บ.นาค้อเหนือ\nต.ป่าบอน อ.โคกโพธิ์ จว.ป.น.",
   "note": "หน.REGU",
   "picUrl": "3940200387878.jpg?alt=media&token=1ddbff23-77ee-402f-a5da-53da6d05c636"
 },
 {
   "nationid": 3940200464961,
   "name": "นายรุสลันโต๊ะมีลา/ซูยิ",
   "address": "๓๕๗ ม.๖ บ.นาประดู่ \nต.นาประดู่ อ.โคกโพธิ์ จว.ป.น.",
   "note": "ซถ.นายมะราวี สารี",
   "picUrl": "3940200464961.jpg?alt=media&token=94b4376a-7004-40f8-a476-c6f26c9535b8"
 },
 {
   "nationid": 3940200516511,
   "name": "นายแวฮาสันมูซอ ",
   "address": "๒๒/๖ บ.เกาะตา ม.๓ \nต.ทุ่งพลา อ.โคกโพธิ์ จว.ป.น.",
   "note": "หน.REGU PLATONG\nต.นาประดู่, ต.ทุ่งพลา,ต.ควนโนรี, ต.ปากล่อ",
   "picUrl": "3940200516511.jpg?alt=media&token=6549a137-1b20-4a8f-947a-1428b1280750"
 },
 {
   "nationid": 3940200648469,
   "name": "นายฮาลีเพ็งยูโซะ",
   "address": "๑๗๙/๑ ม.๕ บ.ตูปะ\nต.ควนโนรี อ.โคกโพธิ์ จว.ป.น.",
   "note": "ฝ่าย EKKONOMI",
   "picUrl": "3940200648469.jpg?alt=media&token=0ddf6a18-92d6-4522-8182-b25565de7eee"
 },
 {
   "nationid": 3940300031609,
   "name": "นายอับดุลเลาะมะมิง",
   "address": "๑๘๔/๒ ม.๕ ต.คลองใหม่ \nอ.ยะรัง จว.ป.น.\n",
   "note": "TL/SABOTAS\nแยลีมอ/ยะรัง\n-ชำนาญประกอบระเบิด\n",
   "picUrl": "3940300031609.jpg?alt=media&token=b3208dd4-8f5c-439e-aaa5-24cbe8fbcea9"
 },
 {
   "nationid": 3940300110037,
   "name": "นายอับดุลเราะมันดามะ",
   "address": "๕ ม.๖ บ.ปะการือสง ต.ตุยง\nอ.หนองจิก จว.ป.น.\n",
   "note": "ไม่อยู่ในพื้นที่\n(ฉก.ปัตตานี)\n",
   "picUrl": "3940300110037.jpg?alt=media&token=2850417d-f233-497b-929b-a754b58f1ae3"
 },
 {
   "nationid": 3940300117295,
   "name": "นายซาการียาสะมะแอ",
   "address": "๑๑๑ ม.๖ บ.ปะการือสง\nต.ตุยง อ.หนองจิก จว.ป.น.\n",
   "note": "ไม่อยู่ในพื้นที่\n(ฉก.ปัตตานี)\n",
   "picUrl": "3940300117295.jpg?alt=media&token=d4e7a54b-aa37-4174-8697-bb7b7cb89c73"
 },
 {
   "nationid": 3940300184260,
   "name": "นายมะรุดิงสามะ",
   "address": "๒๔/๑ ม.๕ บ.ปาแดลางา \nต.บ่อทอง อ.หนองจิก\nจว.ป.น.\n",
   "note": "ชำนาญประกอบระเบิด",
   "picUrl": "3940300184260.jpg?alt=media&token=9a7f1e29-3b87-4291-8979-5120dde5818f"
 },
 {
   "nationid": 3940300260225,
   "name": "นายมะกอรีสะอะ/เจ๊ะฆูยี",
   "address": "๕๐ ม.๔ ต.ลิปะสะโง \nอ.หนองจิก จว.ป.น.",
   "note": "ฝ่าย TERITORI \n(จัดหาที่พัก)",
   "picUrl": "3940300260225.jpg?alt=media&token=85dabe59-ccb8-407c-9559-ae4b887c26fb"
 },
 {
   "nationid": 3940300292844,
   "name": "นายอัลฟาเดลมุสตากีม\n(มุสดีมะลี)\n",
   "address": "๑๑๓ ม.๑ ต.รูสะมิแล อ.เมือง จว.ป.น. ที่อยู่ตามทะบียนราษฎร์ ๗๓/๑ ม.๔ ต.ปุโละปุโย อ.หนองจิก จว.ป.น.\n\n\n",
   "note": "รศ.ขกท.สน.จชต.",
   "picUrl": "3940300292844.jpg?alt=media&token=9266d791-460e-48ed-ae2a-2746849e6195"
 },
 {
   "nationid": 3940300415385,
   "name": "นายอิบรอเฮมมะเซ็ง/\nคอลีนัง\n",
   "address": "๗๕ ม.๒ บ.บากง ต.บางเขา\nอ.หนองจิก จว.ป.น. \n(ทะเบียนบ้านกลาง ๖๙ ม.๓ต.เกาะเปาะ อ.หนองจิก จว.ป.น.)\n",
   "note": "อดีต หน.KOMPi\n-อดีต รอง ผบ.เขตทหารที่ ๓\n-อดีต ฝ่ายการข่าว/ยทุธการ (INTOP)\n",
   "picUrl": "3940300415385.jpg?alt=media&token=3d62841c-2481-4f80-8dd6-8d5e1b5f846f"
 },
 {
   "nationid": 3940300416811,
   "name": "นายมามะ สะมะแอ/คอลีแอ",
   "address": "๖๕ บ.บากง ม.๒ ต.บางเขา\nอ.หนองจิก จว.ป.น. เดิมอยู่ ๑๑๘/๒ บ.บางทัน ม.๓ \nต.บางเขา อ.หนองจิก จว.ป.น.\n",
   "note": "คุมตัว ๑๕ เม.ย.๖๐\nคุมตัว ๑๗ ก.ย.๖๑",
   "picUrl": "3940300416811.jpg?alt=media&token=de0e676c-6f77-4afd-bf01-55b63119b88e"
 },
 {
   "nationid": 3940300427812,
   "name": "นายสาการียาหัดสมัด",
   "address": "๓๗ ม.๔ ต.บางเขา \nอ.หนองจิก จว.ป.น.",
   "note": "คุมตัว ๑๕ ต.ค.๖๐",
   "picUrl": "3940300427812.jpg?alt=media&token=82962cc0-69cc-4e01-9b69-3a5a0e21e639"
 },
 {
   "nationid": 3940300489231,
   "name": "นายหาสันสะแต",
   "address": "๓๔ ม.๒ บ.ปรัง ต.ท่ากำชำ\nอ.หนองจิก จว.ป.น.",
   "note": "คุมตัว ๒๙ ก.ค.๕๙",
   "picUrl": "3940300489231.jpg?alt=media&token=894a6977-39b8-4ca9-b1ef-e4fe4d6c2151"
 },
 {
   "nationid": 3940400002651,
   "name": "นายบารีเปาะสลาเมาะ",
   "address": "๑๐๑ ม.๑ บ.ปะนาเระ\nต.ปะนาเระ อ.ปะนาเระ จว.ป.น.\n",
   "note": "พื้นที่ อ.ปะนาเระ",
   "picUrl": "3940400002651.jpg?alt=media&token=cd717f5e-d208-4979-af3d-cd5a48362d10"
 },
 {
   "nationid": 3940500006560,
   "name": "นายสะมะแอลาเตะ",
   "address": "๒๑ ม.๑ ต.คลองใหม่ อ.ยะรัง จว.ป.น.",
   "note": "หน.KOMPI",
   "picUrl": "3940500006560.jpg?alt=media&token=46705a54-8d4d-45d9-a38b-241a3af4f20f"
 },
 {
   "nationid": 3940500125151,
   "name": "นายอาห์มัดโต๊ะมิง/อามะ บินแวลือโมะ",
   "address": "๔๔ ม.๓ ต.กระหวะ\nอ.มายอจว.ป.น.\n",
   "note": " รอง ปธ.กอจ.ปัตตานีฝ่ายชัรอีย์/อูลามา\n ผอ. (มูเดร์/บาบอ)รร.มะอ์ฮัดดารุลมาอาเรฟ ถนนกลาพอต.อาเนาะรูอ.เมือง จว.ป.น.\n",
   "picUrl": "3940500125151.jpg?alt=media&token=94792f0f-c55e-4613-bdb9-cd9991f555a7"
 },
 {
   "nationid": 3940500174896,
   "name": "นายอันนุงวากาซอ",
   "address": "๒/๒๖๗ ม.๗ บ.โคกกอ ต.บ่อทอง \nอ.หนองจิก จว.ป.น.",
   "note": "หน.REGU",
   "picUrl": "3940500174896.jpg?alt=media&token=c335ebe2-a429-4ad6-bba2-7836acdf68e2"
 },
 {
   "nationid": 3940500211724,
   "name": "นายอับดุลรอห์มาน ตูปะ/อุสตาซแม ตูปะ",
   "address": "๕๔/๑ ถ.สิโรรส ๑๐ ต.สะเตง อ.เมือง จว.ย.ล. ภูมิลำเนาเดิมอ.มายอ จว.ป.น.",
   "note": "ผู้จัดการ รร.ธรรมวิทยามูลนิธิ อ.เมือง จว.ย.ล.\n- กรรมการมัสยิดกลางยะลา",
   "picUrl": "3940500211724.png?alt=media&token=f22ba234-aab7-48eb-96f7-ff29486ba3f5"
 },
 {
   "nationid": 3940500228244,
   "name": "นายหามะสาเมาะ",
   "address": "๑๖๔/๑ ม.๒ ต.มายอ อ.มายอ จว.ป.น.",
   "note": "คุมตัว ๑๙ ต.ค.๕๗\nยอมรับ/ให้ข้อมูล\nประกันตัวสู้คดี/ยกฟ้อง",
   "picUrl": "3940500228244.jpg?alt=media&token=52f7945d-d8a9-487d-ad74-f67e2f317429"
 },
 {
   "nationid": 3940500277008,
   "name": "นายอาหามะจาจ้า",
   "address": "๕๘ ม.๑ ต.ตลิ่งชัน อ.บันนังสตา จว.ย.ล.",
   "note": "หน.REGU",
   "picUrl": "3940500277008.jpg?alt=media&token=2aa9dacf-a134-481e-ade1-ed4b461a7791"
 },
 {
   "nationid": 3940500334834,
   "name": "นายตารมีซีมะแซ/มีซี",
   "address": "๑๐ ม.๔ ต.เกาะจัน อ.มายอ\nจว.ป.น.",
   "note": "",
   "picUrl": "3940500334834.jpg?alt=media&token=fd9a984c-ca82-40ec-ae52-2baf63129fb1"
 },
 {
   "nationid": 3940500348517,
   "name": "นายมะสะกรี หามะ/ยิ่งยง",
   "address": "๕๕ ม.๕ บ.แยระ ต.ปะจัน \nอ.มายอ จว.ป.น.\n",
   "note": "รศ.ขกท.สน.จชต.\n-เมาะมาวี,กอลำ \nอ.ยะรัง จว.ป.น.\n",
   "picUrl": "3940500348517.jpg?alt=media&token=1c4a746d-13dc-404c-a94f-3b51e4a71a5d"
 },
 {
   "nationid": 3940600017853,
   "name": "นายอันวาร์ดือราแม/บาบอ",
   "address": "๕๘ ม.๕ บ.บาแฆะ ต.พิเทน \nอ.ทุ่งยางแดง จว.ป.น.\n",
   "note": "รอง หน.KOMPI ปาลัส",
   "picUrl": "3940600017853.jpg?alt=media&token=ebd4c8e0-11a5-4cd3-913f-6716e564dfb1"
 },
 {
   "nationid": 3940600067471,
   "name": "นายอายุ กาหะมะ/บาบอยุ",
   "address": " เดิมอยู่ ๑๒ ม.๑ ต.พิเทนอ.ทุ่งยางแดง จว.ป.น.\n ปัจจุบันอยู่ ๖ ม.๕ ต.น้ำดำอ.ทุ่งยางแดง\n",
   "note": "กอจ.ปัตตานี\n ผู้รับใบอนุญาต/เจ้าของปอเนาะศาลาฟี ม.๕ต.น้ำดำ อ.ทุ่งยางแดง\n",
   "picUrl": "3940600067471.jpg?alt=media&token=59d3ec88-10e9-4599-8dbf-10e01dd2e679"
 },
 {
   "nationid": 3940700084645,
   "name": "นายอาดำเจ๊ะเลาะ",
   "address": "๖๙/๒ บ.ช่องแมว ม.๔ ต.ละหาร อ.สายบุรี จว.ป.น.",
   "note": "คุมตัว ๒๙ ก.ค.๕๐\n-คุมตัว ๕ ก.ค.๕๔\n-รายงานตัว \nศปก.อ.สายบุรี\n",
   "picUrl": "3940700084645.jpg?alt=media&token=1b1180f0-478a-480a-9f13-0424a842c987"
 },
 {
   "nationid": 3940700142793,
   "name": "นายสะแมเวาะเล",
   "address": "๑๐๗/๑ ม.๒ ต.ปะเสยะวอ อ.สายบุรี จว.ป.น.",
   "note": "TL/SABOTAS ปาลัส",
   "picUrl": "3940700142793.png?alt=media&token=063021ff-bfb4-42e9-b493-a515f175985e"
 },
 {
   "nationid": 3940700343209,
   "name": "นายฮิซบุลลอฮบือซา",
   "address": "๑๕๕/๓ ม.๑ ต.บางเก่า อ.สายบุรี จว.ป.น.",
   "note": "ฝ่าย OPERASI\n(ยุทธการ)\n",
   "picUrl": "3940700343209.jpg?alt=media&token=36553b03-bc97-429f-8a90-afb6718ebfdd"
 },
 {
   "nationid": 3940800003671,
   "name": "นายยารานิงแตมามุ/โต๊ะแช",
   "address": "๔๑ ม.๓ บ.กูวิง ต.ไทรทอง อ.ไม้แก่น จว.ป.น.",
   "note": " อดีต ผบ.ร้อย.๓/หน.KOMPI ตะลุบัน เขตทหาร (KAM) ๓\n(ปัตตานี-สงขลา)\nฝ่าย Personalia(KAM) ๓\n",
   "picUrl": "3940800003671.jpg?alt=media&token=6bc4c967-9464-47e0-8193-f7f34ac2825d"
 },
 {
   "nationid": 3940800008061,
   "name": "นายพิศาลสาแม/เปาะยัง",
   "address": "๖๖/๑ บ.จือกอ ม.๓ \nต.ศรีบรรพต อ.ศรีสาคร\nจว.น.ธ.",
   "note": "นายมะรอพี บาเหม\nนายอิสมาแอ กาดง\nคุมตัว ๑๙ ต.ค.๕๘\nยอมรับ/ให้ข้อมูล",
   "picUrl": "3940800008061.jpg?alt=media&token=d6ef9246-0785-404a-bda5-8d18e382dc3b"
 },
 {
   "nationid": 3940900011749,
   "name": "นายยาการียากือโนะ",
   "address": "๒๘ ม.๒ ต.ตาลีอายร์ \nอ.ยะหริ่ง จว.ป.น.\n",
   "note": "หน.PLATONG \nพื้นที่ อ.ยะรัง\n-ผู้ประสานงาน\nPLATONG ยะรัง\n",
   "picUrl": "3940900011749.jpg?alt=media&token=43478e4e-a110-4857-ba98-1b8885ec7ada"
 },
 {
   "nationid": 3940900098917,
   "name": "อับดุลเลาะ เวาะโซะ/อาปัง",
   "address": "๕๐/๑ บ.บ้านวังทองใต้ \nม.๙ ต.บ้านแหร อ.ธารโต จว.ย.ล.\n",
   "note": "ฝ่าย LOGISTIK",
   "picUrl": "3940900098917.jpg?alt=media&token=c0649c71-2dc7-401c-b493-8e66a0eeac0b"
 },
 {
   "nationid": 3940900147187,
   "name": "นายมูหัมมัดดอเลาะ",
   "address": "๒ ม.๔ บ.ชะเอาะ ต.มะนังยง \nอ.ยะหริ่ง จว.ป.น.\n",
   "note": "หน.REGU",
   "picUrl": "3940900147187.jpg?alt=media&token=83d98416-8c48-41a4-8520-952453e605c2"
 },
 {
   "nationid": 3940900196765,
   "name": "นายอุสมานสะแลแม็ง",
   "address": "๑๑๓/๒ บ.บาโลย ม.๓ \nต.บาโลย อ.ยะหริ่ง จว.ป.น.\n",
   "note": "ฝ่าย LOGISTIK\n-ฝ่าย PHA \n",
   "picUrl": "3940900196765.jpg?alt=media&token=3ee9b6f1-494b-4764-aa9a-157fdcec6a12"
 },
 {
   "nationid": 3940900197851,
   "name": "นายอับดุลฮาดีดาหาเล็ง",
   "address": "๑๓๙ ม.๓ ต.บาโลย\nอ.ยะหริ่ง จว.ป.น.\n",
   "note": "หน.REGU \nต.พิเทน อ.ทุ่งยางแดง\n",
   "picUrl": "3940900197851.jpg?alt=media&token=4f1b373c-dfe5-4bc7-85a1-6f9dd406f77d"
 },
 {
   "nationid": 3940900331875,
   "name": "นายอูมาร์ยูโซะ/จ่า",
   "address": "๓๐ ม.๓ ต.ปุลากง \nอ.ยะหริ่ง จว.ป.น.\n",
   "note": "ฝ่าย LOGISTIK\n(จัดหา/เก็บซ่อนอาวุธ)\n",
   "picUrl": "3940900331875.jpg?alt=media&token=2a532b52-aa2e-45ca-bd3d-17354fd18951"
 },
 {
   "nationid": 3940900522565,
   "name": "นายอะมะ ดือเระห์/จอปี/จาปี",
   "address": "๑๖ บ.ดาโต๊ะ ม.๔ ต.แหลมโพธิ์\nอ.ยะหริ่ง จว.ปน.\n",
   "note": "หน.KOMPI\nตะลุบัน/สายบุรี\n",
   "picUrl": "3940900522565.jpg?alt=media&token=8152b8c3-817c-4a8c-996e-cbd6083e5295"
 },
 {
   "nationid": 3941000007327,
   "name": "นายมะรอยีกาหม๊ะ/ลาเต๊ะ",
   "address": "๔๘ ม.๒ ต.เขาตูม อ.ยะรัง จว.ป.น.",
   "note": "คุมตัว ๒ มิ.ย.๕๘",
   "picUrl": "3941000007327.jpg?alt=media&token=64f789a9-e1f3-412b-bfeb-513db4c25d41"
 },
 {
   "nationid": 3941000070944,
   "name": "นายอาดือนันสิเดะ",
   "address": "๓๙/๒ ม.๒ ต.สะดาวา \nอ.ยะรัง จว.ป.น.\n",
   "note": "หน.ฝ่าย LOGISTIK\n(จัดหา/เก็บซ่อนอาวุธ)\n\n",
   "picUrl": "3941000070944.jpg?alt=media&token=f7c2e06d-ce1f-405b-b5e9-4fe63347027b"
 },
 {
   "nationid": 3941000108062,
   "name": "นายมะซานูซีลือบาน๊ะ",
   "address": "๑๒๒/๑ ม.๔ บ.อุเบ็ง ต.ปะแต\nอ.ยะหา จว.ย.ล.\n",
   "note": "หน.KOMPI",
   "picUrl": "3941000108062.png?alt=media&token=b7878a16-8aa0-44b1-9e26-ee7b9c9077ac"
 },
 {
   "nationid": 3941000187299,
   "name": "นายยาซะยานยา ",
   "address": "๑๒๘ บ.คอกช้าง ม.๗ \nต.แม่หวาด อ.ธารโต จว.ย.ล.\n",
   "note": "หน.PLATONG เบตง\n-TL/SABOTAS \n",
   "picUrl": "3941000187299.jpg?alt=media&token=62976d4d-382a-4ff5-a059-330592023306"
 },
 {
   "nationid": 3941000268523,
   "name": "นายสุรียามูซอ",
   "address": "๒๖/๖ บ.จาเราะแป ต.ธารโต\nอ.ธารโต จว.ย.ล.\n",
   "note": "หน.KOMPI \nยะหา-กาบัง\n",
   "picUrl": "3941000268523.png?alt=media&token=d2d94471-b54f-4e0f-a172-df41a25d66ba"
 },
 {
   "nationid": 3941000308045,
   "name": "นายอาหะมัดลือแมซา",
   "address": "๒๐๕ ม.๔ บ.บันนังกูแว\nต./อ.บันนังสตา จว.ย.ล.\n",
   "note": "หน.KOMPI",
   "picUrl": "3941000308045.png?alt=media&token=10a8e41b-fa11-4b5e-9c36-602b98399d0c"
 },
 {
   "nationid": 3941000336456,
   "name": "นายบาร์ลานดือราแม/โซ๊ะ",
   "address": "๑๕๘ บ.โฉลง ม.๕ ต.คลองใหม่\nอ.ยะรัง จว.ป.น.\n",
   "note": "ฝ่าย EKONOMI",
   "picUrl": "3941000336456.jpg?alt=media&token=82ed62a0-aa3a-4be8-b830-b5e6568218d8"
 },
 {
   "nationid": 3941000339641,
   "name": "นายมะแซยามาสาเร๊ะ/เปาะซูแซ",
   "address": "๑๙๖ บ.โคกหญ้าคา ม.๑\nต.คลองใหม่ อ.ยะรัง จว.ป.น.\n",
   "note": "รอง หน.KOMPI\n-หน.สั่งการ ฝ่ายปฏิบัติการด้านระเบิด\nPLATONG เมาะมาวี, กอลำ อ.ยะรัง\n",
   "picUrl": "3941000339641.jpg?alt=media&token=4f52e1df-8d4f-4a21-bde7-f7ed0f28a82d"
 },
 {
   "nationid": 3941000342219,
   "name": "นายอานูวา ยูโซะ",
   "address": "๒๑๘/๓ บ.กอตอรานอ ม.๑ \nต.คลองใหม่ อ.ยะรัง จว.ป.น.\n",
   "note": "ฝ่าย SABOTAS\n-หน.TL/SABOTAS\nแยลีมอ/ยะรัง\n-ชำนาญประกอบระเบิด\n-ควบคุม/สั่งการ และประสานการปฏิบัติกับสมาชิกเครือข่าย TL ระดับ PLATONG ต่าง ๆ ของ KOMPI ยะรัง\n",
   "picUrl": "3941000342219.jpg?alt=media&token=94e92aaf-e8a0-43a6-ab54-2d944c23efd0"
 },
 {
   "nationid": 3941000356511,
   "name": "นายอาหามะมะเซ็ง",
   "address": "๖๗/๑ บ.บารอ ม.๓ \nต.คลองใหม่ อ.ยะรัง จว.ป.น.\n",
   "note": "TL/SABOTAS\nแยลีมอ/ยะรัง\n\n\n",
   "picUrl": "3941000356511.jpg?alt=media&token=5f4e4090-6c02-4c16-8dcf-5b8a79ed7783"
 },
 {
   "nationid": 3941000361841,
   "name": "นายมูดิง บาซอ/ครูดิง ยะรัง",
   "address": "๖๐ ม. ๔ บ.ต้นมะขาม\nต.เมาะมาวี อ.ยะรัง จว.ป.น.\n",
   "note": " อดีตข้าราชครูสามัญ ร.ร.ละหารยามู ม.๒ ต.กอลำ อ.ยะรัง จว.ป.น. (เกษียณ ๑ ต.ค.๕๖)\n- น้องนายสะแปอิง บาซอ\n",
   "picUrl": "3941000361841.jpg?alt=media&token=f45b32c8-8870-48a1-8e7a-181ab9a64200"
 },
 {
   "nationid": 3941000406888,
   "name": "นายดอรอแมหะยีหะซา/ \nอับดุลเราะมานเดวานี/\nอุสตาซแม แดวอ/แม เทวดา\n",
   "address": "๑๑๓/๑ ม.๔ ต.สะนออ.ยะรัง จว.ป.น.",
   "note": " กรรมการบริษัท บูมีปุตราจำกัด จว.ป.น.\n- รองประธานสถาบันภาษามลายูไทยแลนด์\n- อดีตอุสตาซรร.ธรรมวิทยามูลนิธิ\n- อดีตบรรณาธิการหนังสือพิมพ์ Fajar\n",
   "picUrl": "3941000406888.png?alt=media&token=ef097f5e-4a51-4867-bfb3-cfa40ca43460"
 },
 {
   "nationid": 3941000435705,
   "name": "นายมะยากีวาแวนิ",
   "address": "๒๘/๑ บ.สุงาบารู ม.๔\nต.คลองใหม่ อ.ยะรัง จว.ป.น.\n",
   "note": "คุมตัว ๙ เม.ย.๖๐",
   "picUrl": "3941000435705.jpg?alt=media&token=216eb5c7-326c-4052-9be7-064aa00f402d"
 },
 {
   "nationid": 3941000444445,
   "name": "นายมะลีดาโอ๊ะ",
   "address": "๑๗๙/๒ บ.โฉลง ม.๕\nต.คลองใหม่ อ.ยะรัง จว.ป.น.\n",
   "note": "TL/SABOTAS\nPLATONG เมาะมาวี\n(ฝ่ายผลิตระเบิด)\n",
   "picUrl": "3941000444445.jpg?alt=media&token=7198a68b-9833-4287-8bfe-53a32120ea74"
 },
 {
   "nationid": 3941000444518,
   "name": "นายมะยะโก๊ะลาเต๊ะ",
   "address": "๑๗๙/๑ ม.๕ ต.คลองใหม่ \nอ.ยะรัง จว.ป.น.\n",
   "note": "หน.PLATONG\nต.นาประดู่, ต.ทุ่งพลา,ต.ควนโนรี, ต.ปากล่อ",
   "picUrl": "3941000444518.jpg?alt=media&token=c12ac6e4-dc91-4365-aa90-dc512192edff"
 },
 {
   "nationid": 3941000507889,
   "name": "นายแวสาเฮาะ ดอเลาะ/\nแดวอ/เทวดา\n",
   "address": "๒๒๑ บ.ต้นมะขาม ม.๔ \nต.เมาะมาวี อ.ยะรัง จว.ป.น.\n",
   "note": "TL/SABOTAS\nPLATONG เมาะมาวี\n(ฝ่ายผลิตระเบิด)\n",
   "picUrl": "3941000507889.jpg?alt=media&token=c39a9c05-fc31-4ae3-8b47-2a89d6852037"
 },
 {
   "nationid": 3941000519739,
   "name": "นายมะสะกือรี อาซู/โต๊ะเจ๊ะ",
   "address": "๓๗/๒ ม.๓ บ.จาเราะแป \nต.ธารโต อ.ธารโต จว.ย.ล.\n",
   "note": "หน.KOMPI\nธารโต/เบตง\n-หน.PLATONG ธารโต\n-คู่บัดดี้นายอภินันต์ฯ\n",
   "picUrl": "3941000519739.png?alt=media&token=2628391b-fbb6-4ba6-b92d-84f6b98d86ca"
 },
 {
   "nationid": 3941000539250,
   "name": "นายนิแมซา/มีซี/กาเซ็ง",
   "address": "๒๒ ม.๔ ต.กอลำ อ.ยะรัง จว.ป.น.",
   "note": "คุมตัว ๓ ต.ค.๕๖\n-คุมตัว ๒๓ ก.ค.๕๗\n",
   "picUrl": "3941000539250.jpg?alt=media&token=766dbbd2-d4ec-41d8-b1bf-6a07193b6f7d"
 },
 {
   "nationid": 3941000539721,
   "name": "นายอาบัสอาลี/ฮิม",
   "address": "๒๖/๑ บ.ปูลากาซิง ม.๔ ต.กอลำ\nอ.ยะรัง จว.ป.น.\n",
   "note": "คุมตัว ๑๕ ก.ย.๖๐",
   "picUrl": "3941000539721.jpg?alt=media&token=c1ca68ef-938e-4b5c-aeb8-1f3e5c36d527"
 },
 {
   "nationid": 3941000557614,
   "name": "นายรุสดีเกะรา/เปาะจิ",
   "address": "๑๑/๑ ม.๒ ต.เขาตูม อ.ยะรัง จว.ป.น. (บ้านภรรยาอยู่ ต.ยะรมอ.เบตง จว.ย.ล.)",
   "note": "หน.REGU",
   "picUrl": "3941000557614.png?alt=media&token=85250c18-0944-4e79-91ec-0f9d61de656b"
 },
 {
   "nationid": 3941000602083,
   "name": "เจ๊ะอุสมาน บือแน/เปาะวี",
   "address": "๕๐/๑ บ.ปูลากาซิง ม.๔ ต.กอลำ\nอ.ยะรัง จว.ป.น.\n",
   "note": "คุมตัว ๓๐ สค.๖๐",
   "picUrl": "3941000602083.jpg?alt=media&token=7069dfd8-29e1-40a0-9650-548c4030cec2"
 },
 {
   "nationid": 3941000619792,
   "name": "นายฮารงค์กอแต",
   "address": "๔๖ บ.ลากูนิง ม.๕ ต.เขาตูม อ.ยะรัง จว.ป.น. \n(บ้านย่อย บ.จาเราะบองอ)\n",
   "note": "TL/SABOTAS\nแยลีมอ/ยะรัง\n\n",
   "picUrl": "3941000619792.jpg?alt=media&token=551138f5-6007-49e0-b7e2-7a0a908f9eb6"
 },
 {
   "nationid": 3941100030928,
   "name": "นายยุครี การี/แม",
   "address": "๘๖ ม.๖ บ.คอลอกาปะ ต.กะรุบี \nอ.กะพ้อ จว.ป.น.\n",
   "note": "ฝ่าย LOGISTIK\nจัดหา/เก็บซ่อนอาวุธ\n",
   "picUrl": "3941100030928.jpg?alt=media&token=15263b35-4310-4e97-810c-e6e00a8e8c3c"
 },
 {
   "nationid": 3941100060681,
   "name": "นายซอและมะเซ็ง",
   "address": "๕๑/๒ ม.๓ บ.น้ำใส ต.เกียร์ \nอ.สุคิริน จว.น.ธ.\n",
   "note": "หน.KOMPI\nเขตสุคิริน (โซนภูเขา)\n",
   "picUrl": "3941100060681.jpg?alt=media&token=f0b7a5c6-ee2d-45f4-89bc-bf0f729bf8ed"
 },
 {
   "nationid": 3941100076782,
   "name": "นายอิบรอเฮมมะเซ็ง/\nซาเฮาะ/เปาะจิ\n",
   "address": "๒๙ ม.๓ ต.ปล่องหอย อ.สายบุรี จว.ป.น.",
   "note": "ฝ่าย LOGISTIK\nจัดหา/เก็บซ่อนอาวุธ\n\n",
   "picUrl": "3941100076782.jpg?alt=media&token=e9044368-4b3b-45fd-b904-aafca9e55ae9"
 },
 {
   "nationid": 3941100090953,
   "name": "นายสุกรียาเดมะ",
   "address": "๑๐๒ ม.๖ บ.โลทู ต.ปล่องหอย อ.กะพ้อ จว.ป.น.",
   "note": "หน.REGU",
   "picUrl": "3941100090953.jpg?alt=media&token=93794004-edf6-4a86-9bc1-90b28ac4c939"
 },
 {
   "nationid": 394900570586,
   "name": "นายดูนเลาะ แวมะนอ",
   "address": "๑๙๖ ม.๓ บ.ท่าด่านต.ตะโละกาโปร์ อ.ยะหริ่ง จว.ป.น.(หลบหนี)",
   "note": " อดีต ผบ.เขตทหารที่ ๓\n- บุตรเขยนายดูนเลาะ แวมะนอเลขาธิการ BRN\n",
   "picUrl": "394900570586.png?alt=media&token=f10c052f-6df9-429a-8e97-a885dc2f8174"
 },
 {
   "nationid": 3949800057273,
   "name": "รอซารีเจะเลาะ/โต๊ะแว",
   "address": "๑๓ ต.ตะลุบัน อ.สายบุรี \nจว.ป.น.\n",
   "note": "รับผิดชอบ\nอ.ไม้แก่น\n",
   "picUrl": "3949800057273.jpg?alt=media&token=2bfd537c-f541-4a70-bee1-ba338bddceff"
 },
 {
   "nationid": 3950100058155,
   "name": "นายอัหมัดตือง๊ะ/ตอริ",
   "address": "๓๐ ถ.จะปะกียาอุทิศ อ.เมือง จว.ย.ล. เดิมอยู่ ๒๕๑ ม.๔ ต.ลิดล\n อ.เมือง จว.ย.ล.\n",
   "note": "ผบ.เขตทหารที่ ๑\nฝ่าย INTOP \n(การข่าว/ยุทธการ)\n-หน.KOMPI \nกือดา/เมืองยะลา\n",
   "picUrl": "3950100058155.jpg?alt=media&token=a2660df1-b8e2-46e8-8781-99be627e2b9e"
 },
 {
   "nationid": 3950100119014,
   "name": "นายอาบะห์เจะอาลี",
   "address": "๔๗/๑ ม.๒ บ.ปุโรง ต.ปุโรง \nอ.กรงปินัง จว.ย.ล.\n",
   "note": "หน.PLATONG",
   "picUrl": "3950100119014.jpg?alt=media&token=92ec4008-8249-4c17-923f-53dbd1b117c5"
 },
 {
   "nationid": 3950100175151,
   "name": "นายมะกอเซ็งเจ๊ะมะ\nปะจู/กอเซ็ง\n",
   "address": "๖๑/๑ บ.เนียง ม.๔ \nต.เปาะเส้ง อ.เมือง จว.ย.ล.\n",
   "note": "หน.PLATONG",
   "picUrl": "3950100175151.png?alt=media&token=10637c81-5379-43d2-9470-9e1f7c200d04"
 },
 {
   "nationid": 3950100240280,
   "name": "นายบูกรีมีหิ/อาวี",
   "address": "๕๐ บ.ดุซง ม.๑ ต.สะเอะ\nอ.กรงปินัง จว.ย.ล.",
   "note": "คุมตัว ๒๘ พ.ย.๕๐",
   "picUrl": "3950100240280.jpg?alt=media&token=c0b81036-c1e7-4f2e-95dc-28467e05ce45"
 },
 {
   "nationid": 3950100371139,
   "name": "นายมะสุกรีฮารี/\nฮาเซ็ม อับดุลเลาะห์\n",
   "address": "๕๐ ม.๓ ต.ลำใหม่ อ.เมือง \nจว.ย.ล.\n",
   "note": " กรรมการฝ่ายอูลามา DKP (โครงสร้างเก่า)",
   "picUrl": "3950100371139.jpg?alt=media&token=6136dca5-6acc-4d9e-88a6-48a04ac9218c"
 },
 {
   "nationid": 3950100378125,
   "name": "นายไพศอลหะยีสะมะแอ",
   "address": "๖๑/๑ ม.๔ บ.ปอเยาะ\nต.ลำใหม่ อ.เมือง จว.ย.ล.",
   "note": "นายมุกตาร์ มาหะ\nนายอัสวัน อาแวกาจิ\nนายดอรอแมโดนุ๊",
   "picUrl": "3950100378125.jpg?alt=media&token=6c669a41-4776-4d85-8d31-62e54532d748"
 },
 {
   "nationid": 3950100439655,
   "name": "นายอาบูตอเละสาและ",
   "address": "\n๑๕๕ ม.๗ บ.ต้นหยี ต.ลำพะยา \nอ.เมือง จว.ย.ล.",
   "note": "หน.PLATONG",
   "picUrl": "3950100439655.jpg?alt=media&token=710b4f6e-cbd8-495e-8452-b474cc73724d"
 },
 {
   "nationid": 3950100500231,
   "name": "นายมะดารีอารง",
   "address": "๘๗/๑ ม.๔ บ.สาคอ ต.ท่าสาป\nอ.เมือง จว.ย.ล.\n",
   "note": "หน.PLATONG",
   "picUrl": "3950100500231.jpg?alt=media&token=26549686-ed0c-440a-ad29-0f1d36589d0f"
 },
 {
   "nationid": 3950100592950,
   "name": "นายอุสมานนาวา",
   "address": "๒ ซ.วสันต์พัฒนา ๔ ถ.ขวัญเมือง\nต.สะเตง อ.เมือง จว.ย.ล.\n",
   "note": "ฝ่าย EKONOMI\nเขต ๑\n-หน.ฝ่ายการเงิน\n",
   "picUrl": "3950100592950.jpg?alt=media&token=5f9ce89b-28b1-4949-b895-032c24771fdf"
 },
 {
   "nationid": 3950100650968,
   "name": "นายอับดุลรอเซะดีสะเอะ",
   "address": "๕๑/๑ ม.๙ บ.อุเป ต.กรงปินัง \nอ.กรงปินัง จว.ย.ล.\n",
   "note": "แกนนำฝ่ายทหาร เขต ๑",
   "picUrl": "3950100650968.png?alt=media&token=d95b337e-4300-4f05-9737-e733ca94fdaa"
 },
 {
   "nationid": 3950100653088,
   "name": "นายอาแซยานยา/เจ๊ะกูอาแซ",
   "address": "๗๕ บ.อุเป (บ.รือเป) ม.๙ต.กรงปินัง อ.กรงปินัง จว.ย.ล.",
   "note": " อดีตผู้บริหาร ร.ร.พัฒนาอิสลามวิทยา (ปอเนาะลำใหม่) ต.ลำใหม่\n บุตรนายสะมะแอ ฮารีปธ.กอจ.ยะลา\n ผู้แทน BRN ในการพูดคุยสันติภาพกับคณะของ ลมช. หัวหน้าคณะพูดคุย/เจรจา ",
   "picUrl": "3950100653088.png?alt=media&token=64aec46b-cc75-430b-8c78-3b894491078d"
 },
 {
   "nationid": 3950200052264,
   "name": "นายไพศาลยารง",
   "address": "๒๓๔/๑ ม.๓ ต.บางปู\nอ.ยะหริ่ง จว.ป.น.\n",
   "note": "ฝ่าย PHA",
   "picUrl": "3950200052264.png?alt=media&token=3b35d4f8-9b44-45c2-9f33-028e245a6638"
 },
 {
   "nationid": 3950300049087,
   "name": "นายนูรุดดินกาจะลากี",
   "address": "๑๗๔ ม.๔ บ.บียอ ต.บาเจาะ \nอ.บันนังสตา จว.ย.ล.\n",
   "note": "หน.PLATONG",
   "picUrl": "3950300049087.png?alt=media&token=76efb922-3349-41cd-b0a0-09dd5abc0aeb"
 },
 {
   "nationid": 3950300053459,
   "name": "นายมารซูกียูโสะ",
   "address": "๒๒ ม.๑ บ.เขื่อนบางลาง\nต.เขื่อนบางลาง อ.บันนังสตา\nจว.ยล.\n",
   "note": "หน.KOMPI \nเขื่อนบางลาง-ธารโต\n",
   "picUrl": "3950300053459.png?alt=media&token=20aa6c2e-0275-4401-a8b4-9bcdaf45f1a8"
 },
 {
   "nationid": 3950300135498,
   "name": "นายนิเซ๊ะนิฮะ/เปาะนิมะ",
   "address": "๓๒/๕ ม.๓ บ.แบรอกุวิง ต.ตะลุโบะ อ.เมือง จว.ป.น.",
   "note": " อดีตประธานชมรม PNYS ปี ๒๕๓๗\n- เมื่อ ๑๖ ก.ย.๕๔ เคยถูก จนท.ควบคุมตัวที่ ศพส.ศชต. ต้องสงสัยอยู่เบื้องหลังการชุมนุมประท้วงในพื้นที่ จชต.หลายเหตุการณ์ และเป็นแกนนำระดับสูงฝ่ายปกครองของ ผกร. และได้รับการปล่อยตัวเมื่อ ๔ ต.ค.๕๔\n",
   "picUrl": "3950300135498.jpg?alt=media&token=1c45fbed-ff6d-4e0f-874d-ebb07310067c"
 },
 {
   "nationid": 3950300189067,
   "name": "มูฮำหมัดอาซิ แวกือจิ/ปะจูยิ",
   "address": "๔๖๑ บ.บือราเป๊ะ ม.๗ \nต.บันนังสตา อ.บันนังสตา จว.ย.ล.\n",
   "note": "หน.REGU กอลำ",
   "picUrl": "3950300189067.jpg?alt=media&token=68f72666-1adb-415b-b4cd-66dc91116a57"
 },
 {
   "nationid": 3950300233279,
   "name": "นายเฟาซีกลูแป/ยี/เลาะ",
   "address": "๑๘๐ ม.๖ บ.บือซู\nต./อ.บันนังสตา\n",
   "note": "หน.KOMPI\nแยลีมอ/ยะรัง\n",
   "picUrl": "3950300233279.jpg?alt=media&token=7b4b6082-9cfd-4e14-a0ce-62f724537244"
 },
 {
   "nationid": 3950300237894,
   "name": "นายฮารงแวกือจิ",
   "address": "๑๒๙/๑ บ.บาโงยแจเกาะ \nม.๑๐ ต.บันนังสตา \nอ.บันนังสตา จว.ย.ล.\n",
   "note": "TL/SABOTAS KAM ๑\n-ชำนาญประกอบระเบิด\n\n",
   "picUrl": "3950300237894.jpg?alt=media&token=75b247ad-ac8f-4257-a02b-a4234c2680ee"
 },
 {
   "nationid": 3950400001973,
   "name": "นายนุมันแวหะยี/มัง",
   "address": "๑๓๑ บ.หน้าเกษตร ม.๒ \nต.ธารโต อ.ธารโต จว.ย.ล.\n",
   "note": "หน.REGU\n-TL/SABOTAS\nธารโต/เบตง\n",
   "picUrl": "3950400001973.jpg?alt=media&token=e3b49dd6-11ed-44c1-997a-90d8319bff79"
 },
 {
   "nationid": 3950400031783,
   "name": "นายอิบรอเฮมสาและ/มูฮา",
   "address": "๒๔ บ.แหร ม.๑ ต.บ้านแหร\nอ.ธารโต จว.ย.ล.\n",
   "note": "TL/SABOTAS\nธารโต/เบตง\n",
   "picUrl": "3950400031783.jpg?alt=media&token=e0d7d8df-01ee-4af7-8a9b-b806c227f4dc"
 },
 {
   "nationid": 3950400038079,
   "name": "นายมามุมะแซ/ปะดอมุ",
   "address": "๔๗ บ.แหร ม.๑ ต.บ้านแหร\nอ.ธารโต จว.ย.ล.\n",
   "note": "ฝ่าย LOGISTIK\n-หน.REGU\n",
   "picUrl": "3950400038079.png?alt=media&token=a06137a6-5166-4080-9365-b618747b8a26"
 },
 {
   "nationid": 3950400039954,
   "name": "นายอภินันต์สะเตาะ",
   "address": "๓ ม.๖ ต./อ.ธารโต จว.ย.ล.เดิมอยู่ ๑๑๑ ม.๑ ต.บ้านแหร \nอ.ธารโต จว.ย.ล. \n",
   "note": "ฝ่าย PERSONALIA\nเขต ๑\n",
   "picUrl": "3950400039954.png?alt=media&token=8ec36c68-dc05-4663-9c60-d646bf7f156c"
 },
 {
   "nationid": 3950400042068,
   "name": "นายซาการียาเจ๊ะโซะ/ยา",
   "address": "๒๙/๘ บ.บัวทอง ม.๒ \nต.บ้านแหร อ.ธารโต จว.ย.ล.\n",
   "note": "หน.REGU",
   "picUrl": "3950400042068.png?alt=media&token=c2975884-0420-44f3-be54-b36274fd655d"
 },
 {
   "nationid": 3950400050915,
   "name": "นายสะมะแอเจะดอมะ",
   "address": "๕๑ ม.๔ บ.ปูโละสะนิแย\nต.บ้านแหร อ.ธารโต จว.ย.ล. \n",
   "note": "หน.REGU",
   "picUrl": "3950400050915.jpg?alt=media&token=6bfcd00b-62d2-4158-9f69-5a9a083d3593"
 },
 {
   "nationid": 3950500089449,
   "name": "นายฮูไบดีละห์รอมือลี",
   "address": "๔๑ ม.๔ บ.ฆอรอราแม \nต.ปะแต อ.ยะหา จว.ย.ล.\n",
   "note": "รอง ผบ.ทหารเขตที่ ๑\n-หน.KOMPI ยะหา,\nกรงปินัง\n-ฝ่าย LOGISTIK\n",
   "picUrl": "3950500089449.jpg?alt=media&token=0fc158fb-e5d7-4a56-8086-098a48bc889f"
 },
 {
   "nationid": 3950500104685,
   "name": "นายสาการียา สาและ/เด๊ายา",
   "address": "๕๓ บ.บาโด ม.๓ ต.ยุโป \nอ.เมือง จว.ย.ล.\n",
   "note": "TL/SABOTAS\nกือดา/เมือง\n\n",
   "picUrl": "3950500104685.jpg?alt=media&token=db9257ac-f629-41ea-8835-5cfc06f1ddfd"
 },
 {
   "nationid": 3950500194013,
   "name": "นายอับดุลเลาะลาเต๊ะ",
   "address": "๑๙/๑ บ.เจาะตาแม ม.๔\nต.กาตอง อ.ยะหา จว.ย.ล.\n",
   "note": "TL/SABOTAS ยะหา",
   "picUrl": "3950500194013.jpg?alt=media&token=cf360b3d-93f0-4eb5-ab2c-a97bead08d3e"
 },
 {
   "nationid": 3950600054167,
   "name": "นายมาหามะอีซอแวหะมะ",
   "address": "๖๒/๑ บ.ลูโบ๊ะนิบง ม.๑\nต.กอตอตือร๊ะ อ.รามัน จว.ย.ล.\n",
   "note": "รอง หน.KOMPI\nรามัน/กาบู\n",
   "picUrl": "3950600054167.png?alt=media&token=8be50bf6-d1b2-49d0-845f-34bd85510413"
 },
 {
   "nationid": 3950600068575,
   "name": "นายดอรอแมโดนุ๊",
   "address": "๔๓ ม.๒ ต.กาลูปัง อ.รามัน\nจว.ย.ล.",
   "note": "คุมตัว ๒๐ พ.ย.๕๑",
   "picUrl": "3950600068575.jpg?alt=media&token=f191403c-9be2-48c5-bde9-c2cc15707e58"
 },
 {
   "nationid": 3950600091551,
   "name": "นายมะสดี รอมะ",
   "address": "๙๙ ม.๒ ต.กอตอตือร๊ะ \nอ.รามัน จว.ย.ล.",
   "note": "อับดุลลาเต๊ะ เรียง\nนายมะรีซัน สะตะ\nคุมตัว ๘ ส.ค.๕๙\nยอมรับให้ข้อมูล",
   "picUrl": "3950600091551.jpg?alt=media&token=3b1e2c2c-ba17-4c71-9f19-204adcadc398"
 },
 {
   "nationid": 3950600155699,
   "name": "นายมะโลยีอิแต/บาฮา",
   "address": "๑๒/๒ บ.บาลอ ม.๑ ต.บาลอ\nอ.รามัน จว.ย.ล.\n",
   "note": "หน.REGU\n(ตรวจสอบพื้นที่)\n",
   "picUrl": "3950600155699.png?alt=media&token=de177680-504d-4a3e-9ef9-92e359774bf3"
 },
 {
   "nationid": 3950600175363,
   "name": "นายมะรีซันสะตะ/ยัง",
   "address": "๒๒/๔ ม.๗ ต.บาลอ อ.รามัน\nจว.ย.ล",
   "note": "คุมตัว ๘ ส.ค.๕๙",
   "picUrl": "3950600175363.jpg?alt=media&token=9db50af5-c3bd-437b-9371-e5ab364225aa"
 },
 {
   "nationid": 3950600175711,
   "name": "นายซัยฟุลลอฮ ซาฟรุ",
   "address": "๕๘ ม.๗ บ.ปาลูกาปาลัส \nต.บาลอ อ.รามัน จว.ย.ล.",
   "note": "นายสูลกีฟลีปอทอง",
   "picUrl": "3950600175711.jpg?alt=media&token=e0b31d84-eed1-49c2-845b-1d983058835c"
 },
 {
   "nationid": 3950600219859,
   "name": "นายหีพนีมะเร๊ะ",
   "address": " ๙๒/๔ ม.๒ ต.อาซ่องอ.รามัน จว.ย.ล.\n ปัจจุบันหลบหนีอยู่ มซ.\n",
   "note": "แกนนำฝ่ายทหาร เขต ๑\n\n",
   "picUrl": "3950600219859.png?alt=media&token=eed450a2-84b0-4102-b821-15b8674a236c"
 },
 {
   "nationid": 3950600228785,
   "name": "นายอุสมาน ยะโก๊ะ/บาเร๊าะ",
   "address": "๑๑๑ ม.๑ ถ.จรูญวิถี ต.กายูบอเกาะ อ.รามัน จว.ย.ล. เดิมอยู่ ๒๓ บ.สะโต ม.๕ ต.อาซ่อง อ.รามัน จว.ย.ล.",
   "note": "แกนนำฝ่ายทหาร เขต ๑",
   "picUrl": "3950600228785.jpg?alt=media&token=4ada1d36-c5b8-4f7d-895c-f5d6ae788d32"
 },
 {
   "nationid": 3950600345099,
   "name": "นายอายูมินกือนิ",
   "address": "๑๐/๓ ม.๔ ต.จะกว๊ะ อ.รามัน จว.ย.ล.",
   "note": "นายไสฝูลาเต๊ะ",
   "picUrl": "3950600345099.jpg?alt=media&token=e8337e41-62b3-4331-8a2e-d3c0e9416508"
 },
 {
   "nationid": 3950600385341,
   "name": "นายมะรอซาริงสาและดิง\n(เป๊าะจิ)\n",
   "address": "๑๐๙/๑ บ.บือแนบูเก๊ะ ม.๔ต.ตะโล๊ะหะลอ อ.รามัน จว.ย.ล.",
   "note": "หน.REGU",
   "picUrl": "3950600385341.jpg?alt=media&token=d594feb8-2652-44a7-8ff6-f9b3d9b7d632"
 },
 {
   "nationid": 3950600399512,
   "name": "นายอับดุลเลาะนิมะ",
   "address": "๖๙ บ.ตะโละเลาะ ต.บือมัง \nอ.รามัน จว.ย.ล.\n",
   "note": "ฝ่าย PHA ",
   "picUrl": "3950600399512.png?alt=media&token=7f90f980-d073-4cdb-a02b-aaee4a438778"
 },
 {
   "nationid": 3950600414821,
   "name": "นายสาหูดินโต๊ะเจ๊ะมะ",
   "address": "๗๒ ม.๓ บ.ดุซงตาวา ต.บือมัง \nอ.รามัน จว.ย.ล.\n",
   "note": "หน.KOMPI",
   "picUrl": "3950600414821.png?alt=media&token=369cfe12-779b-42b3-aeb6-79c9414c9c8e"
 },
 {
   "nationid": 3950600525160,
   "name": "นายนุ๊แกะซิ",
   "address": "๙๓/๓ม.๔ ต.บาโงสะโต อ.ระแงะ จว.น.ธ.\n ปัจจุบัน หลบหนีอยู่ มซ.\n",
   "note": "",
   "picUrl": "3950600525160.png?alt=media&token=7f0b5d8f-ba40-4620-939a-1ff5c34a0c98"
 },
 {
   "nationid": 3950600535289,
   "name": "นายมะรูดีซิมะ",
   "address": "๔๖ ม.๑ ต.โกตาบารู อ.รามัน จว.ย.ล.",
   "note": "คุมตัว ๒๑ ก.พ.๔๙\n-รศ.ขกท.สน.จชต.\n",
   "picUrl": "3950600535289.jpg?alt=media&token=df0e4f27-59bd-439d-8a3f-09a0adebb71d"
 },
 {
   "nationid": 3960100017536,
   "name": "อิสมาแอ มะเซ็ง/เปาะจิแอ",
   "address": " ๑๑๑/๑ ม.๘ บ.ทุ่งโต๊ะดัง\nต.บางปอ อ.เมือง จว.น.ธ.\n ปัจจุบันหลบหนีอยู่ มซ.\n",
   "note": " อดีต ผบ.เขตทหารที่ ๓\n- บุตรเขยนายดูนเลาะ แวมะนอเลขาธิการ BRN\n",
   "picUrl": "3960100017536.jpg?alt=media&token=e7446156-aa60-4191-9b6b-c472376e9a03"
 },
 {
   "nationid": 3960100049881,
   "name": "นายอาลียะห์เจ๊ะเตะ",
   "address": "๒๕/๑ ม.๓ ต.บางปอ \nอ.เมือง จว.น.ธ.\n",
   "note": "หน.REGU",
   "picUrl": "3960100049881.jpg?alt=media&token=9d25e9ee-8f1e-4316-9668-288d85af81ff"
 },
 {
   "nationid": 3960100055474,
   "name": "นายการูซามัน เจ๊ะดอเล๊าะ",
   "address": "๙๑ ม.๓ บ.โคกสุมุ ต.บางปอ\nอ.เมือง จว.น.ธ.\n",
   "note": "หน.REGU",
   "picUrl": "3960100055474.jpg?alt=media&token=6b65ea22-92df-4d37-90ee-dd2617ebe94b"
 },
 {
   "nationid": 3960100072743,
   "name": "นายดอรอแม อูมา",
   "address": "๔๗/๒ บ.กำแพง ม.๒\nต.กะลุวอ อ.เมือง จว.น.ธ.\n",
   "note": " อดีตอุสตาซรร.อิสลามบูรพา/ปอเนาะสะปอมม.๕ ต.กะลุวอเหนือ อ.เมือง จว.น.ธ.\nหน.ฝ่ายการศึกษา DKP (โครงสร้างเดิม)",
   "picUrl": "3960100072743.jpg?alt=media&token=01051f5b-73ba-4c33-940e-0d372fe5fc76"
 },
 {
   "nationid": 3960100078733,
   "name": "นายโลกกือนันสือแม",
   "address": "๑๗๖ ม.๕ บ.โผลง ต.โต๊ะเด็ง\nอ.สุไหงปาดี จว.น.ธ.\n",
   "note": "COMMANDO\n(คอมมานโด)\n",
   "picUrl": "3960100078733.jpg?alt=media&token=d032467a-00bf-495b-9b1a-67bc0e82890d"
 },
 {
   "nationid": 3960100128901,
   "name": "นายรอมลี แบเลาะ/\nอับดุลการิม คอลิบ\n",
   "address": "๖๔ ม.๘ บ.ทุ่งโต๊ะดัง \nต.บางปอ อ.เมือง จว.น.ธ.\n",
   "note": " โฆษก ผู้แทน BRN ในการพูดคุยสันติภาพกับคณะของ ลมช.\n ยังคงปรากฎการเคลื่อนไหวแถลงการณ์ผ่านยูทูป ในนามฝ่ายประชาสัมพันธ์ BRN (BadanPenerangan BRN)\n",
   "picUrl": "3960100128901.jpg?alt=media&token=33803570-4dec-42ff-850e-9740cf9065e5"
 },
 {
   "nationid": 3960100151759,
   "name": "นายกูเฮงยูโซ๊ะ/แบเฮง",
   "address": "๔๗ ม.๘ บ.ไอปาแซ ต.ตันหยงลิมอ อ.ระแงะ จว.น.ธ.",
   "note": "TL/SABOTAS\n\n",
   "picUrl": "3960100151759.jpg?alt=media&token=a45031e6-0d64-46c7-a4b3-33c1f2cf978e"
 },
 {
   "nationid": 3960100366194,
   "name": "นายไรนาอาบีเด็งสามะ/แยนา",
   "address": "๓๐ ม.๔ บ.รือเปาะ ต.ดุงซงญอ\nอ.จะแนะ จว.น.ธ.\n",
   "note": "หน.PLATONG\nต.ดุซงญอ\n",
   "picUrl": "3960100366194.jpg?alt=media&token=c983efe2-e1b6-4679-8076-a8a830aa7b24"
 },
 {
   "nationid": 3960200112488,
   "name": "นายสับรียูโซ๊ะ",
   "address": "๕๗ ม.๘ ต.ศาลาใหม่ อ.ตากใบ จว.น.ธ. ",
   "note": "หน.KOMPI\n -หน.PLATONG \n",
   "picUrl": "3960200112488.png?alt=media&token=6978981c-fd32-4ccf-8e47-1e3a665dd46f"
 },
 {
   "nationid": 3960200167053,
   "name": "นายยืนยงจิ",
   "address": "๓๓๑/๑ ม.๒ ต.เจ๊ะเห \nอ.ตากใบ จว.น.ธ.\n",
   "note": "TL/SABOTAS\nตากใบ\n",
   "picUrl": "3960200167053.jpg?alt=media&token=f4a6d5cc-bc67-4ab4-860e-75c6f234902e"
 },
 {
   "nationid": 3960200394068,
   "name": "นายอำรัญอาแวหามะ",
   "address": "๓ บ.ยูโย ม.๖ ต.บางขุนทอง\nอ.ตากใบ จว.น.ธ.\n",
   "note": "หน.ฝ่าย LOGISTIK",
   "picUrl": "3960200394068.png?alt=media&token=768079ae-2d92-45c1-8c8a-78c1a687cda1"
 },
 {
   "nationid": 3960200430536,
   "name": "นายมาหามะมิงมามะ/มิง",
   "address": "๘๘ ม.๔ บ.ตะเหลี่ยง\nต.เกาะสะท้อน อ.ตากใบ จว.น.ธ. \n",
   "note": "หน.PLATONG",
   "picUrl": "3960200430536.jpg?alt=media&token=040b8871-7483-47c5-b104-2a74ccfff668"
 },
 {
   "nationid": 3960200465399,
   "name": "นายนิเซ็งนิสุหลง",
   "address": "๑๕ ม.๕ ต.โฆษิต อ.ตากใบ จว.น.ธ.",
   "note": "หน.KOMPI",
   "picUrl": "3960200465399.jpg?alt=media&token=b81614c7-64fc-411c-acf3-dde270fcedd9"
 },
 {
   "nationid": 3960300048145,
   "name": "นายอาบัส เจ๊ะหะ/อาแว",
   "address": "๑๘๐/๑ บ.ดูกูสุเหร่าม.๗ต.บาเจาะ อ.บาเจาะ จว.น.ธ.",
   "note": "TL/SABOTAS\nบาเจาะ\n",
   "picUrl": "3960300048145.jpg?alt=media&token=5a71a266-3ca9-4279-9849-1fe6f3047d55"
 },
 {
   "nationid": 3960300318215,
   "name": "นายซุลกิฟลี มะสาแมง/ชางลี\n๓-๙๖๐๓-๐๐๓๑๘-๒๑-๕\n \n\n",
   "address": "๙๖ บ.ยือลาแป ม.๓ ต.สุวารี\nอ.รือเสาะ จว.น.ธ.\n",
   "note": "รศ.ขกท.สน.จชต.\n-เคยถูกควบคุมตัวมาแล้ว ๖ ครั้ง ล่าสุด เมื่อ ๔ เม.ย.๖๑\n",
   "picUrl": "3960300318215.jpg?alt=media&token=21b3ba4f-ac31-4888-9d71-95ddd372266d"
 },
 {
   "nationid": 3960300333664,
   "name": "นายมะรอมือลีกาแจกาซอ",
   "address": "๑๐๒/๑ บ.ตันหยง ม.๔\nต.บาเร๊ะใต้ อ.บาเจาะ จว.น.ธ.\n",
   "note": "หน.PLATONG\n อ.บาเจาะ, รอยต่อ \n ต.โคกเคียน อ.เมือง \n นราธิวาส\n",
   "picUrl": "3960300333664.jpg?alt=media&token=374257ca-754b-48ad-8101-bada5334beca"
 },
 {
   "nationid": 3960400124391,
   "name": "นายแวฮาสมิงบือซา/มิง",
   "address": "๔๑ ม.๕ บ.โต๊ะแม ต.ละหาร อ.ยี่งอ จว.น.ธ.\n\n\n",
   "note": "TL/SABOTAS\n\n",
   "picUrl": "3960400124391.jpg?alt=media&token=f4a894f0-8c48-4e04-a458-5dac8ce4a394"
 },
 {
   "nationid": 3960400142356,
   "name": "นายมะเสาอุดีบีรู",
   "address": "๒ ม.๓ ต.ลูโบะบือซา อ.ยี่งอ จว.น.ธ.",
   "note": "ฝ่าย EKONOMI ",
   "picUrl": "3960400142356.jpg?alt=media&token=4391d801-5f27-46a8-86b4-25c6171c62d4"
 },
 {
   "nationid": 3960400250841,
   "name": "นายมะสูดิงมะกาเซ็ง/เปาะซู",
   "address": "๔๐/๑ ม.๗ ต.จอเบาะ อ.ยี่งอ\nจว.น.ธ.\n",
   "note": "ฝ่าย POLITIK /JURU\n(จัดหาที่พัก/พยาบาล)\n",
   "picUrl": "3960400250841.jpg?alt=media&token=732345a4-a4c1-4385-8839-ad23feef83bd"
 },
 {
   "nationid": 3960400260561,
   "name": "นายมะดารีวาหลง",
   "address": "๔๗/๑ ม.๕ บ.กูยิ ต.ตะปอเยาะ \nอ.ยี่งอ จว.น.ธ.\n",
   "note": "หน.KOMPI\nเขตบาเจาะ (โซนภูเขา)\n -หน.PLATONG อ.ยี่งอ\n",
   "picUrl": "3960400260561.jpg?alt=media&token=2f788d2c-6bcc-4716-8c71-602e2aca6633"
 },
 {
   "nationid": 3960400260651,
   "name": "นายอับดุลมานะลาเต๊ะ",
   "address": "๔๘ ม.๕ ต.ตะปอเยาะ \nอ.ยี่งอ จว.น.ธ.\n",
   "note": "ฝ่าย PHA \n(ฮารีเมา)\n",
   "picUrl": "3960400260651.jpg?alt=media&token=ec290a92-e567-4743-9380-92998ed4e62e"
 },
 {
   "nationid": 3960400265440,
   "name": "นายอาลียะห์นีลา/กะยะห์",
   "address": "๑๑๑ บ.กูยิ ม.๕ ต.ตะปอเยาะ อ.ยี่งอ จว.น.ธ.",
   "note": "TL/SABOTAS\nบาเจาะ\n",
   "picUrl": "3960400265440.jpg?alt=media&token=0d870656-1823-4e68-83f2-8373f0f3579f"
 },
 {
   "nationid": 3960500010648,
   "name": "นายอัสมันสามะแม็ง",
   "address": "๑๓๙ ม.๑ บ.เจาะไอร้อง \nต.จวบ อ.เจาะไอร้อง จว.น.ธ.\n",
   "note": "ฝ่าย PHA \n(ฮารีเมา)\n\n",
   "picUrl": "3960500010648.png?alt=media&token=44761380-a388-4708-b965-27267b804225"
 },
 {
   "nationid": 3960500017913,
   "name": "นายดือราพาเจ๊ะอูมา ",
   "address": "๑๐๔/๒ บ.กูจิงลือปะ ม.๔\nต.เฉลิม อ.ระแงะ จว.น.ธ. \n",
   "note": "หน.PLATONG\nต.เฉลิม, \nต.มะรือโบออก\n\n",
   "picUrl": "3960500017913.jpg?alt=media&token=8584f1da-aea1-434e-a20f-424fd170ca44"
 },
 {
   "nationid": 3960500098961,
   "name": "นายยะยาเปาะแต",
   "address": "๑๒๗ ม.๘ ต.ตันหยงลิมอ \nอ.ระแงะ จว.น.ธ.\n",
   "note": "หน.REGU",
   "picUrl": "3960500098961.png?alt=media&token=f29c0c1e-20bd-4e9a-8a0a-0060e4d3e8e7"
 },
 {
   "nationid": 3960500099517,
   "name": "นายอาหามะ บูละ /เปาะซู",
   "address": "๑๑๖ บ.กายูมาตี ม.๘ \nต.ตันหยงลิมอ อ.ระแงะ จว.น.ธ.\n\n",
   "note": "TL/SABOTAS\nระแงะ (โซนทะเล)\n\n",
   "picUrl": "3960500099517.jpg?alt=media&token=1fa7d64e-f929-4b02-907a-fd5e1c54071f"
 },
 {
   "nationid": 3960500206046,
   "name": "นายอับดุลเลาะห์มะมิง/\nโซล่าเลาะ\n",
   "address": "๑๒๓ ม.๑๐ บ.โต๊ะเปาะฆะ \nต.ตันหยงมัส อ.ระแงะ จว.น.ธ.\n",
   "note": "หน.PLATONG\nต.ตันหยงมัส,\nต.บาโงสะโต\n",
   "picUrl": "3960500206046.jpg?alt=media&token=c430e34e-8ad5-419e-a0a0-7f9b03b3d907"
 },
 {
   "nationid": 3960500216360,
   "name": "นายอิสมาแอ ดอเล๊าะ",
   "address": "๔๐ ม.๑ บ.สาเม๊าะ ต.บองอ\nอ.ระแงะ จว.น.ธ.\n",
   "note": "COMMANDO\n(คอมมานโด)\n",
   "picUrl": "3960500216360.png?alt=media&token=9a829fbb-3109-441b-9518-6a007fc131c2"
 },
 {
   "nationid": 3960500225024,
   "name": "นายต่วนแซะ ต่วนกือจิ/เซะ",
   "address": "๑๔ ม.๒ บ.ลาแป ต.บองอ \nอ.ระแงะ จว.น.ธ.\n",
   "note": "ชำนาญประกอบระเบิด\n- TL/SABOTAS\nระแงะ (โซนภูเขา)\n",
   "picUrl": "3960500225024.jpg?alt=media&token=3ff12f24-52c5-4fff-ab9e-721708b78c8f"
 },
 {
   "nationid": 3960500231698,
   "name": "ต่วนมะ ยี่งอ/ตูแวมะ/สุกรี",
   "address": "๔๙/๑ บ.กูวิง ม.๕ ต.บานา \nอ.เมือง จว.ป.น. เดิมอยู่ ๗๕/๑ \nบ.ลาแป ม.๒ ต.บองอ อ.ระแงะ จว.น.ธ. \n",
   "note": "หน.KOMPI\nกือดา/เมือง\n",
   "picUrl": "3960500231698.jpg?alt=media&token=d6599887-ef9d-48b8-b3d5-a0e2b006db41"
 },
 {
   "nationid": 3960500235887,
   "name": "นายต่วนซอรีต่วนกือจิ",
   "address": "๑๓๐ บ.ลาแป ม.๒ ต.บองอ \nอ.ระแงะ จว.น.ธ.\n\n",
   "note": "ฝ่าย PERSONALIA\n(การฝึก)\n-หน.PLATONG\nต.ดุซงญอ \n\n",
   "picUrl": "3960500235887.jpg?alt=media&token=77aea66e-b969-4351-bf58-0b2f66316da3"
 },
 {
   "nationid": 3960500240236,
   "name": "นายสามะอุง หะยีดีเย๊าะดีย๊ะ",
   "address": "๔๓ บ.บัวทองใต้ ม.๙ ต.บ้านแหร อ.ธารโต จว.ย.ล. เดิมอยู่ ๒๗/๑ บ.กูตงม.๓ ต.บองอ อ.ระแงะ\nจว.น.ธ.\n",
   "note": "หน.REGU",
   "picUrl": "3960500240236.png?alt=media&token=cafe9a40-4cfe-446a-b1b5-d30e80fa6694"
 },
 {
   "nationid": 3960500260318,
   "name": "นายซุลกิฟลีมาน๊ะ ",
   "address": "๘ บ.ดารุลอิซาน ม.๑๔ \nต.บูกิต อ.เจาะไอร้อง จว.น.ธ.\n",
   "note": "ผบ.เขตทหารย่อย \n-ฝ่าย OPERASI\n(ยุทธการ)\n",
   "picUrl": "3960500260318.jpg?alt=media&token=77d5a3ba-617a-446a-884c-5b88812bde7d"
 },
 {
   "nationid": 3960500262183,
   "name": "นายสะอุดีเจ๊ะโด",
   "address": "๓๓ ม.๑ บ.เจาะเกาะ\nต.บูกิต อ.เจาะไอร้อง จว.น.ธ.\n",
   "note": "ฝ่าย PHA \n(ฮารีเมา)\n\n",
   "picUrl": "3960500262183.jpg?alt=media&token=cb75e0ff-7970-4fe8-a1e2-9cf8121d6a5c"
 },
 {
   "nationid": 3960500342284,
   "name": "นายมูฮามะซูลกิฟลีสือแม",
   "address": "๑๓๑ ม.๒ ต.บูกิต อ.เจาะไอร้อง \nจว.น.ธ.\n",
   "note": "หน.PLATONG \nเขตปาเสมัส\n",
   "picUrl": "3960500342284.jpg?alt=media&token=bd3abaa6-3088-4f11-a549-ef3994d50463"
 },
 {
   "nationid": 3960500396554,
   "name": "นายรอสดีดอแมะ/แบดี",
   "address": "๗๑ ม.๑ ต.บาโงสะโต อ.ระแงะ จว.น.ธ.",
   "note": "รอง ผบ.เขตทหาร",
   "picUrl": "3960500396554.jpg?alt=media&token=4b9eb41b-aa3c-427c-9bb9-6fbca66cff55"
 },
 {
   "nationid": 3960500397747,
   "name": "นายอุสมานแอสะ/เปาะซู",
   "address": "๘๕ ม.๑ ต.บาโงสะโต อ.ระแงะ\nจว.น.ธ.\n",
   "note": "ฝ่าย ULAMA",
   "picUrl": "3960500397747.jpg?alt=media&token=4696be3c-2a3f-473a-8813-7279a0e26635"
 },
 {
   "nationid": 3960500401576,
   "name": "นายอิสมะแอนิมะ\n(บอตอ/ปะดอรอนิง/เปาะนิ)\n",
   "address": "๖๙ บ.ตะโล๊ะเลาะ ม.๑ ต.บือมัง อ.รามัน จว.ย.ล.",
   "note": "ซถ.นายอัซมัน กาซา\n-คุมตัว ๑๐ ก.ย.๖๐\n-หลบหนีประกัน\n",
   "picUrl": "3960500401576.png?alt=media&token=515332d8-3fbe-4c20-bc5e-c7c1c619fa1c"
 },
 {
   "nationid": 3960500409435,
   "name": "นายยาการียาเลาะยีตา",
   "address": "๗๖ ม.๒ ต.บาโงสะโต \nอ.ระแงะ จว.น.ธ.\n",
   "note": "รายงานตัว \nศปก.ระแงะ\n-ปัจจุบันอยู่ มซ.\n",
   "picUrl": "3960500409435.png?alt=media&token=84e76353-2745-48e9-9e4d-56be501ab990"
 },
 {
   "nationid": 3960500431031,
   "name": "อับดุลตอเละ บาเย๊าะกาเซ๊ะ",
   "address": "๖๔/๒ ม.๔ บ.กูจิงลือปะ ต.เฉลิม อ.ระแงะ จว.น.ธ.",
   "note": "หน.REGU\n-ฝ่าย PHA \n(ฮารีเมา)\n",
   "picUrl": "3960500431031.jpg?alt=media&token=01700721-e80f-453d-9676-f5fe96588d98"
 },
 {
   "nationid": 3960500462832,
   "name": "นายอารงดือราแม/บาบอ",
   "address": "๑๓๑ ม.๘ บ.กูแบปูยู\nต.มะรือโบออก อ.เจาะไอร้อง จว.น.ธ.\n",
   "note": "รอง หน.KOMPI\n-ฝ่าย PERSONALIA\n(ฝ่ายจัดการฝึก)\n",
   "picUrl": "3960500462832.png?alt=media&token=8c1d8c03-53ee-4358-ae86-7ba40a7e4951"
 },
 {
   "nationid": 3960500463839,
   "name": "นายอับดุลการี ยูโซ๊ะ/ฟาติม",
   "address": "๑๔๓/๑ ม.๘ บ.กูแบปูยู\nต.มะรือโบออก อ.เจาะไอร้อง จว.น.ธ.\n",
   "note": "ฝ่าย PHA \n(ฮารีเมา)\n\n",
   "picUrl": "3960500463839.png?alt=media&token=4e0a6ca8-3688-4b1e-9e47-c054d9281abe"
 },
 {
   "nationid": 3960500479972,
   "name": "นายเด็งอาแวจิ",
   "address": "๑๒๙/๑ ม.๙ บ.ปาเร๊ะรูโบ๊ะ ต.มะรือโบออก \nอ.เจาะไอร้อง จว.น.ธ.\n",
   "note": "อดีตรองเลขานุการ ฝ่ายทหารDKP โครงสร้างเก่า)\n- คณะกรรมการทหาร/แดแว เมล์\n",
   "picUrl": "3960500479972.png?alt=media&token=29258ffa-8622-463c-a155-e6e8b2ab81ea"
 },
 {
   "nationid": 3960500499299,
   "name": "นายมูฮัมหมัดขาเดร์",
   "address": "๒ ม.๖ บ.บาโงกูโบ ต.บองอ \nอ.ระแงะ จว.น.ธ.\n",
   "note": "ฝ่าย POLITIK /JURU\n(จัดหาที่พัก/พยาบาล)\n",
   "picUrl": "3960500499299.jpg?alt=media&token=6401ecb8-48fa-42bf-9d15-239910373a74"
 },
 {
   "nationid": 3960500548443,
   "name": "นายสะมะอุงสุหลง",
   "address": "๑๗๑/๓ ม.๒ บ.ยานิงต.จวบ อ.เจาะไอร้อง จว.น.ธ.",
   "note": "อดีต หน.ฝ่ายทหาร DKP\n- อดีต ผบ.เขตทหารที่ ๒\n- แกนนำร่วมปล้นปืน พัน.พัฒนา ๔ เมื่อ ๔ ม.ค.๔๗\n- หลบหนีอยู่ มซ.,บรูไน\n",
   "picUrl": "3960500548443.png?alt=media&token=9ea65679-9131-4e15-8a40-d303ec9cc967"
 },
 {
   "nationid": 3960500562365,
   "name": "นายบือราเฮง ปะจูศาลา",
   "address": "๓๘/๒ ม.๔ ต.จวบอ.เจาะไอร้อง จว.น.ธ..",
   "note": " อดีตอิหม่ามประจำมัสยิด บ.ยานิง ม.๒ ต.จวบ\nอ.เจาะไอร้อง\n- อดีตอุสตาซ ร.ร.สัมพันธ์วิทยา ต.จวบ อ.เจาะไอร้อง\n- อดีต หน.ฝ่ายปกครอง (DKP)\n",
   "picUrl": "3960500562365.png?alt=media&token=9f6ac11b-f4a5-43fc-975b-08116c85bcdc"
 },
 {
   "nationid": 3960500646763,
   "name": "นายอาซือมิงปูเตะ",
   "address": "๑๓๖ ม.๒ ต.ตันหยงลิมอ \nอ.ระแงะ จว.น.ธ.\n",
   "note": "หน.REGU",
   "picUrl": "3960500646763.jpg?alt=media&token=68136e9c-72ed-429e-8d39-d1ff672496e0"
 },
 {
   "nationid": 3960500708394,
   "name": "นายมะนูเด็นสามะ/จูเด็ง",
   "address": "๑๕๒ บ.บูเก๊ะตาโมง ม.๗ ต.บูกิต\nอ.เจาะไอร้อง จว.น.ธ.\n",
   "note": "ผบ.เขตทหารที่ ๒",
   "picUrl": "3960500708394.jpg?alt=media&token=30e92570-cefe-4e18-be84-b405979b8af5"
 },
 {
   "nationid": 3960500717105,
   "name": "นายมูสยูวันเจ๊ะอาแซ/เจ๊ะกา",
   "address": "๒๖๕ ม.๑๒ บ.บูเก๊ะกือจิ ต.บูกิต \nอ.เจาะไอร้อง จว.น.ธ.\n",
   "note": "หน.PLATONG",
   "picUrl": "3960500717105.jpg?alt=media&token=11cc8423-8243-4ced-bcfe-f52b219efe02"
 },
 {
   "nationid": 3960500756674,
   "name": "นายอิบรอเฮ็มสูดี",
   "address": "๕๓/๑ ม.๕ บ.น้ำตก \nต./อ.สุคิริน จว.น.ธ.\n",
   "note": "หน.REGU",
   "picUrl": "3960500756674.jpg?alt=media&token=0e74984c-230a-4f2a-b99d-aa2c2ae35e66"
 },
 {
   "nationid": 3960500784554,
   "name": "นายอาแซเจ๊ะหลง/\nฮาซัน ตอยิบ/อาแซ ตอเยะ",
   "address": "๔๖ ม.๗ ต.บาโงสะโต \nอ.ระแงะ จว.น.ธ",
   "note": "ปัจจุบันอยู่ อ.ยือเต๊ะ รัฐตรังกานู มซ.\nหน.คณะผู้แทน BRN ในการพูดคุยสันติภาพกับคณะของ ลมช.\nควบตำแหน่งในDPP ด้วย",
   "picUrl": "3960500784554.jpg?alt=media&token=11bafc16-0e30-48a5-93fb-90e85afc23bc"
 },
 {
   "nationid": 3960500789033,
   "name": "นายอัสอารีลีเมาะ/รอฮิง",
   "address": "๘/๒ ม.๖ บ.บาโง ดุดุง ต.จวบ อ.เจาะไอร้อง จว.น.ธ.",
   "note": "TL/SABOTAS\nตากใบ\n",
   "picUrl": "3960500789033.jpg?alt=media&token=685841b0-cb32-47ff-a36f-30a48b6d4375"
 },
 {
   "nationid": 3960500858612,
   "name": "นายอับดุลอาซิสาและ",
   "address": " ๓๙ ถ.สิโรรส ๑๐\nต.สะเตง อ.เมือง จว.ย.ล. \n ปัจจุบันหลบหนีอยู่ มซ.\n",
   "note": " อดีตอุสตาซ ร.ร.ธรรมวิทยามูลนิธิ อ.เมือง จว.ย.ล.\n เดิมมีตำแหน่งเป็นกรรมการฝ่ายปกครองระดับแดอาเราะห์–เขตปกครอง/กัสยะลา\n",
   "picUrl": "3960500858612.png?alt=media&token=22870575-3f23-49ee-a6a5-a1eaadf96908"
 },
 {
   "nationid": 3960600013291,
   "name": "นายอับดุลเลาะสามามะ/\nอุตาซเลาะ จีนอ/อาบูนาเบร์/ เปาะซรากัม\n",
   "address": "๔๑ ม.๒ ต.สาวอ อ.รือเสาะ จว.น.ธ.",
   "note": "อดีตอุสตาซรร. มะอาหัดอิสลามียะห์/ปอเนาะบาลอ\n เป็นหนึ่งในผู้แทน BRN ที่ร่วมในพิธีลงนามแสดงเจตจำนงเพื่อพูดคุยสันติภาพเมื่อ ๒๘ ก.พ.๕๖\n",
   "picUrl": "3960600013291.png?alt=media&token=9552a379-747f-48b5-afe7-254944812982"
 },
 {
   "nationid": 3960600026309,
   "name": "นายมะดากี ดะอุแม/เปาะซีงอ",
   "address": "๑๖ ม.๓ บ.จือแร ต.สาวอ \nอ.รือเสาะ จว.น.ธ. \n",
   "note": "ครูสอนศาสนา รร.มะอาหัดอิสลามียะห์/ปอเนาะบาลอ อ.รามัน จว.ย.ล.\n- อดีตนายก อบต.สาวอ",
   "picUrl": "3960600026309.jpg?alt=media&token=c1bdfa6a-2ec3-4ee9-bb7a-57c1e26a10ed"
 },
 {
   "nationid": 3960600049058,
   "name": "นายอับดุลลาเต๊ะ เรียง",
   "address": "๗๖/๑ ม.๓ ต.เรียง อ.รือเสาะ \nจว.น.ธ.",
   "note": "นายอายุทธ อิตำ\nนายมะสดี รอมะ\nนายมะรีซัน สะตะ\nคุมตัว ๔ ส.ค.๕๙\nยอมรับให้ข้อมูล",
   "picUrl": "3960600049058.jpg?alt=media&token=e8f32fd8-5cd7-4537-bfce-c51cf95ad545"
 },
 {
   "nationid": 3960600081431,
   "name": "นายมะดาโอ๊ะแก่ต่อง",
   "address": "๑๒ บ.บูเกะนากอ ม.๒\nต.ลาโละ อ.รือเสาะ จว.น.ธ.\n",
   "note": " ประสานงานต่างประเทศ DKP (โครงสร้างเก่า)",
   "picUrl": "3960600081431.jpg?alt=media&token=2e0d69dd-15e3-4643-ac6f-c71bd1735a50"
 },
 {
   "nationid": 3960600163691,
   "name": "นายอีดือเระมาหะ ดอรอแม",
   "address": "๔๐/๔ ม.๕ บ.ลาโละ ต.ลาโละ อ.รือเสาะ จว.น.ธ. เดิมอยู่ ๒ ม.๕ บ.ลาโละ ต.ลาโละ อ.รือเสาะ จว.น.ธ. ",
   "note": "ฝ่าย LOGISTIK\n(จัดหา/เก็บซ่อนอาวุธ)\n",
   "picUrl": "3960600163691.jpg?alt=media&token=6d92ff7e-a89d-4a6a-a0f1-f604672a76ad"
 },
 {
   "nationid": 3960600190397,
   "name": "นายซารีซานอัมรีดือราแม",
   "address": "๓๙ ม.๖ บ.ล่องควน ต.คูหา \nอ.สะบ้าย้อย จว.ส.ข.",
   "note": "อดีต หน.KOMPI / TL / SABOTAS\n(ชำนาญด้านระเบิด)",
   "picUrl": "3960600190397.jpg?alt=media&token=a56d4e9c-7a33-488e-a531-08df3b844ce8"
 },
 {
   "nationid": 3960600289903,
   "name": "นายอับดุลการีหะแว",
   "address": "๑๗ ม.๕ บ.ลาโละ ต.ลาโละอ.รือเสาะ จว.น.ธ.",
   "note": "รายงานตัว \nศปก.รือเสาะ \n-ซถ.อิสมะแอ โตะ\n-ซถ.นายพิศาล สาแม\n",
   "picUrl": "3960600289903.jpg?alt=media&token=df7b3b86-30a8-44b9-9764-4d91fc2f08f1"
 },
 {
   "nationid": 3960600311798,
   "name": "นายอับดุลฮาดีอับดุลเลาะ",
   "address": "๓๒ ม.๙ ต.รือเสาะ อ.รือเสาะ จว.น.ธ.",
   "note": "ฝ่าย LOGISTIK",
   "picUrl": "3960600311798.jpg?alt=media&token=b1ccd659-f750-43a6-8087-36d8e84c14bd"
 },
 {
   "nationid": 3960600346818,
   "name": "ดุลละหะเล็งยามาสะกา",
   "address": "๓๑ ม.๔ ต.จะกว๊ะ อ.รามัน จว.ย.ล. (ปัจจุบันแยกเป็น ม.๕\nบ.ลีเซ็งใน/บ.พงยือเร๊ะ)\n",
   "note": "ผู้ประสานงาน\nฝ่าย EKONOMI\n\n",
   "picUrl": "3960600346818.png?alt=media&token=33465646-68fa-4536-98ba-415cc7a2cdaa"
 },
 {
   "nationid": 3960600399831,
   "name": "นายแวปะดอ เจ๊ะเต๊ะ",
   "address": "๕๑๐ บ.บลูกา ม.๑\nต.รือเสาะ อ.รือเสาะ จว.น.ธ.\n",
   "note": " อาศัยอยู่ในรัฐตรังกานูมซ.\nผู้แทน BRN ในการพูดคุยสันติภาพกับคณะของ ลมช.\n- หน.คณะผู้บริหาร สภาซูรอปาตานี (MARA PATANI)\n",
   "picUrl": "3960600399831.png?alt=media&token=78275c6a-1a21-44bd-b4cf-1a321ecf824a"
 },
 {
   "nationid": 3960600399849,
   "name": "นายดอรอแมเจ๊ะแต/\nดอแมยะบะ/\nอับดุลเราะห์มาน บินอับดุลเลาะ\n",
   "address": "๕๑๐ ม.๑ ต.รือเสาะออก \nอ.รือเสาะ จว.น.ธ.\n",
   "note": "กรรมการทหารฝ่ายความมั่นคง (โครงสร้างเก่า)",
   "picUrl": "3960600399849.jpg?alt=media&token=fea36ff5-ea98-4813-be7e-49773bd17b2c"
 },
 {
   "nationid": 3960600403501,
   "name": "นายมะสุดิง กาจิ",
   "address": "ที่อยู่เดิม ๕๖๕/๕ ม.๑ ต.รือเสาะออก \nอ.รือเสาะ จว.น.ธ.\nที่อยู่ปัจจุบัน ๔๓/๑ \nม.๒ ต.รือเสาะ อ.รือเสาะ\n",
   "note": "อดีตอุสตาซร.ร.วัฒนธรรมอิสลามพ่อมิ่ง ม.๓ ต.พ่อมิ่ง อ.ปะนาเระ จ.ปัตตานี (รู้จักกันในนาม “อุสตาซมะลอดิง”)\n สอนศาสนาตามปอเนาะต่างๆ และค้าขาย\nหน.ฝ่ายการเงิน DPP (โครงสร้างเดิม)\n",
   "picUrl": "3960600403501.png?alt=media&token=00a65bbb-57a8-4fbd-a439-054017224b31"
 },
 {
   "nationid": 3960700087016,
   "name": "นายฟีดาอีอับดุลลาเต๊ะ",
   "address": "๕๒ บ.ดูซงปาแย ม.๒ ต.เมาะมาวี\nอ.ยะรัง จว.ป.น. เดิมอยู่ ๓๙ \nบ.จือแรง ม.๕ ต.ตะมะยูง\nอ.ศรีสาคร จว.น.ธ.\n",
   "note": "หน.PLATONG \nเมาะมาวี, กอลำ \n(คู่บัดดี้นายมะรูดี ซิมะ\n",
   "picUrl": "3960700087016.jpg?alt=media&token=7479e6fa-6997-49e6-be18-55b09b9b2539"
 },
 {
   "nationid": 3960700118663,
   "name": "นายมูหัมมะดานียาตาลี",
   "address": "๖๓ ม.๓ บ.บีโล๊ะ ต.ซากอ \nอ.ศรีสาคร จว.น.ธ.\n",
   "note": "หน.ฝ่าย EKONOMI\n(เศรษฐกิจ)\n",
   "picUrl": "3960700118663.png?alt=media&token=fba8eb2c-ce42-4ec7-a59b-5b67ef2649c5"
 },
 {
   "nationid": 3960800270751,
   "name": "นายซราฮันฮามะ",
   "address": "๓๕/๑ ม.๗ ต.มาโมง \nอ.สุคิริน จว.น.ธ.\n",
   "note": "หน.REGU",
   "picUrl": "3960800270751.jpg?alt=media&token=15e95b5e-3fcc-4e2d-b2d2-7578f6e8cecd"
 },
 {
   "nationid": 3960800304442,
   "name": "นายแฟนดี ปะจู/แบดี",
   "address": "๒/๔ บ.ตือมายู ม.๑ \nต.เอราวัณ อ.แว้ง จว.น.ธ.\n\n",
   "note": "TL/SABOTAS\nแว้ง\n",
   "picUrl": "3960800304442.jpg?alt=media&token=9d9e6a8c-3316-4388-a13a-d9d2948af87f"
 },
 {
   "nationid": 3961100070598,
   "name": "นายมะเยะยาซิง/เยะ",
   "address": "๕๗/๒ บ.ละหารเหนือ ม.๘ต.ปะลุรู อ.สุไหงปาดี ",
   "note": "TL/SABOTAS\nตากใบ\n",
   "picUrl": "3961100070598.jpg?alt=media&token=a7decb43-31d7-437d-882e-e1962a057130"
 },
 {
   "nationid": 3961100075328,
   "name": "นายยาการียา สาและ/ลงยา",
   "address": "๒๖/๓ บ.กม.๓๘ ม.๖\nต.อัยเยอร์เวง อ.เบตง จว.ย.ล.\n",
   "note": "ฝ่าย PHA",
   "picUrl": "3961100075328.jpg?alt=media&token=ba68bea3-1e36-46ee-aca9-5c07285640a6"
 },
 {
   "nationid": 3961100184984,
   "name": "นายอารีพห์โซ๊ะโก",
   "address": "๔๑ ม.๑ ต.กาวะ อ.สุไหงปาดี จว.น.ธ.",
   "note": "ฝ่ายประสานงาน",
   "picUrl": "3961100184984.jpg?alt=media&token=7b500233-d103-41b0-a6c4-46b71a24db68"
 },
 {
   "nationid": 3961100193894,
   "name": "นายลุกมารณ์อูเซ็ง",
   "address": "๓๗ ม.๒ บ.กาวะ ต.กาวะ \nอ.สุไหงปาดี จว.น.ธ.\n",
   "note": "หน.PLATONG",
   "picUrl": "3961100193894.jpg?alt=media&token=a367115b-1ea4-421a-8535-fc278c2fd47b"
 },
 {
   "nationid": 3961100200441,
   "name": "นายซูรีมะอารง",
   "address": "๒๒๙ ม.๒ บ.กาวะต.กาวะ \nอ.สุไหงปาดี จว.น.ธ.\n\n",
   "note": "หน.KOMPI\n -หน.PLATONG\n",
   "picUrl": "3961100200441.jpg?alt=media&token=06eef5ed-38b2-4846-9a45-8b2a710eabc7"
 },
 {
   "nationid": 3961100226075,
   "name": "นายอับดุลเลาะอาแวยูโซ๊ะ",
   "address": "๑๗๑ บ.ไอร์ลาฆอ (บ้านบริวารบ.ไอร์โซร์ ม.๕ ต.ช้างเผือกอ.จะแนะ จว.น.ธ. (บ้านภรรยา) เดิมอยู่ ๑๒/๑ บ.มะนังกาแยงม.๓ ต.จะแนะ อ.จะแนะ จว.น.ธ.",
   "note": "หน.PLATONG\nต.ช้างเผือก\n",
   "picUrl": "3961100226075.jpg?alt=media&token=1bf8e23b-b15f-4804-9d05-c6b187aae69b"
 },
 {
   "nationid": 3961100269793,
   "name": "นายอิสมะแอมะหนุ๊",
   "address": "๕๙ บ.บือราแง ม.๓ ต.โต๊ะเด็ง\nอ.สุไหงปาดี จว.น.ธ.\n",
   "note": "หน.PLATONG",
   "picUrl": "3961100269793.jpg?alt=media&token=3ee1fa7f-89bc-4410-91ee-939d9bb225be"
 },
 {
   "nationid": 3961100347921,
   "name": "มูฮัมหมัดใบฮาดี วาเฮ็ง/แบกี",
   "address": " ๘๓/๑ ม.๕ ต.ริโก๋\nอ.สุไหงปาดี จว.น.ธ.\n ปัจจุบันหลบหนีอยู่ มซ.\n",
   "note": "อดีต ผบ.เขตทหารที่ ๒",
   "picUrl": "3961100347921.jpg?alt=media&token=cf02e736-1bba-400c-b738-3763589eaf51"
 },
 {
   "nationid": 3961100426287,
   "name": "นายอับดุลเล๊าะแซมซูดิง",
   "address": "๕๘/๑ ม.๔ บ.ไอบาตู ต.โต๊ะเด็ง\nอ.สุไหงปาดี จว.น.ธ.\n",
   "note": "COMMANDO\n(คอมมานโด)\n",
   "picUrl": "3961100426287.jpg?alt=media&token=4083e1e0-f80a-4557-864a-37fe38c62a90"
 },
 {
   "nationid": 3961200027261,
   "name": "นายอับดุลฮากิมปูตะ",
   "address": "๑๒ ม.๖ บ.ไอร์กรอส ต.จะแนะ อ.จะแนะ จว.น.ธ.",
   "note": "ผบ.เขตทหารย่อย \n-หน.Kompi \n",
   "picUrl": "3961200027261.jpg?alt=media&token=14d27a5f-923a-48c0-adf7-04b8e6ba3601"
 },
 {
   "nationid": 3961200097145,
   "name": "นายยาการียาอาแวกือจิ",
   "address": "๔๔/๑๑ ม.๔ บ.บองอ ต.บองอ\nอ.ระแงะ จว.น.ธ.\n",
   "note": "ฝ่าย PHA \n(ฮารีเมา)\n",
   "picUrl": "3961200097145.jpg?alt=media&token=09e88c2f-9aaa-4455-abd5-bea4516728e9"
 },
 {
   "nationid": 3961200146880,
   "name": "นายมาหะมะอาบู",
   "address": "๖๑/๑ ม.๔ บ.ปารี ต.จะแนะ\nอ.จะแนะ จว.น.ธ.\n",
   "note": "ปลดหมาย พ.ร.ก.\nปัจจุบันอยู่มาเลเซีย\n(ข้อมูลเดิม)\n\n",
   "picUrl": "3961200146880.jpg?alt=media&token=93ee2985-8b76-4b17-9e70-bdaefa66d69f"
 },
 {
   "nationid": 3961200157148,
   "name": "นายอาซีซันมะดาแฮ",
   "address": "๕๙ บ.บูเกะบาลอ(ตือกอ) ม.๗ ต./อ.จะแนะ จว.น.ธ.",
   "note": "คุมตัว ๒๕ พ.ค.๕๘",
   "picUrl": "3961200157148.jpg?alt=media&token=9435f256-85af-4cfc-bf4d-4f1c4f54cd40"
 },
 {
   "nationid": 3961200158021,
   "name": "นายแวอารงเจ๊ะอูมา",
   "address": "๗๒ ม.๑๐ ต.จะแนะ \nอ.จะแนะ จว.น.ธ.\n",
   "note": "หน.KOMPI\nเขตจะแนะ (โซนภูเขา)\n",
   "picUrl": "3961200158021.jpg?alt=media&token=ae981d24-baad-42f9-a546-b70f21ad2087"
 },
 {
   "nationid": 3961200159213,
   "name": "นายมาหะมุมามะ\n(ปะดอมามุ/ปะดอมุ ตือกอ)\n",
   "address": " ๙๒ ม.๗ บ.ตือกอ\nต.จะแนะ อ.จะแนะ \n ปัจจุบันหลบหนีอยู่ มซ. \n",
   "note": " จบปริญญาตรี สาขากฎหมายอิสลามสถาบันศาสนาอิสลามแห่งชาติซูนัน กูนุงจาตี บันดุง จ.ชวาตะวันตก อินโดนีเซีย\n อดีตอุสตาซร.ร.สัมพันธ์วิทยา อ.เจาะไอร้อง ฯ หน.ฝ่ายการเงิน DKP (โครงสร้างเดิม)\n",
   "picUrl": "3961200159213.png?alt=media&token=6a81fea4-5e89-44f3-bb02-f2d32214a463"
 },
 {
   "nationid": 3961200162516,
   "name": "นายสะอารอวีสะมะแอ",
   "address": "๑๗๑ บ.ไอร์ลาฆอ (บ้านบริวารบ.ไอร์โซร์ ม.๕ ต.ช้างเผือกอ.จะแนะ จว.น.ธ. (บ้านภรรยา) เดิมอยู่ ๑๒/๑ บ.มะนังกาแยงม.๓ ต.จะแนะ อ.จะแนะ จว.น.ธ.",
   "note": "หนีประกันชั้นศาล",
   "picUrl": "3961200162516.jpg?alt=media&token=4b65fb41-8ea3-471f-958a-11ea92e3dbe8"
 },
 {
   "nationid": 39660400011910,
   "name": "นายแวอุมามามุ",
   "address": "๒๔/๔ บ.มูแบ ม.๑ ต.ยี่งอ \nอ.ยี่งอ จว.น.ธ.\n",
   "note": "ฝ่าย PHA \n(ฮารีเมา)\n\n",
   "picUrl": "39660400011910.png?alt=media&token=a2c7e0c6-04da-4c2a-87c2-02cf4e50fe4a"
 },
 {
   "nationid": 3969900100731,
   "name": "นายไซนูเด็งหะยีอาแด",
   "address": "๕๘/๒ ถ.ยะกัง ๑ ต.บางนาค อ.เมือง จว.น.ธ.",
   "note": "TL/SABOTAS\n\n\n",
   "picUrl": "3969900100731.jpg?alt=media&token=2803d48c-315b-4914-bbd8-42c3af8ee6a5"
 },
 {
   "nationid": 3969900145701,
   "name": "นายวาเหะ หะยีอาแซ/ครูวาเหะ\n(อับดุลวาเฮะ อูมา)\n",
   "address": "๓๔/๒๗ ถ.ยะกัง ๒ เทศบาลเมืองนราธิวาส อ.เมือง จว.น.ธ.",
   "note": " อดีตครูสอนอิสลามศึกษา \nร.ร.นราสิขาลัย อ.เมืองน.ธ.\n อดีตผู้จัดการ ร.ร.บูกิตอิสลา",
   "picUrl": "3969900145701.jpg?alt=media&token=73a8b5d3-98d9-49bf-9854-a9b9d0fec2ea"
 },
 {
   "nationid": 4950200001153,
   "name": "นายแวหามะมะสีละ",
   "address": "๑๐/๗ ม.๔ ต.จะกว๊ะ อ.รามัน จว.ย.ล.",
   "note": "หน.ฝ่าย POLITIK \n(การเมือง / การข่าว)\n",
   "picUrl": "4950200001153.jpg?alt=media&token=0012c079-3e23-4c4b-96a0-f369831dbd03"
 },
 {
   "nationid": 5900500029639,
   "name": "นายอดุลย์ มุณี",
   "address": "๓๙/๑ ม.๓ ถ.เทพาต. /อ.เทพา จว.ส.ข.\nเคยเช่าบ้านอาศัยอยู่ที่ถ.สิโรรส ๑๐ ตลาดเก่า อ.เมือง จ.ยะลา\n(หลบหนีอยู่ มซ.)\n",
   "note": "",
   "picUrl": "5900500029639.png?alt=media&token=e76b1884-64d6-491f-b29c-b38fb9a79581"
 },
 {
   "nationid": 5940100017412,
   "name": "นายมะโตะบู",
   "address": "๘ ม.๓ บ.ซาไก ต.บ้านแหร\nอ.ธารโต จว.ย.ล.\n",
   "note": "หน.KOMPI",
   "picUrl": "5940100017412.jpg?alt=media&token=a972a984-054e-4b24-8174-fcd97de7f68e"
 },
 {
   "nationid": 5940100018842,
   "name": "นายอิสยาซะห์หะแย",
   "address": "๑๓ บ.กอแลปิเละ ม.๖ \nต.ปะกาฮะรัง อ.เมือง จว.ป.น.",
   "note": "นายสะรี กอตอ\nนายอับดุลรอเซ๊ะ กูเตะ\nนายสาการียา สาตำ\nนายโมหะมัด อาแว\nนายอัซมัน กาซา\nนายซาบาหรีเจะอาลี",
   "picUrl": "5940100018842.jpg?alt=media&token=bf2fb249-3ea0-4ebe-9661-47d1735b84f7"
 },
 {
   "nationid": 5940100021291,
   "name": "มูฮำหมัดซอบรีหยีมามุ",
   "address": "๗๖/๒ ม.๑ ต.รูสะมีแล\nอ.เมือง จว.ป.น.\n",
   "note": "TL/SABOTAS\nกือดา/เมือง\n",
   "picUrl": "5940100021291.jpg?alt=media&token=4f06b909-8ef8-4676-a598-4dc455a2d00a"
 },
 {
   "nationid": 5940299004573,
   "name": "นายมะราวีสารี/มะตอวี ",
   "address": "๑๘๑/๒ บ.ตุปะ ม.๕ \nต.ควนโนรี อ.โคกโพธิ์ จว.ป.น.",
   "note": "คุมตัว ๒๑ ต.ค.๕๘\n-ซถ.แวรอซะ บือราเฮง",
   "picUrl": "5940299004573.jpg?alt=media&token=f22d566a-317b-4522-980e-d57cda017a85"
 },
 {
   "nationid": 5940300020638,
   "name": "นายฟาเดลเสาะหมาน",
   "address": "๙ บ.ทุ่งยาว ม.๖ ต.โคกโพธิ์ \nอ.โคกโพธิ์ จว.ป.น.",
   "note": "นายสาการียา สาตำ \nคุมตัว ๒๒ เม.ย.๕๖\nรายงานตัว ๒๐ ก.ค.๕๘\nยอมรับ/ให้ข้อมูล",
   "picUrl": "5940300020638.jpg?alt=media&token=1c428527-2eab-4d2b-8715-b499c746d387"
 },
 {
   "nationid": 5940300027373,
   "name": "นายอับดุลฮากิม หะยีลาเปะ",
   "address": "๒/๑๖ บ.ใหม่ทุ่งนเรนทร์ ม.๙ \nต.บ่อทอง อ.หนองจิก จว.ป.น.",
   "note": "คุมตัว ๑๐ มี.ค.๖๐\nซ้ำ (ไม่นับยอด)",
   "picUrl": "5940300027373.jpg?alt=media&token=39bab5db-a830-4f59-be5d-ae8672a67263"
 },
 {
   "nationid": 5940700002114,
   "name": "นายรออีซะ แวอาแซ/แบรู",
   "address": "๑๙ ม.๒ บ.กาแระ ต.มะนังดาลำอ.สายบุรี จว.ป.น.",
   "note": "รับผิดชอบ\nอ.กะพ้อ\n",
   "picUrl": "5940700002114.jpg?alt=media&token=bc96c769-1c85-4dfb-a65d-963f62a9daed"
 },
 {
   "nationid": 5941000017292,
   "name": "นายอิลยาสเจ๊ะดือเร๊ะ/จิงจัง",
   "address": "๓๖ บ.เกาะบาตอ ม.๕ \nต.เมาะมาวี อ.ยะรัง จว.ป.น.\n",
   "note": "TL/SABOTAS\nPLATONG เมาะมาวี\n(ฝ่ายผลิตระเบิด)\n",
   "picUrl": "5941000017292.jpg?alt=media&token=35d442ca-ce69-4455-9d4f-0b678e670f7b"
 },
 {
   "nationid": 5941000020501,
   "name": "นายลุกมานมีนา",
   "address": "๙๔/๑ บ.เกาะเปาะใต้ ม.๒ \nต.เกาะเปาะ อ.หนองจิก จว.ป.น.",
   "note": "นายหาเมล ดามิ\nปัจจุบันกำลังศึกษาอยู่ประเทศอินโดนีเซีย",
   "picUrl": "5941000020501.jpg?alt=media&token=12220236-4433-419c-91ec-4743e402641f"
 },
 {
   "nationid": 5941099006875,
   "name": "นายอิบรอเฮ็มเจ๊ะหะ",
   "address": "๒๙๓/๒๙ ม.๗ บ.คอกช้าง\nต.แม่หวาด อ.ธารโต จว.ย.ล.\n",
   "note": "ฝ่าย PHA \n-หน.REGU คอกช้าง\n",
   "picUrl": "5941099006875.png?alt=media&token=814fcf86-777b-43d2-a3dd-c9064dfeb5da"
 },
 {
   "nationid": 5950300031672,
   "name": "นายอับดุลเลาะบาเน็ง",
   "address": "๗๘/๑ ม.๔ บ.บันนังกูแว\nต./อ.บันนังสตา จว.ย.ล.\n",
   "note": "COMMANDO / PHA ",
   "picUrl": "5950300031672.png?alt=media&token=6d213da2-3b84-43d4-8df8-5c91b8b28817"
 },
 {
   "nationid": 5950300033667,
   "name": "นายรอซะยะโกะ/แบยะ",
   "address": "๒๐ บ.บาเจาะ ม.๒ ต.บาเจาะ\nอ.บันนังสตา จว.ย.ล.\n",
   "note": "ฝ่าย LOGISTIK",
   "picUrl": "5950300033667.png?alt=media&token=ecc6ceaf-2227-4b22-90a9-a587bcb3adde"
 },
 {
   "nationid": 5950301047882,
   "name": "นายอับดุลเลาะ ตาเป๊าะโต๊ะ",
   "address": "๑๕ ม.๙ บ.เจาะบันตัง\nต./อ.บันนังสตา จว.ย.ล.\n",
   "note": "หน.KOMPI",
   "picUrl": "5950301047882.jpg?alt=media&token=ff4a3bb8-806d-434b-8724-27c0caa2d92d"
 },
 {
   "nationid": 5950600022504,
   "name": "นายการียากาเต๊ะ",
   "address": "๔๔/๑ บ.บารู ม.๙ \nต.บันนังสตา อ.บันนังสตา จว.ย.ล.",
   "note": "ซถ.มุสตอฟา อาลีมามะ",
   "picUrl": "5950600022504.jpg?alt=media&token=c47954c1-512c-4134-af5d-ac34a5eff576"
 },
 {
   "nationid": 5950699001831,
   "name": "นายอาลียัสมองมะแซ/ซอฟะ",
   "address": "๑๒/๑ บ.บาเฮ ม.๔ ต.บือมัง\nอ.รามัน จว.ย.ล.\n",
   "note": "หน.PLATONG\nต.กอตอตือร๊ะ, \nต.กายูบอเกาะ\n(บางส่วน)\n",
   "picUrl": "5950699001831.png?alt=media&token=09a42028-f491-4de3-a44b-58e61f00ccd8"
 },
 {
   "nationid": 5950699002128,
   "name": "นายซับรีมาหามุ/แช",
   "address": "๒๐ บ.กาดือแป ม.๕ \nต.กอตอตือร๊ะ อ.รามัน จว.ย.ล.\n",
   "note": "ฝ่าย LOGISTIK",
   "picUrl": "5950699002128.png?alt=media&token=461c71b4-099b-430b-bbab-59919b75a79e"
 },
 {
   "nationid": 5959900003431,
   "name": "นายมะยากีเปาะกียา/เย๊ะ",
   "address": "๗๕ ซอยมัสยิด ๑ ถ.สิโรรส\nต.สะเตง อ.เมือง จว.ย.ล.\n",
   "note": "อุซตาซ รร.ธรรม ฯ",
   "picUrl": "5959900003431.jpg?alt=media&token=8697c21d-fe52-4bae-8af2-7c2fd0a42bf1"
 },
 {
   "nationid": 5960500023401,
   "name": "นายกูลีลือบา",
   "address": "๘ ม.๑ บ.สาเม๊าะ ต.บองอ\nอ.ระแงะ จว.น.ธ.\n",
   "note": "หน.PLATONG \nเขตกำปงบารู\n-ฝ่าย PERSONALIA\n(ฝ่ายจัดการฝึก)\n",
   "picUrl": "5960500023401.jpg?alt=media&token=a4195a78-a817-4ac9-94a0-b1875d5918a2"
 },
 {
   "nationid": 5960500028101,
   "name": "นายอำรันมิง/ปะจู ตันหยงมัส",
   "address": "๔/๑ ม.๕ บ.ตราแดะ ต.บาโงสะโต อ.ระแงะ จว.น.ธ.",
   "note": "หน.REGU",
   "picUrl": "5960500028101.jpg?alt=media&token=c4b2626d-35f5-4ae1-b9d8-badf08e85def"
 },
 {
   "nationid": 5960600002279,
   "name": "อับดุลหะเล็ง ดอเล๊าะ/ลาโย๊ะ",
   "address": "๒๔/๑ บ.ไอร์กลูแป ม.๒ \nต.โคกสะตอ อ.รือเสาะ จว.น.ธ\n",
   "note": "ฝ่าย LOGISTIK",
   "picUrl": "5960600002279.jpg?alt=media&token=ed8c6e3e-4a60-4cad-9733-7f98ce7aee57"
 },
 {
   "nationid": 5960600016156,
   "name": "นายซิมะแซ/ปูอะ/เตาปุ๊",
   "address": "๑๗/๑ ม.๙ บ.พงยือติ \nต.ลาโละ อ.รือเสาะ จว.น.ธ. \n\n\n",
   "note": "TL/SABOTAS\nรือเสาะ\n",
   "picUrl": "5960600016156.jpg?alt=media&token=8dbad0c3-3eff-462d-9028-7f5bc4f6ac4f"
 },
 {
   "nationid": 5960600023179,
   "name": "นายอับดุลเล๊าะยูนุ๊/จิมะ",
   "address": "๒๐/๗ ม.๗ บ.สะโลว์ \nต./อ.รือเสาะ จว.น.ธ.\n",
   "note": "ฝ่าย EKONOMI",
   "picUrl": "5960600023179.jpg?alt=media&token=a92b6896-b6a6-4652-bfd0-e9f2b3d36ad8"
 },
 {
   "nationid": 5961100005628,
   "name": "นายซูกรีมันกูบารู",
   "address": "๓๘/๑ ม.๘ ต.ปะลุรู\nอ.สุไหงปาดี จว.น.ธ.\n",
   "note": "TL/SABOTAS\nตากใบ\n",
   "picUrl": "5961100005628.jpg?alt=media&token=665063f2-864b-4fc9-9f2e-c7a3798ffb25"
 }
]
';
		
		//-------------------------------------------
		switch ($explodeText[0]) { 
			case '#newPersonData':
                                $opts = array('http' => array( 'method' => "POST",
                                          'header' => "Content-type: application/json",
                                          'content' => $newPersonData
                                           )
                                        );
                                $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey='.MLAB_API_KEY.'';
                                $context = stream_context_create($opts);
                                $returnValue = file_get_contents($url,false,$context);
				if($returnValue){
				       $textReplyMessage= "OK";
				     
				}else{
				       $textReplyMessage= "NO";
				}
				
                                       $textMessage = new TextMessageBuilder($textReplyMessage);
	                               $multiMessage->add($textMessage);
				       $replyData = $multiMessage;
				break;
			case '#p':
				if (!is_null($explodeText[1])){
			          $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey='.MLAB_API_KEY.'&q={"nationid":"'.$explodeText[1].'"}');
                                  $data = json_decode($json);
                                  $isData=sizeof($data);
                                 if($isData >0){
                                    $count=1;
                                    foreach($data as $rec){
	                               $count++;
                                       $textReplyMessage= "\nหมายเลข ปชช. ".$rec->nationid."\nชื่อ".$rec->name."\nที่อยู่".$rec->address."\nหมายเหตุ".$rec->note;
                                       $textMessage = new TextMessageBuilder($textReplyMessage);
	                               $multiMessage->add($textMessage);
				       //$log_note= $log_note.$textReplyMessage;
	                              if (!is_null($rec->picUrl)){
	                               $picFullSize = "https://firebasestorage.googleapis.com/v0/b/carlicenseplate.appspot.com/o/$rec->picUrl";
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
				      }else{ 
	                               $picFullSize = "https://firebasestorage.googleapis.com/v0/b/carlicenseplate.appspot.com/o/demo_person.png?alt=media&token=0e0da7f2-ecbd-4751-9a97-2fe9f52fe663";
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
				      }
			               $replyData = $multiMessage;
                                    }//end for each
                                 }else{ //$isData <0  ไม่พบข้อมูลที่ค้นหา
                                   $textReplyMessage= "ไม่พบ ".$explodeText[1]."  ในฐานข้อมูลของหน่วย";
	                           $textMessage = new TextMessageBuilder($textReplyMessage);
	                           $multiMessage->add($textMessage);
			           $replyData = $multiMessage;
                                   } // end $isData>0
				}else{ // no $explodeText[1]
			          $textReplyMessage= "ให้ข้อมูลสำหรับการตรวจสอบบุคคลไม่ครบค่ะ";
			          $textMessage = new TextMessageBuilder($textReplyMessage);
			          $multiMessage->add($textMessage);
			          $replyData = $multiMessage;
		                }// end !is_null($explodeText[1])
				//$log_note=$log_note."\n User select #p ".$textReplyMessage;
			        break;
                                                                    					
			    case '#c':
				if (!is_null($explodeText[1])){
			          $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/register_south?apiKey='.MLAB_API_KEY.'&q={"license_plate":"'.$explodeText[1].'"}');
                                  $data = json_decode($json);
                                  $isData=sizeof($data);
                                 if($isData >0){
                                    $count=1;
                                    foreach($data as $rec){
	                               $count++;
                                       $textReplyMessage= "\n ทะเบียน ".$rec->license_plate."\nยี่ห้อ".$rec->brand."\nรุ่น".$rec->model."\nสี".$rec->color."\nผู้ครอบครอง ".$rec->user."\nประวัติ".$rec->note."\nหากข้อมูลรถไม่เป็นไปตามนี้ให้สงสัยว่าทะเบียนปลอม";
                                       $textMessage = new TextMessageBuilder($textReplyMessage);
	                               $multiMessage->add($textMessage);
				       //$log_note= $log_note.$textReplyMessage;
	                              if (!is_null($rec->picUrl)){
	                               $picFullSize = "https://firebasestorage.googleapis.com/v0/b/lisa-77436.appspot.com/o/$rec->picUrl";
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
				      }else{
				       $picFullSize = "https://firebasestorage.googleapis.com/v0/b/lisa-77436.appspot.com/o/carsImage%2Fdemo_car.png?alt=media&token=e183745a-5fa0-41b7-89b4-d863c572adc3";
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
				      }
			               $replyData = $multiMessage;
                                    }//end for each
                                 }else{ //$isData <0  ไม่พบข้อมูลที่ค้นหา
                                   $textReplyMessage= "ไม่พบ ".$explodeText[1]."  ในฐานข้อมูลของหน่วย";
	                           $textMessage = new TextMessageBuilder($textReplyMessage);
	                           $multiMessage->add($textMessage);
			           $replyData = $multiMessage;
                                   } // end $isData>0
				}else{ // no $explodeText[1]
			          $textReplyMessage= "ให้ข้อมูลสำหรับการตรวจสอบยานพาหนะไม่ครบค่ะ";
			          $textMessage = new TextMessageBuilder($textReplyMessage);
			          $multiMessage->add($textMessage);
			          $replyData = $multiMessage;
		                }// end !is_null($explodeText[1])
				//$log_note=$log_note."\n User select #p ".$textReplyMessage;
			        break;
				case '#f':
	                             $json = file_get_contents('https://api.mlab.com/api/1/databases/crma51/collections/phonebook?apiKey='.MLAB_API_KEY.'&q={"$or":[{"name":{"$regex":"'.$explodeText[1].'"}},{"lastname":{"$regex":"'.$explodeText[1].'"}},{"nickname":{"$regex":"'.$explodeText[1].'"}},{"nickname2":{"$regex":"'.$explodeText[1].'"}},{"position":{"$regex":"'.$explodeText[1].'"}}]}');
                                     $data = json_decode($json);
                                     $isData=sizeof($data);
			             $count = 1;
                                     if($isData >0){
		                       $textReplyMessage = "คุณ".$displayName."\n";
		                       $hasImageUrlStatus = false;
                                       foreach($data as $rec){
                                         $textReplyMessage= $textReplyMessage.$count.' '.$rec->rank.$rec->name.' '.$rec->lastname.' ('.$rec->position.' '.$rec->deploy_position.') '.$rec->Email.' โทร '.$rec->Tel1." ค่ะ\n\n";
				         if(isset($rec->Image) and (!$hasImageUrlStatus) and ($count<5)){
			                  $imageUrlStatus=true;
		 	                  $imageUrl="https://www.hooq.info/wp-content/uploads/".$rec->Image;
	                                  $imageMessage = new ImageMessageBuilder($imageUrl,$imageUrl);
	                                  $multiMessage->add($imageMessage);
		                            }
			                  $count++;
                                         }//end for each
		                         $textMessage = new TextMessageBuilder($textReplyMessage);
		                         $multiMessage->add($textMessage);
		                         $founduser= 1;
	                              }else{
		                       $founduser= NULL;
			               $textReplyMessage="\nไม่พบข้อมูลใน ฐานข้อมูลบุคคล\n";
	                               }
				
                                       $json2 = file_get_contents('https://api.mlab.com/api/1/databases/crma51/collections/user_register?apiKey='.MLAB_API_KEY.'&q={"userName":{"$regex":"'.$explodeText[1].'"}}');
			               $data2 = json_decode($json2);
                                       $isData2=sizeof($data2);
                                       if($isData2 >0){
		                         $textReplyMessage2 = "ตรวจสอบในฐานข้อมูล register ใหม่\n";
		                         $hasImageUrlStatus = false;
                                         foreach($data2 as $rec2){
                                            $textReplyMessage2= $textReplyMessage2.$count.'. '.$rec2->userName."\n\n";                                  	   
			                    $count++;
                                             }//end for each
		                         $textMessage2 = new TextMessageBuilder($textReplyMessage2);
		                         $multiMessage->add($textMessage2);
		                         $founduser2= 1;
	                              }else{//don't found data
				         $founduser2=NULL;
					 $textReplyMessage2="\nไม่พบข้อมูลใน Register\n";
				         }
		               if((is_null($founduser)) and (is_null($founduser2))) {
				$textReplyMessage2= $textReplyMessage.$textReplyMessage2."\nลิซ่า หาชื่อ ".$explodeText[1]." ไม่พบค่ะ".$founduser.$founduser2;
		                $textMessage2 = new TextMessageBuilder($textReplyMessage2);
		                $multiMessage->add($textMessage2);
	                       }
		                $replyData = $multiMessage;
                   break;
			   case '#lisa':
				if(!isset($explodeText[2])){ // just question, 
				$json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/hooqbot?apiKey='.MLAB_API_KEY.'&q={"question":"'.$explodeText[1].'"}');
                                $data = json_decode($json);
                                $isData=sizeof($data);
                                if($isData >0){
                                   foreach($data as $rec){
                                           $textReplyMessage= $textReplyMessage."\n".$explodeText[1]." คือ\n".$rec->answer."\n";
                                           }//end for each
				    $textMessage = new TextMessageBuilder($textReplyMessage);
		                    $multiMessage->add($textMessage);
					$randNumber=rand(1,400);$picUrl=strval($randNumber);
					$picFullSize = 'https://www.hooq.info/photos/'.$picUrl.'.jpg';
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
					$randNumber=$randNumber+1;$picUrl=strval($randNumber);
					$picFullSize = 'https://www.hooq.info/photos/'.$picUrl.'.jpg';
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
					$randNumber=$randNumber+1;$picUrl=strval($randNumber);
					$picFullSize = 'https://www.hooq.info/photos/'.$picUrl.'.jpg';
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
		                    $replyData = $multiMessage;
                                    }
				}else{// no answer
                                //Post New Data
		                $indexCount=1;$answer='';
	                        foreach($explodeText as $rec){
		                       $indexCount++;
		                       if($indexCount>1){
		                           $answer= $answer." ".$explodeText[$indexCount];
		                          }
	                                }
                                $newData = json_encode(array('question' => $explodeText[1],'answer'=> $answer) );
                                $opts = array('http' => array( 'method' => "POST",
                                          'header' => "Content-type: application/json",
                                          'content' => $newData
                                           )
                                        );
                                $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/hooqbot?apiKey='.MLAB_API_KEY.'';
                                $context = stream_context_create($opts);
                                $returnValue = file_get_contents($url,false,$context);
                                       if($returnValue){
		                          $textReplyMessage= $textReplyMessage."\nขอบคุณที่สอนลิซ่าค่ะ";
		                          $textReplyMessage= $textReplyMessage."\nลิซ่าจำได้แล้วว่า ".$explodeText[1]." คือ ".$answer;
	                                      }else{ $textReplyMessage= $textReplyMessage."\nCannot teach Lisa";
		                                     }
				    $textMessage = new TextMessageBuilder($textReplyMessage);
		                    $multiMessage->add($textMessage);
					$picUrl=rand(1,400);
					$picFullSize = "https://www.hooq.info/photos/".$picUrl.".jpg";
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
					$picUrl=$picUrl+1;
					$picFullSize = "https://www.hooq.info/photos/".$picUrl.".jpg";
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
					$picUrl=$picUrl+2;
					$picFullSize = "https://www.hooq.info/photos/".$picUrl.".jpg";
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
		                    $replyData = $multiMessage;
				}// end no answer, just question only
                                 break;

			   case '#tran':
			        $text_parameter = str_replace("#tran ","", $text);  
                                if (!is_null($explodeText[1])){ $source =$explodeText[1];}else{$source ='en';}
                                if (!is_null($explodeText[2])){ $target =$explodeText[2];}else{$target ='th';}
                                $result=tranlateLang($source,$target,$text_parameter);
				$flexData = new ReplyTranslateMessage;
                                $replyData = $flexData->get($text_parameter,$result);
				//$log_note=$log_note."\n User select #tran ".$text_parameter.$result;
		                break;
			
			   default: 
				$replyData ="";
				break;
                        }//end switch 
			
			}// end check user status == 1
		   
	              }// end User Registered 
		
		//-- บันทึกการเข้าใช้งานระบบ ---//
		
              if(!is_null($displayName)){
		      $displayName =$displayName;
	      }elseif(isset($userName)){
		      $displayName =$userName;
		 }else{
		      $displayName = ' ';
	      }
              if(is_null($pictureUrl)){$pictureUrl ='';}
		   $newUserData = json_encode(array('displayName' => $displayName,'userId'=> $userId,'dateTime'=> $dateTimeNow,
						    'log_note'=>$log_note,'pictureUrl'=>$pictureUrl) );
                           $opts = array('http' => array( 'method' => "POST",
                                          'header' => "Content-type: application/json",
                                          'content' => $newUserData
                                           )
                                        );
           
            $url = 'https://api.mlab.com/api/1/databases/crma51/collections/use_log?apiKey='.MLAB_API_KEY.'';
            $context = stream_context_create($opts);
            $returnValue = file_get_contents($url,false,$context);
		
	} // end of !is_null($userId)
	
	
	
            // ส่งกลับข้อมูล
	    // ส่วนส่งกลับข้อมูลให้ LINE
           $response = $bot->replyMessage($replyToken,$replyData);
           if ($response->isSucceeded()) { echo 'Succeeded!'; return;}
              // Failed ส่งข้อความไม่สำเร็จ
             $statusMessage = $response->getHTTPStatus() . ' ' . $response->getRawBody(); echo $statusMessage;
             $bot->replyText($replyToken, $statusMessage);   
	}//end if event is textMessage
}// end foreach event
function tranlateLang($source, $target, $text_parameter)
{
    $text = str_replace($source,"", $text_parameter);
    $text = str_replace($target,"", $text);  
    $trans = new GoogleTranslate();
    $result = $trans->translate($source, $target, $text);	    
    return $result;
}
class ReplyTranslateMessage
{
    /**
     * Create  flex message
     *
     * @return \LINE\LINEBot\MessageBuilder\FlexMessageBuilder
     */
    public static function get($question,$answer)
    {
        return FlexMessageBuilder::builder()
            ->setAltText('Lisa')
            ->setContents(
                BubbleContainerBuilder::builder()
                    ->setHero(self::createHeroBlock())
                    ->setBody(self::createBodyBlock($question,$answer))
                    ->setFooter(self::createFooterBlock())
            );
    }
    private static function createHeroBlock()
    {
	   
        return ImageComponentBuilder::builder()
            ->setUrl('https://www.hooq.info/wp-content/uploads/2019/02/Connect-with-precision.jpg')
            ->setSize(ComponentImageSize::FULL)
            ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
            ->setAspectMode(ComponentImageAspectMode::FIT)
            ->setAction(new UriTemplateActionBuilder(null, 'https://www.hooq.info'));
    }
    private static function createBodyBlock($question,$answer)
    {
        $title = TextComponentBuilder::builder()
            ->setText($question)
            ->setWeight(ComponentFontWeight::BOLD)
	    ->setwrap(true)
            ->setSize(ComponentFontSize::SM);
        
        $textDetail = TextComponentBuilder::builder()
            ->setText($answer)
            ->setSize(ComponentFontSize::LG)
            ->setColor('#000000')
            ->setMargin(ComponentMargin::MD)
	    ->setwrap(true)
            ->setFlex(2);
        $review = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setMargin(ComponentMargin::LG)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([$title,$textDetail]);
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setContents([$review]);
    }
    private static function createFooterBlock()
    {
        
        $websiteButton = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::LINK)
            ->setHeight(ComponentButtonHeight::SM)
            ->setFlex(0)
            ->setAction(new UriTemplateActionBuilder('เพิ่มเติม','https://www.hooq.info'));
        $spacer = new SpacerComponentBuilder(ComponentSpaceSize::SM);
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setFlex(0)
            ->setContents([$websiteButton, $spacer]);
    }
} 
class ReplyCarRegisterMessage
{
    /**
     * Create  flex message
     *
     * @return \LINE\LINEBot\MessageBuilder\FlexMessageBuilder
     */
    public static function get($question,$answer,$picUrl)
    {
        return FlexMessageBuilder::builder()
            ->setAltText('Lisa')
            ->setContents(
                BubbleContainerBuilder::builder()
                    ->setHero(self::createHeroBlock($picUrl))
                    ->setBody(self::createBodyBlock($question,$answer))
                    ->setFooter(self::createFooterBlock($picUrl))
            );
    }
    private static function createHeroBlock($picUrl)
    {
	   
        return ImageComponentBuilder::builder()
            ->setUrl($picUrl)
            ->setSize(ComponentImageSize::FULL)
            ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
            ->setAspectMode(ComponentImageAspectMode::FIT)
            ->setAction(new UriTemplateActionBuilder(null, $picUrl));
    }
    private static function createBodyBlock($question,$answer)
    {
        $title = TextComponentBuilder::builder()
            ->setText($question)
            ->setWeight(ComponentFontWeight::BOLD)
	    ->setwrap(true)
            ->setSize(ComponentFontSize::SM);
        
        $textDetail = TextComponentBuilder::builder()
            ->setText($answer)
            ->setSize(ComponentFontSize::LG)
            ->setColor('#000000')
            ->setMargin(ComponentMargin::MD)
	    ->setwrap(true)
            ->setFlex(2);
        $review = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            //->setLayout(ComponentLayout::BASELINE)
            ->setMargin(ComponentMargin::LG)
            //->setMargin(ComponentMargin::SM)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([$title,$textDetail]);
	
	    /*    
        $place = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([
                TextComponentBuilder::builder()
                    ->setText('ที่อยู่')
                    ->setColor('#aaaaaa')
                    ->setSize(ComponentFontSize::SM)
                    ->setFlex(1),
                TextComponentBuilder::builder()
                    ->setText('Samsen, Bangkok')
                    ->setWrap(true)
                    ->setColor('#666666')
                    ->setSize(ComponentFontSize::SM)
                    ->setFlex(5)
            ]);
        $time = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([
                TextComponentBuilder::builder()
                    ->setText('Time')
                    ->setColor('#aaaaaa')
                    ->setSize(ComponentFontSize::SM)
                    ->setFlex(1),
                TextComponentBuilder::builder()
                    ->setText('10:00 - 23:00')
                    ->setWrap(true)
                    ->setColor('#666666')
                    ->setSize(ComponentFontSize::SM)
                    ->setFlex(5)
            ]);
	    
        $info = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setMargin(ComponentMargin::LG)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([$place, $time]);*/
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            //->setContents([$review, $info]);
            ->setContents([$review]);
    }
    private static function createFooterBlock($picUrl)
    {
        
        $websiteButton = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::LINK)
            ->setHeight(ComponentButtonHeight::SM)
            ->setFlex(0)
            ->setAction(new UriTemplateActionBuilder('เพิ่มเติม','https://www.hooq.info'));
        $spacer = new SpacerComponentBuilder(ComponentSpaceSize::SM);
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setFlex(0)
            ->setContents([$websiteButton, $spacer]);
    }
} 

	
	
	
