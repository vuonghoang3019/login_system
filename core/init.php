<?php 
    include 'config.php';
    include 'classes/PHPMailer.php';
    include 'classes/Exception.php';
    include 'classes/SMTP.php';
    date_default_timezone_set('Asia/Ho_Chi_Minh'); 


    
    //auto load
      
    spl_autoload_register(function($class){
		require_once "classes/{$class}.php";
	});
    $userObj   = new Users;
    $verifyObj = new Verify;
    //session
    session_start();
?>