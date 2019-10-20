<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>TSU 1DB</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>TSU 1DB : หน่วยเฝ้าตรวจ ขกท.สน.จชต.</h1>
				<a href="people.php"><i class="fas fa-user-circle"></i>People</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>People ระบบค้นหาข้อมูลบุคคล</h2>
      <p><form action="people.php" method="post">
				<label for="username">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="nationalId" placeholder="nationalId" id="nationalId" required>
				<input type="submit" value="ตกลง">
			</form>
      </p>
			<p>
			<?php
      
if ( isset($_POST['nationalId']) ) {
	
$nationalId = $_POST['nationalId'];
$json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey=6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv&q={"nationid":"'.$nationalId.'"}');
$data = json_decode($json);
  $isData=sizeof($data);
  if($isData >0){
    $count=0;
    echo "<table>";
    foreach($data as $rec){
	      $count++;
        echo "<tr><td>".$count."<td>".$rec->name."</td>";
        echo "<td>".$rec->nationid."</td>";
        echo "<td>".$rec->address."</td>";
        echo "<td>".$rec->note."</td></tr>";
    }//end foreach  
   echo " </table>";
  }else{
    echo "You don't have any data";
  }
		}//end User registered
    } // end if isset nationalID for search
?>
</p>
		</div>
	</body>
</html>
