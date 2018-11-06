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
		         $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/register_south?apiKey='.MLAB_API_KEY.'&q={"license_plate":"'.$explodeText[1].'"}');
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
		
		
		
	case '#new':
              $x_tra = str_replace("#new ","", $text);
         
              //Post New Data
              $newData = '[
  {
    "license_plate": "ป9388นราธิวาส",
    "brand": "YAMAHA",
    "model": "Y111ED",
    "color": "ดำ",
    "user": "นายมาหามุด ลอมะ3-9605-00238-40-1 เลขที่ 13/5ม.3บ.กูตงต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ท9358ยะลา",
    "brand": "YAMAHA",
    "model": "Y111D",
    "color": "น้ำเงิน",
    "user": "นายสะมะแอ เจ๊ะเต๊ะ3-9501-00651-46-8เลขที่61/5ม.9บ้านอุเปต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "คยจ175สงขลา",
    "brand": "YAMAHA",
    "model": "4D0400",
    "color": "เขียว",
    "user": "นายอับดุลรอหีม เบนอาดัม 3-9402-00134-24-4  เลขที่ 70 หมู่ 10 บ.ท่าคลอง ต.โคกโพธิ์",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "คตฉ663สงขลา",
    "brand": "YAMAHA",
    "model": "LTA-135ED",
    "color": "แดง",
    "user": "นายอายุ สาและ 3-9402-00220-18-3  เลขที่  59 หมู่ 5 ล้อแตก ต.บางโกระ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขทน234นราธิวาส",
    "brand": "YAMAHA",
    "model": "5P0700",
    "color": "ขาว",
    "user": "น.ส.สนะ ฮามะ3-9605-00368-69-1 เลขที่ 127/5ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขตฉ806นราธิวาส",
    "brand": "YAMAHA",
    "model": "50S400",
    "color": "ขาว",
    "user": "นางแมะมูเน๊าะ สะแปอิง3-9605-00433-10-7 เลขที่ 91ม.4บ.บาโงต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขตฉ488นราธิวาส",
    "brand": "YAMAHA",
    "model": "20B500",
    "color": "ขาว",
    "user": "นายยะโกะ เจ๊ะหลง3-9605-00236-90-5 เลขที่ 3ม.3บ.กูตงต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขฉล135นราธิวาส",
    "brand": "YAMAHA",
    "model": "20B200",
    "color": "ดำ",
    "user": "นายมูฮำมัดเสากี เจ๊ะเล๊าะ3-9605-00628-46-8 เลขที่ 92ม.5บ.ตราแดะต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขจษ583นราธิวาส",
    "brand": "YAMAHA",
    "model": "33S400",
    "color": "น้ำเงิน",
    "user": "นายอาหะมัด อูมา2-9602-00007-96-1เลขที่135ม.5บ.โคกกะเปาะต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขจว498นราธิวาส",
    "brand": "YAMAHA",
    "model": "20B100",
    "color": "ขาว",
    "user": "นายยูโซ๊ะ มะเล๊าะ/บอบอ3-9602-00408-01-8เลขที่32/1ม.5บ้านอุเผะต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขงท81นราธิวาส",
    "brand": "YAMAHA",
    "model": "AH-115D",
    "color": "ขาว",
    "user": "นายซูกีพลี แฉะ3-9605-00056-17-6 เลขที่ 30ม.1บ.มะรือโบตกต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขงฉ140ยะลา",
    "brand": "YAMAHA",
    "model": "20B400",
    "color": "ดำ",
    "user": "นายอาแซ วาแม 3-9506-00250-82-9  เลขที่ 65 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขพ546ยะลา",
    "brand": "YAMAHA",
    "model": "20B300",
    "color": "ดำ",
    "user": "นางซารีปะห์ ยีดิง 3-9604-00066-62-5  เลขที่ 4 หมู่ 5 บาโงยือแบ็ง กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขธ198นราธิวาส",
    "brand": "YAMAHA",
    "model": "BE-115D",
    "color": "ม่วง",
    "user": "นายมะหะมะสิศิ ลาเต๊ะ3-9605-00056-67-2 เลขที่ 37ม.2บ.ปละเอ็งต.มะรือโบตก",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขขธ165นราธิวาส",
    "brand": "YAMAHA",
    "model": "AH-115D",
    "color": "ดำ",
    "user": "นายนิอัสวาดี้ เสมอภพ3-9699-00014-53-7 เลขที่ 178ม.8บ.ตันหยงมัสต.ตันหยงมัส",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขกย991นราธิวาส",
    "brand": "YAMAHA",
    "model": "BF-115D-CW",
    "color": "แดง",
    "user": "นายอาหมัด สาอุ3-9605-00345-99-2 เลขที่ 80/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขกม495นราธิวาส",
    "brand": "YAMAHA",
    "model": "BG-115D-L",
    "color": "น้ำเงิน",
    "user": "นายยะยอ ลือบา3-9605-00230-96-6 เลขที่ 71/1ม.2บ.ลาแปต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขกข163นราธิวาส",
    "brand": "YAMAHA",
    "model": "BE-115C",
    "color": "ม่วง",
    "user": "นายอาหะมัด อูมา2-9602-00007-96-1เลขที่135ม.5บ.โคกกะเปาะต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขกก104นราธิวาส",
    "brand": "YAMAHA",
    "model": "BE-115C",
    "color": "ม่วง",
    "user": "นายดือราพา เจ๊ะอูมา3-9605-00017-91-3 เลขที่ 104/2ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "ผบ.มว.๑ (หน.PLATONG)"
  },
  {
    "license_plate": "กษว478ปัตตานี",
    "brand": "YAMAHA",
    "model": "50S300",
    "color": "ดำ",
    "user": "นายมูหะมะยะโกะ ใสทอง 5-9503-00004-74-8 เลขที่ 15/2 หมู่ 8 กอลอกาลี ตะโละดือรามัน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษล479นราธิวาส",
    "brand": "YAMAHA",
    "model": "BD-115C",
    "color": "เขียว",
    "user": "นายรอฟา เจ๊ะดาโอ๊ะ3-9605-00389-10-8 เลขที่ 127/1ม.4บ.บองอต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กวค59ปัตตานี",
    "brand": "YAMAHA",
    "model": "4D0400",
    "color": "ชมพู",
    "user": "น.ส.ซารียะห์ แอสะ3-9605-00402-57-1 เลขที่ 152/1ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวค12นราธิวาส",
    "brand": "YAMAHA",
    "model": "BT-115E",
    "color": "แดง",
    "user": "นายอิบรอเห็ง เจ๊ะอูมา3960500236492 เลขที่ 158ม.2บ.ลาแปต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กรร104นราธิวาส",
    "brand": "YAMAHA",
    "model": "KB-105C",
    "color": "แดง",
    "user": "นายซะรอนิง นิเง๊าะ3-9605-00643-97-7 เลขที่ 20ม.2บ.บาโงบือราแงต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กรบ905นราธิวาส",
    "brand": "YAMAHA",
    "model": "Y111E",
    "color": "ดำ",
    "user": "นายต่วนมะ ซายอ3-9605-00632-92-4 เลขที่ 67/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กรฉ878นราธิวาส",
    "brand": "YAMAHA",
    "model": "L-105",
    "color": "น้ำเงิน",
    "user": "นายอาลียาสะ บูละ3-9605-00646-95-3 เลขที่ 58/1ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "กรรมการฝ่ายปกครองระดับแดอาเราะห์"
  },
  {
    "license_plate": "กยน487นราธิวาส",
    "brand": "YAMAHA",
    "model": "K-105C",
    "color": "ดำ",
    "user": "นายมาหะมะ เปาะเต๊ะ3-9605-00240-90-2 เลขที่ 34ม.3บ.กูตงต.บองอ",
    "note": "กรรมการอาเยาะฝ่ายอูลามา"
  },
  {
    "license_plate": "กยท304นราธิวาส",
    "brand": "YAMAHA",
    "model": "Y100K",
    "color": "น้ำเงิน",
    "user": "นายยะยอ ลือบา3-9605-00230-96-6 เลขที่ 71/1ม.2บ.ลาแปต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กมค432ปัตตานี",
    "brand": "YAMAHA",
    "model": "BE-115D",
    "color": "เขียว",
    "user": "นายซักการียา มะเซ็ง 3-9411-00076-77-4   เลขที่ 29 หมู่ 3 ปล่องหอย",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กพฉ164นราธิวาส",
    "brand": "YAMAHA",
    "model": "Y100T",
    "color": "ดำ",
    "user": "นายสะมะแอ เล็งฮะ3-9602-00250-02-3เลขที่28/1ม.1บ.ปูยูต.เกาะสะท้อน",
    "note": "กรรมการอาเยาะ ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "กบษ96นราธิวาส",
    "brand": "YAMAHA",
    "model": "Y111",
    "color": "น้ำเงิน",
    "user": "นางนิเม๊าะ ตีงี3-9605-00641-42-7 เลขที่ 58ม.7บ.ตันหยงลิมอต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กบต143นราธิวาส",
    "brand": "YAMAHA",
    "model": "Y100K",
    "color": "ดำ",
    "user": "นายดอเล๊าะ สะมะแอ5-9601-99000-73-8เลขที่51/2ม.3บ.โคกแมแนต.มะนังตายอ",
    "note": "กรรมการฝ่ายปกครอง ระดับแดอาเราะห์"
  },
  {
    "license_plate": "1กจ5805ยะลา",
    "brand": "YAMAHA",
    "model": "SE661",
    "color": "แดง",
    "user": "น.ส.กามีล๊ะ มามะ3-9605-00638-55-8 เลขที่ 90/2ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กง5454นราธิวาส",
    "brand": "YAMAHA",
    "model": "UE052",
    "color": "น้ำตาล",
    "user": "นายอับดุลอาซิ อารงค์3-9602-00227-56-1เลขที่66ม.3บ.ศาลาใหม่ต.ศาลาใหม่",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กฆ6951นราธิวาส",
    "brand": "YAMAHA",
    "model": "20B100",
    "color": "ขาว",
    "user": "นายอุสมาน มะมิง3-9605-00753-32-2 เลขที่ 23/2ม.1บ.ปูโงะต.กาลิซา",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "1กก1877ปัตตานี",
    "brand": "YAMAHA",
    "model": "20BA00",
    "color": "ขาว",
    "user": "นายมะรอนี สาและ 3-9402-00216-06-2 เลขที่ 5/1 หมู่ 5 ล้อแตก ต.บางโกระ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กท8102สงขลา",
    "brand": "VOLVO",
    "model": "740GL",
    "color": "ดำ",
    "user": "นายมาหะมะสอดี มะเด็ง 3-9402-00356-98-1  เลขที่ 124/3  หมู่ 7 บ.คลองช้างออก ต.นาเกตุ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ผต4604สงขลา",
    "brand": "TOYOTA",
    "model": "KUN35R-URMSHT A9",
    "color": "ดำ",
    "user": "นายมะรอดี ตอคอ3-9605-00497-92-0 เลขที่ 165ม.5บ.ลาไมต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ผต4354สงขลา",
    "brand": "TOYOTA",
    "model": "KUN15R-TRMDHT C9",
    "color": "น้ำตาล",
    "user": "นายบาฮารี วาดิง3-9605-00105-52-5 เลขที่ 29ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "บห7020สงขลา",
    "brand": "TOYOTA",
    "model": "KUN16R-TRMDYT A3",
    "color": "เทา",
    "user": "นายซุลกิฟลี สาอิ 1-9402-00061-21-6 เลขที่  4/1  หมู่ 1 บ.ป่าบอน ต.ป่าบอน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บธ1103ปัตตานี",
    "brand": "TOYOTA",
    "model": "KUN16R-CRMSYT B1",
    "color": "เทา",
    "user": "นายซุลกิฟลี สาอิ 1-9402-00061-21-6 เลขที่  4/1  หมู่ 1 บ.ป่าบอน ต.ป่าบอน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บท7035ปัตตานี",
    "brand": "TOYOTA",
    "model": "-",
    "color": "เขียว",
    "user": "นายการียา บินเจ๊ะเดร์3-9601-00103-82-7 เลขที่ 119ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "บท2532ปัตตานี",
    "brand": "TOYOTA",
    "model": "KUN16R-CRMSYT B1",
    "color": "เทา",
    "user": "นายอับดุลเล๊าะ ปูเต๊ะ3-9605-00044-57-7 เลขที่ 17/18ม.7บ.บูแบบาเดาะต.มะรือโบตก",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บท1397ปัตตานี",
    "brand": "TOYOTA",
    "model": "",
    "color": "เทา",
    "user": "นายสุกรี มะบา 2-9411-00008-25-8  เลขที่ 80 หมู่ 2 เจาะกะพ้อ กะรุบี",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "บต1775ปัตตานี",
    "brand": "TOYOTA",
    "model": "LN90RCRMSST",
    "color": "เขียว",
    "user": "นายซอรี อูมา3-9605-00236-72-7 เลขที่ 1ม.3บ.กูตงต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บฉ9193 ยะลา",
    "brand": "TOYOTA",
    "model": "KUN35R-URMSHT A7",
    "color": "ดำ",
    "user": "นางนูรียะ ฮาแว 3-9407-00160-13-9 เลขที่ 54 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บฉ7507นราธิวาส",
    "brand": "TOYOTA",
    "model": "GUN122R-BTMXYT A1",
    "color": "ขาว",
    "user": "นายเจ๊ะอุสมาน บากา3-9602-00323-00-4เลขที่20/1ม.3บ.ปะดาดอต.นานาค",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บฉ6451ปัตตานี",
    "brand": "TOYOTA",
    "model": "HILUX",
    "color": "น้ำตาล",
    "user": "นายต่วนมะ ต่วนลอโมง3-9605-00507-08-9 เลขที่ 88ม.6บ.บาโงกูโบต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บฉ5290นราธิวาส",
    "brand": "TOYOTA",
    "model": "KUN15R-URMSHT A9",
    "color": "เทา",
    "user": "นายมะแด มาเย๊าะกาเซ๊ะ3-9605-00431-36-8 เลขที่ 89/1ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "หน.อาเยาะ/(Logistik)"
  },
  {
    "license_plate": "บฉ4482นราธิวาส",
    "brand": "TOYOTA",
    "model": "KUN35R-URMDHT A8",
    "color": "เทา",
    "user": "นายปฐพี มีนา/มะนอร์3-9602-00360-83-0เลขที่85ม.8บ.ราญอต.เกาะสะท้อน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บฉ4393นราธิวาส",
    "brand": "TOYOTA",
    "model": "HILUX",
    "color": "เขียว",
    "user": "นายอิสะมะแอ เล็งฮะ3-9602-00371-30-1เลขที่50/2ม.1บ.ปูยูต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บฉ4005นราธิวาส",
    "brand": "TOYOTA",
    "model": "KUN36R-CRMSYT B3",
    "color": "ดำ",
    "user": "นายต่วนมา ตีงี3-9605-00650-28-1 เลขที่ 61ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บฉ3562ปัตตานี",
    "brand": "TOYOTA",
    "model": "HILUX",
    "color": "ดำ",
    "user": "นายอดินันท์ สาและ 3-9402-00332-38-1 เลขที่ 107/4  หมู่ 7 บ.คลองช้างออก ต.นาเกตุ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บจ9380ยะลา",
    "brand": "TOYOTA",
    "model": "KUN15R-CRMSHT A2",
    "color": "เทา",
    "user": "นายอับดุลรอแมน เสนสะนา 3-9402-00624-77-2 เลขที่ 29/2  หมู่ 3 .ควนโนร ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ8804ยะลา",
    "brand": "TOYOTA",
    "model": "KUN15R-CRMSYT B1",
    "color": "เทา",
    "user": "นายมาหะมะมาลิกี นิกาเร็ง3-9605-00086-88-1 เลขที่ 294ม.1บ.ฮูลูปาเร๊ะ ต.ตันหยงมัส",
    "note": "กรรมการอาเยาะฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "บจ8285นราธิวาส",
    "brand": "TOYOTA",
    "model": "-",
    "color": "เทา",
    "user": "นายอับดุลเลาะ เจ๊ะหะ3-9605-00224-62-1 เลขที่ 11ม.2บ.ลาแปต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ6157นราธิวาส",
    "brand": "TOYOTA",
    "model": "KUN15R-CRMSHT B2",
    "color": "น้ำตาล",
    "user": "นายมีซี อาแว1-9605-00094-29-2 เลขที่ 19ม.6บ.บาโงกูโบต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บจ3534นราธิวาส",
    "brand": "TOYOTA",
    "model": "-",
    "color": "ขาว",
    "user": "นายฮาเล็ง สาและ3-9602-00311-30-8เลขที่43/1ม.1บ.แฆแบ๊ะต.นานาค",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ251นราธิวาส",
    "brand": "TOYOTA",
    "model": "HILUX",
    "color": "น้ำตาล",
    "user": "นายอับดุลตอเล๊ะ แดอีแต3-9605-00803-99-1 เลขที่ 237ม.1บ.ปูโงะต.กาลิซา",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บจ153นราธิวาส",
    "brand": "TOYOTA",
    "model": "HILUX",
    "color": "น้ำตาล",
    "user": "นายดราการียา กะโด3-9605-00364-88-1 เลขที่ 27ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บง6365นราธิวาส",
    "brand": "TOYOTA",
    "model": "-",
    "color": "เทา",
    "user": "น.ส.ซารียะห์ แอสะ3-9605-00402-57-1 เลขที่ 152/1ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บง5905นราธิวาส",
    "brand": "TOYOTA",
    "model": "HILUX",
    "color": "เทา",
    "user": "นายกาลียา อิง3-9605-00448-56-2 เลขที่ 27ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บค4473นราธิวาส",
    "brand": "TOYOTA",
    "model": "HILUX",
    "color": "ขาว",
    "user": "นายมาหะมะรอปี เจ๊ะแว3-9602-00190-49-7เลขที่'5/2ม.1บ.บางขุนทองต.บางขุนทอง",
    "note": "กรรมการอาเยาะ ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "บค2671นราธิวาส",
    "brand": "TOYOTA",
    "model": "HILUX",
    "color": "น้ำเงิน",
    "user": "นายซาการียา เปาะจิ3-9605-00636-63-6 เลขที่ 66ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บ964นราธิวาส",
    "brand": "TOYOTA",
    "model": "HILUX",
    "color": "เทา",
    "user": "นายอับดุลรอแม กือจิ 3-9411-00018-42-1  เลขที่ 15 หมู่ 4 ตะโละดือรามัน ตะโละดือรามัน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ญม1340กรุงเทพมหานคร",
    "brand": "TOYOTA",
    "model": "HILUX",
    "color": "ดำ",
    "user": "นายมะรูดิง เจ๊ะโด3-9605-00770-44-8 เลขที่ 83/2ม.5บ.ลาไมต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กธ2724สงขลา",
    "brand": "TOYOTA",
    "model": "-",
    "color": "น้ำตาล",
    "user": "นายกาลียา อิง3-9605-00448-56-2 เลขที่ 27ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กท4216สงขลา",
    "brand": "TOYOTA",
    "model": "HILUX",
    "color": "น้ำตาล",
    "user": "นายมะรอปะ หะยีลาเด็ง3-9605-00436-18-1 เลขที่ 12/2ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กจ5630ยะลา",
    "brand": "TOYOTA",
    "model": "GUN165R-STTMHT A1",
    "color": "ขาว",
    "user": "นายอับดุลรอแมน เสนสะนา 3-9402-00624-77-2 เลขที่ 29/2  หมู่ 3 .ควนโนร ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กจ4563ยะลา",
    "brand": "TOYOTA",
    "model": "NSP152R-AHXRKT C1",
    "color": "ขาว",
    "user": "น.ส.โนร์ฮูดา อาแว1-9605-00105-61-8 เลขที่ 160ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "กง8037นราธิวาส",
    "brand": "TOYOTA",
    "model": "SOLUNA",
    "color": "น้ำตาล",
    "user": "นายแวลียะห์ รอนิง3-9604-00110-05-5 เลขที่ 140ม.9บ.ลูโลวต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กง3531นราธิวาส",
    "brand": "TOYOTA",
    "model": "NSP152R-AHXRKT B1",
    "color": "เทา",
    "user": "นายยูโซ๊ะ มะเล๊าะ/บอบอ3-9602-00408-01-8เลขที่32/1ม.5บ้านอุเผะต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กง1847นราธิวาส",
    "brand": "TOYOTA",
    "model": "NCP150R-BEPDKT A1",
    "color": "ขาว",
    "user": "นายซายูตี เจ๊ะลี3-9605-00608-39-0 เลขที่ 105ม.1บ.ทำนบต.เฉลิม",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กค879นราธิวาส",
    "brand": "TOYOTA",
    "model": "CORONA",
    "color": "เขียว",
    "user": "นายรุสลี สาแลมะ3-9606-00065-00-2 เลขที่ 27/2ม.7บ.บูแบบาเดาะต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กข9000นราธิวาส",
    "brand": "TOYOTA",
    "model": "TGN51R-NKPSKT A1",
    "color": "เทา",
    "user": "นายสือแม เจ๊ะสะแลแม3-9602-00429-60-1เลขที่70ม.4บ.ตะเหลียงต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กก5763ปัตตานี",
    "brand": "TOYOTA",
    "model": "COROLLA",
    "color": "เขียว",
    "user": "นายต่วนมะ ซายอ3-9605-00632-92-4 เลขที่ 67/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "3กฮ437กรุงเทพมหานคร",
    "brand": "TOYOTA",
    "model": "TGN26R-PRPSKT A1",
    "color": "เทา",
    "user": "นายมาหมะสกรี ลาเต๊ะ3-9602-00385-29-8เลขที่80/1ม.2บ.เกาะสะท้อนต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขธธ905นราธิวาส",
    "brand": "TIGER",
    "model": "SM110S-R",
    "color": "ชมพู",
    "user": "นายมูฮำมัดเสากี เจ๊ะเล๊าะ3-9605-00628-46-8 เลขที่ 92ม.5บ.ตราแดะต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "งธต316สงขลา",
    "brand": "SUZUKI",
    "model": "FL125FS",
    "color": "ดำ",
    "user": "นายมัสลัน สาอุ3-9605-00346-05-1 เลขที่ 80/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "งขง88สงขลา",
    "brand": "SUZUKI",
    "model": "FW110",
    "color": "ดำ",
    "user": "นายอิมบราเฮง กาเต๊ะ 3-9503-00214-13-4  เลขที่ 130/2 หมู่ 6 กือลองแตยอ ม่วงเตี้ย",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขธจ127นราธิวาส",
    "brand": "SUZUKI",
    "model": "RC100J",
    "color": "น้ำเงิน",
    "user": "น.ส.เจ๊ะหม๊ะ อูมา3-9605-00649-47-9 เลขที่ 174ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขทต680นราธิวาส",
    "brand": "SUZUKI",
    "model": "FW110S",
    "color": "ดำ",
    "user": "นายมะนูเด็ง เง๊าะเล็ง2-9602-00015-46-8เลขที่77ม.4ต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขทค946นราธิวาส",
    "brand": "SUZUKI",
    "model": "FW110",
    "color": "น้ำเงิน",
    "user": "นายตอลา สาแม3-9605-00680-55-4 เลขที่ 69ม.5บ.ตราแดะต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขฉษ75นราธิวาส",
    "brand": "SUZUKI",
    "model": "FW110S",
    "color": "ดำ",
    "user": "นายดือเระ สามะ 3-9411-00029-35-1 เลขที่ 44 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขงว913นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XD",
    "color": "เทา",
    "user": "นายเจ๊ะมามะ มะแซ3-9499-00139-44-1เลขที่73/1ม.1บ.ตาบาต.เจ๊ะเห",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขงธ788นราธิวาส",
    "brand": "SUZUKI",
    "model": "FK110",
    "color": "แดง",
    "user": "นายมะซอบรี ลาเต๊ะ3-9605-00254-44-0 เลขที่ 35ม.6บ.อาแวต.มะรือโบตก",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขงท609นราธิวาส",
    "brand": "SUZUKI",
    "model": "FK110",
    "color": "ดำ",
    "user": "นายอับดุลเล๊าะ สะแลแม1-9605-00153-66-3 เลขที่ 59ม.6บ.อาแวต.มะรือโบตก",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขคษ593นราธิวาส",
    "brand": "SUZUKI",
    "model": "FK110",
    "color": "ดำ",
    "user": "นายมาหะมะ เปาะเต๊ะ3-9605-00240-90-2 เลขที่ 34ม.3บ.กูตงต.บองอ",
    "note": "กรรมการอาเยาะฝ่ายอูลามา"
  },
  {
    "license_plate": "ขคล313นราธิวาส",
    "brand": "SUZUKI",
    "model": "FK110",
    "color": "น้ำเงิน",
    "user": "นางแยน๊ะ สะแลแม3-9602-00032-44-1เลขที่22ม.1บ.จาเราะต.ไพรวัน",
    "note": "หน.อาเยาะ"
  },
  {
    "license_plate": "ขขว659นราธิวาส",
    "brand": "SUZUKI",
    "model": "UY125SL",
    "color": "ดำ",
    "user": "นายมัสลัน ยูโซ๊ะ1-9602-00034-46-4เลขที่89/1ม.7บ.ภัทรภักดีต.เจ๊ะเห",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขขย186นราธิวาส",
    "brand": "SUZUKI",
    "model": "FK110",
    "color": "ดำ",
    "user": "นายไพศาล หะยีเจ๊ะโว๊ะ3-9605-00063-97-1 เลขที่ 37/1ม.1บ.ตันหยงมัสเก่าต.ตันหยงมัส",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขกษ252นราธิวาส",
    "brand": "SUZUKI",
    "model": "UY125S",
    "color": "ดำ",
    "user": "นายมาหะมะ เปาะเต๊ะ3-9605-00240-90-2 เลขที่ 34ม.3บ.กูตงต.บองอ",
    "note": "กรรมการอาเยาะฝ่ายอูลามา"
  },
  {
    "license_plate": "ขกง268นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSU",
    "color": "เหลือง",
    "user": "นายมะนาอิง ลาเต๊ะ3-9605-00855-88-5 เลขที่ 106/1ม.6บ.กาเด็งต.กาลิซา",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขกข962ปัตตานี",
    "brand": "SUZUKI",
    "model": "FW110S",
    "color": "ขาว",
    "user": "นายอับดุลมานะ เจ๊ะและ 3-9411-00103-40-2  เลขที่ 30 หมู่ 8 มะแนลาแล ปล่องหอย",
    "note": "กรรมการฝ่ายปกครองระดับแดอาเราะห์"
  },
  {
    "license_plate": "ขกก154นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSLU",
    "color": "ดำ",
    "user": "นายอับดุลรอซะ มะแซ3-9602-00097-74-8เลขที่144ม.5บ.โคกกูแวต.พร่อน",
    "note": "กรรมการอาเยาะ ฝ่ายประชาสัมพันธ์"
  },
  {
    "license_plate": "กษว889นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCMU",
    "color": "เหลือง",
    "user": "นายมะซอตา ซาแล๊ะ3-9605-00606-38-9 เลขที่ 71ม.1บ.ทำนบต.เฉลิม",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กษล701นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSLU",
    "color": "ดำ",
    "user": "นายอับดุลเล๊าะ โอ๊ะโด๊ะ5-9605-99000-32-8 เลขที่ 172/1ม.5บ.ลาไมต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษย38นราธิวาส",
    "brand": "SUZUKI",
    "model": "-",
    "color": "เขียว",
    "user": "นายมะกอเดร์ แยนา3-9408-00073-98-0 เลขที่ 149ม.8บ.กำปงปาเร๊ะต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษบ21ปัตตานี",
    "brand": "SUZUKI",
    "model": "FW110",
    "color": "ดำ",
    "user": "นายนูรดีน ดือเฆะ 3-9402-00610-54-2  เลขที่ 128/8  หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษท217นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSU",
    "color": "น้ำเงิน",
    "user": "นายสะอูดี ดาแห3-9602-00435-13-9เลขที่88ม.7ต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กษต497นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "เทา",
    "user": "นายมาน ตาปา3-9602-00151-48-3เลขที่188/1ม.3ต.ศาลาใหม่",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กษฉ895นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSDU",
    "color": "น้ำเงิน",
    "user": "นายรอมือลี ปิ3-9602-00399-22-1เลขที่91ม.6ต.บางขุนทอง",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กษฉ226นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "น้ำเงิน",
    "user": "นายอับดุลเล๊าะ ปูเต๊ะ3-9605-00044-57-7 เลขที่ 17/18ม.7บ.บูแบบาเดาะต.มะรือโบตก",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กษจ493ปัตตาน",
    "brand": "SUZUKI",
    "model": "-",
    "color": "น้ำเงิน",
    "user": "นายสุไฮหลี หนิจิบุลัด 1-9402-00009-68-1 เลขที่ 55/2 หมู่ 4 บ.คลองช้าง ต.นาเกตุ",
    "note": "ผบ.หมู่ 1 มว.1"
  },
  {
    "license_plate": "กษค225นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "น้ำเงิน",
    "user": "นายซัยนุง ดาโอ๊ะ3-9605-00676-91-3 เลขที่ 18ม.5บ.ตราแดะต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษข8นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "แดง",
    "user": "นายมะอาซรี สาระสีนา3-9602-00366-63-3เลขที่'30/1ม.4บ.กอลอต๊ะต.นานาค",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กษก438นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSDU",
    "color": "แดง",
    "user": "นายสาบีลา มะเด็ง/แบมะ2-9605-00019-38-8 เลขที่ 32/25ม.2บ.ลาแปต.บองอ",
    "note": "หน.ฝ่ายระเบิด (TL/เลอทูปัน)"
  },
  {
    "license_plate": "กวว373ปัตตานี",
    "brand": "SUZUKI",
    "model": "FL125FS",
    "color": "เทา",
    "user": "นายรอมลี สีบู 3-9402-00134-74-1 เลขที่ 69 หมู่ 10 บ.ท่าคลอง ต.โคกโพธิ์",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กวพ69นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSDU",
    "color": "แดง",
    "user": "นายมูฮัมมัดบือราฮัม ต่วนลอหนิ3-9605-00232-29-2 เลขที่ 79ม.2บ.ลาแปต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กวท312นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSU",
    "color": "แดง",
    "user": "นายมานพธ์ หะยีนาแว/ไซนุง3-9605-00635-65-6 เลขที่ 48/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวต155ปัตตานี",
    "brand": "SUZUKI",
    "model": "FD110XCSMU",
    "color": "น้ำเงิน",
    "user": "นายฮาวารี มะเด็ง 3-9402-00357-006 เลขที่ 124/3 หมู่ 7 บ.คลองช้างออก ต.นาเกตุ",
    "note": "ชุด TL มว.2 (ชํานาญดานระเบิด)"
  },
  {
    "license_plate": "กวฉ752นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSU",
    "color": "ฟ้า",
    "user": "นายอาลียะห์ เจ๊ะเตะ3-9605-00176-72-4 เลขที่ 4ม.7บ.บูแบบาเดาะต.มะรือโบตก",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กวจ539นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSDU",
    "color": "ฟ้า",
    "user": "นายอับดุลตอเละ เจ๊ะแม3-9605-00233-16-7 เลขที่ 88ม.2บ.ลาแปต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กวก161นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "ฟ้า",
    "user": "นายมะลาเซ็ง มะ3-9605-00644-37-0 เลขที่ 25/1ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กลล605นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD125X",
    "color": "น้ำตาล",
    "user": "นายมะยาเอะ มามะ5-9605-99000-01-8 เลขที่ 10ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กลล266นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCMU",
    "color": "เหลือง",
    "user": "นายอับดุลเลาะ ตีงี/แบเลาะ2-9605-00651-22-8 เลขที่ 120ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กลร363นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "แดง",
    "user": "นายดอเล๊าะ มือลอ3-9605-00420-50-1 เลขที่ 74ม.3บ.ลูโบ๊ะบาตูต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กลย314นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "น้ำเงิน",
    "user": "นายมะรอดี ตอคอ3-9605-00497-92-0 เลขที่ 165ม.5บ.ลาไมต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กลม147นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSU",
    "color": "เหลือง",
    "user": "นายการียา บินเจ๊ะเดร์3-9601-00103-82-7 เลขที่ 119ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กลธ833นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSDU",
    "color": "ฟ้า",
    "user": "นายมูฮำมัดบัสรี สือแลแม1-9602-00013-89-1เลขที่12ม.9ต.เกาะสะท้อน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กลท202นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "แดง",
    "user": "นายเอกชัย ดือราแม3-9699-00352-68-4 เลขที่ 79ม.6บ.บาโงกูโบต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กลง373นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "เทา",
    "user": "นายซูปียัง เจ๊ะโซ๊ะ2-9605-00017-47-4 เลขที่ 44/6ม.1บ.สาเม๊าะต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กลง197นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "ดำ",
    "user": "นายมูหำหมัดสุกรี ดะและ3-9410-00400-26-0 เลขที่ 81/3ม.3บ.กูตงต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กรษ41ปัตตานี",
    "brand": "SUZUKI",
    "model": "UY125S",
    "color": "แดง",
    "user": "นายรอมลี สีบู 3-9402-00134-74-1 เลขที่ 69 หมู่ 10 บ.ท่าคลอง ต.โคกโพธิ์",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กรร651นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "เหลือง",
    "user": "นายนิแม็ง ต่วนจำปากอ3-9605-00641-74-5 เลขที่ 147ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กรย957นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD125X",
    "color": "น้ำตาล",
    "user": "นายมาหะมะโซรี แตแว3-9603-00215-43-2 เลขที่ 140/4ม.8บ.อาแนต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กรบ272นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110X",
    "color": "น้ำเงิน",
    "user": "น.ส.รอซีดะ นิแม2-9605-00006-08-1 เลขที่ 83/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กรน336นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XSM",
    "color": "แดง",
    "user": "นายอับดุลมาน๊ะ ดิง3-9605-00636-97-1 เลขที่ 67ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กรต914นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XC",
    "color": "น้ำเงิน",
    "user": "นายมะซอแล๊ะ สาแล๊ะ3-9605-00381-25-5 เลขที่ 62/2ม.4บ.บองอต.บองอ",
    "note": "ผบ.ร้อย.๘(หน.KOMPI)"
  },
  {
    "license_plate": "กรฉ144นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110X",
    "color": "น้ำตาล",
    "user": "นายมะยูรี หนิแว3-9605-00502-22-2 เลขที่ 44/2ม.6บ.บาโงกูโบต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กรง297นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCS",
    "color": "ฟ้า",
    "user": "นายรอสือลี เล็งฮะ/ยาลี3-9602-00373-46-0เลขที่52/1ม.1บ.ปูยูต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กรก286ปัตตานี",
    "brand": "SUZUKI",
    "model": "FK110S",
    "color": "ดำ",
    "user": "นายรอมลี สีบู 3-9402-00134-74-1 เลขที่ 69 หมู่ 10 บ.ท่าคลอง ต.โคกโพธิ์",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กยล846ปัตตานี",
    "brand": "SUZUKI",
    "model": "UY125SL",
    "color": "เทา",
    "user": "นายอับดุลรอแม กือจิ 3-9411-00018-42-1  เลขที่ 15 หมู่ 4 ตะโละดือรามัน ตะโละดือรามัน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยล469ยะลา",
    "brand": "SUZUKI",
    "model": "FD110XCU",
    "color": "น้ำเงิน",
    "user": "นายตอพา เจ๊ะโซ๊ะ5-9501-00006-93-3เลขที่46/2ม.9บ้านอุเปต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยร969นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSM",
    "color": "แดง",
    "user": "นายเจ๊ะอารง เจ๊ะหะ1-9605-00042-20-9 เลขที่ 73/2ม.6บ.บาโงกูโบต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กยร70ยะลา",
    "brand": "SUZUKI",
    "model": "FD110XCU คศ.2004",
    "color": "น้ำเงิน",
    "user": "นายมะเด็ง/ซาการียา สาเมาะ 3-9402-00167-27-4 เลขที่ 20/2 หมู่ 2 บ.คลองหิน ต.ปากลอ",
    "note": "ผบ.หมู่ 1 มว.2"
  },
  {
    "license_plate": "กยร354นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110X",
    "color": "น้ำตาล",
    "user": "นายซัยนุง ดาโอ๊ะ3-9605-00676-91-3 เลขที่ 18ม.5บ.ตราแดะต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยย698นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110X",
    "color": "น้ำเงิน",
    "user": "นายมะปาสะดี แกมะ5-9605-99000-13-1 เลขที่ 4ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กยม124นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110X",
    "color": "น้ำเงิน",
    "user": "นายต่วนมะ ต่วนลอโมง3-9605-00507-08-9 เลขที่ 88ม.6บ.บาโงกูโบต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยน528นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110X",
    "color": "น้ำเงิน",
    "user": "นายนิแว มะดีเยาะ3-9601-00304-86-5 เลขที่ 21/1ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยธ435นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XS",
    "color": "เทา",
    "user": "นายมาน๊ะ มามะ1-9602-00001-39-6เลขที่71ม.7บ.จาแบปะต.เกาะสะท้อน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กยท229นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XS",
    "color": "เทา",
    "user": "นายกาหริ่ม สือมาแอ3-9602-00222-33-0เลขที่92ม.3บ.ศาลาใหม่ต.ศาลาใหม่",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กมธ17ปัตตานี",
    "brand": "SUZUKI",
    "model": "FD110XCSU",
    "color": "น้ำเงิน",
    "user": "นายอับดุลมุตอลิบ ดิง 3-9402-00127-74-4 เลขที่ 22/2 หมู่ 6 บ.ทุ่งยาว ต.โคกโพธิ์",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กมต960ยะลา",
    "brand": "SUZUKI",
    "model": "RC110K",
    "color": "น้ำเงิน",
    "user": "นายตอพา เจ๊ะโซ๊ะ5-9501-00006-93-3เลขที่46/2ม.9บ้านอุเปต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กพง126นราธิวาส",
    "brand": "SUZUKI",
    "model": "RC110",
    "color": "น้ำเงิน",
    "user": "นายอิบรอเฮ็ง ปาละมูมิง3-9605-00219-46-6 เลขที่ 63ม.1บ.มะแก่งต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กพข706ปัตตานี",
    "brand": "SUZUKI",
    "model": "FD110XCSU",
    "color": "แดง",
    "user": "นายมะกอเซ็ง สาเหาะ 3-9402-00611-23-9  เลขที่ 135 หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กบร835นราธิวาส",
    "brand": "SUZUKI",
    "model": "RC110K",
    "color": "เขียว",
    "user": "นายมะรอเซ๊ะ กูโน3-9602-00238-53-8เลขที่132/2ม.9บ.กาเยาะมาตีต.ไพรวัน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กกว805นราธิวาส",
    "brand": "SUZUKI",
    "model": "RC110",
    "color": "ดำ",
    "user": "นายรอวี หะยีดิง/ยูโซ๊ะ2-9604-00008-13-9 เลขที่ 61ม.6บ.บาโงอาแซต.ตันหยงมัส",
    "note": "ฝ่าย Pha(ชำนาญป่าภูเขา)"
  },
  {
    "license_plate": "กกว47นราธิวาส",
    "brand": "SUZUKI",
    "model": "RC110C",
    "color": "ดำ",
    "user": "นายอารอฟัด สือรี3-9605-00642-20-2 เลขที่ 148ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กกต992นราธิวาส",
    "brand": "SUZUKI",
    "model": "RC110",
    "color": "แดง",
    "user": "นายสือมาน มะแซ1-9605-00003-99-8 เลขที่ 57ม.3บ.กูตงต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กกฉ691นราธิวาส",
    "brand": "SUZUKI",
    "model": "RC110D",
    "color": "ดำ",
    "user": "นายฟัครุรอฎี แกมะ/แบมัง3-9605-00100-97-3 เลขที่ 7ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กค6792นราธิวาส",
    "brand": "SUZUKI",
    "model": "FL125FS",
    "color": "แดง",
    "user": "น.ส.ซารียะห์ แอสะ3-9605-00402-57-1 เลขที่ 152/1ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กก9713ยะลา",
    "brand": "SUZUKI",
    "model": "FW110",
    "color": "ขาว",
    "user": "นายมะซาวี ดอเลาะ 2-9411-00012-57-3 เลขที่ 34 หมู่ 8 ปาแดกามูดิง กะรุบี",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขตย254นราธิวาส",
    "brand": "STALLION",
    "model": "125MINI",
    "color": "ขาว",
    "user": "นายซำซูดีน บือซา3-9605-00406-37-7 เลขที่ 41ม.2บ.เจ๊ะเกต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บฉ1346นราธิวาส",
    "brand": "NISSAN",
    "model": "KTGD21FX4%GLAM",
    "color": "เทา",
    "user": "นายฮาเล็ง สาและ3-9602-00311-30-8เลขที่43/1ม.1บ.แฆแบ๊ะต.นานาค",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ9656ปัตตานี",
    "brand": "NISSAN",
    "model": "-",
    "color": "เขียว",
    "user": "นายหนิรอสือลี หนิสาเร๊ะ3-9605-00506-29-5 เลขที่ 63/2ม.6บ.บาโงกูโบต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บง816นราธิวาส",
    "brand": "NISSAN",
    "model": "CGD21SFU1%DX",
    "color": "ดำ",
    "user": "นายกามา อาลี3-9602-00098-60-4เลขที่137ม.5บ.โคกกูแวต.พร่อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บง7822นราธิวาส",
    "brand": "NISSAN",
    "model": "KTGF21FX%DXAM",
    "color": "เขียว",
    "user": "นายอายุ ดือเร๊ะ3-9602-00116-17-3เลขที่165ม.5บ.ปูลาเจ๊ะมูดอต.ศาลาใหม่",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บง3189นราธิวาส",
    "brand": "NISSAN",
    "model": "-",
    "color": "เขียว",
    "user": "นายไซมิง นิกาเร็ง3-9602-00371-79-3เลขที่38ม.1บ.ปูยูต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บง2935ปัตตานี",
    "brand": "NISSAN",
    "model": "",
    "color": "ดำ",
    "user": "นายอับดุลรอซิ อาแว 3-9402-00400-77-7  เลขที่ 12 หมู่ 4  บ.คลองช้างออก ต.นาเกตุ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขง9766สงขลา",
    "brand": "NISSAN",
    "model": "CVLGRVYD40UHG",
    "color": "",
    "user": "นายมะรอดี ตอคอ3-9605-00497-92-0 เลขที่ 165ม.5บ.ลาไมต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กง6984นราธิวาส",
    "brand": "NISSAN",
    "model": "-",
    "color": "เทา",
    "user": "นายอายิ ยามะนอ3-9602-00147-92-3เลขที่'28/1ม.6บ.กูบูต.ไพรวัน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บฉ5135นราธิวาส",
    "brand": "MITSUBISHI",
    "model": "-",
    "color": "เทา",
    "user": "นายอิสมะแอ นิแว3-9601-00274-69-9เลขที่201/5ม.5บ.โคกกูแวต.พร่อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "บฉ2716ปัตตานี",
    "brand": "MITSUBISHI",
    "model": "",
    "color": "เทา",
    "user": "นายมุมิง กือนิ 3-9411-00030-22-7   เลขที่ 18 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "บจ7093นราธิวาส",
    "brand": "MITSUBISHI",
    "model": "KA4TNENMZRU",
    "color": "เทา",
    "user": "นายมาหามะ บูละ3-9605-00099-51-7 เลขที่ 34/3ม.2บ.ลาแปต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ1484ปัตตานี",
    "brand": "MITSUBISHI",
    "model": "L200",
    "color": "เขียว",
    "user": "นายต่วนยูนุ๊ ดอฆอ3-9605-00648-62-6 เลขที่ 138ม.7บ.ตันหยงลิมอต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ1056นราธิวาส",
    "brand": "MITSUBISHI",
    "model": "K67TCENDRU",
    "color": "แดง",
    "user": "นายหนิรอสือลี หนิสาเร๊ะ3-9605-00506-29-5 เลขที่ 63/2ม.6บ.บาโงกูโบต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขข7668นราธิวาส",
    "brand": "MITSUBISHI",
    "model": "KA4TXJYHZPRU",
    "color": "ดำ",
    "user": "นายมูฮำมัดเสากี เจ๊ะเล๊าะ3-9605-00628-46-8 เลขที่ 92ม.5บ.ตราแดะต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษ1395สงขลา",
    "brand": "MITSUBISHI",
    "model": "KB4TGJYXZPRU",
    "color": "เทา",
    "user": "นายอับดุลมานะ บือราเฮง3-9605-00613-46-6 เลขที่ 56ม.2บ.เจ๊ะเกต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กง838นราธิวาส",
    "brand": "MITSUBISHI",
    "model": "KA4TNJNUZRU",
    "color": "ดำ",
    "user": "นายมะอาดี ดือราแม3-9605-00637-00-4 เลขที่ 67/2ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กง825นราธิวาส",
    "brand": "MITSUBISHI",
    "model": "K77TGJENXRU",
    "color": "เขียว",
    "user": "นายยะยอ ลือบา3-9605-00230-96-6 เลขที่ 71/1ม.2บ.ลาแปต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กง3352ปัตตานี",
    "brand": "MITSUBISHI",
    "model": "K64TJENMRU",
    "color": "น้ำตาล",
    "user": "นายเชิดศักดิ์ เจะโส๊ะเจะหลี 1-9402-00003-01-1 เลที่ 83 หมู่ 11 บ.คลองปอม ต.โคกโพธิ์",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กค7336ปัตตานี",
    "brand": "MITSUBISHI",
    "model": "KA4TNJNUZRU",
    "color": "ดำ",
    "user": "นายหนิรอสือลี หนิสาเร๊ะ3-9605-00506-29-5 เลขที่ 63/2ม.6บ.บาโงกูโบต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กค5644นราธิวาส",
    "brand": "MITSUBISHI",
    "model": "-",
    "color": "แดง",
    "user": "นายเจ๊ะอารง เจ๊ะหะ1-9605-00042-20-9 เลขที่ 73/2ม.6บ.บาโงกูโบต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กค5389ปัตตานี",
    "brand": "MITSUBISHI",
    "model": "",
    "color": "เทา",
    "user": "นายอาแว เตาะสาตู 3-9403-00265-27-8  เลขที่ 124/9  หมู่ 7 บ.คลองช้างออก ต.นาเกตุ",
    "note": "กรรมการฝ่ายปกครองระดับแดอาเราะห"
  },
  {
    "license_plate": "กค4517นราธิวาส",
    "brand": "MITSUBISHI",
    "model": "KA4TNJNUZRU",
    "color": "เทา",
    "user": "นายอิบรอฮิม บินอาแด3-9605-00090-31-5 เลขที่ 330ม.11ต.ตันหยงมัส",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "2กย6027กรุงเทพมหานคร",
    "brand": "MITSUBISHI",
    "model": "KG4WGYPSRU",
    "color": "ขาว",
    "user": "นายเจ๊ะมามะ มะแซ3-9499-00139-44-1เลขที่73/1ม.1บ.ตาบาต.เจ๊ะเห",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บท6973ปัตตานี",
    "brand": "MAZDA",
    "model": "-",
    "color": "เขียว",
    "user": "นายนิแว มะดีเยาะ3-9601-00304-86-5 เลขที่ 21/1ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ3902ปัตตานี",
    "brand": "MAZDA",
    "model": "",
    "color": "เทา",
    "user": "นายมาห์รุดิน ตาเยะ 3-9402-00321-16-8 เลขที่ 126 หมู่ 5 บ.สลาม ต.นาประดู่",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บค7846ปัตตานี",
    "brand": "MAZDA",
    "model": "",
    "color": "น้ำเงิน",
    "user": "นายอาแว เตาะสาตู 3-9403-00265-27-8  เลขที่ 124/9  หมู่ 7 บ.คลองช้างออก ต.นาเกตุ",
    "note": "กรรมการฝ่ายปกครองระดับแดอาเราะห"
  },
  {
    "license_plate": "5กพ5062กรุงเทพมหานคร",
    "brand": "MAZDA",
    "model": "UL5VRAN",
    "color": "เทา",
    "user": "นายอาหะมัด อูมา2-9602-00007-96-1เลขที่135ม.5บ.โคกกะเปาะต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ตค190ปัตตานี",
    "brand": "KUBOTA",
    "model": "M9540DTH",
    "color": "ส้ม",
    "user": "นายซอลาฮุดดิน ตาเยะ 5-9411-00001-96-7   เลขที่ 43 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "1ตค9945กรุงเทพมหานคร",
    "brand": "KUBOTA",
    "model": "M108S",
    "color": "ส้ม",
    "user": "นายนูรดีน ดือเฆะ 3-9402-00610-54-2  เลขที่ 128/8  หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ผต5920สงขลา",
    "brand": "ISUZU",
    "model": "TFR86JEQN4 (M)",
    "color": "ดำ",
    "user": "นายมะสะกรี แดมอ 3-9402-00368-08-3 เลขที่ 20   หมู่ 5  บ.ท่าเรือ ต.ท่าเรือ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ผก8572สงขลา",
    "brand": "ISUZU",
    "model": "TFR86HSM2A (M)",
    "color": "เทา",
    "user": "นายแวฮามะ บากา3-9605-00787-44-8 เลขที่ 96ม.7บ.บาโงสะโตต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บว9795สงขลา",
    "brand": "ISUZU",
    "model": "TFR85HPM8B (M)",
    "color": "เทา",
    "user": "นายเจะอาลี กามะ 3-9411-00026-76-9   เลขที่ 18/1 หมู่ 8 ปาแดกามูดิง กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บว3847สงขลา",
    "brand": "ISUZU",
    "model": "TFR86HPM5A (M)",
    "color": "เทา",
    "user": "นายสาการียา เกาะและ 3-9402-00250-10-4 เลขที่ 8 หมู่ 2 คูระ ม่วงเตี้ย",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บล9265สงขลา",
    "brand": "ISUZU",
    "model": "TFR86HPM5A (M)",
    "color": "เทา",
    "user": "นายมุสะตอปากามา สาและ 3-9402-00355-59-3 เลขที่  107  หมู่ 7 บ.คลองช้างออก ต.นาเกตุ",
    "note": "กรรมการฝ่ายปกครองระดับแดอาเราะห"
  },
  {
    "license_plate": "บล1065สงขลา",
    "brand": "ISUZU",
    "model": "TFR86HPM5A (M)",
    "color": "เทา",
    "user": "นายอับดุลมานะ เจ๊ะและ 3-9411-00103-40-2  เลขที่ 30 หมู่ 8 บือแนลาแล ปล่องหอย",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บล1065 สงขลา",
    "brand": "ISUZU",
    "model": "TFR86HPM5A (M)",
    "color": "เทา",
    "user": "นายอับดุลมานะ เจ๊ะและ 3-9411-00103-40-2  เลขที่ 30 หมู่ 8 มะแนลาแล ปล่องหอย",
    "note": "กรรมการฝ่ายปกครองระดับแดอาเราะห์"
  },
  {
    "license_plate": "บต7911ปัตตานี",
    "brand": "ISUZU",
    "model": "TFR77HPM5DAM",
    "color": "น้ำตาล",
    "user": "นายอาดือนัน ปาแซ2-9605-00013-12-6 เลขที่ 237/2ม.2บ.กาลิซาต.กาลิซา",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "บต1786ปัตตานี",
    "brand": "ISUZU",
    "model": "",
    "color": "เทา",
    "user": "นายมาซาตา สาและ 3-9411-00103-59-3 เลขที่ 32 หมู่ 8 มะแนดาแล ปล่องหอย",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บฉ9194ยะลา",
    "brand": "ISUZU",
    "model": "TFR54HSM2AAM",
    "color": "น้ำเงิน",
    "user": "นายมะดารี เจะเลาะ 3-9402-00562-03-3 เลขที่  26 หมู่ 6 บ.โคกอ้น ต.ท่าเรือ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "บฉ7535นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR86JEMN2 (M)",
    "color": "เทา",
    "user": "นายอาดือนัน เล๊าะ3-9602-00373-15-0เลขที่103ม.1บ.ปูยูต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "บฉ7106นราธิวาส",
    "brand": "ISUZU",
    "model": "-",
    "color": "ขาว",
    "user": "นายมาหะมะรอปี เจ๊ะแว3-9602-00190-49-7เลขที่'5/2ม.1บ.บางขุนทองต.บางขุนทอง",
    "note": "กรรมการอาเยาะ ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "บฉ5568นราธิวาส",
    "brand": "ISUZU",
    "model": "TFS77HPM7DAM",
    "color": "ดำ",
    "user": "นายมะหะมะ มูซอ3-9605-00638-23-0 เลขที่ 175ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บฉ5523นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR86JERH6 (M)",
    "color": "เทา",
    "user": "นายอาบูฮะซัน ยูโซ๊ะ3-9602-00353-69-8เลขที่26ม.6ต.เจ๊ะเห",
    "note": "ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "บฉ3565นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR54HB W/H M",
    "color": "เขียว",
    "user": "นายสะอารอนิง อาแว1-9602-00032-00-3เลขที่75ม.8ต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "บฉ2390นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR86HPM4A (M)",
    "color": "เทา",
    "user": "นายมามะ ตาเย๊ะ3-9602-00111-14-7เลขที่121/1ม.5บ.ปูลาเจ๊ะมูดอต.ศาลาใหม่",
    "note": "กรรมการอาเยาะ ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "บฉ1265ปัตตานี",
    "brand": "ISUZU",
    "model": "TFR54HPBD M",
    "color": "เขียว",
    "user": "นายมะรอบี ดีแม 3-9501-00106-54-1  เลขที่ 152/1หมู่ 5 บ.จุปะ ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ918นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR54HPYSM",
    "color": "ฟ้า",
    "user": "นายสือแม เจ๊ะสะแลแม3-9602-00429-60-1เลขที่70ม.4บ.ตะเหลียงต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ6726นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR86HPM5A (M)",
    "color": "เทา",
    "user": "นายมะนาวี มะหะมะ5-9605-00021-84-1 เลขที่ 114/1ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ6344นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR54HB W H M",
    "color": "เขียว",
    "user": "นายมะหะมะ มูซอ3-9605-00638-23-0 เลขที่ 175ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ6097ปัตตานี",
    "brand": "ISUZU",
    "model": "-",
    "color": "เทา",
    "user": "นายการียา บินเจ๊ะเดร์3-9601-00103-82-7 เลขที่ 119ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "บจ5687นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR86HPM8B (M)",
    "color": "เทา",
    "user": "นายมะโรส เจ๊ะซอ3-9605-00084-07-2 เลขที่ 269/1ม.1บ.ตันหยงมัสเก่าต.ตันหยงมัส",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ5634นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR86HPM8B (M)",
    "color": "ดำ",
    "user": "นายต่วนมะ ซายอ3-9605-00632-92-4 เลขที่ 67/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ3700นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR85HPM8C (M)",
    "color": "เทา",
    "user": "นายมาหะมะ เปาะเต๊ะ3-9605-00240-90-2 เลขที่ 34ม.3บ.กูตงต.บองอ",
    "note": "กรรมการอาเยาะฝ่ายอูลามา"
  },
  {
    "license_plate": "บจ2168นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR86HPM5A (M)",
    "color": "เทา",
    "user": "นายซาการียา เปาะจิ3-9605-00636-63-6 เลขที่ 66ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บจ1962นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR86HPM5A (M)",
    "color": "ฟ้า",
    "user": "นายยายอ โต๊ะมามง3-9605-00502-40-1 เลขที่ 36ม.6บ.บาโงกูโบต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บค2287นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR54HPYSLBDM",
    "color": "เทา",
    "user": "นายนิเฮง ลอโมง3-9605-00644-24-8 เลขที่ 24ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บก6689นราธิวาส",
    "brand": "ISUZU",
    "model": "-",
    "color": "ดำ",
    "user": "นายแวฮามะ บากา3-9605-00787-44-8 เลขที่ 96ม.7บ.บาโงสะโตต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "นข1065นราธิวาส",
    "brand": "ISUZU",
    "model": "QFR54E",
    "color": "ขาว",
    "user": "นายกามา อาลี3-9602-00098-60-4เลขที่137ม.5บ.โคกกูแวต.พร่อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กร8468สงขลา",
    "brand": "ISUZU",
    "model": "TFR85HDQ8H (P)",
    "color": "ขาว",
    "user": "นายอัสรี กามะ 3-9411-00026-74-2   เลขที่ 18/1 หมู่ 8 ปาแดกามูดิง กะรุบี",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กจ5897ปัตตานี",
    "brand": "ISUZU",
    "model": "TFR85HDM8H (M)",
    "color": "เทา",
    "user": "นายนิเซ็ง อีดือเร๊ะ3-9605-00431-85-6 เลขที่ 76ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กง7885ปัตตานี",
    "brand": "ISUZU",
    "model": "TFR85JCQH6 (P)",
    "color": "ขาว",
    "user": "นางฮาลีเมาะ สือแม 3-9506-00431-32-7   เลขที่ 4 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "กง5747ปัตตานี",
    "brand": "ISUZU",
    "model": "TFR77HDM8HBM",
    "color": "เทา",
    "user": "นายอำรำ จือนะรา 2-9411-00012-63-8 เลขที่ 87 หมู่ 6 โลทู ปล่องหอย",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กค8186นราธิวาส",
    "brand": "ISUZU",
    "model": "TFR77HDM5DAM",
    "color": "น้ำตาล",
    "user": "นายมะนาอิง ลาเต๊ะ3-9605-00855-88-5 เลขที่ 106/1ม.6บ.กาเด็งต.กาลิซา",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กค6276ปัตตานี",
    "brand": "ISUZU",
    "model": "TFR86HDM8H (P)",
    "color": "ขาว",
    "user": "นายอับดุลรอแมน เสนสะนา 3-9402-00624-77-2 เลขที่ 29/2  หมู่ 3 .ควนโนร ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กค5531ยะลา",
    "brand": "ISUZU",
    "model": "TFR77HDM8HBM",
    "color": "เทา",
    "user": "นายดอเลาะ หะยียุโซ๊ะ 3-9402-00610-32-1  เลขที่ 128/3  หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กข7408นราธิวาส",
    "brand": "ISUZU",
    "model": "TFS85HDM7K (M)",
    "color": "ดำ",
    "user": "นายต่วนดาโอ๊ะ ลือบา3-9605-00231-25-3 เลขที่ 73/2ม.2บ.ลาแปต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "สปย567กรุงเทพมหานคร",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "ขาว",
    "user": "นายอัสรี เจ๊ะเต๊ะ1-9605-00179-04-2 เลขที่ 62ม.6บ.บาโงกูโบต.บองอ",
    "note": "กลุ่มเสี่ยง"
  },
  {
    "license_plate": "ลธต547กรุงเทพมหานคร",
    "brand": "HONDA",
    "model": "NF100R(A)",
    "color": "แดง",
    "user": "นายต่วนฮารง ซายอ3-9605-00633-04-1 เลขที่ 25ม.1บ.ตันหยงลิมอต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "รนล686กรุงเทพมหานคร",
    "brand": "HONDA",
    "model": "NC110BC(A)",
    "color": "ขาว",
    "user": "นายอดุลย์ สือแม3-9605-00370-01-6 เลขที่ 129/2ม.8บ.อาแนต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ภจ4976กรุงเทพมหานคร",
    "brand": "HONDA",
    "model": "CIVIC",
    "color": "น้ำตาล",
    "user": "นายสะมะแอ สาแม3-9005-00229-57-7เลขที่66/1ม.7บ้านกรงปินังต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "งขธ458สงขลา",
    "brand": "HONDA",
    "model": "KT110D",
    "color": "ดำ",
    "user": "นายมะนาเซ เล๊าะยีตา3-9605-00414-91-9 เลขที่ 151ม.2บ.กาลิซาต.กาลิซา",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "คษว508สงขลา",
    "brand": "HONDA",
    "model": "NF110TM",
    "color": "แดง",
    "user": "นายมักตา ลาเต๊ะ 3-9411-00010-98-6  เลขที่ 55 หมู่ 2 เจาะกะพ้อ กะรุบี",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขธษ458นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KDFD(TH)",
    "color": "น้ำเงิน",
    "user": "นายกูนุห์ ลือบา3-9605-00230-95-8 เลขที่ 71ม.2บ.ลาแปต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขธล762นราธิวาส",
    "brand": "HONDA",
    "model": "KT110D",
    "color": "ขาว",
    "user": "นายแวลียะห์ รอนิง3-9604-00110-05-5 เลขที่ 140ม.9บ.ลูโลวต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขธล616นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KDFD(TH)",
    "color": "ดำ",
    "user": "นายอาบูฮะซัน ยูโซ๊ะ3-9602-00353-69-8เลขที่26ม.6ต.เจ๊ะเห",
    "note": "ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "ขธล199นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MSFC(TH)",
    "color": "แดง",
    "user": "นายอดุลย์ สือแม3-9605-00370-01-6 เลขที่ 129/2ม.8บ.อาแนต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขธร933นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KDFD(TH)",
    "color": "น้ำเงิน",
    "user": "นายปฐพี มีนา/มะนอร์3-9602-00360-83-0เลขที่85ม.8บ.ราญอต.เกาะสะท้อน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขธร291นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KDFC(TH)",
    "color": "ดำ",
    "user": "นายฮามะ วาดิง 3-9408-00069-98-2   เลขที่ 26 หมู่ 8 คอลอกาปะ กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขธย840นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MSFC(TH)",
    "color": "น้ำเงิน",
    "user": "นายฮามะ วาดิง 3-9408-00069-98-2   เลขที่ 26 หมู่ 8 คอลอกาปะ กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขธม688นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MSFC(TH)",
    "color": "น้ำเงิน",
    "user": "มูหัมมัดกามารูดิง เง๊าะมูซอ3-9602-00227-13-7เลขที่67ม.3บ.ศาลาใหม่ต.ศาลาใหม่",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขธพ76นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "แดง",
    "user": "นายกามา อาลี3-9602-00098-60-4เลขที่137ม.5บ.โคกกูแวต.พร่อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขธฉ654นราธิวาส",
    "brand": "HONDA",
    "model": "ACF110SFC(TH)",
    "color": "ฟ้า",
    "user": "นางแยน๊ะ สะแลแม3-9602-00032-44-1เลขที่22ม.1บ.จาเราะต.ไพรวัน",
    "note": "หน.อาเยาะ"
  },
  {
    "license_plate": "ขธจ138นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125MSFC(TH)",
    "color": "แดง",
    "user": "มูหามัดฟาร์มี มูดอ/ฮาเซ็ม1-9602-00040-17-1เลขที่93ม.7บ.จาแบปะต.เกาะสะท้อน",
    "note": "LOGISTIC จ.นราธิวาส"
  },
  {
    "license_plate": "ขธง873นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KDFC(TH)",
    "color": "ดำ",
    "user": "นายมะซอฮี เจ๊ะอูมา1-9605-00206-25-2 เลขที่ 61ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "กลุ่มเสี่ยง"
  },
  {
    "license_plate": "ขธข517นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KSFC(TH)",
    "color": "ดำ",
    "user": "นายสาอารอนิง อีแต3-9605-00506-18-0 เลขที่ 67/1ม.6บ.บาโงกูโบต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขธก836นราธิวาส",
    "brand": "HONDA",
    "model": "ANC125BSTC(TH)",
    "color": "ดำ",
    "user": "นายซุลกีพลี สูแน1-9605-00097-17-8 เลขที่ 154ม.6บ.กาเด็งต.กาลิซา",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขทล485นราธิวาส",
    "brand": "HONDA",
    "model": "ACD110SFC(3TH)",
    "color": "แดง",
    "user": "นายนิเฮง ลอโมง3-9605-00644-24-8 เลขที่ 24ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขทย268นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125MSFC(TH)",
    "color": "แดง",
    "user": "นายนิรอสดี นิโซะ3-9605-00035-27-6 เลขที่ 116ม.8บ.กำปงปาเร๊ะต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขทม239นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MSFC(TH)",
    "color": "น้ำเงิน",
    "user": "นายมาหะมะมาลิกี นิกาเร็ง3-9605-00086-88-1 เลขที่ 294ม.1บ.ฮูลูปาเร๊ะ ต.ตันหยงมัส",
    "note": "กรรมการอาเยาะฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "ขทธ705นราธิวาส",
    "brand": "HONDA",
    "model": "ACD110SFC(3TH)",
    "color": "ดำ",
    "user": "น.ส.พารีดะห์ ขาเดร์3-9605-03056-80-5 เลขที่ 136ม.8บ.อาแนต.บองอ",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "ขทธ476นราธิวาส",
    "brand": "HONDA",
    "model": "ACD110SFC(3TH)",
    "color": "ขาว",
    "user": "นายมะอาดี ดือราแม3-9605-00637-00-4 เลขที่ 67/2ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขทธ396นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125MSFC(TH)",
    "color": "แดง",
    "user": "นางฮาลีเมาะ สือแม 3-9506-00431-32-7   เลขที่ 4 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "ขตษ806นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "ดำ",
    "user": "นายแวดราแม แวโนะ3-9606-00063-60-3 เลขที่ 130ม.8บ.กำปงปาเร๊ะต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขตม995นราธิวาส",
    "brand": "HONDA",
    "model": "NS110S",
    "color": "แดง",
    "user": "นางรอกายะห์ สะแลแม 3-9411-00031-19-3   เลขที่ 32 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขตม839นราธิวาส",
    "brand": "HONDA",
    "model": "NS110D",
    "color": "HONDA",
    "user": "นายดอเลาะ สาแม1-9605-00111-30-8 เลขที่ 273ม.1บ.ตันหยงมัสเก่าต.ตันหยงมัส",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขตม615นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "น้ำเงิน",
    "user": "นายต่วนปา ระยีแก3-9605-00109-42-3 เลขที่ 132ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขตม467นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "น้ำเงิน",
    "user": "นายดาโอ๊ะ สูดี3-9605-00757-68-9 เลขที่ 180/2ม.5บ.ลาไมต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขตพ71นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "น้ำเงิน",
    "user": "นายนิรอสดี นิกูโน3-9605-00273-94-1 เลขที่ 17/2ม.1บ.ทำนบต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขตน57นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "ขาว",
    "user": "นายเมาอูเซ็ง มูซอดี3-9410-00004-06-2 เลขที่ 180ม.5บ.ลาไมต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขตฉ400นราธิวาส",
    "brand": "HONDA",
    "model": "NS110D",
    "color": "ดำ",
    "user": "นายสาและ ปูเต๊ะ3-9605-00634-28-5 เลขที่ 12ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขตจ586นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "ดำ",
    "user": "นายอุสมาน มามะ1-9602-00059-70-0ม.7บ.จาแบปะต.เกาะสะท้อน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขตค173นราธิวาส",
    "brand": "HONDA",
    "model": "NS110P",
    "color": "ดำ",
    "user": "น.ส.ยารีด๊ะ จอแม3-9605-00450-75-3 เลขที่ 100/3ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขตข915นราธิวาส",
    "brand": "HONDA",
    "model": "NS110D",
    "color": "ขาว",
    "user": "นายสะอารอนิง อาแว1-9602-00032-00-3เลขที่75ม.8ต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขตข483นราธิวาส",
    "brand": "HONDA",
    "model": "NS110S",
    "color": "แดง",
    "user": "น.ส.แวเสาะ ยะโก๊ะ3-9605-00370-04-1 เลขที่ 128/9ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขตก401นราธิวาส",
    "brand": "HONDA",
    "model": "KT110C(B)",
    "color": "ขาว",
    "user": "นายมูฮำหมัดซอเร มะมิง3-9605-00753-33-1 เลขที่ 23ม.1บ.ปูโงะต.กาลิซา",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขฉษ381นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(C)",
    "color": "ดำ",
    "user": "นายเปาซี มะสือสะ3-9602-00456-19-5เลขที่116ม.7ต.โฆษิต",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขฉว705นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(C)",
    "color": "ขาว",
    "user": "น.ส.ต่วนเม๊าะ มะมิง3-9602-00427-75-6เลขที่4ม.8ต.เกาะสะท้อน",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "ขฉล954นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BM(A)",
    "color": "น้ำตาล",
    "user": "นายบือราเฮง นิแม5-9605-00022-04-9 เลขที่ 72ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขฉย951นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "ดำ",
    "user": "นายมาหะมะมาลิกี นิกาเร็ง3-9605-00086-88-1 เลขที่ 294ม.1บ.ฮูลูปาเร๊ะ ต.ตันหยงมัส",
    "note": "กรรมการอาเยาะฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "ขฉย662นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายแวลียะห์ รอนิง3-9604-00110-05-5 เลขที่ 140ม.9บ.ลูโลวต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขฉน596นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "ขาว",
    "user": "นายอาสมัน สาและ3-9602-00016-43-8เลขที่95/1ม.1บ.แฆแบ๊ะต.นานาค",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขฉน482นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "ดำ",
    "user": "นายรอสือลี เล็งฮะ/ยาลี3-9602-00373-46-0เลขที่52/1ม.1บ.ปูยูต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขฉท795นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "ดำ",
    "user": "นายรุสลัน วาเต๊ะ3-9605-00503-55-5 เลขที่ 45/4ม.6บ.บาโงกูโบต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขฉง296นราธิวาส",
    "brand": "HONDA",
    "model": "NF110K(A)",
    "color": "น้ำตาล",
    "user": "นายยูกิฟลี เจ๊ะโซ๊ะ3-9605-00365-43-8 เลขที่ 6ม.10บ.บองอต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขฉค450นราธิวาส",
    "brand": "HONDA",
    "model": "NC110AP(S)",
    "color": "ดำ",
    "user": "นายนิเซ็ง อีดือเร๊ะ3-9605-00431-85-6 เลขที่ 76ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขฉก450นราธิวาส",
    "brand": "HONDA",
    "model": "NF110TM(A)",
    "color": "ขาว",
    "user": "นายมูอัมมาร์ สาและ1-9602-00048-08-2เลขที่4/1ม.1บ.แฆแบแฆ๊ะต.นานาค",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขฉก290นราธิวาส",
    "brand": "HONDA",
    "model": "SMILE",
    "color": "แดง",
    "user": "นายอับดุลราเต๊ะ แป3-9605-00368-33-0 เลขที่ 115/5ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้งฝ่ายอูลามา"
  },
  {
    "license_plate": "ขจษ242นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BB",
    "color": "ขาว",
    "user": "นางยูไนด๊ะ เต๊ะ3-9605-00594-97-6 เลขที่ 75/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขจว998นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BM(A)",
    "color": "ขาว",
    "user": "นายสมาน แวสือนิ3-9605-00778-70-8 เลขที่ 9ม.5บ.กาหนั๊วะต.กาลิซา",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขจว94นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "ขาว",
    "user": "นายมามะ ยูโซ๊ะ3-9602-00168-07-6 เลขที่ 82/3ม.6บ.บาโงกูโบต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขจร248นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "ฟ้า",
    "user": "นายยาลี บากา3-9605-00491-33-6 เลขที่ 90ม.5บ.ลาไมต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขจย427นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(C)",
    "color": "ดำ",
    "user": "นายยูฮารี สะมะแอ1-9602-00065-96-3เลขที่325/1ม.1ต.เจ๊ะเห",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขจม449นราธิวาส",
    "brand": "HONDA",
    "model": "ND125(J)",
    "color": "น้ำเงิน",
    "user": "นายอาลียะห์ เจ๊ะเตะ3-9605-00176-72-4 เลขที่ 4ม.7บ.บูแบบาเดาะต.มะรือโบตก",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขจม320นราธิวาส",
    "brand": "HONDA",
    "model": "NOVA SONIC RS",
    "color": "เทา",
    "user": "นายบราเฮ็ง คูตงราเซะ3-9605-00366-29-9 เลขที่ 144/3ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขจพ216นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "ดำ",
    "user": "นายสุไหมิง ฮามะ3-9605-00371-52-7 เลขที่ 142/1ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขจน709นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "ดำ",
    "user": "นายกาลียา อิง3-9605-00448-56-2 เลขที่ 27ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขจต799นราธิวาส",
    "brand": "HONDA",
    "model": "NF110R",
    "color": "ดำ",
    "user": "นายการียา แวอีแต1-9602-00047-79-5เลขที่10ม.4ต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขจต61นราธิวาส",
    "brand": "HONDA",
    "model": "NF110R",
    "color": "ดำ",
    "user": "นายอาดือนัน เล๊าะ3-9602-00373-15-0เลขที่103ม.1บ.ปูยูต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขจจ561นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายรอมือลี ปิ3-9602-00399-22-1เลขที่91ม.6ต.บางขุนทอง",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขจค501นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "น้ำเงิน",
    "user": "นายมามะ ตาเย๊ะ3-9602-00111-14-7เลขที่121/1ม.5บ.ปูลาเจ๊ะมูดอต.ศาลาใหม่",
    "note": "กรรมการอาเยาะ ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "ขจค136นราธิวาส",
    "brand": "HONDA",
    "model": "KT110B",
    "color": "ชมพู",
    "user": "นายเจ๊ะมามะ มะแซ3-9499-00139-44-1เลขที่73/1ม.1บ.ตาบาต.เจ๊ะเห",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขจข996นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายซำซูเด็งแวนาซา1-9602-00006-89-4เลขที่55/1ม.6ต.เจ๊ะเห",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขจข797นราธิวาส",
    "brand": "HONDA",
    "model": "NF110TM",
    "color": "ขาว",
    "user": "นายต่วนดาโอ๊ะ ลือบา3-9605-00231-25-3 เลขที่ 73/2ม.2บ.ลาแปต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขจข418นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MT(B)",
    "color": "ขาว",
    "user": "นายมูฮำหมัดซอเร มะมิง3-9605-00753-33-1 เลขที่ 23ม.1บ.ปูโงะต.กาลิซา",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขงว724ยะลา",
    "brand": "HONDA",
    "model": "AFS125KSFC(TH)",
    "color": "ดำ",
    "user": "น.ส.นูรฮัลมีซัม เจ๊ะสะนิ3-9605-00711-16-6 เลขที่ 81/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขงน950นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายอาบูฮะซัน ยูโซ๊ะ3-9602-00353-69-8เลขที่26ม.6ต.เจ๊ะเห",
    "note": "ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "ขงน814นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(B)",
    "color": "ดำ",
    "user": "นายมะสกรี นิแม3-9605-00637-41-1 เลขที่ 72/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขงน292นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BP",
    "color": "แดง",
    "user": "นางซารีหม๊ะ กามะ 3-9411-00026-51-3    เลขที่ 2 หมู่ 8 ปาแดกามูดิง กะรุบี",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "ขงท449นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(B)",
    "color": "ดำ",
    "user": "นายมามะ ตาเย๊ะ3-9602-00111-14-7เลขที่121/1ม.5บ.ปูลาเจ๊ะมูดอต.ศาลาใหม่",
    "note": "กรรมการอาเยาะ ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "ขงจ167นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BM",
    "color": "ขาว",
    "user": "นายอับดุลรอฮิม แอสะ/บอรอเฮง3-9605-00397-73-9 เลขที่ 85/1ม.1บ.บาโงตาต.บาโงสะโต",
    "note": "กรรมการอาเยาะฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "ขงค882นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "แดง",
    "user": "นายตอลา สาแม3-9605-00680-55-4 เลขที่ 69ม.5บ.ตราแดะต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขงค343นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เขียว",
    "user": "นายกาลียา อิง3-9605-00448-56-2 เลขที่ 27ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขง910สงขลา",
    "brand": "HONDA",
    "model": "FB263CLN",
    "color": "ขาว",
    "user": "นายสะมะแอ เล็งฮะ3-9602-00250-02-3เลขที่28/1ม.1บ.ปูยูต.เกาะสะท้อน",
    "note": "กรรมการอาเยาะ ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "ขคษ458ยะลา",
    "brand": "HONDA",
    "model": "KT110C(B)",
    "color": "แดง",
    "user": "นายสมาน ดูลาสะ 3-9411-00025-58-4  เลขที่ 16/3 หมู่ 5   กะรุบี",
    "note": "กรรมการอาเยาะ ฝ่ายประชาสัมพันธ์"
  },
  {
    "license_plate": "ขคว882นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(B)",
    "color": "ดำ",
    "user": "นายมาหามะยูดิง สาและ/บูคอรี3-9605-00255-32-2 เลขที่ 47/2ม.6บ.อาแวต.มะรือโบตก",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขคล437นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BB",
    "color": "ขาว",
    "user": "นายมะหะมะ มูซอ3-9605-00638-23-0 เลขที่ 175ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขคล310นราธิวาส",
    "brand": "HONDA",
    "model": "NF125T(B)",
    "color": "ดำ",
    "user": "นายสารอวี อาแว1-9602-00428-08-6เลขที่11ม.8ต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขคร806นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เขียว",
    "user": "น.ส.ต่วนเม๊าะ มะมิง3-9602-00427-75-6เลขที่4ม.8ต.เกาะสะท้อน",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "ขคร386นราธิวาส",
    "brand": "HONDA",
    "model": "NF125T(B)",
    "color": "ดำ",
    "user": "นายนิแว สาและ3-9602-00436-89-5เลขที่58ม.8ต.เกาะสะท้อน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขคย577นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(B)",
    "color": "ดำ",
    "user": "นายสาแล๊ะ มามะ3-9602-00435-60-1เลขที่61ม.7บ.จาแบปะต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขคย528นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(B)",
    "color": "ดำ",
    "user": "นายมะอาดี ดือราแม3-9605-00637-00-4 เลขที่ 67/2ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขคม335นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BC(A)",
    "color": "แดง",
    "user": "นายเจ๊ะมามะ มะแซ3-9499-00139-44-1เลขที่73/1ม.1บ.ตาบาต.เจ๊ะเห",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขคน98นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BC(A)",
    "color": "แดง",
    "user": "นายนิรอสดี นิกูโน3-9605-00273-94-1 เลขที่ 17/2ม.1บ.ทำนบต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขคน51นราธิวาส",
    "brand": "HONDA",
    "model": "D125M(J)",
    "color": "WAVE125",
    "user": "นายกาลียา อิง3-9605-00448-56-2 เลขที่ 27ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขคฉ192นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BC(A)",
    "color": "แดง",
    "user": "นายอับดุลตอเล๊ะ แดอีแต3-9605-00803-99-1 เลขที่ 237ม.1บ.ปูโงะต.กาลิซา",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขคจ296นราธิวาส",
    "brand": "HONDA",
    "model": "ND125M(J)",
    "color": "แดง",
    "user": "นายมะรูดิง เจ๊ะโด3-9605-00770-44-8 เลขที่ 83/2ม.5บ.ลาไมต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขคค766นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(A)",
    "color": "เทา",
    "user": "นางซาปีน๊ะ ดอแม3-9605-00639-28-7 เลขที่ 16ม.7บ.ตันหยงลิมอต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขคข926นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "น้ำเงิน",
    "user": "นายเจ๊ะอัลซู อิสมารอฮิม3-9605-00373-04-0 เลขที่ 163ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขษ698นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายกาลียา อิง3-9605-00448-56-2 เลขที่ 27ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขษ459ยะลา",
    "brand": "HONDA",
    "model": "CS150R",
    "color": "ดำ",
    "user": "นายตอฮาฮูเซ็น โตะดา/ซอบรี 3–9411–00019–47-9  เลขที่ 17/3 หมู่ 3 กำปง กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขษ269นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BC(A)",
    "color": "ขาว",
    "user": "นายอัรมาน ตาฮู3-9605-00433-60-3 เลขที่ 83/2ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขขษ179นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(B)",
    "color": "ดำ",
    "user": "นายมะรง อาบ๊ะ3-9605-00362-94-3 เลขที่ 57/1ม.3บ.กูตงต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขล740นราธิวาส",
    "brand": "HONDA",
    "model": "ND125(J)",
    "color": "ดำ",
    "user": "น.ส.ต่วนเม๊าะ มะมิง3-9602-00427-75-6เลขที่4ม.8ต.เกาะสะท้อน",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "ขขล281นราธิวาส",
    "brand": "HONDA",
    "model": "NF125T(B)",
    "color": "ดำ",
    "user": "นายกอเซ็ง อาแวซูโว3-9605-00637-35-7 เลขที่ 161ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขม965นราธิวาส",
    "brand": "HONDA",
    "model": "NF100R(A)",
    "color": "น้ำเงิน",
    "user": "น.ส.กามีล๊ะ มามะ3-9605-00638-55-8 เลขที่ 90/2ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขม252นราธิวาส",
    "brand": "HONDA",
    "model": "NC110AC",
    "color": "ดำ",
    "user": "นายอิบรอเห็ง เจ๊ะอูมา3960500236492 เลขที่ 158ม.2บ.ลาแปต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขขน623นราธิวาส",
    "brand": "HONDA",
    "model": "ND125(D)",
    "color": "น้ำเงิน",
    "user": "นายมามะ ยูโซ๊ะ3-9602-00168-07-6 เลขที่ 82/3ม.6บ.บาโงกูโบต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขน424นราธิวาส",
    "brand": "HONDA",
    "model": "NC110AR",
    "color": "น้ำตาล",
    "user": "นายฮาเล็ง สาและ3-9602-00311-30-8เลขที่43/1ม.1บ.แฆแบ๊ะต.นานาค",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขธ841นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(B)",
    "color": "ดำ",
    "user": "นายอุสือมัน เล็งฮะ3-9602-00009-40-1เลขที่69ม.1ต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขขธ557นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(B)",
    "color": "ดำ",
    "user": "นายรุสลี สาแลมะ3-9606-00065-00-2 เลขที่ 27/2ม.7บ.บูแบบาเดาะต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขธ123นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BS",
    "color": "แดง",
    "user": "น.ส.ซุริยา ยูโซ๊ะ1-9605-00024-90-1 เลขที่ 109ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "ขขฉ746นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BS",
    "color": "ดำ",
    "user": "นายรอสือลี เล็งฮะ/ยาลี3-9602-00373-46-0เลขที่52/1ม.1บ.ปูยูต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขฉ720นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(A)",
    "color": "ดำ",
    "user": "นางสาลมา เต๊ะ3-9605-00371-42-0 เลขที่ 116/1ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขฉ361ปัตตานี",
    "brand": "HONDA",
    "model": "AFS125KSFC(TH)",
    "color": "ดำ",
    "user": "นายอับดุลขอเดร์ กาเสม 3-9402-00387-81-9 เลขที่ 64 หมู่ 5 บ.นาค้อเหนือ ต.ป่าบอน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขขข575นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(A)",
    "color": "ดำ",
    "user": "นายซูวีรา อิสมารอฮิม3-9605-00373-06-6 เลขที่ 163ม.8บ.อาแนต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขขก825นราธิวาส",
    "brand": "HONDA",
    "model": "NF125T(A)",
    "color": "เทา",
    "user": "นายสมาน แวสือนิ3-9605-00778-70-8 เลขที่ 9ม.5บ.กาหนั๊วะต.กาลิซา",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขขก101นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "น้ำเงิน",
    "user": "นายสือแม เจ๊ะสะแลแม3-9602-00429-60-1เลขที่70ม.4บ.ตะเหลียงต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขกษ234ปัตตานี",
    "brand": "HONDA",
    "model": "HONDA",
    "color": "เขียว",
    "user": "นายต่วนรีดูวัน นิราแม 3-9611-00190-29-1    เลขที่ 19 หมู่ 2 มะแนดาแล ปล่องหอย",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขกษ200นราธิวาส",
    "brand": "HONDA",
    "model": "NC110BS",
    "color": "เทา",
    "user": "นายหนิรอสือลี หนิสาเร๊ะ3-9605-00506-29-5 เลขที่ 63/2ม.6บ.บาโงกูโบต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขกว337นราธิวาส",
    "brand": "HONDA",
    "model": "NF100K(A)",
    "color": "แดง",
    "user": "นายกูดีรมาน ลอโมง1-9605-00023-16-6 เลขที่ 24ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขกล335ปัตตานี",
    "brand": "HONDA",
    "model": "AFS110MSFC(TH)",
    "color": "แดง",
    "user": "นายบือราเฮง หะยีดือราแม 3-9403-00295-34-7  เลขที่ 134/7   หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขกล15นราธิวาส",
    "brand": "HONDA",
    "model": "NF125T(A)",
    "color": "น้ำเงิน",
    "user": "น.ส.ฮามีนะ เจ๊ะอาแซ3-9605-00235-00-3 เลขที่ 128/9ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขกท995นราธิวาส",
    "brand": "HONDA",
    "model": "NF125MC(A)",
    "color": "ดำ",
    "user": "น.ส.พารีดะห์ ขาเดร์3-9605-03056-80-5 เลขที่ 136ม.8บ.อาแนต.บองอ",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "ขกต432นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100S",
    "color": "น้ำเงิน",
    "user": "นายแวสาแม อาลี 3-9411-00039-46-1  เลขที่ 22 หมู่ 2 อูแตบือราแง ตะโล๊ะดือรามัน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขกง757ปัตตานี",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "แดง",
    "user": "นายนูรดีน ดือเฆะ 3-9402-00610-54-2  เลขที่ 128/8  หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ขกค818ปัตตานี",
    "brand": "HONDA",
    "model": "NF125C(C)",
    "color": "ขาว",
    "user": "นายมะลาเซ็ง เจะมะ2-9605-00016-73-7 เลขที่ 52/2ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "ขกค282นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100S",
    "color": "เทา",
    "user": "นายมูฮันฮามะ ลอมะ3-9605-00238-51-7 เลขที่ 13/3ม.3บ.กูตงต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กษษ512นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "ดำ",
    "user": "นายเจ๊ะอารง เจ๊ะหะ1-9605-00042-20-9 เลขที่ 73/2ม.6บ.บาโงกูโบต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กษว670ยะลา",
    "brand": "HONDA",
    "model": "NF100R(B)",
    "color": "ดำ",
    "user": "นายอับดุลรอแมน เสนสะนา 3-9402-00624-77-2 เลขที่ 29/2  หมู่ 3 .ควนโนร ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษว430นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "แดง",
    "user": "นายซอรี อูมา3-9605-00236-72-7 เลขที่ 1ม.3บ.กูตงต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษว381ปัตตานี",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "แดง",
    "user": "นายมะดาโหะ ยะโกะ 3-9403-00292-19-4 เลขที่ 109/5  หมู่ 7 บ.คลองช้างออก ต.นาเกตุ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กษล296นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "ดำ",
    "user": "นายรุสตัน วาเด็ง3-9601-00120-07-1 เลขที่ 74ม.4บ.บองอต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กษย668นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "เขียว",
    "user": "นายตอลา สาแม3-9605-00680-55-4 เลขที่ 69ม.5บ.ตราแดะต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษย441ปัตตานี",
    "brand": "HONDA",
    "model": "NS110D",
    "color": "ขาว",
    "user": "นายมะกอเซ็ง สาเหาะ 3-9402-00611-23-9  เลขที่ 135 หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษม490ปัตตานี",
    "brand": "HONDA",
    "model": "ACB110SB(TH)",
    "color": "ดำ",
    "user": "นายโซะเปียน ปล่องหอย/เวาะ 3-9411-00058-45-8   เลขที่ 26 หมู่ 5  อ ตะโละดือรามัน",
    "note": "มวลชนจัดตั้ง (เดิมสมาชิกฯ)"
  },
  {
    "license_plate": "กษน936นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "ดำ",
    "user": "นายมะซอบือรี บือราเฮง3-9605-00507-50-0 เลขที่ 8ม.6บ.บาโงกูโบต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กษน231ปัตตานี",
    "brand": "HONDA",
    "model": "NF125C(C)",
    "color": "ดำ",
    "user": "นายการียา มะแดเฮาะ3-9410-00170-44-2 เลขที่ 180/1ม.5บ.ลาไมต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษฉ86ปัตตานี",
    "brand": "HONDA",
    "model": "KT110B(A)",
    "color": "ชมพู",
    "user": "นางฟาดีละห์ หะแว 3-9603-00264-94-8  เลขที่ 10 หมู่ 8 ปาแดกามูดิง กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษฉ58นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "ดำ",
    "user": "นายสูไหมี อาแว3-9605-00237-06-5 เลขที่ 3/1ม.3บ.กูตงต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กษจ674นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "เขียว",
    "user": "นายอาหะมัด อูมา2-9602-00007-96-1เลขที่135ม.5บ.โคกกะเปาะต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กษค823นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "ดำ",
    "user": "นายซายูตี เจ๊ะลี3-9605-00608-39-0 เลขที่ 105ม.1บ.ทำนบต.เฉลิม",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กษค554นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "แดง",
    "user": "นายมะรอปะ หะยีลาเด็ง3-9605-00436-18-1 เลขที่ 12/2ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กษก528นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "ดำ",
    "user": "นางแยน๊ะ สะแลแม3-9602-00032-44-1เลขที่22ม.1บ.จาเราะต.ไพรวัน",
    "note": "หน.อาเยาะ"
  },
  {
    "license_plate": "กษก312นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "ขาว",
    "user": "นายนิฮามิ เจ๊ะหะ3-9605-00650-52-3 เลขที่ 129ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวษ798นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "ดำ",
    "user": "น.ส.นุรฮาญาตี สมาแอ3-9605-00370-12-1 เลขที่ 134/1ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวล747ยะลา",
    "brand": "HONDA",
    "model": "NF100R(A)",
    "color": "เทา",
    "user": "นายมะอาดี ดือราแม3-9605-00637-00-4 เลขที่ 67/2ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กวม964นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "ดำ",
    "user": "นายอับดุลอาซิ อารงค์3-9602-00227-56-1เลขที่66ม.3บ.ศาลาใหม่ต.ศาลาใหม่",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวม289นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "น้ำเงิน",
    "user": "นายบาฮารี วาดิง3-9605-00105-52-5 เลขที่ 29ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กวพ784นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "น้ำเงิน",
    "user": "นายนิเซ็ง นิสุหลง3-9602-00465-39-9เลขที่15ม.5ต.โฆษิต",
    "note": "ผบ.ร้อย.๑ (หน.KOMPI)"
  },
  {
    "license_plate": "กวพ301นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "แดง",
    "user": "น.ส.อัยนิง ตีโมมาเย๊าะ3-9605-00432-31-3 เลขที่ 81/1ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวพ197นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เขียว",
    "user": "นายต่วนมา ตีงี3-9605-00650-28-1 เลขที่ 61ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวบ347ยะลา",
    "brand": "HONDA",
    "model": "NC110BS",
    "color": "น้ำเงิน",
    "user": "นายอับดุลรอแมน เสนสะนา 3-9402-00624-77-2 เลขที่ 29/2  หมู่ 3 .ควนโนร ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวธ437ปัตตานี",
    "brand": "HONDA",
    "model": "KT110B",
    "color": "ชมพู",
    "user": "น.ส.โนร์ฮูดา อาแว1-9605-00105-61-8 เลขที่ 160ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "กวธ140นราธิวาส",
    "brand": "HONDA",
    "model": "NF110TM(A)",
    "color": "แดง",
    "user": "นายมะรอปะ หะยีลาเด็ง3-9605-00436-18-1 เลขที่ 12/2ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวท498นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายสามะ ตีเม๊าะ3-9605-00686-23-4 เลขที่ 158ม.5บ.ตราแดะต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวต697ปัตตานี",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "ดำ",
    "user": "นายยูโซ๊ะ มามะ/หะยียูโซ๊ะ 3-9499-00252-16-7  เลขที่ 91 หมู่ 3 .ควนโนร ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวต451นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "น้ำเงิน",
    "user": "นายสะอารอนิง อาแว1-9602-00032-00-3เลขที่75ม.8ต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กวจ867ปัตตานี",
    "brand": "HONDA",
    "model": "NF125MC(C)",
    "color": "ดำ",
    "user": "นายสาการียา เกาะและ 3-9402-00250-10-4 เลขที่ 8 หมู่ 2 คูระ ม่วงเตี้ย",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กวค270นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "เขียว",
    "user": "นายนาโซรี ด๊ะ3-9605-00436-83-1 เลขที่ 22ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "หน.ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "กวข949นราธิวาส",
    "brand": "HONDA",
    "model": "NOVA SONIC RS SUPER",
    "color": "เทา",
    "user": "นายมัสลัน ยูโซ๊ะ1-9602-00034-46-4เลขที่89/1ม.7บ.ภัทรภักดีต.เจ๊ะเห",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กวก645ปัตตานี",
    "brand": "HONDA",
    "model": "ND125(J)",
    "color": "ดำ",
    "user": "นายบือราเฮง หะยีดือราแม 3-9403-00295-34-7  เลขที่ 134/7   หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กลษ297นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เขียว",
    "user": "นายสะมะแอ นิแม2-9605-00028-64-6 เลขที่ 72ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กลษ196ยะลา",
    "brand": "HONDA",
    "model": "ND125(D)",
    "color": "ดำ",
    "user": "นายสาและ มะเกะ3-9501-00650-62-3เลขที่47/2ม.9บ้านอุเปต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กลย129นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เขียว",
    "user": "นายอับดุลราเต๊ะ แป3-9605-00368-33-0 เลขที่ 115/5ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้งฝ่ายอูลามา"
  },
  {
    "license_plate": "กลม30นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE126",
    "color": "เทา",
    "user": "นายแวลียะห์ รอนิง3-9604-00110-05-5 เลขที่ 140ม.9บ.ลูโลวต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กลพ398นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เขียว",
    "user": "นายดราการียา กะโด3-9605-00364-88-1 เลขที่ 27ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กลบ478นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "ดำ",
    "user": "นายอับดุลเล๊าะ ดาหมิ3-9605-00237-57-0เลขที่118/2ม.9ต.ไพรวัน",
    "note": "เปอมูดอ(ย้านมาจาก๓๕ ๖/๑ ม.๓ บ.กูตง ต.บองอ อ.ระแงะ)"
  },
  {
    "license_plate": "กลบ473นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "น้ำเงิน",
    "user": "นายมาหามัดซานุซี สาแม3-9605-00217-46-3 เลขที่ 93/1ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กลน36นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "แดง",
    "user": "นายอิบรอฮิม บินอาแด3-9605-00090-31-5 เลขที่ 330ม.11ต.ตันหยงมัส",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กลง957นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "เขียว",
    "user": "นายอับดุลวอฮา สาอุ5-9605-99021-57-1 เลขที่ 153ม.9บ.ลูโลวต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กลก910ปัตตานี",
    "brand": "HONDA",
    "model": "ND125M(J)",
    "color": "ดำ",
    "user": "นายมะรอบี ดีแม 3-9501-00106-54-1  เลขที่ 152/1หมู่ 5 บ.จุปะ ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กรษ398นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เขียว",
    "user": "นายนิเซ็ง อีดือเร๊ะ3-9605-00431-85-6 เลขที่ 76ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กรล225นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "แดง",
    "user": "น.ส.อัยนิง ตีโมมาเย๊าะ3-9605-00432-31-3 เลขที่ 81/1ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กรย844นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "แดง",
    "user": "น.ส.ต่วนเม๊าะ มะมิง3-9602-00427-75-6เลขที่4ม.8ต.เกาะสะท้อน",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "กรย460นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "เทา",
    "user": "นายอาหะมะ เจ๊ะสแลแม3-9605-00635-30-3 เลขที่ 162ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กรม759นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เขียว",
    "user": "นายอับดุลวาเหะ ยูโซ๊ะ3-9605-00503-40-7 เลขที่ 44ม.6บ.บาโงกูโบต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กรพ289นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายมาหะมะ เปาะเต๊ะ3-9605-00240-90-2 เลขที่ 34ม.3บ.กูตงต.บองอ",
    "note": "กรรมการอาเยาะฝ่ายอูลามา"
  },
  {
    "license_plate": "กรท601นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "น้ำเงิน",
    "user": "นายอนันต์ นอหะมะ3-9602-00044-06-7เลขที่123/1ม.9บ.บาเดาะมาตีต.ไพรวัน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กรต3ปัตตานี",
    "brand": "HONDA",
    "model": "NF125T(B)",
    "color": "ดำ",
    "user": "นายรอมือลี/(เปลี่ยนชื่อเป็น)ว่ะลียุดดีน เจ๊ะหลง3-9605-00381-06-9 เลขที่ 59/1ม.4บ.บองอต.บองอ",
    "note": "หน.ชุดปฏิบัติการ"
  },
  {
    "license_plate": "กรต105นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายเจ๊ะดาโอ๊ะ เจ๊ะอาแซ3-9602-00191-59-1เลขที่114ม.1บ.บางขุนทองต.บางขุนทอง",
    "note": "หน.อาเยาะ"
  },
  {
    "license_plate": "กรค339นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "แดง",
    "user": "นายรูซือลัน ปิ3-9605-00012-97-7เลขที่56ม.10ต.ไพรวัน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กรข640นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "เทา",
    "user": "นายมะแซ มามะ3-9602-00435-14-7เลขที่89ม.7บ.จาแบปะต.เกาะสะท้อน",
    "note": "กรรมการอาเยาะ ฝ่ายประชาสัมพันธ์"
  },
  {
    "license_plate": "กรก155ยะลา",
    "brand": "HONDA",
    "model": "WAVE100 คศ.2004",
    "color": "น้ำเงิน",
    "user": "นายสุลกิฟลี เจะมะ 3-9402-00293-76-8  เลขที่ 21/1  หมู่ 8 บ.โผงโผงใน ต.ปากล่อ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กร9089สงขลา",
    "brand": "HONDA",
    "model": "CP264AGN",
    "color": "เทา",
    "user": "นายสือแม เจ๊ะสะแลแม3-9602-00429-60-1เลขที่70ม.4บ.ตะเหลียงต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กร9089นราธิวาส",
    "brand": "HONDA",
    "model": "CP264AGN",
    "color": "เทา",
    "user": "นายสือแม เจ๊ะสะแลแม3-9602-00429-60-1เลขที่70ม.4ต.เกาะสะท้อน",
    "note": "ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "กยษ892นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "แดง",
    "user": "นายมาหะมะอารีปี ยำปลี3-9602-00099-06-6เลขที่169ม.5บ.โคกกูแวต.พร่อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยษ656นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายบือราเฮง นิแม5-9605-00022-04-9 เลขที่ 72ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยษ651นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายอาเดล นิแม3-9605-00783-14-1 เลขที่ 12ม.7บ.บาโงสะโตต.บาโงสะโต",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กยว782นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "แดง",
    "user": "นายมาหะมะ มูซอ3-9605-00225-28-8 เลขที่ 17/3ม.2บ.ลาแปต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยว547นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "แดง",
    "user": "นายไซมิง นิกาเร็ง3-9602-00371-79-3เลขที่38ม.1บ.ปูยูต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยล866ยะลา",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "ดำ",
    "user": "นายสะมะแอ เจ๊ะเต๊ะ3-9501-00651-46-8เลขที่61/5ม.9บ้านอุเปต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยล243นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "น้ำเงิน",
    "user": "นายมามะ ตาเย๊ะ3-9602-00111-14-7เลขที่121/1ม.5บ.ปูลาเจ๊ะมูดอต.ศาลาใหม่",
    "note": "กรรมการอาเยาะ ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "กยร983นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "น้ำเงิน",
    "user": "น.ส.ฮายาตี นิสมาแอ3-9605-00372-41-8 เลขที่ 116/1ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยร664นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "เทา",
    "user": "นายอับดุลเลาะ ดะและ3-9410-00400-29-4 เลขที่ 81/3ม.4บ.บองอต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กยร351ปัตตานี",
    "brand": "HONDA",
    "model": "ND125M(J)",
    "color": "ดำ",
    "user": "นายมาหะมะ สะแลแม 3-9402-00628-57-3  เลขที่ 83 หมู่ 3 .ควนโนร ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยร215นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เขียว",
    "user": "นางรูฮานา สามานะ3-9611-00384-04-5 เลขที่ 90/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยย178นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "ดำ",
    "user": "นายการียา ยามา3-9605-00805-18-7 เลขที่ 47/1ม.6บ.กาเด็งต.กาลิซา",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยย172นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM EXCES",
    "color": "ดำ",
    "user": "นายมะรอปะ หะยีลาเด็ง3-9605-00436-18-1 เลขที่ 12/2ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยพ563นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "เทา",
    "user": "นายอับดุลเลาะ ตีงี/แบเลาะ2-9605-00651-22-8 เลขที่ 120ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กยพ166นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "เทา",
    "user": "นายอาซมิง ดือเร๊ะ3-9602-00116-22-0ม.5บ.ปูลาเจ๊ะมูดอต.ศาลาใหม่",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กยน902นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "น้ำเงิน",
    "user": "น.ส.ซอบารียะ สาและ3-9605-00554-22-2 เลขที่ 75/2ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยน220นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เขียว",
    "user": "นายมูหะมะนูรี เจ๊ะเซ็ง3-9602-00098-24-8เลขที่152ม.3บ.ใหญ่ต.พร่อน",
    "note": "กรรมการอาเยาะ ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "กยธ821นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "แดง",
    "user": "นายมะรอยี บากา3-9602-00461-02-4เลขที่75/1ม.5บ.ปลักปลาต.โฆษิต",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กยท345นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายอิบรอเห็ง เจ๊ะอูมา3960500236492 เลขที่ 158ม.2บ.ลาแปต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กยท291นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เทา",
    "user": "นายตุแวเด็ง กุสุหลง3-9602-00100-79-0เลขที่204ม.5บ.โคกกูแวต.พร่อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กยง853นราธิวาส",
    "brand": "HONDA",
    "model": "DRDREAM EXCES",
    "color": "เขียว",
    "user": "นายมะนอ มะดิเย๊าะ3-9605-00488-74-2 เลขที่ 61ม.5บ.ลาไมต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กยง572ยะลา",
    "brand": "HONDA",
    "model": "DREAM คศ.2003",
    "color": "ดำ",
    "user": "นายอับดุลรอแม กือจิ 3-9411-00018-42-1  เลขที่ 15 หมู่ 4 ตะโละดือรามัน ตะโละดือรามัน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กยค627นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "ดำ",
    "user": "นายอับดุลเล๊าะ ตาเยะ3-9612-00013-18-9 เลขที่ 74/5ม.4บ.บองอต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กยก655นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM EXCES",
    "color": "ดำ",
    "user": "นายมาหมะสกรี ลาเต๊ะ3-9602-00385-29-8เลขที่80/1ม.2บ.เกาะสะท้อนต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กมษ665นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เขียว",
    "user": "นายมะตอลา เปาะดิง3-9605-00434-92-2 เลขที่ 124ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "หน.อาเยาะ/โตะแนแบ"
  },
  {
    "license_plate": "กมษ31นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เขียว",
    "user": "นายมูฮำหมัดซอเร มะมิง3-9605-00753-33-1 เลขที่ 23ม.1บ.ปูโงะต.กาลิซา",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กมษ192นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เทา",
    "user": "นายสือแม เจ๊ะสะแลแม3-9602-00429-60-1เลขที่70ม.4บ.ตะเหลียงต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กมว803นราธิวาส",
    "brand": "HONDA",
    "model": "NOVA125RS",
    "color": "เขียว",
    "user": "นายสมาน ยามีตี3-9602-00042-85-4เลขที่114ม.9ต.ไพรวัน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กมว733นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM EXCES",
    "color": "เขียว",
    "user": "นายยาลี เจ๊ะเต๊ะ3-9601-00365-41-4 เลขที่ 62ม.6บ.บาโงกูโบต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กมล365นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เทา",
    "user": "นายอาลือมัน ซาและ3-9605-00575-62-9 เลขที่ 12/1ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "หน.อาเยาะ"
  },
  {
    "license_plate": "กมล128นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM EXCES",
    "color": "เขียว",
    "user": "นายยูกี สาและ3-9605-00506-20-1 เลขที่ 68/1ม.6บ.บาโงกูโบต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กมม472นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เทา",
    "user": "นายมะสกรี นิแม3-9605-00637-41-1 เลขที่ 72/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กมพ879นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM EXCES",
    "color": "เขียว",
    "user": "นายตอลา สาแม3-9605-00680-55-4 เลขที่ 69ม.5บ.ตราแดะต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กมบ951นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "แดง",
    "user": "นายอับดุลตอเละ เจ๊ะแม3-9605-00233-16-7 เลขที่ 88ม.2บ.ลาแปต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กมบ609นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เทา",
    "user": "นายเจ๊ะเต๊ะ อาแว3-9602-00326-63-1เลขที่64ม.4บ.ปะดาดอต.นานาค",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กมบ300นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "น้ำเงิน",
    "user": "นายอิสมาแอ ดาหมิ3-9605-00237-54-5 เลขที่ 6/1ม.3บ.กูตงต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กมธ965นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM EXCES",
    "color": "เขียว",
    "user": "นายอับดุลฮากิม ปูตะ3-9612-00027-26-1 เลขที่ 12ม.6บ.ไอร์กรอสต.จะแนะ",
    "note": "ผบ.ร้อย.2 (หน.Kompi)"
  },
  {
    "license_plate": "กมจ877ปัตตานี",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "ดำ",
    "user": "นายนาคอรี สะตาปอ 1-9402-00024-69-8 เลขที่  28 หมู่ 1 บ.ควน ต.ท่าเรือ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กมง200นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เหลือง",
    "user": "นายกามา อาลี3-9602-00098-60-4เลขที่137ม.5บ.โคกกูแวต.พร่อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กมค634นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM EXCES",
    "color": "เขียว",
    "user": "นายมามะอะตัง เซ็งโต๊ะ3-9602-00396-49-4เลขที่47ม.6บ.ยูโยต.บางขุนทอง",
    "note": "กรรมการอาเยาะ ฝ่ายอูลามา"
  },
  {
    "license_plate": "กมค445นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เขียว",
    "user": "นายมานะ สมะแอ3-9605-00637-86-1 เลขที่ 79ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กมก299ปัตตานี",
    "brand": "HONDA",
    "model": "WAVE100S",
    "color": "ดำ",
    "user": "น.ส.มะสง คาเดร์ 2-9402-00552-57-7  เลขที่  67  หมู่  5 บ.ท่าเรือ ต.ปากล่อ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กม7618สงขลา",
    "brand": "HONDA",
    "model": "ACCORD",
    "color": "น้ำตาล",
    "user": "นายอับดุลมานะ เจ๊ะและ 3-9411-00103-40-2  เลขที่ 30 หมู่ 8 มะแนลาแล ปล่องหอย",
    "note": "กรรมการฝ่ายปกครองระดับแดอาเราะห์"
  },
  {
    "license_plate": "กม1577สงขลา",
    "brand": "HONDA",
    "model": "CRV",
    "color": "ดำ",
    "user": "นายอับดุลวอฮา สาอุ5-9605-99021-57-1 เลขที่ 153ม.9บ.ลูโลวต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กพษ649นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM EXCES",
    "color": "ม่วง",
    "user": "นายอาสมัน สาและ3-9602-00016-43-8เลขที่95/1ม.1บ.แฆแบ๊ะต.นานาค",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กพย255สตูล",
    "brand": "HONDA",
    "model": "AFS110KDFC(TH)",
    "color": "น้ำเงิน",
    "user": "นายอับดุลรอหะ ยะโกะ 3-9402-00250-73-2 เลขที่ 27/2 หมู่ 3 ตันหยง ม่วงเตี้ย",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กพย186นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เขียว",
    "user": "นายอิสมาแอ ดอเล๊าะ3-9605-00216-36-0 เลขที่ 40ม.1บ.สาเมาะต.บองอ",
    "note": "commando คอมมานโด"
  },
  {
    "license_plate": "กพพ433นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "แดง",
    "user": "นายแวฮาเซ็ง อะ3-9605-00036-86-8 เลขที่ 148/1ม.8บ.กำปงปาเร๊ะต.มะรือโบตก",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กพน802นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "ดำ",
    "user": "นายซาการียา เปาะจิ3-9605-00636-63-6 เลขที่ 66ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กพง225นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "ดำ",
    "user": "นายซอและ สือรี3-9605-00631-93-6 เลขที่ 13ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กพข954ปัตตานี",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "เขียว",
    "user": "นายมาห์รุดิน ตาเยะ 3-9402-00321-16-8 เลขที่ 126 หมู่ 5 บ.สลาม ต.นาประดู่",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กพก302ปัตตานี",
    "brand": "HONDA",
    "model": "WAVE100",
    "color": "เทา",
    "user": "นายอับดุลนาเซ บางิง 3-9402-00055-58-2  เลขที่ 42  หมู่ 5 บ.ท่าเรือ ต.ท่าเรือ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กบย434ปัตตานี",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "น้ำเงิน",
    "user": "นายดาโอะ สะมะแอ 3-9403-00187-09-9  เลขที่ 134/3   หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กบม52นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "แดง",
    "user": "นายรุสลัม อาแวบือซา3-9605-00497-24-5 เลขที่ 148/1ม.5บ.ลาไมต.บองอ",
    "note": "ผบ.มว.2 (หน.PLATONG)"
  },
  {
    "license_plate": "กบธ667ปัตตานี",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "แดง",
    "user": "นายซูวีรา อิสมารอฮิม3-9605-00373-06-6 เลขที่ 163ม.8บ.อาแนต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กบง355นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM EXCES",
    "color": "เขียว",
    "user": "นายกูดีรมาน ลอโมง1-9605-00023-16-6 เลขที่ 24ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กนน298ยะลา",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "ฟ้า",
    "user": "นายอาแซ สาแมสารี 3-9402-00446-63-7  เลขที่ 24/2 หมู่ 1 บ.ทุ่งพลา ต.ปากล่อ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กทก138นราธิวาส",
    "brand": "HONDA",
    "model": "DREAMXZ",
    "color": "เขียว",
    "user": "นายอาแว สู3-9602-00107-65-4เลขที่58ม.8ต.ศาลาใหม่",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กตว255ปัตตานี",
    "brand": "HONDA",
    "model": "",
    "color": "ดำ",
    "user": "นายซอลาฮุดดิน ตาเยะ 5-9411-00001-96-7   เลขที่ 43 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กฉ8522สงขลา",
    "brand": "HONDA",
    "model": "CIVIC",
    "color": "น้ำตาล",
    "user": "นายสือแม เจ๊ะสะแลแม3-9602-00429-60-1เลขที่70ม.4บ.ตะเหลียงต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กฉ8522นราธิวาส",
    "brand": "HONDA",
    "model": "CIVIC",
    "color": "น้ำตาล",
    "user": "นายสือแม เจ๊ะสะแลแม3-9602-00429-60-1เลขที่70ม.4ต.เกาะสะท้อน",
    "note": "ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "กจ2338ยะลา",
    "brand": "HONDA",
    "model": "CITY",
    "color": "น้ำตาล",
    "user": "นายดอฮะ สูแน3-9605-00808-57-7 เลขที่ 154ม.6บ.กาเด็งต.กาลิซา",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กง7318ปัตตานี",
    "brand": "HONDA",
    "model": "CIVIC",
    "color": "เทา",
    "user": "นายสะบีดี อามิง 3-9411-00025-88-6   เลขที่ 6/3 หมู่ 5 บาโงยือแบ็ง กะรุบี",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กค2967ปัตตานี",
    "brand": "HONDA",
    "model": "CIVIC",
    "color": "เทา",
    "user": "นายฮาซัน บากา 3-9403-00457-89-4  เลขที่ 82  หมู่ 8 บ.สลาม ต.นาประดู่",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กกร136ยะลา",
    "brand": "HONDA",
    "model": "",
    "color": "ม่วง",
    "user": "นายนาคอรี สะตาปอ 1-9402-00024-69-8 เลขที่  28 หมู่ 1 บ.ควน ต.ท่าเรือ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กกฉ282นราธิวาส",
    "brand": "HONDA",
    "model": "LS125",
    "color": "HONDA",
    "user": "นายอาลียาซะ วานิ3-9605-00153-28-7 เลขที่ 116/2ม.9บ.สะโลว์ต.มะรือโบตก",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "3กพ7354กรุงเทพมหานคร",
    "brand": "HONDA",
    "model": "AFS125CSFE(TH)",
    "color": "ดำ",
    "user": "นายมะปาสะดี แกมะ5-9605-99000-13-1 เลขที่ 4ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "3กค5825กรุงเทพมหานคร",
    "brand": "HONDA",
    "model": "GM262CLX",
    "color": "เทา",
    "user": "นายอับดุลรอแมน เสนสะนา 3-9402-00624-77-2 เลขที่ 29/2  หมู่ 3 .ควนโนร ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กฉ453นราธิวาส",
    "brand": "HONDA",
    "model": "WW150G(TH)",
    "color": "น้ำเงิน",
    "user": "นายกามา อาลี3-9602-00098-60-4เลขที่137ม.5บ.โคกกูแวต.พร่อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กฉ3874นราธิวาส",
    "brand": "HONDA",
    "model": "MSX125F(TH)",
    "color": "ขาว",
    "user": "นายมะนาวี มะหะมะ5-9605-00021-84-1 เลขที่ 114/1ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กฉ2564นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KSFE(TH)",
    "color": "ขาว",
    "user": "นายดาโอ๊ะ สูดี3-9605-00757-68-9 เลขที่ 180/2ม.5บ.ลาไมต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กฉ1333นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125MSFG(TH)",
    "color": "แดง",
    "user": "นายอับดุลเล๊าะ ดาหมิ3-9605-00237-57-0เลขที่118/2ม.9ต.ไพรวัน",
    "note": "เปอมูดอ(ย้านมาจาก๓๕ ๖/๑ ม.๓ บ.กูตง ต.บองอ อ.ระแงะ)"
  },
  {
    "license_plate": "1กฉ1048นราธิวาส",
    "brand": "HONDA",
    "model": "WW150G(TH)",
    "color": "ขาว",
    "user": "นายมะรูดิง เจ๊ะโด3-9605-00770-44-8 เลขที่ 83/2ม.5บ.ลาไมต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กจ9902นราธิวาส",
    "brand": "HONDA",
    "model": "ACG110CBTH(TH)",
    "color": "ม่วง",
    "user": "นายอิสมะแอ นิแว3-9601-00274-69-9เลขที่201/5ม.5บ.โคกกูแวต.พร่อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "1กจ8594ปัตตานี",
    "brand": "HONDA",
    "model": "ACG110CBTG(TH)",
    "color": "ดำ",
    "user": "น.ส.โนร์ฮูดา อาแว1-9605-00105-61-8 เลขที่ 160ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "1กจ8492นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125CSFG(TH)",
    "color": "ขาว",
    "user": "นายฮาเล็ง สาและ3-9602-00311-30-8เลขที่43/1ม.1บ.แฆแบ๊ะต.นานาค",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กจ842นราธิวาส",
    "brand": "HONDA",
    "model": "MSX125G(TH)",
    "color": "ดำ",
    "user": "นายมาหะมะรอปี เจ๊ะแว3-9602-00190-49-7เลขที่'5/2ม.1บ.บางขุนทองต.บางขุนทอง",
    "note": "กรรมการอาเยาะ ฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "1กจ784นราธิวาส",
    "brand": "HONDA",
    "model": "NBC110MDFG(2TH)",
    "color": "น้ำตาล",
    "user": "นายอับดุลฮามิ ปิ3-9605-00834-29-2 เลขที่ 1/1ม.3บ.กูตงต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กจ7418นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KDFH(2TH)",
    "color": "ดำ",
    "user": "นายกูเฮง ยูโซ๊ะ3-9601-00151-75-9 เลขที่ 47ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ (กลุ่มประกอบระเบิด)"
  },
  {
    "license_plate": "1กจ732ปัตตานี",
    "brand": "HONDA",
    "model": "AFS110MCFF(TH)",
    "color": "น้ำเงิน",
    "user": "นายซุลกิฟลี สาอิ 1-9402-00061-21-6 เลขที่  4/1  หมู่ 1 บ.ป่าบอน ต.ป่าบอน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กจ6793นราธิวาส",
    "brand": "HONDA",
    "model": "NF125C(B)",
    "color": "ขาว",
    "user": "น.ส.ซารียะห์ แอสะ3-9605-00402-57-1 เลขที่ 152/1ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กจ3154นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125MSFG(TH)",
    "color": "ดำ",
    "user": "นางแยน๊ะ สะแลแม3-9602-00032-44-1เลขที่22ม.1บ.จาเราะต.ไพรวัน",
    "note": "หน.อาเยาะ"
  },
  {
    "license_plate": "1กจ3050นราธิวาส",
    "brand": "HONDA",
    "model": "ACB125CBTG(TH)",
    "color": "ดำ",
    "user": "นายรอมือลี เจ๊ะหลง3-9605-00199-89-9 เลขที่ 47ม.10บ.โต๊ะเปาะฆะต.ตันหยงมัส",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กจ1911นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MCFH(TH)",
    "color": "น้ำเงิน",
    "user": "นายอัดนัน แมกองมือแน3-9605-00417-87-0 เลขที่ 37ม.8บ.บาโยต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กง9572ยะลา",
    "brand": "HONDA",
    "model": "AFS110MCFH(TH)",
    "color": "ดำ",
    "user": "นายสาและ มะเกะ3-9501-00650-62-3เลขที่47/2ม.9บ้านอุเปต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กง945นราธิวาส",
    "brand": "HONDA",
    "model": "MSX125F(TH)",
    "color": "แดง",
    "user": "นายเอดี กาซอ1-9605-00088-55-1 เลขที่ 61ม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "กลุ่มเสี่ยง"
  },
  {
    "license_plate": "1กง9341นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125MSFG(TH)",
    "color": "ดำ",
    "user": "นายมะอาดี ดือราแม3-9605-00637-00-4 เลขที่ 67/2ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "1กง9124นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KSFF(TH)",
    "color": "ดำ",
    "user": "น.ส.ฮายาตี สมาแอ 1-9411-00033-50-5  เลขที่ 38 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "1กง8540ปัตตานี",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "ดำ",
    "user": "นายมักตา ลาเต๊ะ 3-9411-00010-98-6  เลขที่ 55 หมู่ 2 เจาะกะพ้อ กะรุบี",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "1กง6044ปัตตานี",
    "brand": "HONDA",
    "model": "NF125MT(B)",
    "color": "ดำ",
    "user": "นางคอซีนะ นาเซ 3-9408-00062-92-9  เลขที่ 1 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กง5066นราธิวาส",
    "brand": "HONDA",
    "model": "ACB125CBTF(TH)",
    "color": "น้ำเงิน",
    "user": "นายมาหามะ ดือเร๊ะ3-9602-00351-54-7เลขที่81ม.6ต.นานาค",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "1กง4837นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เทา",
    "user": "นายอิบบือราเฮง สือรี3-9605-00634-69-2 เลขที่ 13ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "1กง457นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MCFF(TH)",
    "color": "น้ำเงิน",
    "user": "นายยูโซ๊ะ มะเล๊าะ/บอบอ3-9602-00408-01-8เลขที่32/1ม.5บ้านอุเผะต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กง3593ปัตตานี",
    "brand": "HONDA",
    "model": "AFS110KSFF(TH)",
    "color": "ดำ",
    "user": "นายแวกอยี ตาเละ 1-9402-00077-30-9 เลขที่ 25/1  หมู่ 2 บ.โพธ ต.มะกรูด",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กง2119ปัตตานี",
    "brand": "HONDA",
    "model": "AFS110KSFF(TH)",
    "color": "ดำ",
    "user": "นายคอยา อาลีมูซอ 5-9402-99004-97-2 เลขที่ 6 หมู่ 5 ล้อแตก ต.บางโกระ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กง172ปัตตานี",
    "brand": "HONDA",
    "model": "AFS110MSFF(TH)",
    "color": "ดำ",
    "user": "นายมะรอเพะ ดือรามะ 3-9402-00218-63-4 เลขที่  36  หมู่ 5  ล้อแตก ต.บางโกระ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กง1073ยะลา",
    "brand": "HONDA",
    "model": "WW150G(TH)",
    "color": "น้ำเงิน",
    "user": "นายมีซี อาแว1-9605-00094-29-2 เลขที่ 19ม.6บ.บาโงกูโบต.บองอ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กฆ9677นราธิวาส",
    "brand": "HONDA",
    "model": "ACB125CBTF(TH)",
    "color": "แดง",
    "user": "น.ส.อาอิด๊ะ เจ๊ะอูเซ็ง/ดะห์3-9602-00426-50-4เลขที่60ม.4บ.ตะเหลียงต.เกาะสะท้อน",
    "note": "เปอมูดี"
  },
  {
    "license_plate": "1กฆ803นราธิวาส",
    "brand": "HONDA",
    "model": "ACF110SFF(3TH)",
    "color": "ขาว",
    "user": "นายอัสมาน มะลี3-9604-00300-50-4 เลขที่ 87ม.5บ.บาโงระนะต.มะรือโบตก",
    "note": "มวลชนจัดตั้งฝ่ายอูลามา"
  },
  {
    "license_plate": "1กฆ7890ปัตตานี",
    "brand": "HONDA",
    "model": "ACF110SFF(TH)",
    "color": "ดำ",
    "user": "นางฟาดีละห์ หะแว 3-9603-00264-94-8  เลขที่ 10 หมู่ 8 ปาแดกามูดิง กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กฆ4447นราธิวาส",
    "brand": "HONDA",
    "model": "ACF110SFF(3TH)",
    "color": "ดำ",
    "user": "นางรูฮานี มะยูโซ๊ะ3-9601-00341-08-6 เลขที่ 67/1ม.4บ.บาโงต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กฆ3461นราธิวาส",
    "brand": "HONDA",
    "model": "ACF110SFF(3TH)",
    "color": "แดง",
    "user": "นายดอเลาะ บือซา3-9605-00189-25-7 เลขที่ 150/4ม.12ต.ตันหยงมัส",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กฆ317ปัตตานี",
    "brand": "HONDA",
    "model": "AFS125MSFE(TH)",
    "color": "ดำ",
    "user": "น.ส.คอลิเยาะ ดอเลาะ 3-9402-00611-13-1  เลขที่ 134/7   หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กค9852นราธิวาส",
    "brand": "HONDA",
    "model": "ND125(J)",
    "color": "เขียว",
    "user": "นายมือร้าม ดอเล๊าะ3-9605-00374-83-6 เลขที่ 183ม.3บ.กูตงต.บองอ",
    "note": "กรรมการอาเยาะฝ่ายเศรษฐกิจ"
  },
  {
    "license_plate": "1กค9254นราธิวาส",
    "brand": "HONDA",
    "model": "WAVE125",
    "color": "เขียว",
    "user": "นายอิสะมะแอ เล็งฮะ3-9602-00371-30-1เลขที่50/2ม.1บ.ปูยูต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กค9244นราธิวาส",
    "brand": "HONDA",
    "model": "ACF110SFF(3TH)",
    "color": "แดง",
    "user": "นายกามา อาลี3-9602-00098-60-4เลขที่137ม.5บ.โคกกูแวต.พร่อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กค885นราธิวาส",
    "brand": "HONDA",
    "model": "NBC110MDFE(2TH)",
    "color": "ขาว",
    "user": "นายมะรอปะ หะยีลาเด็ง3-9605-00436-18-1 เลขที่ 12/2ม.5บ.ลูโบ๊ะกาเย๊าะต.เฉลิม",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กค7905นราธิวาส",
    "brand": "HONDA",
    "model": "ACF110SFF(3TH)",
    "color": "แดง",
    "user": "นายอุสมาน มะมิง3-9605-00753-32-2 เลขที่ 23/2ม.1บ.ปูโงะต.กาลิซา",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "1กค7898ยะลา",
    "brand": "HONDA",
    "model": "WAVE",
    "color": "เทา",
    "user": "นายอับดุลรอเชะ มาหะมะ3-9503-00073-16-6เลขที่42ม.8บ้านลือมุต.กรงปินัง",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กค7584นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125KSFE(TH)",
    "color": "แดง",
    "user": "นายฮัลบูเล๊าะ อาแว2-9602-00015-73-5เลขที่11ม.8ต.เกาะสะท้อน",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "1กค5532นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125CSFE(TH)",
    "color": "ดำ",
    "user": "นางอามีเนาะ การี 3-9411-00030-67-7   เลขที่ 24 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กค4849นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MCFE(TH)",
    "color": "น้ำเงิน",
    "user": "นายบราเฮ็ง คูตงราเซะ3-9605-00366-29-9 เลขที่ 144/3ม.8บ.อาแนต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กค4373ปัตตานี",
    "brand": "HONDA",
    "model": "ACF110CBFF(TH)",
    "color": "เทา",
    "user": "นายฮาซัน บากา 3-9403-00457-89-4  เลขที่ 82  หมู่ 8 บ.สลาม ต.นาประดู่",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กค3082นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125CSFE(TH)",
    "color": "ดำ",
    "user": "นายซูไฮรัน มะแซม.4บ.กูจิงลือปะต.เฉลิม",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กค1839ปัตตานี",
    "brand": "HONDA",
    "model": "ACF110SFE(3TH)",
    "color": "ดำ",
    "user": "นายมะยากี สะมะ 3-9402-00215-23-6 เลขที่ 35 หมู่ 4 ต.บางโกระ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กข5262นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125MSFD(TH)",
    "color": "ขาว",
    "user": "นายสาแล๊ะ รอสอดอ3-9602-00396-72-9เลขที่'7/1ม.6บ.ยูโยต.บางขุนทอง",
    "note": "กรรมการฝ่ายปกครอง ระดับแดอาเราะห์"
  },
  {
    "license_plate": "1กข4208นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MSFE(TH)",
    "color": "น้ำเงิน",
    "user": "นายมัดนาวี หะยีดือรานิง3-9605-00404-48-4 เลขที่ 19/1ม.2บ.เจ๊ะเกต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กข4165นราธิวาส",
    "brand": "HONDA",
    "model": "NF100K(B)",
    "color": "ดำ",
    "user": "นายปฐพี มีนา/มะนอร์3-9602-00360-83-0เลขที่85ม.8บ.ราญอต.เกาะสะท้อน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กข3653นราธิวาส",
    "brand": "HONDA",
    "model": "ACF110SFE(3TH)",
    "color": "แดง",
    "user": "นางแยน๊ะ สะแลแม3-9602-00032-44-1เลขที่22ม.1บ.จาเราะต.ไพรวัน",
    "note": "หน.อาเยาะ"
  },
  {
    "license_plate": "1กข3088นราธิวาส",
    "brand": "HONDA",
    "model": "ANC125BCTD(TH)",
    "color": "แดง",
    "user": "นางซาปีน๊ะ ดอแม3-9605-00639-28-7 เลขที่ 16ม.7บ.ตันหยงลิมอต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กก9472นราธิวาส",
    "brand": "HONDA",
    "model": "ANC125BCTD(TH)",
    "color": "ขาว",
    "user": "นายมานพธ์ หะยีนาแว/ไซนุง3-9605-00635-65-6 เลขที่ 48/1ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กก8057นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KSFD(TH)",
    "color": "ขาว",
    "user": "นายอัมรัน อาแว3-9605-00237-07-3 เลขที่ 3/3ม.3บ.กูตงต.บองอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กก6284นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KSFD(TH)",
    "color": "",
    "user": "นางแยน๊ะ สะแลแม3-9602-00032-44-1เลขที่22ม.1บ.จาเราะต.ไพรวัน",
    "note": "หน.อาเยาะ"
  },
  {
    "license_plate": "1กก574นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MSFC(TH)",
    "color": "แดง",
    "user": "นายมาหมะสกรี ลาเต๊ะ3-9602-00385-29-8เลขที่80/1ม.2บ.เกาะสะท้อนต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กก5731นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125KSFD(TH)",
    "color": "แดง",
    "user": "นายอับดุลรอมัน ดอฮะ1-9602-00052-53-5เลขที่40ม.1บ.ตาบาต.ไพรวัน",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กก570นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MSFC(TH)",
    "color": "แดง",
    "user": "นายมาหมะสกรี ลาเต๊ะ3-9602-00385-29-8เลขที่80/1ม.2บ.เกาะสะท้อนต.เกาะสะท้อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กก5213นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MSFD(TH)",
    "color": "ขาว",
    "user": "นายกูเฮง ยูโซ๊ะ3-9601-00151-75-9 เลขที่ 47ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "สมาชิกปฏิบัติการ (กลุ่มประกอบระเบิด)"
  },
  {
    "license_plate": "1กก5210นราธิวาส",
    "brand": "HONDA",
    "model": "ACF110SFE(3TH)",
    "color": "ขาว",
    "user": "นายบาฮารี วาดิง3-9605-00105-52-5 เลขที่ 29ม.8บ.ไอร์ปาเซต.ตันหยงลิมอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "1กก5064นราธิวาส",
    "brand": "HONDA",
    "model": "AFS125KSFD(TH)",
    "color": "แดง",
    "user": "นายกามา อาลี3-9602-00098-60-4เลขที่137ม.5บ.โคกกูแวต.พร่อน",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กก5014นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MSFD(TH)",
    "color": "ดำ",
    "user": "นายยามา เจ๊ะเฮง1-9605-00106-00-2 เลขที่ 178ม.1บ.ทำนบต.มะรือโบตก",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "1กก4409นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110MSFD(TH)",
    "color": "แดง",
    "user": "นายมะหะมะ มูซอ3-9605-00638-23-0 เลขที่ 175ม.1บ.ตะโละต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กก2835ปัตตานี",
    "brand": "HONDA",
    "model": "AFS125MSFD(TH)",
    "color": "ขาว",
    "user": "นายสาการียา เกาะและ 3-9402-00250-10-4 เลขที่ 8 หมู่ 2 คูระ ม่วงเตี้ย",
    "note": "มวลจนจัดตั้ง ฝ่ายอูลามา"
  },
  {
    "license_plate": "1กก2835ปัตตานี",
    "brand": "HONDA",
    "model": "AFS125MSFD(TH)",
    "color": "ขาว",
    "user": "นายสาการียา เกาะและ 3-9402-00250-10-4 เลขที่ 8 หมู่ 2 คูระ ม่วงเตี้ย",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กก1309นราธิวาส",
    "brand": "HONDA",
    "model": "AFS110KSFD(TH)",
    "color": "น้ำเงิน",
    "user": "นายรอพา เจ๊ะสแม1-9605-00126-77-1 เลขที่ 157/4ม.8บ.อาแนต.บองอ",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กค6289นราธิวาส",
    "brand": "FORD",
    "model": "ESCAPE",
    "color": "น้ำตาล",
    "user": "นายแวฮามะ บากา3-9605-00787-44-8 เลขที่ 96ม.7บ.บาโงสะโตต.บาโงสะโต",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "2กฉ6699กรุงเทพมหานคร",
    "brand": "DUCATI",
    "model": "M101AB",
    "color": "แดง",
    "user": "นายซอลาฮุดดิน ตาเยะ 5-9411-00001-96-7   เลขที่ 43 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "บจ1848นราธิวาส",
    "brand": "CHEVROLET",
    "model": "COLORADO",
    "color": "น้ำตาล",
    "user": "นายรอมือลี/(เปลี่ยนชื่อเป็น)ว่ะลียุดดีน เจ๊ะหลง3-9605-00381-06-9 เลขที่ 59/1ม.4บ.บองอต.บองอ",
    "note": "หน.ชุดปฏิบัติการ"
  },
  {
    "license_plate": "กง6892ปัตตานี",
    "brand": "CHEVROLET",
    "model": "1JT19",
    "color": "ขาว",
    "user": "นายนิเฮง ลอโมง3-9605-00644-24-8 เลขที่ 24ม.2บ.ทุ่งขมิ้นต.ตันหยงลิมอ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "1กก5731ยะลา",
    "brand": "YAMAHA",
    "model": "UE051",
    "color": "ขาว",
    "user": "นายยะละ สาแม 1-9402-00020-13-7 เลขที่  37/1 หมู่ 5 ล้อแตก ต.บางโกระ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กวก188นราธิวาส",
    "brand": "SUZUKI",
    "model": "FD110XCSU",
    "color": "แดง",
    "user": "นายอำรัญ อาแวหามะ3-9602-00354-06-8เลขที่3ม.6บ.บางขุนทองต.บางขุนทอง",
    "note": "LOGISTIK สนับสนุน/จัดเก็บ"
  },
  {
    "license_plate": "กง2302ยะลา",
    "brand": "SUZUKI",
    "model": "SJ413W",
    "color": "น้ำเงิน",
    "user": "นายสุลกิฟลี เจะมะ 3-9402-00293-76-8  เลขที่ 21/1  หมู่ 8 บ.โผงโผงใน ต.ปากล่อ",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ตค1602สงขลา",
    "brand": "KUBOTA",
    "model": "L4508DI SPECIAL",
    "color": "ส้ม",
    "user": "นายรุสดี แวมาม 3-9402-00441-47-3  เลขที่ 31   หมู่ 5 บ.ป่าลาม ต.ช้างให้ตก",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ศฎ7493กรุงเทพมหานคร",
    "brand": "ISUZU",
    "model": "TFR85HWA0B",
    "color": "ดำ",
    "user": "นายดอรอแม หะยีสาเมาะ 3-9402-00250-00-7   เลขที่ 7 หมู่ 2 คูระ ม่วงเตี้ย",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ผฉ2450สงขลา",
    "brand": "ISUZU",
    "model": "TFR86JEQH5 (M)",
    "color": "ดำ",
    "user": "นางมีเนาะ ยูโซะ 3-9402-00609-98-6  เลขที่ 130  หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "บธ432ปัตตานี",
    "brand": "ISUZU",
    "model": "TFR86HPM8B (M)",
    "color": "ดำ",
    "user": "นายนูรดีน ดือเฆะ 3-9402-00610-54-2  เลขที่ 128/8  หมู่ 1 บ.แม่กัง ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "ฬรต970กรุงเทพมหานคร",
    "brand": "HONDA",
    "model": "NF125C(C)",
    "color": "ดำ",
    "user": "นายมะดารี เจะเลาะ 3-9402-00562-03-3 เลขที่  26 หมู่ 6 บ.โคกอ้น ต.ท่าเรือ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กลม593ปัตตานี",
    "brand": "HONDA",
    "model": "NF125C(B)",
    "color": "ดำ",
    "user": "นายมะอูเซ็ง การี 3-9411-00030-63-4  เลขที่ 78 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กลต549นราธิวาส",
    "brand": "HONDA",
    "model": "DREAM",
    "color": "ดำ",
    "user": "นายมูฮัมมะมูสตาวา สะนิ 3-9411-00029-19-9   เลขที่ 4 หมู่ 6 คอลอกาปะ กะรุบี",
    "note": "เปอมูดอ"
  },
  {
    "license_plate": "กยข764ปัตตานี",
    "brand": "HONDA",
    "model": "NF125C(A)",
    "color": "ดำ",
    "user": "นายมุสะตอปากามา สาและ 3-9402-00355-59-3 เลขที่  107  หมู่ 7 บ.คลองช้างออก ต.นาเกตุ",
    "note": "กรรมการฝ่ายปกครองระดับแดอาเราะห"
  },
  {
    "license_plate": "1กก4421ปัตตานี",
    "brand": "HONDA",
    "model": "AFS110KSFD(TH)",
    "color": "ขาว",
    "user": "นายนาคอรี สะตาปอ 1-9402-00024-69-8 เลขที่  28 หมู่ 1 บ.ควน ต.ท่าเรือ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "ขค9655สงขลา",
    "brand": "CHEVROLET",
    "model": "OPTRA",
    "color": "เทา",
    "user": "นายรอหะ เจะเหง๊าะ 3-9402-00648-19-1   เลขที่ 178  หมู่ 5 บ.จุปะ ต.ควนโนร",
    "note": "มวลชนจัดตั้ง"
  },
  {
    "license_plate": "กค9272ปัตตานี",
    "brand": "",
    "model": "",
    "color": "น้ำเงิน",
    "user": "นายมาหะมะสอดี มะเด็ง 3-9402-00356-98-1  เลขที่ 124/3  หมู่ 7 บ.คลองช้างออก ต.นาเกตุ",
    "note": "สมาชิกปฏิบัติการ"
  },
  {
    "license_plate": "กกธ158นราธิวาส",
    "brand": "",
    "model": "DREAM ACCESS",
    "color": "ดำ",
    "user": "นายดอฮะ สูแน3-9605-00808-57-7 เลขที่ 154ม.6บ.กาเด็งต.กาลิซา",
    "note": "มวลชนจัดตั้ง"
  }
]';
            
		
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
		
		
		
          default:
		// $replyText=$replyText.$displayName.$statusMessage;
		break;
            }//end switch
	    
	    $bot->replyText($reply_token, $replyText);
    }//end if text
}// end foreach event
?>
