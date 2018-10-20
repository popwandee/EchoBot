<?php

require_once __DIR__ . '/vendor/autoload.php';



// Line Developers
 define("CHANNEL_SECRET", 'db66a0aa1a057415832cfd97f6963cb3');
 define("CHANNEL_ACCESS_TOKEN",'22hdP860hpFYokOIcmae6cdlKPpriZO3/XHhRWkLEp8YPkXjS8R36U7reDuvpliAtRKnkbKLNAh2/QByqEocSkrGx3yyz1T6dGdHu9nrSc3t5PejaraL26vuKjCppl3mQ7k/lqhZ4F3XaWH8/4tWiAdB04t89/1O/w1cDnyilFU=');



// Line Message APIに接続
$input = file_get_contents('php://input');
$json = json_decode($input);
$event = $json--->events[0];
$http_client = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new \LINE\LINEBot($http_client, ['channelSecret' => getenv('CHANNEL_SECRET')]);

// 
$event_type = $event->type;
$event_message_type = $event->message->type;

// 
if ('message' == $event_type) {

    // 
    if ('text' == $event_message_type) {

        // 
        $text = $event->message->text;

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
         // $text_message_builder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text);
         // $response = $bot->replyMessage($event->replyToken, $text_message_builder);
    }

    // ถ้าประเภทข้อความเป็นพิกัด
    else if ('location' == $event_message_type) {
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

    // สำหรับข้อความอื่น ๆ นอกเหนือจากข้อความและข้อมูลตำแหน่ง
    else {
        $text_message_builder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('เขาส่งแสตมป์หรือไม่? แต่ฉันไม่สามารถตอบสนองได้ ฉันขอโทษ NYA <img draggable="false" class="emoji" alt="🍜" src="https://s.w.org/images/core/emoji/2.4/svg/1f35c.svg" id="exifviewer-img-3" exifid="-1690832363" oldsrc="https://s.w.org/images/core/emoji/2.4/svg/1f35c.svg" scale="0">');
        $response = $bot->replyMessage($event->replyToken, $text_message_builder);
    }
}

// เมื่อ event เป็นการติดตาม follow bot
else if ('follow' == $event_type) {
    // แสดงตัวอิโมจิ
    $text_message_builder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('ขอบคุณสำหรับการเพิ่มเพื่อน! ขอแสดงความนับถือ NYA<img draggable="false" class="emoji" alt="🍜" src="https://s.w.org/images/core/emoji/2.4/svg/1f35c.svg" id="exifviewer-img-4" exifid="-1690832363" oldsrc="https://s.w.org/images/core/emoji/2.4/svg/1f35c.svg" scale="0">');
    $response = $bot->replyMessage($event->replyToken, $text_message_builder);
}

// เมื่อ event เป็นการเข้าร่วม join bot
else if ('join' == $event_type) {
    // แสดงตัวอิโมจิ
    $text_message_builder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('ยินดีต้อนรับ! ลองพูดคุยเกี่ยวกับราเม็ง<img draggable="false" class="emoji" alt="🍜" src="https://s.w.org/images/core/emoji/2.4/svg/1f35c.svg" id="exifviewer-img-5" exifid="-1690832363" oldsrc="https://s.w.org/images/core/emoji/2.4/svg/1f35c.svg" scale="0">よろしくにゃ<img draggable="false" class="emoji" alt="🍜" src="https://s.w.org/images/core/emoji/2.4/svg/1f35c.svg" id="exifviewer-img-6" exifid="-1690832363" oldsrc="https://s.w.org/images/core/emoji/2.4/svg/1f35c.svg" scale="0">');
    $response = $bot->replyMessage($event->replyToken, $text_message_builder);
}

// การเข้าถึงจากแหล่งอื่นๆ เช่น browser
else {
    $text_message_builder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('การเข้าถึงที่ไม่ได้รับอนุญาต');
    $response = $bot->replyMessage($event->replyToken, $text_message_builder);

    echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
}

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
