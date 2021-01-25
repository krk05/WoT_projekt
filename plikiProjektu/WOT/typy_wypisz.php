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
</head>
<body>

<?php

	$zapytanie = "SELECT * FROM typczolgu";
	$zap = mysqli_query($wot, $zapytanie);
				
	while($wiersz = mysqli_fetch_assoc($zap)) 
	{
		$typ= $wiersz["typ"];
		$ikona = $wiersz['ikona'];
		echo "Typ: $typ ---- ikona: ";
		?>

		<img src="<?php echo $ikona;?>" alt="obrazek" ><br>

		<?php 
	}

			
?>

</body>
</html>