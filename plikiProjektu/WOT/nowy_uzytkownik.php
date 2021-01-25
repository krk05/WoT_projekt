<?php
	session_start();
	require_once("config.php");

	$wot = mysqli_connect($serwer,$user,$haslo,$baza_danych);
	if(!$wot)
		die("Nie polaczono");	
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="styles.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container-fluid" style=" height: 100vh; ">
	<h2 class="tytul">NOWY UŻYTKOWNIK</h2>

<div class="row" style="height: 100%">	
	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>

	    <div class="col-sm-6" style="background-color:#4d4d4d;">



	    	<div class="login">
			   	<form action="obsluga.php" method="post">
						<br> <input type="text" name="nick" placeholder="nick" required style=" width : 40%; height: 50px;"><br>
						<br> <input type="email" name="email" placeholder="email" required style=" width : 40%; height: 50px;"><br>
						<br> <input type="password" name="haslo" placeholder="hasło" required style=" width : 40%; height: 50px;"><br><br>
						<input type="submit" name="dodaj" value="Zatwierdź" style="width: 20%">
				</form>

				<div style="color:white;">
					<?php
						if(!empty($_SESSION["nowyUzytkownik"]))
						{
							echo "<br>" . $_SESSION["nowyUzytkownik"] . "<br><br>";
							unset($_SESSION["nowyUzytkownik"]);
							session_regenerate_id();
						}
					?>
				</div>
		    
				<br><br>
				<a href="index.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">ZALOGUJ</p></a>
			</div>
		</div>

	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>
	  
</div>
</div>
</body>
</html>

