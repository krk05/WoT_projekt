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
	<?php if( empty($_SESSION['user'])) 
	{

		$_SESSION["komunikat"] = "Zaloguj się aby uzyskać dostęp do tej strony!";
		header("Location: index.php");		    	
	}
	else 	session_regenerate_id();
	$path = "gracz.php";
	if(isSet($_GET["user"]) && $_GET["user"] == "admin")
    	{
    		$path = "admin.php";
    	}
?>

<div class="container-fluid" style=" height: 100vh; ">
	<h2 class="tytul">MODYFIKUJ DANE</h2>

<div class="row" style="height: 100%">	
	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>

	    <div class="col-sm-6" style="background-color:#4d4d4d;">

	    	<div class="row">	
	    		<div class="col-sm-4" style="margin-top: 5%;">


	    			<a href = "<?php echo $path?>" class="btn btn-outline-secondary" role="button" style="border-color: white; color: white"><p style="padding:10px;">WRÓĆ </p></a> 
	   			</div>
	    		<div class="col-sm-4" >
	    		</div>
	    		<div class="col-sm-4" >
	    		</div> 
			</div>

	    	<div class="login">
		    	<div style="color:white;">
					<?php
					if(isSet($_SESSION["udaneModyfikujUzytkownika"]) && !empty($_SESSION['udaneModyfikujUzytkownika']))
					{
						echo $_SESSION['udaneModyfikujUzytkownika'] . "<br><br><br>";
						
					}

					if(isSet($_SESSION["nieudaneModyfikujUzytkownika"]) && !empty($_SESSION['nieudaneModyfikujUzytkownika'])) 
						{
							echo $_SESSION['nieudaneModyfikujUzytkownika'] . "<br><br><br>";
								}
						unset($_SESSION['udaneModyfikujUzytkownika']);
						unset($_SESSION['nieudaneModyfikujUzytkownika']);

					?>
				</div>
		    
			   	<form action="obsluga.php" method="post">
					<input type="text" name="nowy_nick" placeholder="nowy nick" style=" width : 60%; height: 50px;"><br><!--value="<?php // echo $wart_nick; ?>"--> <br><br>
					<input type="text" name="nowe_haslo" placeholder="nowe haslo" style=" width : 60%; height: 50px;" ><br> <!--value="<?php // echo $wart_haslo; ?>"--><br><br>
					<input type="text" name="nowy_email" placeholder="nowy email" style=" width : 60%; height: 50px;">	<br><br><br>	    
					<input type="submit" name="zatwierdz" value="zatwierdz zmiany" style="width: 40%">
				</form> 
				<br><br>
				
			</div>
		</div>

	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>
	  
</div>
</div>
</body>
</html>