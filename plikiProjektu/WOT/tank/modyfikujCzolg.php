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
<body>
<?php
	if( empty($_SESSION['user'])) 
	{

		$_SESSION["komunikat"] = "Zaloguj się aby uzyskać dostęp do tej strony!";
		header("Location: index.php");		    	
	}
	else 	session_regenerate_id();

    $nazwa = "";
    if(!empty($_SESSION['czolg'])){
    	$nazwa = $_SESSION['czolg'];
    }
  	if(isSet($_POST["id"]))
  	{
  		$nazwa = $_POST["id"];
  	}  

	$zapytanie = "SELECT * FROM czolg where nazwa = '$nazwa';";
		$zap = mysqli_query($wot, $zapytanie);

		while($wiersz = mysqli_fetch_assoc($zap)) 
		{
			$pancerz = $wiersz["pancerz"];
			$zycie = $wiersz["zycie"];
			$silaOgnia	= $wiersz["sila_ognia"];
			$tier = $wiersz["tier"];
			$nacja = $wiersz["nacja"];
			$typ = $wiersz["typ"]; 
		}

?>
<div class="container-fluid" style=" height: 100vh; ">
	<h2 class="tytul"> MODYFIKUJ CZOŁG </h2>


<div class="row" style="height: 100%">	
	   
	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>

	    <div class="col-sm-6" style="background-color:#4d4d4d;">
	    	<div class="row">	
	    		<div class="col-sm-4" style="margin-top: 5%;">
	    			<a href = "czolgi_wypisz.php" class="btn btn-outline-secondary" role="button" style="border-color: white; color: white"><p style="padding:10px;">WRÓĆ </p></a> 
	   			</div>
	    		<div class="col-sm-4" >
	    		</div>
	    		<div class="col-sm-4" >
	    		</div> 
			</div>

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

		    <div class="login">
			   	<form action="obsluga_czolgi.php" method="post" enctype='multipart/form-data'>
					 <input type="text" name="nazwaCzolgu" placeholder="<?php echo $nazwa?>" style=" width : 40%; height: 40px;"><br><br>
					 <input type="number" name="pancerz" placeholder="<?php echo $pancerz ?>"  style=" width : 40%; height: 40px;"><br><br>
				     <input type="number" name="zycie" placeholder="<?php echo $zycie ?>"  style=" width : 40%; height: 40px;"><br><br>
				     <input type="number" name="silaOgnia" placeholder="<?php echo $silaOgnia ?>" style=" width : 40%; height: 40px;"><br><br>
				     <input type="text" name="nacja" placeholder="<?php echo $nacja ?>" style=" width : 40%; height: 40px;"><br><br>

				    <select id="tier" name="tier" style=" width : 40%; height: 40px;">
				    	<option value="" selected disabled hidden><?php echo $tier ?></option>
					  	<option value="IX">IX</option>
					  	<option value="X">X</option>
  					</select><br><br>
				    <select id="typ" name="typ" style=" width : 40%; height: 40px;">
				    	<option value="" selected disabled hidden><?php echo $typ ?></option>
					  	<option value="artillery">artillery</option>
					  	<option value="heavy">heavy</option>
					  	<option value="light">light</option>
					  	<option value="medium">medium</option>
					  	<option value="tanks destroyer">tanks destroyers</option>
  					</select><br><br> 
				    <input type="file" name="myfile"><br><br>
				    <input type="hidden" name="idCzolguModyfikuj" value="<?php echo $nazwa?>">
					<input type="submit" name="modyfikujCzolg" value="Dodaj" style="width: 20%;"><br><br>
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