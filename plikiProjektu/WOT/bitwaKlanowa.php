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
		$nick = "";
	    $mapa = ""; 
		$nick = $_SESSION["user"];
		$zapytanie = "SELECT * FROM gracz  join bitwaKlanowa on id_bitwy_klanowej = id where id_bitwy_klanowej in (select id_bitwy_klanowej from gracz  WHERE nick like '$nick') AND nazwa_klanu in (SELECT nazwa_klanu FROM gracz WHERE nick like '$nick');";
		$zap = mysqli_query($wot, $zapytanie);

		$zapytanie = "SELECT * FROM gracz  join bitwaKlanowa on id_bitwy_klanowej = id  where id_bitwy_klanowej in (select id_bitwy_klanowej from gracz WHERE nick = '$nick') AND nazwa_klanu <> (SELECT nazwa_klanu FROM gracz WHERE nick  like '$nick');";
		$zap2 = mysqli_query($wot, $zapytanie);
?>

<div class="container-fluid" style=" height: 100vh; ">
	<h2 class="tytul" > BITWA KLANOWA</h2>
<div class="row" style="height: 100%">
    <div class="col-sm-4 " style="max-width: 100%;">

		<div style=" position: relative; margin: auto; ">
			<?php $wiersz=mysqli_fetch_assoc($zap); ?>
			<h2 style="text-align: center;"><?php echo $wiersz["nazwa_klanu"];?></h2>
			<table class="table table-hover" style=" text-align: center;">
			    <thead class="thead-dark">
			      <tr>
			        <th>nick</th>
			        <th>WN8</th>
			      </tr>
			    </thead>
			    <tbody>
	  				<tr>
				        	<td><?php echo $wiersz["nick"];?> </td>
				        	<td><?php echo $wiersz["WN8"];?> </td>
				 	</tr>

				<?php
				$i = 0;
					while($wiersz = mysqli_fetch_assoc($zap) ) 
					{
						$mapa = $wiersz["nazwaMapy"];
						if($i == 5) break;
						$i = $i + 1; 
						
						?>
						<tr>
				        	<td><?php echo $wiersz["nick"];?> </td>
				        	<td><?php echo $wiersz["WN8"];?> </td>
				 		</tr>
				      	
				    <?php
				   	}
				 ?>

				</tbody>
 			</table>
			
	</div>
</div>

 <div class="col-sm-4 " style="max-width: 100%; text-align: center;">
 	<?php
 		$zapytanie = "SELECT * FROM mapa join typ_mapy on typ_bitwy=rodzaj_bitwy WHERE nazwa_mapy = '$mapa';";
 		$zap3 = mysqli_query($wot, $zapytanie);
 		while($wiersz = mysqli_fetch_assoc($zap3) ) 
					{
						$mapa = $wiersz["zdjecie"];
						 echo "<h3><b>" . $wiersz["rodzaj_bitwy"] . "</b><br></h3>";
						?>
						<br><br>
						<img src="<?php echo $mapa;?>" alt="obrazek" style="max-width: 250px;">	<br>	
				    <?php
				  		echo "<br><b>" . $wiersz["nazwa_mapy"] . "</b> - " . $wiersz["opis"] . "<br><br>";
					   ?>
					   <div  style='text-align: justify'>
					   	<?php
				    	echo "<b>" . $wiersz["wskazowka"] . "</b><br><br>"; ?> </div> <?php
						echo $wiersz["czas_bitwy"] . " minut<br><br>";

				   	}
				   	?>
			<a href = "gracz.php" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">WRÓĆ</a> 
			<br><br>
			<a href = "obsluga.php?usunBitweKlanowa=tak" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">KONIEC BITWY</a> 
			<br>
			<?php
			if(!empty($_SESSION["bitwa"]))
			{
				echo $_SESSION["bitwa"];
				unset($_SESSION["bitwa"]);
				session_regenerate_id();
			}
			?>
				 

 	
 		<!--obrazek mapy-->
 </div>

 <div class="col-sm-4 " style="max-width: 100%;">

		<div style=" position: center; margin: auto; ">
			<?php $wiersz = mysqli_fetch_assoc($zap2)?>
			<h2 style="text-align: center;"><?php echo $wiersz["nazwa_klanu"]?></h2>
			<table class="table table-hover" style=" text-align: center;">
			    <thead class="thead-dark">
			      <tr>
			        <th>nick</th>
			        <th>WN8</th>
			      </tr>
			    </thead>
			    <tbody>
	  				<tr>
				        <td><?php echo $wiersz["nick"]?> </td>
				        <td><?php echo $wiersz["WN8"]?></td>
				      </tr>
				<?php
					$i = 0;
					while($wiersz = mysqli_fetch_assoc($zap2)) 
					{
						if($i == 5) break;
						$i = $i + 1;
						?>
						<tr>
				        <td><?php echo $wiersz["nick"]?> </td>
				        <td><?php echo $wiersz["WN8"]?></td>
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