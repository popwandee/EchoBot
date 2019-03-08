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
	$log_note='';$textReplyMessage='';
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
              $displayName = $profile['displayName'];
              $statusMessage = $profile['statusMessage'];
              $pictureUrl = $profile['pictureUrl']; 
	      $textReplyMessage= $textReplyMessage."สวัสดีค่ะคุณ ".$displayName." นฝต.ขกท.สน.จชต. ได้พัฒนา นกฮูก ให้ใช้งานได้โดยจำกัดเฉพาะ จนท. ที่เกี่ยวข้องเท่านั้นนะคะ";
	      $textReplyMessage= $textReplyMessage."\n\nค้นหาบุคคล พิมพ์ #p เว้นวรรค ตามด้วยหมายเลข 13 หลัก";
	      $textReplyMessage= $textReplyMessage."\n\nค้นหารถ พิมพ์ #c เว้นวรรค ตามด้วยเลขทะเบียนรถ (กก1234ยะลา) ไม่เว้นวรรค ไม่มีเลข 0 ข้างหน้า";
	      $log_note=$log_note.$textReplyMessage;
	      //$textMessage = new TextMessageBuilder($textReplyMessage);
	      //$multiMessage->add($textMessage);  
		
              }
	if(!is_null($userId)){
	    $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/user_register?apiKey='.MLAB_API_KEY.'&q={"userId":"'.$userId.'"}');
            $data = json_decode($json);
            $isData=sizeof($data);
		if($isData >0){
                    foreach($data as $rec){
                           $textReplyMessage= $textReplyMessage."\nFrom phone \nDisplayname ".$displayName."\n User Id ".$userId;
                           $textReplyMessage= $textReplyMessage."\nFrom DB\nDisplayname ".$rec->displayName."\n Registered Id ".$rec->userId;
                           $textMessage = new TextMessageBuilder($textReplyMessage);
			   $multiMessage->add($textMessage);
			     }//end for each
	           // Postback Event
                   if (($event instanceof \LINE\LINEBot\Event\PostbackEvent)) { $logger->info('Postback message has come');continue; }
	          // Location Event
                   if  ($event instanceof LINE\LINEBot\Event\MessageEvent\LocationMessage) {
		        $logger->info("location -> ".$event->getLatitude().",".$event->getLongitude());
	                $multiMessage =     new MultiMessageBuilder;
	                $textReplyMessage= $textReplyMessage."\n location -> ".$event->getLatitude().",".$event->getLongitude();
			$log_note=$log_note."user sent location ".$textReplyMessage;
                        $textMessage = new TextMessageBuilder($textReplyMessage);
		        $multiMessage->add($textMessage);
	                $replyData = $multiMessage;
	                $response = $bot->replyMessage($replyToken,$replyData);
		        continue;
	                 }
			// Message Event
                   if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
                       $text = $event->getText();$text = strtolower($text);$explodeText=explode(" ",$text);$textReplyMessage="";
			switch ($explodeText[0]) { 
			   case '#p':
				if (!is_null($explodeText[1])){
			          $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey='.MLAB_API_KEY.'&q={"nationid":"'.$explodeText[1].'"}');
                                  $data = json_decode($json);
                                  $isData=sizeof($data);
                                 if($isData >0){
                                    $count=1;
                                    foreach($data as $rec){
	                               $count++;
                                       $textReplyMessage= $textReplyMessage."\nหมายเลข ปชช. ".$rec->nationid."\nชื่อ".$rec->name."\nที่อยู่".$rec->address."\nหมายเหตุ".$rec->note;
                                       $textMessage = new TextMessageBuilder($textReplyMessage);
	                               $multiMessage->add($textMessage);
	                              if (isset($rec->picUrl)){
	                               $picFullSize = "https://www.hooq.info/img/$rec->picUrl.png";
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
				      }
			               $replyData = $multiMessage;
                                    }//end for each
                                 }else{ //$isData <0  ไม่พบข้อมูลที่ค้นหา
                                   $textReplyMessage= $textReplyMessage."\nไม่พบ ".$explodeText[1]."  ในฐานข้อมูลของหน่วย";
	                           $textMessage = new TextMessageBuilder($textReplyMessage);
	                           $multiMessage->add($textMessage);
			           $replyData = $multiMessage;
                                   } // end $isData>0
				}else{ // no $explodeText[1]
			          $textReplyMessage= $textReplyMessage."\nคุณให้ข้อมูลสำหรับการตรวจสอบบุคคลไม่ครบค่ะ";
			          $textMessage = new TextMessageBuilder($textReplyMessage);
			          $multiMessage->add($textMessage);
			          $replyData = $multiMessage;
		                }// end !is_null($explodeText[1])
				$log_note=$log_note."\n User select #p ".$textReplyMessage;
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
                                       $textReplyMessage= $textReplyMessage."\n ทะเบียน ".$rec->license_plate."\nยี่ห้อ".$rec->brand."\nรุ่น".$rec->model."\nสี".$rec->color."\nผู้ครอบครอง ".$rec->user."\nประวัติ".$rec->note."\nหากข้อมูลรถไม่เป็นไปตามนี้ให้สงสัยว่าทะเบียนปลอม";
                                       $textMessage = new TextMessageBuilder($textReplyMessage);
	                               $multiMessage->add($textMessage);
	                              if (isset($rec->picUrl)){
	                               $picFullSize = "https://www.hooq.info/img_car/$rec->picUrl.png";
	                               $imageMessage = new ImageMessageBuilder($picFullSize,$picFullSize);
	                               $multiMessage->add($imageMessage);
				      }
			               $replyData = $multiMessage;
                                    }//end for each
                                 }else{ //$isData <0  ไม่พบข้อมูลที่ค้นหา
                                   $textReplyMessage= $textReplyMessage."\nไม่พบ ".$explodeText[1]."  ในฐานข้อมูลของหน่วย";
	                           $textMessage = new TextMessageBuilder($textReplyMessage);
	                           $multiMessage->add($textMessage);
			           $replyData = $multiMessage;
                                   } // end $isData>0
				}else{ // no $explodeText[1]
			          $textReplyMessage= $textReplyMessage."\nให้ข้อมูลสำหรับการตรวจสอบยานพาหนะไม่ครบค่ะ";
			          $textMessage = new TextMessageBuilder($textReplyMessage);
			          $multiMessage->add($textMessage);
			          $replyData = $multiMessage;
		                }// end !is_null($explodeText[1])
				$log_note=$log_note."\n User select #c ".$textReplyMessage;
			        break;
					/*--------------------------------------*/
		case '#':
	                  $json = file_get_contents('https://api.mlab.com/api/1/databases/crma51/collections/phonebook?apiKey='.MLAB_API_KEY.'&q={"$or":[{"name":{"$regex":"'.$explodeText[1].'"}},{"lastname":{"$regex":"'.$explodeText[1].'"}},{"nickname":{"$regex":"'.$explodeText[1].'"}},{"nickname2":{"$regex":"'.$explodeText[1].'"}},{"position":{"$regex":"'.$explodeText[1].'"}}]}');
                          $data = json_decode($json);
                          $isData=sizeof($data);
                          if($isData >0){
		             $textReplyMessage = $textReplyMessage."\nคุณ".$displayName."\n";
		             $count = 1;
		             $hasImageUrlStatus = false;
		             // default image for flex message
		             $imageUrl="https://www.hooq.info/wp-content/uploads/2019/02/Connect-with-precision.jpg";
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
		                     $replyData = $multiMessage;
				  
		      /*
		              $flexData = new ReplyTranslateMessage;
                              $replyData = $flexData->get($explodeText[1],$textReplyMessage,$imageUrl);
		     
		    */
	                 }else{
		               $textReplyMessage= $textReplyMessage."\nลิซ่า หาชื่อ ".$explodeText[1]." ไม่พบค่ะ , อัพเดตข้อมูลให้ด้วยนะค่ะ ";
		                $textMessage = new TextMessageBuilder($textReplyMessage);
		                $multiMessage->add($textMessage);
		                $replyData = $multiMessage;
	                       }
                     
                   break;
					/* ------------------------------------*/
			   case '$เพิ่มชื่อ':
                                 $x_tra = str_replace('$เพิ่มชื่อ ',"", $text);
                                 $pieces = explode(" ", $x_tra);
                                 $rank=$pieces[0];
                                 $name=$pieces[1];
                                 $lastname=$pieces[2];
                                 $nickname=$pieces[3];
                                 $position=$pieces[4];
                                 $Tel1=$pieces[5];

                                 //Post New Data
                                 $newData = json_encode(array('rank' => $rank,'name'=> $name,'lastname'=> $lastname,'nickname'=> $nickname,'position'=> $position,'Tel1'=> $Tel1) );
                                 $opts = array('http' => array( 'method' => "POST",
                                  'header' => "Content-type: application/json",
                                  'content' => $newData
                                        )
                                      );
                                 $url = 'https://api.mlab.com/api/1/databases/crma51/collections/phonebook?apiKey='.MLAB_API_KEY;
                                 $context = stream_context_create($opts);
                                 $returnValue = file_get_contents($url,false,$context);
                                 if($returnValue)$textReplyMessage= $textReplyMessage."\nขอแสดงความยินดีด้วยค่ะ\n ลิซ่าได้เพิ่มชื่อ \n".$rank." ".$name." ".$lastname." ".$Tel1."\n ในรายชื่อเรียบร้อยแล้วค่ะ";
                                    else $textReplyMessage= $textReplyMessage."\nไม่สามารถเพิ่มชื่อได้";
                                    $textMessage = new TextMessageBuilder($textReplyMessage);
		                    $multiMessage->add($textMessage);
		                    $replyData = $multiMessage;
					break;
			   case '$lisa':
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
		                    $replyData = $multiMessage;
                                 break;
			   case '#tran':
			        $text_parameter = str_replace("#tran ","", $text);  
                                if (!is_null($explodeText[1])){ $source =$explodeText[1];}else{$source ='en';}
                                if (!is_null($explodeText[2])){ $target =$explodeText[2];}else{$target ='th';}
                                $result=tranlateLang($source,$target,$text_parameter);
				$flexData = new ReplyTranslateMessage;
                                $replyData = $flexData->get($text_parameter,$result);
				$log_note=$log_note."\n User select #tran ".$text_parameter.$result;
		                break;
			   default:
                                $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/hooqbot?apiKey='.MLAB_API_KEY.'&q={"question":"'.$explodeText[0].'"}');
                                $data = json_decode($json);
                                $isData=sizeof($data);
                                if($isData >0){
                                   foreach($data as $rec){
                                           $textReplyMessage= $textReplyMessage.$rec->answer."\n";
                                            //-----------------------
                                           }//end for each
                                    }else{
                                         $textReplyMessage='';
                                         }//end no data from server
                                    $textMessage = new TextMessageBuilder($textReplyMessage);
		                    $multiMessage->add($textMessage);
		                    $replyData = $multiMessage;
                      
		               break;
                        }//end switch 
	             }//end if event is textMessage
			
		   }else{ // No userId registered
		           $textReplyMessage= $textReplyMessage."\nคุณ".$displayName." ยังไม่ได้ลงทะเบียน ID ".$userId." ไม่สามารถเข้าถึงฐานข้อมูลได้นะคะ\n กรุณาส่งหมายเลข ID \n".$userId."\nนี้พร้อมแจ้งยศ ชื่อ นามสกุล สังกัด หมายเลขโทรศัพท์ ให้\n นฝต.ขกท.สน.จชต.(ศูนย์ CCTV นฝต.ฯ) เพื่อลงทะเบียนค่ะ";
                           $textMessage = new TextMessageBuilder($textReplyMessage);
			   $multiMessage->add($textMessage);
                           $replyData = $multiMessage;
	              }
		
		//-- บันทึกการเข้าใช้งานระบบ ---//
		   $newUserData = json_encode(array('displayName' => $displayName,'userId'=> $userId,'dateTime'=> $dateTimeNow,'log_note'=>$log_note) );
                           $opts = array('http' => array( 'method' => "POST",
                                          'header' => "Content-type: application/json",
                                          'content' => $newUserData
                                           )
                                        );
            // เพิ่มเงื่อนไข ตรวจสอบว่ามีข้อมูลในฐานข้อมูลหรือยัง
            $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/use_log?apiKey='.MLAB_API_KEY.'';
            $context = stream_context_create($opts);
            $returnValue = file_get_contents($url,false,$context);
		/*
            if($returnValue){
		    $text =  'บันทึกการเข้าถึงข้อมูล '.$userId.' แล้วค่ะ';
	            }else{ $text="ไม่สามารถบันทึกการเข้าถึงข้อมูลได้";
		 
		        }
			$textMessage = new TextMessageBuilder($text);
			   $multiMessage->add($textMessage);
                       $replyData = $multiMessage;
		       */
		} // end of !is_null($userId)
            // ส่งกลับข้อมูล
	    // ส่วนส่งกลับข้อมูลให้ LINE
           $response = $bot->replyMessage($replyToken,$replyData);
           if ($response->isSucceeded()) { echo 'Succeeded!'; return;}
              // Failed ส่งข้อความไม่สำเร็จ
             $statusMessage = $response->getHTTPStatus() . ' ' . $response->getRawBody(); echo $statusMessage;
             $bot->replyText($replyToken, $statusMessage);
}// end foreach event
function tranlateLang($source, $target, $text_parameter)
{
    $text = str_replace($source,"", $text_parameter);
    $text = str_replace($target,"", $text);  
    $trans = new GoogleTranslate();
    $result = $trans->translate($source, $target, $text);	    
    return $result;
}


		      // ---------------------------------------------------------------------------//


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
