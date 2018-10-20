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
              if($returnValue){$replyText = 'เพิ่มรถสำเร็จแล้ว';
			$img_url="https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";
			      }else {$replyText="ไม่สามารถเพิ่มรถได้";
			$img_url="https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";}
              //$bot->replyText($reply_token, $text);
              break;
	 case '#ทะเบียน':
		  $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY.'&q={"licence_plate":"'.$explodeText[1].'"}');
              $data = json_decode($json);
              $isData=sizeof($data);
              if($isData >0){
		   $$replyText="";
		   $count=1;
                foreach($data as $rec){
                  $replyText= $replyText.$count.' '.$rec->licence_plate.' '.$rec->brand.' '.$rec->model.' '.$rec->color."\n ผู้ถือกรรมสิทธิ์ ".$rec->owner."\n ผู้ครอบครอง ".$rec->user."\n หมายเหตุ/ประวัติ ".$rec->note."\n\n";
                  $count++;
                }//end for each
		      $img_url = "https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";
	      }else{
		  $replyText= "ไม่พบข้อมูลทะเบียนรถ ".$explodeText[1];
		      $img_url = "https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";
	      }
			
                  //$bot->replyText($reply_token, $replyText);
                   break;
         
         
          default:
		break;	
            }//end switch
	   $img_uri= "https://qph.fs.quoracdn.net/main-qimg-f93403f6d32bc43b40d85bd978e88bbf";
        
        $url_detail ="https://www.hooq.info";
        // 
         $action = new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('รายละเอียดเพิ่มเติม', $url_detail);

                // สร้างคอลัมน์สำหรับ carousel
                $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder("Name", $text, $img_uri, [$action]);
                $columns[] = $column;
            }

            // model Carousel จากอาร์เรย์ของคอลัมน์
            $carousel_template_builder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder($columns);
            $template_message = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($text, $carousel_template_builder);
            $message = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
            $message->add($template_message);
            $response = $bot->replyMessage($event->replyToken, $message);
	    $bot->replyText($reply_token, $replyText);
    }//end if text
}// end foreach event


       
/*
    // ถ้าประเภทข้อความเป็นพิกัด
    if ('location' == $event_message_type) {
        // เก็บค่าพิกัด
        $latitude = $event->message->latitude;
        $longitude = $event->message->longitude;

        // สร้างลิงค์
        $url = buildGnaviUrl($latitude, $longitude);
        
        // ดึงข้อมูลจากลิงค์พิกัด
        $json = file_get_contents($url);
        $results = resultsParse($json);

        // ถ้ามีข้อมูลจากลิงค์
        if($results != null) {

            // สุ่ม
            shuffle($results);

            // เลือกแค่ 5 
            if (count($results) > 5) {
                $max = 5;
            } else {
                $max = count($results);
            }

            // model Carousel
            $columns = [];
            for ($i = 0; $i < $max; $i++) {
                // // สร้างปุ่มเพื่อให้คลิก
                $action = new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('Store details', $results[$i]['url']);

                // สร้างคอลัมน์สำหรับ carousel
                $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder($results[$i]['name'], $info, $results[$i]['image_url'], [$action]);
                $columns[] = $column;
            }

            // model Carousel จากอาร์เรย์ของคอลัมน์
            $carousel_template_builder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder($columns);
            $template_message = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('เก็บรายการข้อมูล (5 ราย)', $carousel_template_builder);
            $message = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
            $message->add($template_message);
            $response = $bot->replyMessage($event->replyToken, $message);

        } else {
            // เมื่อไม่มีผลการค้นหา
            $text_message_builder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('ฉันขอโทษ NYA ไม่มีร้านราเมนอยู่ใกล้ ๆ . .');
            $response = $bot->replyMessage($event->replyToken, $text_message_builder);
        }
    }
*/
    
// สร้าง URL สำหรับ GourNavi API
function buildGnaviUrl($latitude, $longitude) {

    // การตั้งค่า GourNavi API
    /*
    $gnavi_uri = 'http://api.gnavi.co.jp/RestSearchAPI/20150630/';
    $gnavi_acckey = getenv('GNAVI_API_KEY');
    $gnavi_format = 'json';
    $gnavi_range = 3;
    $gnavi_category = 'RSFST08008'; // บะหมี่ราเมน

    // URL รวม
    $url  = sprintf('%s%s%s%s%s%s%s%s%s%s%s%s%s', $gnavi_uri, '?format=', $gnavi_format, '&keyid=', $gnavi_acckey, '&latitude=', $latitude, '&longitude=', $longitude, '&range=', $gnavi_range, '&category_s=', $gnavi_category);

    return $url;
    */
}

// แยกวิเคราะห์ผลลัพธ์ของ GourNavi API
function resultsParse($json) {
    $obj  = json_decode($json);

    // การเริ่มต้นอาร์เรย์แบบรวม
    $results = [];

    $total_hit_count = $obj->{'total_hit_count'};

    if ($total_hit_count !== null) {
        $n = 0;
        foreach($obj->{'rest'} as $val) {

            // ชื่อร้านค้า
            if (checkString($val->{'name'})) {
                $results[$n]['name'] = $val->{'name'};
            }

            // ที่อยู่
            if (checkString($val->{'address'})) {
                $results[$n]['address'] = $val->{'address'};
            }

            // Gourmet Navigator URL
            if (checkString($val->{'url'})) {
                $results[$n]['url'] = $val->{'url'};
            }

            // จัดเก็บรูปภาพ
            if (checkString($val->{'image_url'}->{'shop_image1'})) {
                $results[$n]['image_url'] = $val->{'image_url'}->{'shop_image1'};
            } else {
                $results[$n]['image_url'] = '※※※ รูปภาพใดก็ได้ URL ※※※';
            }

            // PR
            if (checkString($val->{'pr'})) {
                $results[$n]['pr'] = $val->{'pr'};
            } else {
                $results[$n]['pr'] = '';
            }

            $n++;
        }
    }
    return $results;
}

// ตรวจสอบว่าเป็นสตริงอักขระหรือไม่
function checkString($input) {
    if(isset($input) && is_string($input)) {
        return true;
    } else {
        return false;
    }
}
?>
