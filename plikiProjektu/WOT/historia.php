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
	<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="styles.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
</head>
<body>

<?php
	if( empty($_SESSION["user"])) 
	{

		$_SESSION["komunikat"] = "Zaloguj się aby uzyskać dostęp do tej strony!";
		header("Location: index.php");		    	
	}
	else 	
	{
		$nick = $_SESSION["user"];
		session_regenerate_id();
	}


?>


	<div class="container-fluid" style=" height: 100vh; ">
		<h2 class="tytul"> HISTORIA PLUTONÓW </h2>
			

<div class="row" style="height: 100%">
	   <!-- <div class="col-sm-3" style="text-align: center; margin-top: 5%; position: fixed; left:20px; top: 25%; z-index: 20;">-->
	    <div class="col-sm-3" style="text-align: center; margin-top: 5%;">
	    	<?php

			$zapytanie = "SELECT * FROM pluton where nick1 = '$nick' or nick2 = '$nick' order by data_wygasniecia";
			$zap = mysqli_query($wot, $zapytanie);
			?>	
			<br><br>
			<a href = "pluton.php" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">WRÓĆ</a> 
			<br><br>
	</div>

	    <div class="col-sm-4 offset-sm-2" style="max-width: 100%">
			<div style=" position: relative; margin: auto; ">
				<table class="table table-hover" style=" text-align: center;">
				    <thead class="thead-dark">
				      	<tr>
				        	<th>nick sojusznika</th>
				        	<th>data wygaśnięcia</th>
				      	</tr>
				    </thead>
				    <tbody>						
						<?php
						while($wiersz = mysqli_fetch_assoc($zap)) 
						{ ?>
							<tr>
								<?php
								$nickSojusznika = $wiersz["nick2"];
								if($wiersz["nick2"] == $nick){
									$nickSojusznika = $wiersz["nick1"];
								}
							//	$data = "---";
							//	if(is_null($wiersz["data_wygasniecia"]){
							//		$data = $wiersz["data_wygasniecia"];
							//	}
								?>
								<td><?php echo $nickSojusznika ?> </td>
								<td><?php echo $wiersz["data_wygasniecia"] ?></td>
							</tr>
						<?php } 	
					?>
					</tbody>
	 			</table>
 			</div>	
		</div>
	</div>

<script>

	</script>
			


</body>
</html>