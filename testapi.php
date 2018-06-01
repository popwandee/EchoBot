<?php
$explodeText[1]='Bangkok';
$news_url="http://api.openweathermap.org/data/2.5/forecast?q=".$explodeText[1].",th&units=metric&appid=cb9473cef915ee0ed20ac67817d06289" ;
$content = file_get_contents($news_url); // อ่านข้อมูล JSON
$json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
print_r($json_arr);
//$date=date("F j, Y, g:i a",$json_arr[list][0][dt]);
//echo $date;
//echo $json_arr[list][0][dt];
function print_weather($item, $key)
{
    echo "$key => $item\n";
}
array_walk($json_arr, 'print_weather');
//echo $json_arr[list][0][main][temp_max];
//echo $json_arr[list][0][main][temp_min];
//echo $json_arr[list][0][weather][0][main];
//echo $json_arr[list][0][weather][0][description];
//echo $json_arr[list][0][weather][0][dt_txt];
?>
