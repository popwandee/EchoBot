<?php
echo "hello world <br>";
$nationid='3969900145701';
$json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey=6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv&q={"nationid":"'.$nationid.'"}');

$data = json_decode($json);
  $isData=sizeof($data);
  if($isData >0){
    echo "You have data.<br>";
    foreach($data as $rec){
	      $count++;
        $textReplyMessage= "\nหมายเลข ปชช. ".$rec->nationid."\nชื่อ".$rec->name."\nที่อยู่".$rec->address."\nหมายเหตุ".$rec->note;
    }//end foreach                             
  }else{
    echo "You don't have any data";
  }
