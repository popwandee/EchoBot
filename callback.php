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
	$replyToken = $event->getReplyToken();
	$replyData='No Data';

  // Postback Event
    if (($event instanceof \LINE\LINEBot\Event\PostbackEvent)) {
		$logger->info('Postback message has come');
		continue;
	}
	// Location Event
    if  ($event instanceof LINE\LINEBot\Event\MessageEvent\LocationMessage) {
		$logger->info("location -> ".$event->getLatitude().",".$event->getLongitude());
	        $multiMessage =     new MultiMessageBuilder;
	        $textReplyMessage= "location -> ".$event->getLatitude().",".$event->getLongitude();
                $textMessage = new TextMessageBuilder($textReplyMessage);
		$multiMessage->add($textMessage);
	        $replyData = $multiMessage;
	        $response = $bot->replyMessage($replyToken,$replyData);
		continue;
	}
    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {

        $text = $event->getText();
        $text = strtolower($text);
        $explodeText=explode(" ",$text);
	$textReplyMessage="";

        $multiMessage =     new MultiMessageBuilder;
	    /*
	$groupId='';$roomId='';$userId=''; $userDisplayName='';// default value

	    // ส่วนตรวจสอบผู้ใช้
		$userId=$event->getUserId();
	   // if((!is_null($userId)){
		$response = $bot->getProfile($userId);
                if ($response->isSucceeded()) {// ดึงค่าโดยแปลจาก JSON String .ให้อยู่ใรูปแบบโครงสร้าง ตัวแปร array
                   $userData = $response->getJSONDecodedBody(); // return array
                            // $userData['userId'] // $userData['displayName'] // $userData['pictureUrl']                            // $userData['statusMessage']
                   $userDisplayName = $userData['displayName'];
		   //$bot->replyText($replyToken, $userDisplayName); ใช้ตรวจสอบว่าผู้ถาม ชื่อ อะไร
		}else{
		 //$bot->replyText($replyToken, $userId);  ใช้ตรวจสอบว่าผู้ถาม ID อะไร
			$userDisplayName = $userId;
		}// end get profile
	   // }//end is_null($userId);
	     $textReplyMessage = 'ตอบคุณ '.$userDisplayName.' User id : '.$userId;
                    $textMessage = new TextMessageBuilder($textReplyMessage);
		    $multiMessage->add($textMessage);
		// จบส่วนการตรวจสอบผู้ใช้
		*/

      switch ($explodeText[0]) {

	case '#i':


		 //$picFullSize = $userData['pictureUrl';
                          // $picThumbnail = $userData['pictureUrl';
			  // $imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			  // $multiMessage->add($imageMessage);
		/* ส่วนดึงข้อมูลจากฐานข้อมูล */
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
		          $textReplyMessage= "ไม่พบ ".$explodeText[1]."  ในฐานข้อมูลของหน่วย";
			  $textMessage = new TextMessageBuilder($textReplyMessage);
			  $multiMessage->add($textMessage);
			  //$ranNumber=rand(1,407);
			 // $picFullSize = "https://www.hooq.info/photos/$ranNumber.jpg";
			  //$picThumbnail = "https://www.hooq.info/photos/$ranNumber.jpg";
                         // $picThumbnail = "https://www.hooq.info/photos/thumbnails/tn_$ranNumber.jpg";
			  //$imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			 // $multiMessage->add($imageMessage);
			  $replyData = $multiMessage;
			 // กรณีจะตอบเฉพาะข้อความ
		      //$bot->replyText($replyToken, $textMessage);
		        } // end $isData>0
		   }else{ // no $explodeText[1]
	                $textReplyMessage= "คุณให้ข้อมูลในการสอบถามไม่ครบถ้วนค่ะ";
			$textMessage = new TextMessageBuilder($textReplyMessage);
			  $multiMessage->add($textMessage);
			  //$ranNumber=rand(1,407);
			  //$picFullSize = "https://www.hooq.info/photos/$ranNumber.jpg";
                          //$picThumbnail = "https://www.hooq.info/photos/$ranNumber.jpg";
			  ////$imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			  //$multiMessage->add($imageMessage);
			  $replyData = $multiMessage;
			 // กรณีจะตอบเฉพาะข้อความ
		      //$bot->replyText($replyToken, $textMessage);
		   }// end !is_null($explodeText[1])
		/* จบส่วนดึงข้อมูลจากฐานข้อมูล */


		break; // break case #i
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
    if($returnValue)$text = "ขอแสดงความยินดีด้วยค่ะ\n ลิซ่าได้เพิ่มชื่อ \n".$rank." ".$name." ".$lastname." ".$Tel1."\n ในรายชื่อเรียบร้อยแล้วค่ะ";
    else $text="ไม่สามารถเพิ่มชื่อได้";
    $bot->replyText($replyToken, $text);



  		break; // break case #i
	case '#':
	      $json = file_get_contents('https://api.mlab.com/api/1/databases/crma51/collections/phonebook?apiKey='.MLAB_API_KEY.'&q={"$or":[{"name":{"$regex":"'.$explodeText[1].'"}},{"lastname":{"$regex":"'.$explodeText[1].'"}},{"nickname":{"$regex":"'.$explodeText[1].'"}},{"nickname2":{"$regex":"'.$explodeText[1].'"}},{"position":{"$regex":"'.$explodeText[1].'"}}]}');
              $data = json_decode($json);
              $isData=sizeof($data);
              if($isData >0){
		   $result="";
		   $count=1;
                foreach($data as $rec){
                  $result= $result.$count.' '.$rec->rank.$rec->name.' '.$rec->lastname.' ('.$rec->position.' '.$rec->deploy_position.') '.$rec->Email.' โทร '.$rec->Tel1." ค่ะ\n\n";
                  if(!is_null($rec->Image)){
			  $result= $result.'มีรูปนะครับ'.$rec->Image;
		 	 $imageUrl="https://www.hooq.info/photos/300.jpg";
		  }else{
			  $result= $result." ไม่มีรูปนะครับ";
		 	 $imageUrl="https://www.hooq.info/photos/300.jpg";
		  }
			$count++;
                }//end for each
		    $textReplyMessage= $result;
		     $flexData = new ReplyTranslateMessage;
                     $replyData = $flexData->get($explodeText[1],$textReplyMessage,$imageUrl);
		      /*
		    $textMessage = new TextMessageBuilder($textReplyMessage);
		    $multiMessage->add($textMessage);
		    $replyData = $multiMessage;  
		    */
	      }else{
		  $text= "ลิซ่า หาชื่อ ".$explodeText[1]." ไม่พบค่ะ , อัพเดตข้อมูลให้ด้วยนะค่ะ ";
		     $result= $text;
		     $flexData = new ReplyTranslateMessage;
		     $image=rand(1,83);
	             $picFullSize = "https://www.hooq.info/photos/300.jpg";
                     $replyData = $flexData->get($explodeText[1],$result,$picFullSize);
	      }
                     
                   break;


case '!':
		    $textReplyMessage= "ไม่เอาไม่พูด ,".$explodeText[1].",\n  ดูภาพแก้เซ็งดีกว่าค่ะ ";
			  $textMessage = new TextMessageBuilder($textReplyMessage);
			  $multiMessage->add($textMessage);
		          $image=rand(1,407);

			  $picFullSize = "https://www.hooq.info/photos/$image.jpg";
                          $picThumbnail = "https://www.hooq.info/photos/$image.jpg";
                          $imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			  $multiMessage->add($imageMessage);
		          $image2=$image+1;

			  $picFullSize = "https://www.hooq.info/photos/$image2.jpg";
                          $picThumbnail = "https://www.hooq.info/photos/$image2.jpg";
                          $imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			  $multiMessage->add($imageMessage);
		          $image3=$image+2;

			  $picFullSize = "https://www.hooq.info/photos/$image3.jpg";
                          $picThumbnail = "https://www.hooq.info/photos/$image3.jpg";
                          $imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			  $multiMessage->add($imageMessage);
		      
			  $originalContentUrl = "https://www.facebook.com/watch/?v=384726295595053";
                          $previewImageUrl = "https://scontent.fbkk5-1.fna.fbcdn.net/v/t1.0-9/51654938_2088631114715709_563114124251234304_n.jpg?_nc_cat=109&_nc_ht=scontent.fbkk5-1.fna&oh=c5eacfede9a2e52acd9452cd90658d5d&oe=5CF83498";
                          $videoMessage = new VideoMessageBuilder($originalContentUrl, $previewImageUrl,);
			  $multiMessage->add($videoMessage);

			  $replyData = $multiMessage;
		break; //break case $

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
            // เพิ่มเงื่อนไข ตรวจสอบว่ามีข้อมูลในฐานข้อมูลหรือยัง

            $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/hooqbot?apiKey='.MLAB_API_KEY.'';
            $context = stream_context_create($opts);
            $returnValue = file_get_contents($url,false,$context);
            if($returnValue){
		    $text =  'ขอบคุณที่สอนลิซ่าค่ะ';
		    $text2 = 'ลิซ่าจำได้แล้วว่า '.$explodeText[1]." คือ ".$answer;
	    }else{ $text="Cannot teach Lisa";
		  $text2 = '';
		 }
            
          
		     $flexData = new ReplyTranslateMessage;
		     $image=rand(1,409);
	             $picFullSize = "https://www.hooq.info/photos/$image.jpg";
                     $replyData = $flexData->get($text,$text2,$picFullSize);
            break;
		      // ---------------------------------------------------------------------------//

 case 'แปล':
             $text_parameter = str_replace("แปล ","", $text);
             $text_parameter = str_replace("แปล ","", $text_parameter);
             $source = 'th';
             $target = 'en';
             $trans = new GoogleTranslate();
            $result = "แปลว่า ".$trans->translate($source, $target, $text_parameter)." ค่ะ";
		       $question = $text_parameter;
		     $answer = $result;
		     $flexData = new ReplyTranslateMessage;
		     $image=rand(1,409);
	             $picFullSize = "https://www.hooq.info/photos/$image.jpg";
                     $replyData = $flexData->get($question,$answer,$picFullSize);
             
                break;
case 'tran':
            $text_parameter = str_replace("tran ","", $text);
            $text_parameter = str_replace("tran ","", $text_parameter);
            $source = 'en';
            $target = 'th';
            $trans = new GoogleTranslate();
            $result = "แปลว่า ".$trans->translate($source, $target, $text_parameter)." ค่ะ";
           $question = $text_parameter;
		      $answer = $result;
		     $flexData = new ReplyTranslateMessage;
		     $image=rand(1,409);
	             $picFullSize = "https://www.hooq.info/photos/$image.jpg";
                     $replyData = $flexData->get($question,$answer,$picFullSize);
                               break;
case 'Stock':
case 'stock':
            $symbol=$explodeText[1];
            $text= 'ราคาหุ้นรายวัน '.$symbol.' ';
            $url_get_data ='https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol='.$symbol.'.bk&apikey='.ALPHAVANTAGE_API_KEY;
            $content = file_get_contents($url_get_data); // อ่านข้อมูล JSON
            $jarr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
            $keepdate = true;
            $countm= 0;
            while (list($key) = each($jarr)) { // ทำการ list ค่า key ของ Array ทั้งหมดออกมา
                $KeepMainkey = $key; //เก็บคีย์หลัก
                $count = count($jarr[$key]); // นับจำนวนแถวที่เก็บไว้ใน Array ใน key นั้นๆ
                $getarr1 = $jarr[$key]; //ส่งมอบคุณสมบัติ Array ระดับกลาง
                while (list($key) = each($getarr1)) {
                    if ($KeepMainkey=="Meta Data") {//&& $countm=='1'
                        $text= $text.' '.$key.' '.$getarr1[$key].' ';
                        }
                        $countm++;
                    if ($KeepMainkey!="Meta Data" && $keepdate ) {
                        $keepdate = false;
                        $getarrayday = $getarr1[$key];
                        while (list($key) = each($getarrayday)) {
                            $text= $text.' '.$key.' '.$getarrayday[$key].' ' ; //แสดงคีย์และผลลัพธ์ขอคีย์ของวัน
                            }//สิ้นสุดการลิสต์คีย์ชั้นลึก (ระดับวัน)
                          }
                    }//สิ้นสุดการลิสต์คีย์ชั้นกลาง
                } //สิ้นสุดการลิสต์คีย์ชั้นแรก
              $bot->replyText($replyToken, $text);
break;
case 'News':
case 'news':
                               $news_url='https://newsapi.org/v2/top-headlines?country=th&apiKey='.NEWSAPI_API_KEY ;
                               $content = file_get_contents($news_url); // อ่านข้อมูล JSON
                               $json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
                               $text='';
                                 while (list($key) = each($json_arr)) { // ทำการ list ค่า key ของ Array ทั้งหมดออกมา
                                   if($key=='articles'){
                                    $json_arr1 = $json_arr[$key]; //ส่งมอบคุณสมบัติ Array ระดับกลาง
                                    while (list($key) = each($json_arr1)) {
                                         $text=$text." ".$json_arr1[$key]['title'].$json_arr1[$key]['description'].$json_arr1[$key]['url'];
                                       }
                                   }
                                 }
                                 $bot->replyText($replyToken, $text);
                                  break;


                                     case 'Weather':
                                     case 'weather':
                                     if(is_Null($explodeText[1]))$explodeText[1]="Bangkok";
                                    $news_url="http://api.openweathermap.org/data/2.5/weather?q=".$explodeText[1].",th&units=metric&appid=".OPENWEATHERMAP_API_KEY ;
                                     $content = file_get_contents($news_url); // อ่านข้อมูล JSON
                                     $json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
                                       $text= "รายงานสภาพอากาศ ".$json_arr[name];
                                       $date = date("F j, Y, g:i a",$json_arr[dt]);
                                       $text=$text." เมื่อ ".$date." มีลักษณะอากาศ ".$json_arr[weather][0][main]." ".$json_arr[weather][0][description]." ความกดอากาศ ".$json_arr[main][pressure]."hPa, ความชื้นสัมพัทธ์ ".$json_arr[main][humidity]."%";
                                       $text=$text." อุณหภูมิ ".$json_arr[main][temp]."Celsius, อุณหภูมิสูงสุด ".$json_arr[main][temp_max]."Celsius, อุณหภูมิต่ำสุด ".$json_arr[main][temp_min]."Celsius";
                                       $sunrise = date("F j, Y, g:i a",$json_arr[sys][sunrise]);
                                       $text=$text." พระอาทิตย์ขึ้น ".$sunrise;
                                       $sunset = date("F j, Y, g:i a",$json_arr[sys][sunset]);
                                       $text=$text." พระอาทิตย์ตก ".$sunset;
                                       $bot->replyText($replyToken, $text);
                                        break;
	      case '@51':
		      $question = $text_parameter;
		      $answer = $result;
		     $flexData = new ReplyTranslateMessage;
	             $videoUrl = "https://www.facebook.com/ExquisiteKawaiiMagazine/videos/384726295595053/";
                     $replyData = $flexData->get($question,$answer,$videoUrl);
		      break;
          default:

              $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/hooqbot?apiKey='.MLAB_API_KEY.'&q={"question":"'.$explodeText[0].'"}');
              $data = json_decode($json);
              $isData=sizeof($data);
		  $text='';
              if($isData >0){
                foreach($data as $rec){
                  $text= $text.$rec->answer."\n";
                  //-----------------------
                }//end for each
              }else{
                  $text='';
		      break;
                  //$text= $explodeText[0];
                  //$bot->replyText($reply_token, $text);
              }//end no data from server


                $textReplyMessage= $text;		          
		     
		//$picFullSize = "https://www.hooq.info/RTA/$image.jpg";
                      $question = $explodeText[0];
		      $answer = $text;
		     $flexData = new ReplyTranslateMessage;
		     $image=rand(1,409);
	             $picFullSize = "https://www.hooq.info/photos/$image.jpg";
                     $replyData = $flexData->get($question,$answer,$picFullSize);
		break;
            }//end switch

	   // ส่วนส่งกลับข้อมูลให้ LINE
           $response = $bot->replyMessage($replyToken,$replyData);
           if ($response->isSucceeded()) {
              echo 'Succeeded!';
              return;
              }

              // Failed ส่งข้อความไม่สำเร็จ
             $statusMessage = $response->getHTTPStatus() . ' ' . $response->getRawBody();
             echo $statusMessage;
             $bot->replyText($replyToken, $statusMessage);
         }//end if event is textMessage
}// end foreach event



    class ReplyTranslateMessage
{
    /**
     * Create  flex message
     *
     * @return \LINE\LINEBot\MessageBuilder\FlexMessageBuilder
     */
    public static function get($question,$answer,$picFullSize)
    {
        return FlexMessageBuilder::builder()
            ->setAltText('Lisa')
            ->setContents(
                BubbleContainerBuilder::builder()
                    ->setHero(self::createHeroBlock($picFullSize))
                    ->setBody(self::createBodyBlock($question,$answer))
                    ->setFooter(self::createFooterBlock($picFullSize))
            );
    }
    private static function createHeroBlock($picFullSize)
    {
	   
        return ImageComponentBuilder::builder()
            ->setUrl($picFullSize)
            ->setSize(ComponentImageSize::FULL)
            ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
            ->setAspectMode(ComponentImageAspectMode::COVER)
            ->setAction(new UriTemplateActionBuilder(null, $picFullSize));
    }
    private static function createBodyBlock($question,$answer)
    {
        $title = TextComponentBuilder::builder()
            ->setText($question)
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::SM);
        
        $textDetail = TextComponentBuilder::builder()
            ->setText($answer)
            ->setSize(ComponentFontSize::LG)
            ->setColor('#000000')
            ->setMargin(ComponentMargin::MD)
	    ->setwrap(true)
            ->setFlex(5);
        $review = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setMargin(ComponentMargin::SM)
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
    private static function createFooterBlock($picFullSize)
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

/*
function pushMsg($arrayHeader,$arrayPostData){
		 $strUrl ="https://api.line.me/v2/bot/message/push";
		 $ch=curl_init();
		 curl_setopt($ch,CURLOPT_URL,$strUrl);
		 curl_setopt($ch,CURLOPT_HEADER,false);
		 curl_setopt($ch,CURLOPT_POST,true);
		 curl_setopt($ch,CURLOPT_HTTPHEADER,$arrayHeader);
		 curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($arrayPostData));
		 curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		 curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		 $result=curl_exec($ch);
		 curl_close($ch);
		 }
		 */

  /*
	 case '$เพิ่มรถ':
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
	 case '$ทะเบียน':
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
                  $bot->replyText($reply_token, $text);
                   break;
          case '$เพิ่มคน':
              $x_tra = str_replace("#เพิ่มคน ","", $text);
              $pieces = explode("|", $x_tra);
              $_name=$pieces[0];
              $_surname=$pieces[1];
              $_nickname=$pieces[2];
              $_nickname2=$pieces[3];
              $_telephone=$pieces[4];
              $_jobposition=$pieces[5];
              $_address=$pieces[6];
              //Post New Data

              $newData = json_encode(array('name' => $_name,'surname'=> $_surname,'nickname'=> $_nickname,'nickname2'=> $_nickname2,'telephone'=> $_telephone,'jobposition'=> $_jobposition,'address'=> $_address) );
              $opts = array('http' => array( 'method' => "POST",
                                            'header' => "Content-type: application/json",
                                            'content' => $newData
                                             )
                                          );
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/intelphonebook?apiKey='.MLAB_API_KEY;
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              if($returnValue)$text = 'เพิ่มคนสำเร็จแล้ว';
              else $text="ไม่สามารถเพิ่มคนได้";
              $bot->replyText($reply_token, $text);

              break;


      */
