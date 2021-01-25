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
	<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="styles.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
</head>
<body>

	

<?php
	if( empty($_SESSION['user'])) 
	{

		$_SESSION["komunikat"] = "Zaloguj się aby uzyskać dostęp do tej strony!";
		header("Location: index.php");		    	
	}
	else 	
	{
		$nick = $_SESSION["user"];
		session_regenerate_id();
	}

	if(!empty($_SESSION['komunikat']))
	{
		echo $_SESSION['komunikat'];
		unset($_SESSION["komunikat"]);
		session_regenerate_id();
	}
	$mojKlan = "";
	if(!empty($_GET["klan"]))
	{
		$mojKlan = $_GET["klan"];
	}
?>


	<div class="container-fluid" style=" height: 100vh; ">
		<h2 class="tytul"> BITWA KLANOWA - wybierz klan przeciwny </h2>
	<div style="color:gray;">
				<?php
				$mojPluton = "";
				if(isSet($_SESSION["nowaBitwaKlanowa"]) && !empty($_SESSION['nowaBitwaKlanowa']))
				{
					echo $_SESSION["nowaBitwaKlanowa"];
				}
				unset($_SESSION['nowaBitwaKlanowa']);
				?>
			</div>
			

<div class="row" style="height: 100%">
	    <div class="col-sm-3" style="text-align: center; margin-top: 5%; position: fixed; left:20px; top: 25%; z-index: 20;">
	    	<?php
			$zapytanie = "SELECT * FROM klan where nazwa_klanu <> (SELECT nazwa_klanu from gracz WHERE nick = '$nick') order by nazwa_klanu";
			$zap = mysqli_query($wot, $zapytanie);
			?>	
			<br><br>
			<br><br>
			<a href = "gracz.php" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">WRÓĆ</a> 
			<br><br>
	</div>

	    <div class="col-sm-4 offset-sm-5" style="max-width: 100%">
			<div style=" position: relative; margin: 10%; ">
				<table class="table table-hover" style=" text-align: center;">
				    <thead class="thead-dark">
				      	<tr>
				        	<th>klan</th>
				        	<th>akcja</th>
				      	</tr>
				    </thead>
				    <tbody>
		  
					<?php
					while($wiersz = mysqli_fetch_assoc($zap)) 
					{?>
						<tr>
					    	<td><?php echo $wiersz["nazwa_klanu"]?> </td>
					    	<?php $klan = $wiersz["nazwa_klanu"];  ?>

					       <td>
					       		<form action="obsluga.php" method="post">
					       			<input type="hidden" id="klanPrzeciwny" name="klanPrzeciwny" value="<?php echo $klan ?>">
					       			<input type="hidden" id="mojKlan" name="mojKlan" value="<?php echo $mojKlan ?>">
									<input type="submit" name="losujKlanowa" value="+">
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