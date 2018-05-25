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
     $text = 'รูปอะไรเหรอฮะ';
      $bot->replyText($reply_token, $text);
   }
    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
        $reply_token = $event->getReplyToken();
        $text = $event->getText();
        switch ($text) {
          case 'สอนฮูก':
              $x_tra = str_replace("สอนฮูก","", $text);
              $pieces = explode("|", $x_tra);
              $_question=str_replace("[","",$pieces[0]);
              $_answer=str_replace("]","",$pieces[1]);
              //Post New Data
              $newData = json_encode(
                array(
                  'question' => $_question,
                  'answer'=> $_answer
                )
              );
              $opts = array(
                'http' => array(
                    'method' => "POST",
                    'header' => "Content-type: application/json",
                    'content' => $newData
                 )
              );

              $api_key="6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv";
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/linebot?apiKey='.$api_key.'';
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              $text = 'ขอบคุณที่สอนฮูก ฮะ คุณสามารถสอนให้ฉลาดได้เพียงพิมพ์: สอนฮูก[คำถาม|คำตอบ]';

              break;
          case 'stock': $text=$text.'stock price';break;
          case 'news': $text=$text.'news update';break;
          default:
              $api_key="6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv";
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/linebot?apiKey='.$api_key.'';
              $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/linebot?apiKey='.$api_key.'&q={"question":"'.$text.'"}');
              $data = json_decode($json);
                foreach($data as $rec){
                  $text= $rec->answer;
                  //-----------------------
                }
            }
        $bot->replyText($reply_token, $text);
    }
}

echo "OK4";
