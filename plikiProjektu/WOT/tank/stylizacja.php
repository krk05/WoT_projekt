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
</head>
<body>

	<h2> NOWA STYLIZACJA </h2>

<?php
	if(!empty($_SESSION['komunikat']))
	{
		echo $_SESSION['komunikat'];
		unset($_SESSION["komunikat"]);
		session_regenerate_id();
	}
?>
	<form action="obsluga_czolgi.php" method="post" enctype='multipart/form-data'>
	    <br> <input type="text" name="opis" placeholder="opis" required><br>
	    <br> <input type="file" name="myfile"><br>
		<br><input type="submit" name="stylizacjaDodaj" value="Dodaj">
	</form>

	<?php

	$zapytanie = "SELECT * FROM stylizacja order by opis";
	$zap = mysqli_query($wot, $zapytanie);

	echo "<table style='margin: 0 auto; border-spacing:50px;'><tr><th>nazwa</th><th>zdjecie</th></tr>";
 			
	while($wiersz = mysqli_fetch_assoc($zap)) 
	{
		$zdjecie = "../" . $wiersz["zdjecie"];
		echo"<tr><td>{$wiersz['opis']}</td><td>"?><img src="<?php echo $zdjecie;?>" alt="obrazek" ><?php echo "</td></tr>";
	}	
	echo "</table>";

	?>


			


</body>
</html>