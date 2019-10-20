<?php
define("MLAB_API_KEY", '6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv');
  $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey='.MLAB_API_KEY.'&q={"nationid":"3969900145701"}');
   $data = json_decode($json);
   $isData=sizeof($data);
   if($isData >0){
      $count=1;
      foreach($data as $rec){
	       $count++;
         $textReplyMessage= "\nหมายเลข ปชช. ".$rec->nationid."\nชื่อ".$rec->name."\nที่อยู่".$rec->address."\nหมายเหตุ".$rec->note;
         echo $textReplyMessage; echo "<br>";
      }//end for each
