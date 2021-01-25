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

<div class="container-fluid" style=" height: 100vh; ">
	<h2 class="tytul"> NOWY KLAN </h2>


<div class="row" style="height: 100%">	
	   
	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>

	    <div class="col-sm-6" style="background-color:#4d4d4d;">

	    	<div class="row">	
	    		<div class="col-sm-4" style="margin-top: 5%;">
	    			<a href = "klan.php?user=admin" class="btn btn-outline-secondary" role="button" style="border-color: white; color: white"><p style="padding:10px;">WRÓĆ </p></a> 
	   			</div>
	    		<div class="col-sm-4" >
	    		</div>
	    		<div class="col-sm-4" >
	    		</div> 
			</div>


		    <div style="color:white;">
		    <?php
			if(!empty($_SESSION['komunikat']))
			{
				echo $_SESSION['komunikat'];
				unset($_SESSION["komunikat"]);
				session_regenerate_id();
			}
			?>
			</div>

	<div class="login">
		<form action="klanyObsluga.php" method="post">
		    <br> <input type="text" name="nazwaKlanu"  placeholder="nazwa klanu" required style=" width : 40%; height: 50px;"><br>
		    <br> <input type="number" name="maksGraczy" placeholder="maksymalna liczba graczy" required style=" width : 40%; height: 50px;"><br>
		    <br> <input type="number" name="prowincje"  placeholder="liczba prowincji" required style=" width : 40%; height: 50px;"><br>
			<br><input type="submit" name="nowyKlan" value="Dodaj" style="width: 20%;">
		</form>
	</div>
	
		</div>

	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>




	</tbody>
 </table>
 </div>	
</div>

</body>
</html>