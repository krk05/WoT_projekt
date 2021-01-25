<?php
	session_start();
	session_regenerate_id();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link rel="stylesheet" href="styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>


<div class="container-fluid" style=" height: 100vh">
  <h2 class="tytul">Zaloguj</h2>

  <div class="row" style="height: 100%">	
	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>

	    <div class="col-sm-6" style="background-color:#4d4d4d;">

		    <div class="login">
			   	<form action="obsluga.php" method="post">
					<input type="text" id="nick" name="nick"  placeholder="nick" required style=" width : 40%; height: 50px;"><br><!--value="<?php // echo $wart_nick; ?>"--> <br><br>
					<input type="password" id="haslo" name="haslo" placeholder="hasło" required style=" width : 40%; height: 50px;" ><br> <!--value="<?php // echo $wart_haslo; ?>"--><br><br>
					<button type="button" id="button" onclick="nick_required()">Przywróć hasło</button>
					<input type="hidden" id="edycja" name="edycja" value="nie" style="width: 20%">  &nbsp  &nbsp  &nbsp &nbsp &nbsp     
					<input type="submit" id="zaloguj" name="zaloguj" value="zaloguj" style="width: 20%">
				</form> 
				<br><br>
				<div style="color:white;">
				<?php
				if(!empty($_SESSION["logowanie"]))
					{
						echo $_SESSION["logowanie"] . "<br><br>"; 
						unset($_SESSION["logowanie"]);
					}
				if(!empty($_SESSION["komunikat"]))
					{
						echo $_SESSION["komunikat"] . "<br><br>"; 
						unset($_SESSION["komunikat"]);
					}
				?>
				</div>

				<a href="nowy_uzytkownik.php" class="btn btn-outline-secondary" role="button"><p style="padding:10px;">NOWY UŻYTKOWNIK</p></a>
			</div>
		</div>

	    <div class="col-sm-3">
	    	<div class="left_right"></div>
	    </div>
	  
</div>
</div>
</div>






<script>
	function nick_required()
	{
		var elem = document.getElementById('nick');
		elem.placeholder = "obecny nick";
		elem = document.getElementById('haslo');
		elem.placeholder = "nowe haslo";
		elem = document.getElementById('zaloguj');
		elem.value = 'zatwierdź';
		elem = document.getElementById('edycja');
		elem.value = 'tak';
	}
</script>

</body>
</html>