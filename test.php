<?php
require __DIR__."/vendor/autoload.php";

$news_url='https://newsapi.org/v2/top-headlines?country=th&apiKey=dca7d30a57ec451cad6540a696a7f60a' ;
$content = file_get_contents($news_url); // อ่านข้อมูล JSON
$json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
$count_news=0;
  while (list($key) = each($json_arr)) { // ทำการ list ค่า key ของ Array ทั้งหมดออกมา
    if($key=="articles"){
     $json_arr1 = $json_arr[$key]; //ส่งมอบคุณสมบัติ Array ระดับกลาง
     while (list($key) = each($json_arr1)) {
       $count_news++;
          //echo$json_arr1[$key]['title'];
          //echo " @ ";echo$json_arr1[$key]['publishedAt'];
          //echo$json_arr1[$key]['description'];
          //echo$json_arr1[$key]['urlToImage'];
          //echo " source: ";echo$json_arr1[$key]['source']['name'];
          //echo " ";echo$json_arr1[$key]['url'];
          //echo "</br>";

          if($count_news<5)
             $text_arr[$count_news]=$json_arr1[$key]['title'].$json_arr1[$key]['description'].$json_arr1[$key]['url'];
            else ;
             }
      }
    }

  //echo '$count_news ='.$count_news;
  print_r($text_arr);
  //$text=$text_arr[mt_rand(0, count($text_arr) - 1)];//$text_arr[mt_rand[min,max]]; random index
  //echo "last output is ".$text;
  $explodeText[1]="Bangkok";
  $news_url="http://api.openweathermap.org/data/2.5/weather?q=".$explodeText[1].",th&units=metric&appid=cb9473cef915ee0ed20ac67817d06289";
  $content = file_get_contents($news_url); // อ่านข้อมูล JSON
  $json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
print_r($json_arr);
echo "<br>รายงานสภาพอากาศ ".$json_arr[name];
$date = date("F j, Y, g:i a",$json_arr[dt]);echo " เวลา ".$date;
echo " มีลักษณะอากาศ".$json_arr[weather][0][main].$json_arr[weather][0][description];
echo "<br>ความกดอากาศ ".$json_arr[main][pressure];
echo "<br>ความชื้นสัมพัทธ์ ".$json_arr[main][humidity];
echo "<br>อุณหภูมิ ".$json_arr[main][temp];
echo "<br>อุณหภูมิสูงสุด ".$json_arr[main][temp_max];
echo "<br>อุณหภูมิต่ำสุด ".$json_arr[main][temp_min];
$sunrise = date("F j, Y, g:i a",$json_arr[sys][sunrise]);echo "<br>".$sunrise;
$sunset = date("F j, Y, g:i a",$json_arr[sys][sunset]);echo "<br>".$sunset;
//echo $news_url;
  ?>
