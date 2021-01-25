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

<?php if( empty($_SESSION['user'])) 
	{

		$_SESSION["komunikat"] = "Zaloguj się aby uzyskać dostęp do tej strony!";
		header("Location: index.php");		    	
	}
	else 	session_regenerate_id();
	$path = "../gracz.php";
	$sciezka = "modyfikujKlan.php";
	$koncowka = "";
	$komunikat = "";
	$nick = $_SESSION["user"];
	$nazwaKlanu="";
	if($_SESSION["wroc"] == "admin")
    	{
    		$sciezka2 = "modyfikujKlan.php";
    		$komunikat2 = "zmień";
    		$path = "../admin.php";
    	}

    	$zapytanie = "SELECT count(*) as liczba, nazwa_klanu FROM gracz where nick = '$nick' and nazwa_klanu is not null";
		$zap = mysqli_query($wot, $zapytanie);
		$wiersz = mysqli_fetch_assoc($zap);
		if($wiersz["liczba"] > 0)
		{
			$komunikat = "odejdź";
			$sciezka = "../obsluga.php";
			$nazwaKlanu = $wiersz["nazwa_klanu"];

		//	$koncowka = "?klan=" . $nazwaKlanu;
		}
		else
		{
			$komunikat = "dołącz";
			$sciezka = "../obsluga.php";
    	//	$koncowka = "?user=" . $_SESSION["user"];
		}
?>

	

<div class="container-fluid" style=" height: 100vh; ">
<h2 class="tytul" > PEŁNA LISTA KLANÓW </h2>
	<div style="color:gray;">
		<?php
		if(isSet($_SESSION["bladKlan"]) && !empty($_SESSION['bladKlan']))
		{
			echo $_SESSION["bladKlan"];
			
		}
		unset($_SESSION['bladKlan']);
		?>
	</div>

<div class="row" style="height: 100%">
	    <!--<div class="col-sm-3" style="text-align: center; margin-top: 5%; position: fixed; left:20px; top: 25%; z-index: 20;">-->
	    <div class="col-sm-3"style="text-align: center; margin-top: 5%;">
	    	<form action="klan.php<?php echo $koncowka ?>" method="post" enctype='multipart/form-data'>
				<select name="sortuj" style=" width : 60%; height: 40px; ">
					<option value="" selected disabled hidden>kategoria sortowania</option>
				  	<option value="nazwa_klanu">nazwa</option>
				  	<option value="maks_liczba_graczy">max liczba graczy</option>
				  	<option value="ilosc_prowincji">ilość prowincji</option>
		  		</select><br>
		  		<br><input type="submit" name="sortujKlany" value="Zatwierdz">
			</form>
		
			<?php
			$po = "nazwa_klanu";
			if(isSet($_POST["sortujKlany"]) && !empty($_POST["sortujKlany"]) && isSet($_POST["sortuj"]) && !empty($_POST["sortuj"]))
				$po = $_POST["sortuj"];

			$zapytanie = "SELECT * FROM klan order by " . $po . ";";
			$zap = mysqli_query($wot, $zapytanie);
			?>	
			<br><br>
			<br><br>
			<a href = "dodajKlan.php" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">DODAJ NOWY</a> 
			<a href = "<?php echo $path ?>" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">WRÓĆ</a> 
			<br><br>
	</div>

	    <div class="col-sm-9" style="max-width: 100%">
			<div style=" position: relative; margin: auto; ">
				<table class="table table-hover" style=" text-align: center;">
				    <thead class="thead-dark">
				      	<tr>
				        	<th>nazwa</th>
				        	<th>max liczba graczy</th>
				        	<th>ilość prowincji</th>
				        	<th>akcja</th>
				        	<?php 
				        	 if($_SESSION["user"] == "admin")
								{?>
									<th>admin</th>
				        		<?php } ?>
				      	</tr>
				    </thead>
				    <tbody>
		  
					<?php
					while($wiersz = mysqli_fetch_assoc($zap)) 
					{
						$link = "../" . $wiersz["zdjecie"];
						?>
						<tr>
					       <td><?php echo $wiersz['nazwa_klanu']?> </td>
					       <td><?php echo $wiersz['maks_liczba_graczy'] ?></td>
					       <td><?php echo $wiersz['ilosc_prowincji'] ?></td>
					       <td>
					       	<?php if($komunikat == "odejdź")
					       	{
					       		if($wiersz["nazwa_klanu"] == $nazwaKlanu){ ?>
					       		<form action="<?php echo $sciezka ?>" method="POST">
					       			<input type="submit" name="opuscKlan" value="odejdź">
					       		</form>
					       	<?php
					       }} else { ?>
					       		<form action="<?php echo $sciezka ?>" method="POST">
					       			<input type="hidden" name="nazwaKlanu" value="<?php echo $wiersz['nazwa_klanu'] ?>">
					       			<input type="submit" name="dolaczKlan" value="dołącz">
					       		</form>
					       	<?php } ?>
					       	</td>
					       	<?php if ($nick == "admin"){
					       		?>
					       		<td>
								<form action="klanyObsluga.php" method="POST">
									<input type="hidden" name="idKlanyUsuniecie" value="<?php echo $wiersz['nazwa_klanu']?>">
									<input type="submit" name="usunięcieKlanu" value="usuń" style="width:40%;">
								</form>

								<br>
								
								<form action="modyfikujKlan.php" method="POST">
									<input type="hidden" name="id" value="<?php echo $wiersz['nazwa_klanu']?>">
									<input type="submit" name="modyfikacjaKlanu" value="zmień" style="width:40%;" >
								</form>
							</td>
					       		<?php
					       	} 
							?>	
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