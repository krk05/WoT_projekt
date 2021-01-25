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

<div class="container-fluid" style=" height: 100vh; ">
	<h2 class="tytul" > PEŁNA LISTA MAP </h2>
<div class="row" style="height: 100%">

	   <!-- <div class="col-sm-4" style="text-align: center; margin-top: 5%; position: fixed; left:20px; top: 25%; z-index: 20;">-->
	    <div class="col-sm-4"style="text-align: center; margin-top: 5%;">
	    	<div style="color:gray;">
		    <?php
				if(!empty($_SESSION['komunikatMapa']))
				{
					echo $_SESSION['komunikatMapa'];
					unset($_SESSION["komunikatMapa"]);
					session_regenerate_id();
				}
			?>
			</div>

				<form action="mapa.php" method="post" enctype='multipart/form-data'>
					<select name="sortuj" style=" width : 60%; height: 40px; ">
						<option value="" selected disabled hidden>kategoria sortowania</option>
					  	<option value="nazwa_mapy">nazwa</option>
					  	<option value="opis">opis</option>
					  	<option value="rozmiar">rozmiar</option>
		  		</select><br>
		  		<br><input type="submit" name="sortujMapy" value="Zatwierdz">
				</form>

				<?php
				$po = "nazwa_mapy";

				if(isSet($_POST["sortujMapy"]) && !empty($_POST["sortujMapy"]) && isSet($_POST["sortuj"]) && !empty($_POST["sortuj"]))
					$po = $_POST["sortuj"];

				$zapytanie = "SELECT * FROM mapa order by " . $po . ";";
				$zap = mysqli_query($wot, $zapytanie);
				?>	
				<br><br>
				<br><br>
				<a href = "dodajMape.php" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">DODAJ NOWĄ</a> 
				<a href = "../admin.php" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">WRÓĆ</a> 
				<br><br>
	</div>
	    

	     <div class="col-sm-8" style="max-width: 100%">
			<div style=" position: relative; margin: auto; ">
				<table class="table table-hover" style=" text-align: center;">
				    <thead class="thead-dark">
				      <tr>
				        <th>nazwa</th>
				        <th>opis</th>
				        <th>rozmiar</th>
				        <th>zdjęcie</th>
				        <th>akcja</th>
				      </tr>
				    </thead>
				    <tbody>
		  
					<?php

						while($wiersz = mysqli_fetch_assoc($zap)) 
						{
							$link = "../" . $wiersz["zdjecie"];
							?>
							<tr>
					        <td><?php echo $wiersz['nazwa_mapy']?> </td>
					        <td><?php echo $wiersz['opis'] ?></td>
					        <td><?php echo $wiersz['rozmiar'] ?></td>
					        <td><img src="<?php echo $link;?>" alt="obrazek" style="max-width: 250px;"></td>
					        <td><form action="modyfikujMape.php" method="POST">
					       			<input type="hidden" name="idMapyZmien" value="<?php echo $wiersz['nazwa_mapy'] ?>">
					       			<input type="submit" name="ZmienMape" value="zmień" style="width:60%;" >
					       	</form>
					       	<br>
					       	<form action="mapyObsluga.php" method="POST">
					       		<input type="hidden" name="idMapyUsuniecie" value="<?php echo $wiersz['nazwa_mapy'] ?>">
					       		<input type="submit" name="usunMape" value="usuń" style="width:60%;">
					       	</form>

					       	</td>
					      	</tr>
					    <?php
					   	}
					    ?>
					</tbody>
	 			</table>
 			</div>	
	</div>

	

</div>
</body>
</html>