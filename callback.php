<?php // callback.php
ob_start();
$raw = file_get_contents('php://input');
var_dump(json_decode($raw,1));
$raw = ob_get_clean();
file_put_contents('/tmp/dump.txt', $raw."\n=====================================\n", FILE_APPEND);

echo "Dump temp OK";

define("LINE_MESSAGING_API_CHANNEL_SECRET", '82d7948950b54381bcbd0345be0d4a2c');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", 'BYnvAcR40qJk4fLopvVtVozF00iUqfUjoD33tIPcnjMoXEyG3fzYSE24XRKB5lnttxPePUIHPWdylLdkROwbOESi4rQE3+oSG3njcFj7yoQuaqU27effhhF4lz6lbOfhPjD9mLvHWYZlSbeigV4ETAdB04t89/1O/w1cDnyilFU=');
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
       $a = ['ว้าว ว้าว ว้าว', 'อุ๊ยตาย ว้ายกรีดดดด', 'ชอบๆ', 'ขอบคุณฮะ', 'OK', 'OK, I Like it.'];

    $text = $a[mt_rand(0, count($a) - 1)];//$a[min,max];

     //$text = 'รูปอะไรเหรอฮะ';

      $bot->replyText($reply_token, $text);

   }

    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {

      $reply_token = $event->getReplyToken();

      $text = $event->getText();

      $explodeText=explode(" ",$text);


      switch ($explodeText[0]) {

        case 'สอนเป็ด':

            $x_tra = str_replace("สอนเป็ด ","", $text);

            $pieces = explode("|", $x_tra);

            $_question=str_replace("[","",$pieces[0]);

            $_answer=str_replace("]","",$pieces[1]);

            //Post New Data

            $newData = json_encode(array('question' => $_question,'answer'=> $_answer) );

            $opts = array('http' => array( 'method' => "POST",

                                          'header' => "Content-type: application/json",

                                          'content' => $newData

                                           )

                                        );

            // เพิ่มเงื่อนไข ตรวจสอบว่ามีข้อมูลในฐานข้อมูลหรือยัง

            $api_key="6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv";

            $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/hooqbot?apiKey='.$api_key.'';

            $context = stream_context_create($opts);

            $returnValue = file_get_contents($url,false,$context);

            if($returnValue)$text = 'ขอบคุณที่สอนเป็ด ฮะ คุณสามารถสอนให้ฉลาดได้เพียงพิมพ์: สอนเป็ด [คำถาม|คำตอบ] ต้องเว้นวรรคด้วยนะ  สอบถามราคาหุ้นพิมพ์ stock ถามข่าวพิมพ์ news';
            else $text="Cannot teach Ducky";

            $bot->replyText($reply_token, $text);

            break;

            case 'Stock':
            case 'stock':

                    $symbol=$explodeText[1];
                  $text= 'ราคาหุ้นรายวัน '.$symbol.' ';
                  $url_get_data ='https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol='.$symbol.'.bk&apikey=W6PVFUDUDT6NEEN1';
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

                $bot->replyText($reply_token, $text);

                break;
          case 'News':
          case 'news':
          $text='';
          $news_url='https://newsapi.org/v2/top-headlines?country=th&apiKey=dca7d30a57ec451cad6540a696a7f60a' ;
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
            $bot->replyText($reply_token, $text);
             break;
             case 'Lang':
             case 'lang':
             $text_parameter = str_replace("lang ","", $text);
             $text_parameter = str_replace("Lang ","", $text_parameter);
             $source = 'en';
             $target = 'th';
             $trans = new GoogleTranslate();
             $result = $trans->translate($source, $target, $text_parameter);
             $bot->replyText($reply_token, $result);
                break;

                case 'Weather':
                case 'weather':
                if(is_Null($explodeText[1]))$explodeText[1]="Bangkok";
               $news_url="http://api.openweathermap.org/data/2.5/weather?q=".$explodeText[1].",th&units=metric&appid=cb9473cef915ee0ed20ac67817d06289" ;
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
                  $bot->replyText($reply_token, $text);
                   break;


          default:

              $api_key="6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv";

              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/hooqbot?apiKey='.$api_key.'';

              $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/hooqbot?apiKey='.$api_key.'&q={"question":"'.$explodeText[0].'"}');

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
