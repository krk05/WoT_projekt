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

	<h2 class="tytul" > WYBIERZ STYLIZACJĘ</h2>

<?php
	if(empty($_SESSION["user"]))
	{
		$_SESSION["komunikat"] = "Zaloguj się aby uzyskać dostęp do tej strony!";
		header("Location: ../index.php");
	}

	//id czołgu do zmiany stylizacji, jak nie ma id to wtedy wracamy na gracz
	if(isSet($_POST["dodajStylizacjeDoCzolgu"]) && !empty($_POST["dodajStylizacjeDoCzolgu"]))
	{
		if(!empty($_POST["idCzolguStylizacja"]) && isSet($_POST["idCzolguStylizacja"]))
		{
			$idCzolgu=$_POST["idCzolguStylizacja"];
		}	
	}
	else
	{
		header("Location: ../gracz.php");
	}


?>

<div class="container-fluid" style=" height: 100vh; ">

<div class="row" style="height: 100%">

	 <div class="col-sm-5"style="text-align: center; margin-top: 5%;">	
		<br><br>
		<br><br>
		<a href = "../gracz.php" class="btn btn-outline-secondary" role="button" style=" width : 60%; height: 40px; margin-bottom: 20px;">WRÓĆ</a> 
		<br><br>
	</div>

	<div class="col-sm-7" style="max-width: 100%">


		    <div style="color:gray;">
		    <?php
				if(!empty($_SESSION["stylizacjaDodanie"]))
				{
					echo $_SESSION["stylizacjaDodanie"];
					unset($_SESSION["stylizacjaDodanie"]);
					session_regenerate_id();
				}

				$zapytanie = "SELECT * FROM stylizacja order by opis";
				$zap = mysqli_query($wot, $zapytanie);
			?>
			</div>
			<div style=" position: relative; margin: auto; ">
				<table class="table table-hover" style=" text-align: center;">
				    <thead class="thead-dark">
				      <tr>
				        <th>stylizacja</th>
				        <th>akcja</th>
				      </tr>
				    </thead>
				    <tbody>
		  
					<?php

						while($wiersz = mysqli_fetch_assoc($zap)) 
						{
							$ikona = "../" . $wiersz["zdjecie"];
							?>
							<tr>
					        <td><img src="<?php echo $ikona;?>" alt="obrazek" ></td>
					        <td>
									<form action="obsluga_czolgi.php" method="POST">
										<input type="hidden" name="idCzolguDoStylizacji" value="<?php echo $idCzolgu ?>">
										<input type="hidden" name="stylizacjaDoDodania" value="<?php echo $wiersz['id_stylizacji']?>">
										<input type="submit" name="wybranoStylizacjaDoCzolgu" value="dodaj"  style="width: 80px; height: 40px;">
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