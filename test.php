<?php
require __DIR__."/vendor/autoload.php";

$text='test news api';
echo $text."</br>";
$news_url='https://newsapi.org/v2/top-headlines?country=th&apiKey=dca7d30a57ec451cad6540a696a7f60a' ;
$content = file_get_contents($news_url); // อ่านข้อมูล JSON
$json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
echo "json_arr is "; print_r($json_arr);echo "</br>";
foreach ($json_arr as $key => $json_arr1)
    {
        if (is_array ($json_arr1)
        {/*
          foreach ($json_arr1 as $key => $json_arr2)
              {
                  if (is_array ($json_arr2)
                  {
                    while (list($index) = each($json_arr2)) {
                      echo "print json_arr2 index".$index." &nbsp"; print_r($json_arr2);echo"</br>";
                    }
                  }
                  else
                  {
                      echo $json_arr2."</br>"; // print out element key if not array
                  }
              }*/
        }
        else
        {
            echo $json_arr1."</br>"; // print out element key if not array
        }
    }
/*
while (list($key) = each($json_arr)) { // ทำการ list ค่า key ของ Array ทั้งหมดออกมา
   $KeepMainkey = $key; //เก็บคีย์หลัก
   $count = count($json_arr[$key]); // นับจำนวนแถวที่เก็บไว้ใน Array ใน key นั้นๆ
   $getarr1 = $json_arr[$key]; //ส่งมอบคุณสมบัติ Array ระดับกลาง
   while (list($key) = each($getarr1)) {
     if ($KeepMainkey=="articles") {//&& $countm=='1'
       while()
        $text= $text.' '.$key.' '.$getarr1[$key].' ';
        echo $countm."key is".$key;print_r($text);echo "</br>";
      }
      }
    }//สิ้นสุดการลิสต์คีย์ชั้นกลาง
    */
  echo "last output is ".print_r($text);

  ?>
