<?php
$text="hello yes i do what do you do";
$explodeText=explode(" ",$text);
$text_parameter='';
$count_element=count($explodeText);
foreach($i=1;$i<$count_element;$i++){
  $text_parameter=$text_parameter.$explodeText[$i];
}
echo $text;
echo "<br>".$text_parameter;
