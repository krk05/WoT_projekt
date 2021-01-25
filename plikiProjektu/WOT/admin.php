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


<?php
	if($_SESSION['user'] != "admin")
	{
		$_SESSION["komunikat"] = "Dostęp do panelu administratora <br> mają tylko użytkownicy uprawnieni!";
		header("Location: index.php");
	}

	if( empty($_SESSION["user"])) 
	header("Location: index.php");
	else 	session_regenerate_id();
?>

<div class="container-fluid" style=" height: 100vh; ">
	<h2 class="tytul">PANEL ADMINISTRATORA</h2>

  	<div class="row" style="margin-top: 5%;">	
	    <div class="col-sm-4">
			<a href="wyloguj.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">WYLOGUJ</p></a><br><br>
	    </div>
	    <div class="col-sm-4" >
	    	<a href = "modyfikuj.php?user=admin" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">MODYFIKUJ </p></a> <br><br>
	    </div>
	    <div class="col-sm-4" >
	    	<a href = "gracz.php" class="btn btn-outline-secondary" role="button" ><p style="padding:10px;">PANEL GRACZA</p></a><br><br>
	    </div> 
	</div>

	<div class="row">	
		<div class="col-sm-4" >
	    	<a href = "klany/klan.php" class="btn btn-outline-secondary" role="button"  onclick="klan()"><p style="padding-top:10px;">KLANY</p> </a><br><br>
	    </div>
	    <div class="col-sm-4" >
	    	<a href = "mapy/mapa.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">MAPY</p></a><br><br>
	    </div>
	    <div class="col-sm-4" >
	    	<a href="tank/czolgi_wypisz.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">CZOŁGI</p></a>
	    </div>
	</div>
	
	<img src="panelAdminCzolg.png" class="float-right" alt="wizualizacja czołgu" style="width: 45%;" >

</div>	 

<script>
	function klan() {
	  <?php $_SESSION["wroc"] =  "admin" ?>
	}
</script>


</body>
</html>