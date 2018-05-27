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

echo "ok 2";

$bot = new \LINE\LINEBot(

    new \LINE\LINEBot\HTTPClient\CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN),

    ['channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET]

);

echo "ok 3";

$signature = $_SERVER["HTTP_".\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

$body = file_get_contents("php://input");



$events = $bot->parseEventRequest($body, $signature);



foreach ($events as $event) {

   if ($event  instanceof \LINE\LINEBot\Event\MessageEvent\ImageMessage){

     $reply_token = $event->getReplyToken();
       $a = ['ว้าว ว้าว ว้าว', 'อุ๊ยตาย ว้ายกรีดดดด', 'ไม่หื่นนะฮะ'];

    $text = $a[mt_rand(0, count($a) - 1)];

     //$text = 'รูปอะไรเหรอฮะ';

      $bot->replyText($reply_token, $text);

   }

    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {

        $reply_token = $event->getReplyToken();

        $text = $event->getText();

        $explodeText=explode(" ",$text);

        //$bot->replyText($reply_token, $explodeText[0]);

        switch ($explodeText[0]) {

          case 'สอนเป็ด':

              //$x_tra = str_replace("สอนฮูก","", $text);

              $pieces = explode("|", $explodeText[1]);

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

              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/linebot?apiKey='.$api_key.'';

              $context = stream_context_create($opts);

              $returnValue = file_get_contents($url,false,$context);

              $text = 'ขอบคุณที่สอนเป็ด ฮะ คุณสามารถสอนให้ฉลาดได้เพียงพิมพ์: สอนเป็ด [คำถาม|คำตอบ] ต้องเว้นวรรคด้วยนะ  สอบถามราคาหุ้นพิมพ์ stock ถามข่าวพิมพ์ news';

              $bot->replyText($reply_token, $text);

              break;

          case 'stock':

                $symbol=$explodeText[1];
                $text= 'stock price ราคาหุ้นรายวัน '.$symbol.' ';
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
                     if ($KeepMainkey=="Meta Data" && $countm=='1') {
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

          case 'news':

              $text=$text.' news update ต่อไปจะรายงานข่าวหุ้นด้วยนะครับ';

              $bot->replyText($reply_token, $text);

              break;

          default:

              $api_key="6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv";

              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/linebot?apiKey='.$api_key.'';

              $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/linebot?apiKey='.$api_key.'&q={"question":"'.$explodeText[0].'"}');

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
