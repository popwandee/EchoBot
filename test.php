<?php
require __DIR__."/vendor/autoload.php";

$text='';
$news_url='https://newsapi.org/v2/top-headlines?country=th&apiKey=dca7d30a57ec451cad6540a696a7f60a' ;
$content = file_get_contents($news_url); // อ่านข้อมูล JSON
$json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
$count_news=0;
  while (list($key) = each($json_arr)) { // ทำการ list ค่า key ของ Array ทั้งหมดออกมา
    if($key=="articles"){
     $json_arr1 = $json_arr[$key]; //ส่งมอบคุณสมบัติ Array ระดับกลาง
     while (list($key) = each($json_arr1)) {
       ++$count_news;
          //echo$json_arr1[$key]['title'];
          //echo " @ ";echo$json_arr1[$key]['publishedAt'];
          //echo$json_arr1[$key]['description'];
          //echo$json_arr1[$key]['urlToImage'];
          //echo " source: ";echo$json_arr1[$key]['source']['name'];
          //echo " ";echo$json_arr1[$key]['url'];
          //echo "</br>";
          $text_arr[$count_news]=$json_arr1[$key]['title'].$json_arr1[$key]['description'];
        }
    }
  }
  echo "$count_news =".$count_news;
  print_r($text_arr);
  $text=$text_arr[mt_rand(0, count($text_arr) - 1)];//$text_arr[mt_rand[min,max]]; random index
  echo "last output is ".print_r($text);

  ?>
