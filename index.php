<?php
echo "hello world <br>";
$userId = "Ua300e9b08826b655e221d12b446d34e5";
$json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/user_register?apiKey=6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv&q={"userId":"'.$userId.'"}');
            $data = json_decode($json);
            $isUserRegister=sizeof($data);
		if($isUserRegister <=0){
		   echo "คุณ ยังไม่ได้ลงทะเบียน ID ".$userId." ไม่สามารถเข้าถึงฐานข้อมูลได้นะคะ\n กรุณาพิมพ์ #register ยศ ชื่อ นามสกุล ตำแหน่ง สังกัด หมายเลขโทรศัพท์ เพื่อลงทะเบียนค่ะ";
                          
	         }else{ // User registered
                    
$nationid='3969900145701';
$json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey=6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv&q={"nationid":"'.$nationid.'"}');

$data = json_decode($json);
  $isData=sizeof($data);
  if($isData >0){
    echo "You have data.<br>";
    foreach($data as $rec){
	      $count++;
        $textReplyMessage= "\nหมายเลข ปชช. ".$rec->nationid."\nชื่อ".$rec->name."\nที่อยู่".$rec->address."\nหมายเหตุ".$rec->note;
	    echo $textReplyMessage."<br>";
    }//end foreach                             
  }else{
    echo "You don't have any data";
  }
		}//end User registered
