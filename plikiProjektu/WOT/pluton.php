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
		<h2 class="tytul"> NOWY PLUTON </h2>
	<div style="color:gray;">

				<?php

				$mojPluton = "";
				if(isSet($_SESSION["bladPluton"]) && !empty($_SESSION["bladPluton"]))
				{
					echo $_SESSION["bladPluton"];
					
				}
				unset($_SESSION['bladPluton']);
				$komunikat = "+";
				if(isSet($_SESSION["plutonowy"]) && !empty($_SESSION["plutonowy"]))
				{
					$mojPluton	= $_SESSION["plutonowy"];
					$komunikat = "-";
					
				}
				?>
			</div>
			

<div class="row" style="height: 100%">
	   <!-- <div class="col-sm-3" style="text-align: center; margin-top: 5%; position: fixed; left:20px; top: 25%; z-index: 20;">-->
	    <div class="col-sm-3" style="text-align: center; margin-top: 5%;">
	    	<?php

			$zapytanie = "SELECT * FROM gracz where nick <> '$nick' and id_plutonu is null order by nick";
			$zap = mysqli_query($wot, $zapytanie);
			
			$zapytanie = "SELECT nick FROM gracz where id_plutonu = (SELECT id_plutonu FROM gracz where nick = '$nick') and nick <> '$nick';";	
			$zap2 = mysqli_query($wot, $zapytanie);		
			
			?>	
			<br><br>
			<br><br>
			<?php
			if(!empty($_SESSION["komunikat"]))
				{
					echo "<p style='text-align: ceter;'>" .  $_SESSION["komunikat"] . "</p>";
					unset($_SESSION["komunikat"]);
					session_regenerate_id();
			}
			?><br><br>
			<a href = "gracz.php" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">WRÓĆ</a> 
			<br><br>
			<a href = "historia.php" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">Historia plutonów</a> 
	</div>

	    <div class="col-sm-4 offset-sm-2" style="max-width: 100%">
			<div style=" position: relative; margin: auto; ">
				<table class="table table-hover" style=" text-align: center;">
				    <thead class="thead-dark">
				      	<tr>
				        	<th>nick</th>
				        	<th>akcja</th>
				      	</tr>
				    </thead>
				    <tbody>
		  				<?php
		  				if($komunikat == "-")
						{ ?>
						<tr>
							<?php $wiersz = mysqli_fetch_assoc($zap2);?>
					    	<td><?php echo $wiersz["nick"]; ?> </td>
					      	<td>
					    	   	<form action="obsluga.php" method="post">
									<input type="submit" name="usunPluton" value="<?php echo $komunikat	?> ">
								</form>
							</td>
						</tr>
						<?php } 

					else 
					{
					while($wiersz = mysqli_fetch_assoc($zap)) 
					{
						$plutonowy = $wiersz["nick"]; 
						?>
						
						
						<tr>
							<td><?php echo $plutonowy ?> </td>
							<td>
								<form action="obsluga.php" method="post">
									<input type="hidden" name="plutonowy" value="<?php echo $plutonowy ?>">
									<input type="submit" name="nowyPluton" value="<?php echo $komunikat	?>" >
								</form>
							</td>
						</tr>
						<?php } 
					}
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