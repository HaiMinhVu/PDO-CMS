<?php 
include('../../dbconnect.php'); 
include('../../key.php');

if(count($_SESSION) > 0 && isset($_SESSION['username'])){
	header('location:index.php');
}
else{
	if(isset($_POST['login'])){

        $username = $_POST['username'];
        $password = $_POST['password'];
        $sql = "SELECT id, username, role_id  FROM users Where username=? and password = aes_encrypt(?,?) and status='1'";
        $stmt = $readdb->prepare($sql);
        $stmt->execute([$username, $password, $key]);
        $count = $stmt->rowCount();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($count != 1){
            $message = "<label><font color='red'>Log in failed. Check your username and password</font></label>";
        } 
        else{
            // set session
            $_SESSION['user'] = $row["username"];
            $_SESSION['uid'] = $row['id'];
            $_SESSION['role'] = $row['role_id'];

            $message = $_SESSION['role'];
            // insert sessions
            $iSession = "INSERT INTO sessions (uid) VALUES (?)";
            $sessionnstmt = $writedb->prepare($iSession);
            $sessionnstmt->execute([$row['id']]);

            header('location:index.php'); 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title >Sellmark Content Management</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">


</head>
<body>
  	
	<div class="row" style="margin-top: 100px">
		<div class="col-xs-12 col-sm-12 col-md-12 ">
			<h1 align="center" style="color: blue"><strong>SELLMARK CONTENT MANAGEMENT</strong></h1>
		</div>
	</div>
	<div class="row">
		<div class="offset-sm-3 offset-md-3 offset-lg-3 col-sm-6 col-md-6 col-lg-6" align="center">
			<p>Please use the login credentials provided to you to access Sellmark External Resources.</p>
			<p align="center"><?php echo $message; ?></p>
			<div id="center">
				
				<form class="form-horizontal" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
					<div class="form-group">
	  					<div class="col-sm-10">
	  						<input type="text" name="username" class="form-control" placeholder="Username">
	  					</div>
	  				</div>
	  				<div class="form-group">
	  					<div class="col-sm-10">
	  						<input type="password" name="password" class="form-control" placeholder="Password">
	  					</div>
	  				</div>
	  				<div class="form-group">
					    <div class="col-sm-10">
					    	<input type="submit" name="login" value="Login" class="btn btn-primary" />
					    </div>
				    </div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>