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
		$zapytanie = "SELECT * FROM gracz  join bitwa  on id_bitwy = id where id_bitwy in (select id_bitwy from gracz  WHERE nick like '$nick') AND druzyna in (SELECT druzyna FROM gracz WHERE nick like '$nick');";
		$zap = mysqli_query($wot, $zapytanie);

		$zapytanie = "SELECT * FROM gracz  join bitwa on id_bitwy = id  where id_bitwy in (select id_bitwy from gracz WHERE nick = '$nick') AND druzyna <> (SELECT druzyna FROM gracz WHERE nick  like '$nick');";
		$zap2 = mysqli_query($wot, $zapytanie);

		$zapytanie = "SELECT * FROM bitwa join gracz on id_bitwy = id join mapa using(nazwa_mapy)  join typ_mapy on typ_bitwy=rodzaj_bitwy WHERE nick = '$nick';";
 		$zap3 = mysqli_query($wot, $zapytanie);
 		$wiersz = mysqli_fetch_assoc($zap3);
		$rodzajBitwy = $wiersz["rodzaj_bitwy"];
?>

<div class="container-fluid" style=" height: 100vh; ">
<h2 class="tytul" > <?php echo $rodzajBitwy ?>  </h2>
<div class="row" style="height: 100%">
    <div class="col-sm-4 " style="max-width: 100%;">

		<div style=" position: relative; margin: auto; ">
			<h2 style="text-align: center;">DRUŻYNA 1</h2>
			<table class="table table-hover" style=" text-align: center;">
			    <thead class="thead-dark">
			      <tr>
			      	<th>pluton</th>
			        <th>nick</th>
			        <th>WN8</th>
			      </tr>
			    </thead>
			    <tbody>
	  
				<?php
				$i = 0;
				$numer = 1;
					while($wiersz = mysqli_fetch_assoc($zap) ) 
					{
						$mapa = $wiersz["nazwa_mapy"];
						$id_plutonu = $wiersz["id_plutonu"];
						if($i == 5) break;
						$i = $i + 1; 
						
						?>
						<tr>
							<td><?php if ($id_plutonu != "") 
							{
								if(isSet($_SESSION["$id_plutonu"]))
								{
									echo $_SESSION["$id_plutonu"];
								}
								else
								{
									$_SESSION["$id_plutonu"] = $numer;
									echo $numer;
									$numer++;
								}
							}
							 else echo "-";?> </td>
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
						?><br>
						
						 <a href = "obsluga.php?usunBitwe=tak" class="btn btn-outline-secondary" role="button"  style=" width : 60%; height: 40px; margin-bottom: 20px;">ZAKOŃCZ BITWĘ</a> 
						<br>
						<br>
						<img src="<?php echo $mapa;?>" alt="obrazek" style="max-width: 250px;">	<br>	
				    <?php
				  		echo "<br><b>" . $wiersz["nazwa_mapy"] . "</b> - " . $wiersz["opis"] . "<br><br>";
					   ?>
					   <div  style='text-align: justify'>
					   	<?php
				    	echo "<b>" . $wiersz["wskazowka"] . "</b><br><br>"; ?> </div> <?php
						echo $wiersz["czas_bitwy"] . " minut<br><br>";
						?><a href = "gracz.php" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">WRÓĆ</a> 
			<br><?php
				   	}
				   	?>
			
			
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
			<h2 style="text-align: center;">DRUŻYNA 2</h2>
			<table class="table table-hover" style=" text-align: center;">
			    <thead class="thead-dark">
			      <tr>
			      	<th>pluton</th>
			        <th>nick</th>
			        <th>WN8</th>
			      </tr>
			    </thead>
			    <tbody>
	  
				<?php
					$i = 0;
					$numer = 1;
					while($wiersz = mysqli_fetch_assoc($zap2)) 
					{
						$id_plutonu = $wiersz["id_plutonu"];
						if($i == 5) break;
						$i = $i + 1;
						?>
						<tr>
							<td><?php if ($id_plutonu != "") 
							{
								if(isSet($_SESSION["$id_plutonu"]))
								{
									echo $_SESSION["$id_plutonu"];
								}
								else
								{
									$_SESSION["$id_plutonu"] = $numer;
									echo $numer;
									$numer++;
								}
							}
							 else echo "-";?> </td>
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