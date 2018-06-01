<?php
$explodeText[1]='Bangkok';
$news_url="http://api.openweathermap.org/data/2.5/forecast?q=".$explodeText[1].",th&units=metric&appid=cb9473cef915ee0ed20ac67817d06289" ;
$content = file_get_contents($news_url); // อ่านข้อมูล JSON
$json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
print_r(json_arr);
?>
