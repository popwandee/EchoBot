<?php
session_start();
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	die ('Please fill both the username and password field!');
}
$options = [
    'cost' => 12,
];
echo password_hash("mibn88613", PASSWORD_BCRYPT, $options);
echo "$_POST password is ".$_POST['password']."<br>";
	    $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/user_register?apiKey=6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv&q={"username":"'.$_POST['username'].'"}');
            $data = json_decode($json);
            $isUserRegister=sizeof($data);
if ($isUserRegister > 0) {
	
	// Account exists, now we verify the password.
  foreach($data as $rec){
      $password= $rec->password;
	  echo "$password is ".$password."<br>";
      }//end foreach
	// Note: remember to use password_hash in your registration file to store the hashed passwords.
	if (password_verify($_POST['password'],$password)) {
		// Verification success! User has loggedin!
		// Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
		session_regenerate_id();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['name'] = $_POST['username'];
		$_SESSION['id'] = $id;
		echo 'Welcome ' . $_SESSION['name'] . '!';
	} else {
		echo 'Incorrect password!';
	}
} else {
	echo 'Incorrect username!';
}
