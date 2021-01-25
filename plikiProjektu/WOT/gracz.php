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
	<h2 class="tytul">PANEL GRACZA <?php echo $nick ?></h2>
  	<div class="row" style="margin-top: 5%;">	
	    <div class="col-sm-4">
			<a href="wyloguj.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">WYLOGUJ</p></a><br><br>
	    </div>
	    <div class="col-sm-4" >
	    	<a href = "modyfikuj.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">MODYFIKUJ </p></a> <br><br>
	    </div>
	    <div class="col-sm-4" >
	    	<?php
	    	$zapytanie = "SELECT nazwa_klanu FROM gracz where nick = '$nick'";
			$zap = mysqli_query($wot, $zapytanie);
			while($wiersz = mysqli_fetch_assoc($zap)) 
			{
				$nazwaKlanu = "";
					if($wiersz["nazwa_klanu"] != "")
						$nazwaKlanu = $wiersz["nazwa_klanu"];
			}
			if($nazwaKlanu == "")
			{
			?>
				<a href = "klany/klan.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">KLANY</p></a>
			<?php
			}
			else
			{
			?>
				<a href = "klany/klan.php" class="btn btn-outline-secondary" role="button" onclick="klanIstnieje()"><p style="padding:10px;">KLAN: <?php echo $nazwaKlanu ?></p></a>
			<?php
			}
			?>
	    	<br><br>
	    </div> 
	</div>

	<div class="row">	
	    <div class="col-sm-4">
			<a href="dodajCzolg.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">DODAJ CZOŁG</p></a><br><br>
	    </div>
	    <div class="col-sm-4" >
	    	<?php
	    	$zapytanie = "SELECT id_bitwy FROM gracz where nick = '$nick'";
			$zap = mysqli_query($wot, $zapytanie);
			while($wiersz = mysqli_fetch_assoc($zap)) 
			{
				$idBitwy = "";
					if($wiersz["id_bitwy"] != "")
						$idBitwy = $wiersz["id_bitwy"];
			}
			if($idBitwy == "")
			{
				$zapytanie = "SELECT count(*) as liczba FROM garaz where nick = '$nick';";
				$zap = mysqli_query($wot, $zapytanie);
				$wiersz = mysqli_fetch_assoc($zap);
				if($wiersz["liczba"] > 0)
				{
					?>
				<a href="obsluga.php?losuj=losuj" class="btn btn-outline-secondary" role="button"><p style="padding:10px;"><b>BITWA</b></p></a><br><br>
				<?php
				}
				else {
					?>
					<a class="btn btn-outline-secondary" role="button" style=" cursor: not-allowed; text-decoration: none; opacity: not-allowed" title="musisz mieć co najmniej jeden czołg aby wygenerować bitwę" ><p style="padding:10px;"><b>BITWA</b></p></a><br><br>

					<?php
				}
			
			}
			else
			{
			?>
				<a href="bitwa.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">BITWA: dane</p></a><br><br>
			<?php
			}
			?>
	    	
			    <?php
			

			if(!empty($_SESSION["nowaBitwa"]))
			{
				echo "<p style='text-align: center'>" . $_SESSION["nowaBitwa"] . "</p>";
				unset($_SESSION["nowaBitwa"]);
				session_regenerate_id();
			}

		?>
	    	<!--<a href = "modyfikuj.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">MODYFIKUJ </p></a> <br><br>-->
	    </div>
	    <div class="col-sm-4" >
	    	<?php
	    	$zapytanie = "SELECT nick FROM gracz where id_plutonu = (SELECT	id_plutonu from gracz where nick = '$nick') AND nick <> '$nick'";
			$zap = mysqli_query($wot, $zapytanie);
			while($wiersz = mysqli_fetch_assoc($zap)) 
			{
				$drugiPlutonowy = "";
					if($wiersz["nick"] != "")
						$drugiPlutonowy = $wiersz["nick"];
			}
			if($drugiPlutonowy == "")
			{
			?>
				<a href = "pluton.php" class="btn btn-outline-secondary" role="button" ><p style="padding:10px;">PLUTON </p></a>
			<?php
			}
			else
			{
			?>
				<a href = "pluton.php" class="btn btn-outline-secondary" role="button" onclick="myFunction()"><p style="padding:10px;">PLUTON:  <?php echo $drugiPlutonowy ?></p></a>
			<?php
			}


			?>
	    </div> 
	</div>

	<script>
	function myFunction() {
	  <?php $_SESSION["plutonowy"] =  $drugiPlutonowy ?>
	}

	function klanIstnieje() {
	  <?php $_SESSION["klan"] =  $nazwaKlanu ;
	  		$_SESSION["wroc"] = "gracz";
	  ?>
	}
	</script>




	

		<div class="row">	
	    <div class="col-sm-4">
	    	<?php
	    	if ($nick == "admin"){?>
			<a href="admin.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">ADMIN</p></a><br><br>
		<?php } ?>
	    </div>
	    <div class="col-sm-4" >
	    	<?php if ($nazwaKlanu	 != "")
	{?>

		<?php
	    	$zapytanie = "SELECT id_bitwy_klanowej FROM gracz where nick = '$nick'";
			$zap = mysqli_query($wot, $zapytanie);
			while($wiersz = mysqli_fetch_assoc($zap)) 
			{
				$idBitwy = "";
					if($wiersz["id_bitwy_klanowej"] != "")
						$idBitwy = $wiersz["id_bitwy_klanowej"];
			}
			if($idBitwy == "")
			{
				$zapytanie = "SELECT count(*) as liczba FROM garaz where nick = '$nick';";
				$zap = mysqli_query($wot, $zapytanie);
				$wiersz = mysqli_fetch_assoc($zap);
				if($wiersz["liczba"] > 0)
				{
					?>
				<a href="wybierzKlan.php?klan=<?php echo $nazwaKlanu ?>" class="btn btn-outline-secondary" role="button"><p style="padding:10px;"><b>KLANOWA</b></p></a><br><br>
				<?php
				}
				else {
					?>
					<a class="btn btn-outline-secondary" role="button" style="cursor: not-allowed; text-decoration: none; opacity: not-allowed" title="musisz mieć co najmniej jeden czołg aby wygenerować bitwę" ><p style="padding:10px;"><b>KLANOWA</b></p></a><br><br>

					<?php
				}
			?>				
			<?php
			}
			else
			{
			?>
				<a href="bitwaKlanowa.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">KLANOWA: dane</p></a><br><br>
			<?php
			}
			?>
	    	
			    <?php
			

			if(!empty($_SESSION["nowaBitwaKlanowa"]))
			{
				echo "<p style='text-align: center'>" . $_SESSION["nowaBitwaKlanowa"] . "</p>";
				unset($_SESSION["nowaBitwaKlanowa"]);
				session_regenerate_id();
			}
	}
			
		?>
	    	<!--<a href = "modyfikuj.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">MODYFIKUJ </p></a> <br><br>-->
	    </div>
	    <div class="col-sm-4" >
	    	<!--<a href="dodajCzolg.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">NOWY CZOŁG</p></a><br><br>-->
	    </div> 
	</div>


	

<div class="row">	
	<div class ="col-sm-3" style="text-align: center;margin-top:5%;"><br><br>
		<?php
		$nick = $_SESSION["user"];
		$zapytanie = "SELECT * FROM gracz where nick = '$nick'; ";
		$zap = mysqli_query($wot, $zapytanie);
		$wiersz=mysqli_fetch_assoc($zap);
		?>
		<table class="table table-hover" style=" text-align: center;">
			<thead class="thead-dark">
			<tr>
			    <th>STATYSTYKI</th>
			</tr>
			</thead>
			<tbody>
		  		<tr><th>wygranych bitew: <?php echo $wiersz["wygranych"]; ?></th></tr>
		  		<tr><th>przegranych bitew: <?php echo $wiersz["przegranych"]; ?></th></tr>
		  		<tr><th>średnie uszkodzenia: <?php echo $wiersz["srednie_uszkodzenia"]; ?></th></tr>
		  		<tr><th>WN8: <?php echo $wiersz["WN8"]; ?></th>
		  	</tbody>
		 </table>



	</div>
	<div class="col-sm-9" style="max-width: 100%; margin-top:5%;">
		
		<?php

			$nick = $_SESSION["user"];
			$zapytanie = "SELECT * FROM garaz join czolg using(nazwa) left join stylizacja using(id_stylizacji) join typczolgu using (typ) where nick = '$nick'; ";
			$zap = mysqli_query($wot, $zapytanie);
			?>	
				


			

				<?php
				if(!empty($_SESSION["bladGaraz"]) && !empty($_SESSION["bladGaraz"]))
				{

					echo "<br>" . $_SESSION["bladGaraz"];
					unset($_SESSION["bladGaraz"]);										

				}?>
			<h2 style="border: inherit; background-color: lightgrey; text-align: center;"> MOJE CZOŁGI </h2>
			<div style=" position: relative; margin: auto; ">
				<table class="table table-hover" style=" text-align: center; ">
						
				    <thead class="thead-dark">

				      <tr>
				      	<th>akcja</th>
				        <th>nazwa</th>
				        <th>typ</th>
				        <th>pancerz</th>
				        <th>życie</th>
				        <th>ogień</th>
				        <th>tier</th>
				        <th>nacja</th>
				        <th>stylizacja</th>
				        <th></th>
				        <th>model</th> 
				      </tr>
				    </thead>
				    <tbody>
		  
					<?php

						while($wiersz = mysqli_fetch_assoc($zap)) 
						{
							$ikona =  $wiersz["ikona"];
							$link =  $wiersz["model"];
							$idCzolgu = $wiersz["czolg_id"];
							?>
							<tr>
								 <td>
								 	<form action="obsluga.php" method="POST">
						        		<input type="hidden" name="idCzolguUsun" value="<?php echo $idCzolgu ?>">
										<input type="submit" name="usunGaraz" value="usuń" >
									</form>
									
								</td>
						        <td><?php echo $wiersz['nazwa']?> </td>
						        <td><img src="<?php echo $ikona;?>" alt="obrazek" ></td>
						        <td><?php echo $wiersz['pancerz'] ?></td>
						        <td><?php echo $wiersz['zycie'] ?></td>
						        <td><?php echo $wiersz['sila_ognia'] ?></td>
						        <td><?php echo $wiersz['tier'] ?></td>
						        <td><?php echo $wiersz['nacja'] ?></td>						       
						        
						        <?php 
						        $nazwa = $wiersz["nazwa"];
								if($wiersz["id_stylizacji"] != 0)
								{

									?>
									<td>
										<img src="<?php echo $wiersz['zdjecie'];?>" alt="obrazek" style="max-width: 150px;" >
										</td>
										<td>
										<form action="tank/obsluga_czolgi.php" method="POST">
						        			<input type="hidden" name="idCzolguStylizacjaUsun" value="<?php echo $idCzolgu ?>">
											<input type="submit" name="usunGarazStylizacja" value="-" style="background-color: #ff8080;">
										</form>

										
									</td>

								<?php
								}
								else
								{ 	
									?>
									<td> --- 
										<?php
										if(!empty($_SESSION["usunStylizajce"]) && !empty($_SESSION["idCzolguUsunieto"]))
										{
											if($idCzolgu == $_SESSION["idCzolguUsunieto"])
											{
												echo "<br>" . $_SESSION["usunStylizajce"];
												unset($_SESSION["usunStylizajce"]);										
											}

										}?>
									</td> <td>				
									<form action="tank/dodajStylizacje.php" method="POST">
										<input type="hidden" name="idCzolguStylizacja" value="<?php echo $idCzolgu ?>">
										<input type="submit" name="dodajStylizacjeDoCzolgu" value="+" style="background-color: #39ac73;">
									</form>
								<?php }
								?></td>

						        <td><img src="<?php echo $link;?>" alt="obrazek" style="max-width: 250px;"></td>
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