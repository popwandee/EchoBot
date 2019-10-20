<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Website Title</h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Home Page</h2>
			<p>Welcome back, <?=$_SESSION['name']?>!</p>
			<?php
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
?>
		</div>
	</body>
</html>
