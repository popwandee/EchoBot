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
use LINE\LINEBot\MessageBuilder\Flex;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;
$logger = new Logger('LineBot');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));

define("MLAB_API_KEY", '6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv');
define("LINE_MESSAGING_API_CHANNEL_SECRET", '6f6b7e3b1aff242cd4fb0fa3113f7af3');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", 'RvsMabRN/IlT2BtmEoH+KcIbha8F/aPLWWzMKj8lxz/7f9c/Ygu5qvrUGtdlrTwyQwR5tFcgIGGzCkHO/SzIKrdCqUm+sal4t73YOuTPZsQX4bR35g3ZJGTvFilxvO1LVO/I6B1ouhx3UjGWe+OwswdB04t89/1O/w1cDnyilFU=
');

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

	       $groupId='';$roomId='';// default value
	    if(isset($arrayJson['events'][0]['source']['userId'])){// ตรวจสอบ id สำหรับตอบ push message
	       $userId=$arrayJson['events'][0]['source']['userId'];
	    }  //ตรวจสอบว่าเหตุการณ์เกิดขึ้นในกลุ่มหรือไม่ เพื่อขอ id การตอบให้กลุ่ม
	    else if(isset($arrayJson['events'][0]['source']['groupId'])){
	       $groupId=$arrayJson['events'][0]['source']['groupId'];
	       $userId=$arrayJson['events'][0]['source']['userId'];
	    }  //ตรวจสอบว่าเหตุการณ์เกิดขึ้นในห้องหรือไม่ เพื่อขอ id การตอบให้ห้อง
	    else if(isset($arrayJson['events'][0]['source']['roomId'])){
	       $roomId=$arrayJson['events'][0]['source']['roomId'];
	       $userId=$arrayJson['events'][0]['source']['userId'];
	    }
		 // ตรวจสอบชื่อผู้ถามเพื่อตรวจสอบสิทธิ์ และหรือบันทึกการใช้

	$replyData='No Data';
             $userId="Group : ".$groupId." Room : ".$roomId." User : ".$userId;
             $textMessage = new TextMessageBuilder($userId);
	     $multiMessage->add($textMessage);
             $replyData = $multiMessage;
            


	       $response = $bot->getProfile($userId);
                if ($response->isSucceeded()) {// ดึงค่าโดยแปลจาก JSON String .ให้อยู่ใรูปแบบโครงสร้าง ตัวแปร array 
                   $userData = $response->getJSONDecodedBody(); // return array     
			
                            // $userData['userId'] // $userData['displayName'] // $userData['pictureUrl']  // $userData['statusMessage']
                   $userDisplayName = $userData['displayName']; 
		}else{
		   $userDisplayName = $userId;
		}
		*/
		// จบส่วนการตรวจสอบผู้ใช้
        
foreach ($events as $event) {
	
        $replyToken = $event->getReplyToken();
	 $response = $bot->replyMessage($replyToken,$replyData);
           if ($response->isSucceeded()) { echo 'Succeeded!';return;} // Failed ส่งข้อความไม่สำเร็จ
	
             $statusMessage = $response->getHTTPStatus() . ' ' . $response->getRawBody();
             echo $statusMessage;
             $bot->replyText($replyToken, $statusMessage);
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
       
        $text = $event->getText();
        $text = strtolower($text);
        $explodeText=explode(" ",$text);
	$textReplyMessage="";
        $multiMessage =     new MultiMessageBuilder;
	  
		      
      switch ($explodeText[0]) {
          case 'stock':

                    $symbol=$explodeText[1];
                  $text= 'ราคาหุ้นรายวัน '.$symbol.' ';
                  $url_get_data ='https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol='.$symbol.'.bk&apikey=';
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
          $text='';
          $news_url='https://newsapi.org/v2/top-headlines?country=th&apiKey=' ;
          $content = file_get_contents($news_url); // อ่านข้อมูล JSON
          $json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
          $count_news=0;
            while (list($key) = each($json_arr)) { // ทำการ list ค่า key ของ Array ทั้งหมดออกมา
              if($key=='articles'){
               $json_arr1 = $json_arr[$key]; //ส่งมอบคุณสมบัติ Array ระดับกลาง
               while (list($key) = each($json_arr1)) {
                 if($count_news<6)
                    $text=$text.$json_arr1[$key]['title'].$json_arr1[$key]['description'].$json_arr1[$key]['url'];
                    ++$count_news;
                     }
              }
            }

            //$text=$text_arr[mt_rand(0, count($text_arr) - 1)];//$text_arr[mt_rand[min,max]]; random index
            $bot->replyText($replyToken, $text);
             break;
             case 'Lang':
             case 'lang':
             $text_parameter = str_replace("lang ","", $text);
             $text_parameter = str_replace("Lang ","", $text_parameter);
             $source = 'en';
             $target = 'th';
             $trans = new GoogleTranslate();
             $result = $trans->translate($source, $target, $text_parameter);
             $bot->replyText($replyToken, $result);
                break;

                case 'Weather':
                case 'weather':
                if(is_Null($explodeText[1]))$explodeText[1]="Bangkok";
               $news_url="http://api.openweathermap.org/data/2.5/weather?q=".$explodeText[1].",th&units=metric&appid=" ;
                $content = file_get_contents($news_url); // อ่านข้อมูล JSON
                $json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
                if(is_Null($json_arr)){$explodeText[1]="Bangkok";
                  $news_url="http://api.openweathermap.org/data/2.5/weather?q=".$explodeText[1].",th&units=metric&appid=" ;
                   $content = file_get_contents($news_url); // อ่านข้อมูล JSON
                   $json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
                }
                  $text= "รายงานสภาพอากาศ ".$json_arr[name];
                  $date = date("F j, Y, g:i a",$json_arr[dt]);
                  $text=$text." เมื่อ ".$date." มีลักษณะอากาศ ".$json_arr[weather][0][main]." ".$json_arr[weather][0][description]." ความกดอากาศ ".$json_arr[main][pressure]."hPa, ความชื้นสัมพัทธ์ ".$json_arr[main][humidity]."%";
                  $text=$text." อุณหภูมิ ".$json_arr[main][temp]."Celsius, อุณหภูมิสูงสุด ".$json_arr[main][temp_max]."Celsius, อุณหภูมิต่ำสุด ".$json_arr[main][temp_min]."Celsius";
                  $sunrise = date("F j, Y, g:i a",$json_arr[sys][sunrise]);
                  $text=$text." พระอาทิตย์ขึ้น ".$sunrise;
                  $sunset = date("F j, Y, g:i a",$json_arr[sys][sunset]);
                  $text=$text." พระอาทิตย์ตก ".$sunset;
                  $bot->replyText($reply_token, $text);
                   break;
          default:
		 //$textMessage= $userDisplayName."คุณไม่ได้ถามตามที่กำหนดค่ะ".$replyId.$userId; 
		 //$bot->replyText($replyToken, $textMessage);
	      $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/hooqbot?apiKey='.MLAB_API_KEY.'';
              $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/hooqbot?apiKey='.MLAB_API_KEY.'&q={"question":"'.$explodeText[0].'"}');
              $data = json_decode($json);
              $isData=sizeof($data);
		  $text='';
              if($isData >0){
                foreach($data as $rec){
                  $text= $text." \n".$rec->answer;
                  //-----------------------
                }//end for each
              }else{
                  $text='';
                  //$text= $explodeText[0];
                  //$bot->replyText($reply_token, $text);
              }//end no data from mlab
		      
                  $bot->replyText($replyToken, $text);
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
           


         
