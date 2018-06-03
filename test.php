<?php
$text="hello yes i do what do you do";
$explodeText=explode(" ",$text);
$text_parameter='';
$count_element=count($explodeText);
print_r($explodeText);
echo $count_element;

for($i=1;$i<$count_element;$i++){
  $text_parameter=$text_parameter." ".$explodeText[$i];
  echo $i;
}

echo $text;
echo "<br>text_parameter is ".$text_parameter;

$text_parameter='What is it';
echo $text_parameter;
$lang_url="https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=th&dt=t&q=$text_parameter" ;
 $content = file_get_contents($lang_url); // อ่านข้อมูล JSON
 $json_arr = json_decode($content, true); // แปลงข้อมูล JSON ให้อยู่ในรูปแบบ Array
 echo "<br>".$lang_url."<br>";
print_r($json_arr);
?>
