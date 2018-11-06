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
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY;
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              if($returnValue){$replyText = 'เพิ่มรถสำเร็จแล้ว';
			           $img_url="https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";
			      }else {$replyText="ไม่สามารถเพิ่มรถได้";
			           $img_url="https://plus.google.com/photos/photo/108961502262758121403/6146705217388476082";}
              //$bot->replyText($reply_token, $text);

              break;

	 case '#r':
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
		
         case '#e':
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

        case '#d':
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
		
		
		
	case '#new':
              $x_tra = str_replace("#new ","", $text);
         
              //Post New Data
              $newData = '[
  {
    "licence_plate": "ป9388นราธิวาส",
    "brand": "YAMAHA",
    "model": "Y111ED",
    "color": "ดำ",
    "user": "นายมาหามุด ลอมะ3-9605-00238-40-1 เลขที่ 13/5ม.3บ.กูตงต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "licence_plate": "ท9358ยะลา",
    "brand": "YAMAHA",
    "model": "Y111D",
    "color": "น้ำเงิน",
    "user": "นายสะมะแอ เจ๊ะเต๊ะ3-9501-00651-46-8เลขที่61/5ม.9บ้านอุเปต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "คยจ175สงขลา",
    "brand": "YAMAHA",
    "model": "4D0400",
    "color": "เขียว",
    "user": "นายอับดุลรอหีม เบนอาดัม 3-9402-00134-24-4  เลขที่ 70 หมู่ 10 บ.ท่าคลอง ต.โคกโพธิ์",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "licence_plate": "คตฉ663สงขลา",
    "brand": "YAMAHA",
    "model": "LTA-135ED",
    "color": "แดง",
    "user": "นายอายุ สาและ 3-9402-00220-18-3  เลขที่  59 หมู่ 5 ล้อแตก ต.บางโกระ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "licence_plate": "ขทน234นราธิวาส",
    "brand": "YAMAHA",
    "model": "5P0700",
    "color": "ขาว",
    "user": "น.ส.สนะ ฮามะ3-9605-00368-69-1 เลขที่ 127/5ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "ขตฉ806นราธิวาส",
    "brand": "YAMAHA",
    "model": "50S400",
    "color": "ขาว",
    "user": "นางแมะมูเน๊าะ สะแปอิง3-9605-00433-10-7 เลขที่ 91ม.4บ.บาโงต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "ขตฉ488นราธิวาส",
    "brand": "YAMAHA",
    "model": "20B500",
    "color": "ขาว",
    "user": "นายยะโกะ เจ๊ะหลง3-9605-00236-90-5 เลขที่ 3ม.3บ.กูตงต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "ขฉล135นราธิวาส",
    "brand": "YAMAHA",
    "model": "20B200",
    "color": "ดำ",
    "user": "นายมูฮำมัดเสากี เจ๊ะเล๊าะ3-9605-00628-46-8 เลขที่ 92ม.5บ.ตราแดะต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "ขจษ583นราธิวาส",
    "brand": "YAMAHA",
    "model": "33S400",
    "color": "น้ำเงิน",
    "user": "นายอาหะมัด อูมา2-9602-00007-96-1เลขที่135ม.5บ.โคกกะเปาะต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "licence_plate": "ขจว498นราธิวาส",
    "brand": "YAMAHA",
    "model": "20B100",
    "color": "ขาว",
    "user": "นายยูโซ๊ะ มะเล๊าะ/บอบอ3-9602-00408-01-8เลขที่32/1ม.5บ้านอุเผะต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "ขงท81นราธิวาส",
    "brand": "YAMAHA",
    "model": "AH-115D",
    "color": "ขาว",
    "user": "นายซูกีพลี แฉะ3-9605-00056-17-6 เลขที่ 30ม.1บ.มะรือโบตกต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "ขงฉ140ยะลา",
    "brand": "YAMAHA",
    "model": "20B400",
    "color": "ดำ",
    "user": "นายอาแซ วาแม 3-9506-00250-82-9  เลขที่ 65 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "ขขพ546ยะลา",
    "brand": "YAMAHA",
    "model": "20B300",
    "color": "ดำ",
    "user": "นางซารีปะห์ ยีดิง 3-9604-00066-62-5  เลขที่ 4 หมู่ 5 บาโงยือแบ็ง กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "ขขธ198นราธิวาส",
    "brand": "YAMAHA",
    "model": "BE-115D",
    "color": "ม่วง",
    "user": "นายมะหะมะสิศิ ลาเต๊ะ3-9605-00056-67-2 เลขที่ 37ม.2บ.ปละเอ็งต.มะรือโบตก",
    "note": "เปอมูดอ"
  },
  {
    "licence_plate": "ขขธ165นราธิวาส",
    "brand": "YAMAHA",
    "model": "AH-115D",
    "color": "ดำ",
    "user": "นายนิอัสวาดี้ เสมอภพ3-9699-00014-53-7 เลขที่ 178ม.8บ.ตันหยงมัสต.ตันหยงมัส",
    "note": "เปอมูดอ"
  },
  {
    "licence_plate": "ขกย991นราธิวาส",
    "brand": "YAMAHA",
    "model": "BF-115D-CW",
    "color": "แดง",
    "user": "นายอาหมัด สาอุ3-9605-00345-99-2 เลขที่ 80/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "ขกม495นราธิวาส",
    "brand": "YAMAHA",
    "model": "BG-115D-L",
    "color": "น้ำเงิน",
    "user": "นายยะยอ ลือบา3-9605-00230-96-6 เลขที่ 71/1ม.2บ.ลาแปต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "ขกข163นราธิวาส",
    "brand": "YAMAHA",
    "model": "BE-115C",
    "color": "ม่วง",
    "user": "นายอาหะมัด อูมา2-9602-00007-96-1เลขที่135ม.5บ.โคกกะเปาะต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "licence_plate": "ขกก104นราธิวาส",
    "brand": "YAMAHA",
    "model": "BE-115C",
    "color": "ม่วง",
    "user": "นายดือราพา เจ๊ะอูมา3-9605-00017-91-3 เลขที่ 104/2ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "ผบ.มว.๑ (หน.PLATONG)"
  },
  {
    "licence_plate": "กษว478ปัตตานี",
    "brand": "YAMAHA",
    "model": "50S300",
    "color": "ดำ",
    "user": "นายมูหะมะยะโกะ ใสทอง 5-9503-00004-74-8 เลขที่ 15/2 หมู่ 8 กอลอกาลี ตะโละดือรามัน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "กษล479นราธิวาส",
    "brand": "YAMAHA",
    "model": "BD-115C",
    "color": "เขียว",
    "user": "นายรอฟา เจ๊ะดาโอ๊ะ3-9605-00389-10-8 เลขที่ 127/1ม.4บ.บองอต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "licence_plate": "กวค59ปัตตานี",
    "brand": "YAMAHA",
    "model": "4D0400",
    "color": "ชมพู",
    "user": "น.ส.ซารียะห์ แอสะ3-9605-00402-57-1 เลขที่ 152/1ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "กวค12นราธิวาส",
    "brand": "YAMAHA",
    "model": "BT-115E",
    "color": "แดง",
    "user": "นายอิบรอเห็ง เจ๊ะอูมา3960500236492 เลขที่ 158ม.2บ.ลาแปต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "licence_plate": "กรร104นราธิวาส",
    "brand": "YAMAHA",
    "model": "KB-105C",
    "color": "แดง",
    "user": "นายซะรอนิง นิเง๊าะ3-9605-00643-97-7 เลขที่ 20ม.2บ.บาโงบือราแงต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "licence_plate": "กรบ905นราธิวาส",
    "brand": "YAMAHA",
    "model": "Y111E",
    "color": "ดำ",
    "user": "นายต่วนมะ ซายอ3-9605-00632-92-4 เลขที่ 67/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "กรฉ878นราธิวาส",
    "brand": "YAMAHA",
    "model": "L-105",
    "color": "น้ำเงิน",
    "user": "นายอาลียาสะ บูละ3-9605-00646-95-3 เลขที่ 58/1ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "กรรมการฝ่ายปกครองระดับแดอาเราะห์"
  },
  {
    "licence_plate": "กยน487นราธิวาส",
    "brand": "YAMAHA",
    "model": "K-105C",
    "color": "ดำ",
    "user": "นายมาหะมะ เปาะเต๊ะ3-9605-00240-90-2 เลขที่ 34ม.3บ.กูตงต.บองอ",
    "note": "กรรมการอาเยาะฝ่ายอูลามา"
  },
  {
    "licence_plate": "กยท304นราธิวาส",
    "brand": "YAMAHA",
    "model": "Y100K",
    "color": "น้ำเงิน",
    "user": "นายยะยอ ลือบา3-9605-00230-96-6 เลขที่ 71/1ม.2บ.ลาแปต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "licence_plate": "กมค432ปัตตานี",
    "brand": "YAMAHA",
    "model": "BE-115D",
    "color": "เขียว",
    "user": "นายซักการียา มะเซ็ง 3-9411-00076-77-4   เลขที่ 29 หมู่ 3 ปล่องหอย",
    "note": "สมาชิกปฏิบัติการ"
  }
]';
            
		
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
		
		
		
          default:
		// $replyText=$replyText.$displayName.$statusMessage;
		break;
            }//end switch
	    
	    $bot->replyText($reply_token, $replyText);
    }//end if text
}// end foreach event
?>
