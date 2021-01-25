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

	
	$nazwa_klanu = "";
    if(!empty($_SESSION['klan'])){
    	$nazwa_klanu = $_SESSION['klan'];
    }
  	if(isSet($_POST["id"]))
  	{
  		$nazwa_klanu = $_POST["id"];
  	}  	

	$zapytanie = "SELECT * FROM klan where nazwa_klanu = '$nazwa_klanu';";
		$zap = mysqli_query($wot, $zapytanie);

		while($wiersz = mysqli_fetch_assoc($zap)) 
		{
			$maks_liczba_graczy = $wiersz["maks_liczba_graczy"];
			$ilosc_prowincji = $wiersz["ilosc_prowincji"]; 
		}
?>

<div class="container-fluid" style=" height: 100vh; ">
	<h2 class="tytul"> MODYFIKUJ KLAN </h2>

<div class="row" style="height: 100%">	
	   
	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>

	    <div class="col-sm-6" style="background-color:#4d4d4d;">

	    	<div class="row">	
	    		<div class="col-sm-4" style="margin-top: 5%;">
	    			<a href = "klan.php" class="btn btn-outline-secondary" role="button" style="border-color: white; color: white"><p style="padding:10px;">WRÓĆ </p></a> 
	   			</div>
	    		<div class="col-sm-4" >
	    		</div>
	    		<div class="col-sm-4" >
	    		</div> 
			</div>

	<div class="login">
		 <div style="color:white;">
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
		</div>

		<form action="klanyObsluga.php" method="post">
			<input type="hidden" value="<?php echo $nazwa_klanu; ?>" name = "id">
		    <br> <input type="text" name="nazwa_klanu" placeholder="<?php echo $nazwa_klanu ?>"><br>
		    <br> <input type="text" name="maks_liczba_graczy" placeholder="<?php echo $maks_liczba_graczy ?>" ><br>
		    <br> <input type="text" name="ilosc_prowincji" placeholder="<?php echo $ilosc_prowincji ?>"><br>
			<br><input type="submit" name="zmienKlan" value="Zatwierdź" style="width: 20%;">
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