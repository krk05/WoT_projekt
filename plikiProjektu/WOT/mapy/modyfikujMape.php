<?php
	session_start();

	require_once("../config.php");
	$wot = mysqli_connect($serwer,$user,$haslo,$baza_danych);
	if(!$wot)
		die("Nie polaczono");
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../styles.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<?php if( empty($_SESSION['user'])) 
	{

		$_SESSION["komunikat"] = "Zaloguj się aby uzyskać dostęp do tej strony!";
		header("Location: index.php");		    	
	}
	else 	session_regenerate_id();

	
	if(!empty($_SESSION["idMapyZmien"])){
   			$nazwa_mapy = $_SESSION["idMapyZmien"];
   	}

 	if(isSet($_POST["idMapyZmien"]) &&!empty($_POST["idMapyZmien"]))
 	  	{
    		 $nazwa_mapy = $_POST["idMapyZmien"];	
    	}
    	

	$zapytanie = "SELECT * FROM mapa where nazwa_mapy = '$nazwa_mapy';";
		$zap = mysqli_query($wot, $zapytanie);

		while($wiersz = mysqli_fetch_assoc($zap)) 
		{
			$opis = $wiersz["opis"];
			$rozmiar = $wiersz["rozmiar"]; 
		}
?>

<div class="container-fluid" style=" height: 100vh; ">
	<h2 class="tytul"> MODYFIKUJ MAPE </h2>


<div class="row" style="height: 100%">	
	   
	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>

	    <div class="col-sm-6" style="background-color:#4d4d4d;">

	    	<div class="row">	
	    		<div class="col-sm-4" style="margin-top: 5%;">
	    			<a href = "mapa.php" class="btn btn-outline-secondary" role="button" style="border-color: white; color: white"><p style="padding:10px;">WRÓĆ </p></a> 
	   			</div>
	    		<div class="col-sm-4" >
	    		</div>
	    		<div class="col-sm-4" >
	    		</div> 
			</div>


		    <div style="color:white;">
		    <?php
				if(!empty($_SESSION['zmianaMapy']))
				{
					echo $_SESSION['zmianaMapy'];
					unset($_SESSION["zmianaMapy"]);
					session_regenerate_id();
				}
			?>
			</div>

	<div class="login">
		<?php
		if(isSet($_SESSION["udane"]) && !empty($_SESSION['udane']))
		{
			echo $_SESSION['udane'] . "<br><br><br>";
			
		}

		if(isSet($_SESSION["nieudane"]) && !empty($_SESSION['nieudane'])) 
			{
				echo $_SESSION['nieudane'] . "<br><br><br>";
					}
			unset($_SESSION['udane']);
			unset($_SESSION['nieudane']);

			?>
			<form action="mapyObsluga.php" method="post" enctype='multipart/form-data' >
		    <br> <input type="text" name="nazwa_mapy" placeholder="<?php echo $nazwa_mapy ?> " style=" width : 40%; height: 40px;"><br>
		    <br><select name="opis" style=" width : 40%; height: 40px;">
				    	<?php if($opis == "mapa letnia"){
				    		?>
				    		<option value="mapa letnia">mapa letnia</option>
					  		<option value="mapa zimowa">mapa zimowa</option>
					  <?php } else {
					  	?>
					  	<option value="mapa zimowa">mapa zimowa</option>
					  	<option value="mapa letnia">mapa letnia</option>
				    	<?php } ?>
					  	
  				</select><br>

		    <br> <input type="text" name="rozmiar" placeholder="<?php echo $rozmiar ?>" style=" width : 40%; height: 40px;"><br>
		    <br> <input type="file" name="myfile"><br>
		    <input type="hidden" name="id" value="<?php echo $nazwa_mapy ?>">
			<br><input type="submit" name="zmienMape" value="Zatwierdź" style="width: 20%;">
		</form>
	</div>
	
		</div>

	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>




	</tbody>
 </table>
 </div>	
</div>

</body>
</html>