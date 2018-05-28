<?php
$text='';
$news_url='https://newsapi.org/v2/top-headlines?country=th&apiKey=dca7d30a57ec451cad6540a696a7f60a' ;
$content = file_get_contents($news_url); // อ่านข้อมูล JSON
$json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
$countm= 0;
while (list($key) = each($jarr)) { // ทำการ list ค่า key ของ Array ทั้งหมดออกมา
   $KeepMainkey = $key; //เก็บคีย์หลัก
   $count = count($json_arr[$key]); // นับจำนวนแถวที่เก็บไว้ใน Array ใน key นั้นๆ
   $getarr1 = $json_arr[$key]; //ส่งมอบคุณสมบัติ Array ระดับกลาง
   while (list($key) = each($getarr1)) {
     if ($KeepMainkey=="Meta Data") {//&& $countm=='1'
        $text= $text.' '.$key.' '.$getarr1[$key].' ';
      }
      $countm++;
      if ($KeepMainkey!="Meta Data") {
        $getarrayday = $getarr1[$key];
        while (list($key) = each($getarrayday)) {
          $text= $text.' '.$key.' '.$getarrayday[$key].' ' ; //แสดงคีย์และผลลัพธ์ขอคีย์ของวัน
        }//สิ้นสุดการลิสต์คีย์ชั้นลึก (ระดับวัน)
      }
    }//สิ้นสุดการลิสต์คีย์ชั้นกลาง
  } //สิ้นสุดการลิสต์คีย์ชั้นแรก
  print_r($text);
  ?>
