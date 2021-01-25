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
	<style>

	</style>
</head>
<body>

<div class="container-fluid" style=" height: 100vh; ">
	<h2 class="tytul"> CZOŁGI DO DODANIA </h2>



<div class="row" style="height: 100%">
		
	    <!--<div class="col-sm-3" style="text-align: center; margin-top: 5%; position: fixed; left:20px; top: 25%; z-index: 20;">-->
	    	<div class="col-sm-3" style="text-align: center; margin-top: 5%;">
		    <?php
				if(!empty($_SESSION["bladDodaniaNowegoCzolgu"]))
				{
					echo $_SESSION["bladDodaniaNowegoCzolgu"];
					unset($_SESSION["bladDodaniaNowegoCzolgu"]);
					session_regenerate_id();
				}
			?>
				<form action="dodajCzolg.php" method="post">
					<select name="sortuj" style=" width : 60%; height: 40px; ">
						<option value="" selected disabled hidden>kategoria sortowania</option>
					  	<option value="nazwa">nazwa</option>
					  	<option value="pancerz">pancerz</option>
					  	<option value="zycie">zycie</option>
					  	<option value="sila_ognia">silaOgnia</option>
					  	<option value="tier">tier</option>
					  	<option value="nacja">nacja</option>
					  	<option value="typ">typ</option>
		  		</select><br>
		  		<br><input type="submit" name="sortujCzolgi" value="Zatwierdz">
				</form>
		
				<?php
				$po = "nazwa";
				$nick = $_SESSION["user"];
				if(isSet($_POST["sortujCzolgi"]) && !empty($_POST["sortujCzolgi"]) && isSet($_POST["sortuj"]) && !empty($_POST["sortuj"]))
					$po = $_POST["sortuj"];

				$zapytanie = "SELECT * FROM czolg join typczolgu using(typ) where nazwa not in (select nazwa from garaz where nick = '$nick') order by " . $po . ";";
				$zap = mysqli_query($wot, $zapytanie);
				?>	
				<br><br>
				<br><br>

				<a href = "gracz.php" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">WRÓĆ</a> 
				<br><br>
		</div>
	   

	     <div class="col-sm-9" style="max-width: 100%">


		    <div style="text-align: center;">
			</div>
			<div style=" position: relative; margin: auto; ">
				<table class="table table-hover" style=" text-align: center;">
				    <thead class="thead-dark">
				      <tr>
				        <th>nazwa</th>
				        <th>pancerz</th>
				        <th>życie</th>
				        <th>ogień</th>
				        <th>tier</th>
				        <th>nacja</th>
				        <th>ikona</th>
				        <th>model</th>
				        <th>dodaj</th>
				      </tr>
				    </thead>
				    <tbody>
		  
					<?php

						while($wiersz = mysqli_fetch_assoc($zap)) 
						{
							$ikona = $wiersz["ikona"];
							$link = $wiersz["model"];
							?>
							<tr>
						        <td><?php echo $wiersz['nazwa']?> </td>
						        <td><?php echo $wiersz['pancerz'] ?></td>
						        <td><?php echo $wiersz['zycie'] ?></td>
						        <td><?php echo $wiersz['sila_ognia'] ?></td>
						        <td><?php echo $wiersz['tier'] ?></td>
						        <td><?php echo $wiersz['nacja'] ?></td>
						        <td><img src="<?php echo $ikona;?>" alt="obrazek" ></td>
						        <td><img src="<?php echo $link;?>" alt="obrazek" style="max-width: 250px;"></td>
						        <?php $nazwa = $wiersz['nazwa'];?>
						        <td><form action="obsluga.php" method="post">
						        		<input type="hidden" name="nazwa" value="<?php echo $nazwa ?>">
										<input type="submit" name="nowyCzolg" value="+">
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
</div>
</body>
</html>