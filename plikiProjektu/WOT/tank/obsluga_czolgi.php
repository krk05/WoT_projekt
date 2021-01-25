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
</head>
<body>

<?php
 	if( empty($_SESSION["user"])) 
		header("Location: ../index.php");
	else 	session_regenerate_id();


if(isSet($_POST["czolgiDodaj"]))
		if($_SESSION['user'] == "admin")
	{

		if(isSet($_POST["nazwaCzolgu"])  && !empty($_POST["nazwaCzolgu"])
			&& isSet($_POST["pancerz"])  && !empty($_POST["pancerz"])
			&& isSet($_POST["zycie"])  && !empty($_POST["zycie"])
			&& isSet($_POST["silaOgnia"])  && !empty($_POST["silaOgnia"])
			&& isSet($_POST["tier"])  && !empty($_POST["tier"])
			&& isSet($_POST["typ"])  && !empty($_POST["typ"])
			&& isSet($_POST["nacja"])  && !empty($_POST["nacja"]))
		{	
			$nazwa = $_POST["nazwaCzolgu"];
			$pancerz = $_POST["pancerz"];
			$zycie = $_POST["zycie"];
			$silaOgnia = $_POST["silaOgnia"];
			$tier = $_POST["tier"];
			$typ = $_POST["typ"];
			$nacja = $_POST["nacja"];
			

			if($pancerz <= 0 || $zycie <= 0 || $silaOgnia <= 0)
			{
				$_SESSION["komunikat"] = "ujemne wartosci";
				header("Location: dodaj_czolg.php");
			}

			$flaga = 0;

			$zapytanie = "SELECT count(*) as liczba FROM czolg where nazwa = '$nazwa';";
			$zap = mysqli_query($wot, $zapytanie);

			$wiersz = mysqli_fetch_assoc($zap);
			
			if($wiersz["liczba"] > 0)
			{
				$_SESSION["komunikat"] = "nieunikalna nazwa";
				header("Location: dodaj_czolg.php");
			}
			else
			{
			
				$flaga = 1;
				$currentDir = getcwd();
				$uploadDirectory = "/../obrazy/czolgi/" . $tier . "/" . $typ ."/";
				$fileName = $_FILES['myfile']['name'];
				$fileSize = $_FILES['myfile']['size'];
				$fileTmpName = $_FILES['myfile']['tmp_name'];
				$fileType = $_FILES['myfile']['type'];
				
				if($fileName != "")
				{
					$uploadPath = $currentDir . $uploadDirectory . $fileName;
					if(move_uploaded_file($fileTmpName, $uploadPath))
					{
						$_SESSION["komunikat"] = "udalo sie";
					}
					else
					{
						$flaga = 0;
						$_SESSION["komunikat"] = "nie udalo sie" . $uploadPath;
						header("Location: dodaj_czolg.php");
					}
				}
				else 
				{
					$flaga = 0;
					$_SESSION["komunikat"] = "zly typ lub pusta nazwa";
					header("Location: dodaj_czolg.php");
				}

				if($flaga == 1)
				{
					$path = "obrazy/czolgi/$tier/$typ/$fileName";
					$zapytanie = "INSERT INTO czolg(nazwa, model, pancerz, zycie, sila_ognia, tier, nacja, typ) VALUES ('$nazwa', '$path', '$pancerz', '$zycie', '$silaOgnia', '$tier', '$nacja', '$typ')";
					$wynik_zapytania = mysqli_query($wot, $zapytanie);

					if($wynik_zapytania)
					{
						$_SESSION["komunikat"] = "udalo się dodac nowy czołg";
						header("Location: dodaj_czolg.php");
					}
					else
					{
						$_SESSION["komunikat"] = "jakis blad - nwm jaki";
						header("Location: dodaj_czolg.php");
					}
				}
			}
 		}
 		else
 		{
 			$_SESSION["komunikat"] = "niepelne dane";
			header("Location: dodaj_czolg.php");
 		}
 	}
 	else  
	{
		$_SESSION["komunikat"] = "niezalogowany";
		header("Location: ../index.php");
	}


if(isSet($_POST["stylizacjaDodaj"]))
		if($_SESSION['user'] == "admin")
	{

		if(isSet($_POST["opis"])  && !empty($_POST["opis"]))
		{	
			$opis = $_POST["opis"];
			$flaga = 0;

			$zapytanie = "SELECT count(*) as liczba FROM stylizacja where opis = '$opis';";
			$zap = mysqli_query($wot, $zapytanie);

			while($wiersz = mysqli_fetch_assoc($zap)) 
			{
				if($wiersz["ile"] > 0)
				{
					$_SESSION["komunikat"] = "nieunikalny opis";
					header("Location: stylizacja.php");
				}
			}

			$flaga = 1;
			$currentDir = getcwd();
			$uploadDirectory = "/../obrazy/stylizacja/";
			$fileName = $_FILES['myfile']['name'];
			$fileSize = $_FILES['myfile']['size'];
			$fileTmpName = $_FILES['myfile']['tmp_name'];
			$fileType = $_FILES['myfile']['type'];

			if($fileName != "" )
			{
				$uploadPath = $currentDir. $uploadDirectory . $fileName;
				if(move_uploaded_file($fileTmpName, $uploadPath))
				{
					$_SESSION["komunikat"] = "udalo sie";
				}
				else
				{
					$flaga = 0;
					$_SESSION["komunikat"] = "nie udalo sie";
					header("Location: stylizacja.php");
				}
			}
			else 
			{
				$flaga = 0;
				$_SESSION["komunikat"] = "zly typ lub pusta nazwa";
				header("Location: stylizacja.php");
			}

			if($flaga == 1)
			{
				$path = "obrazy/stylizacja/$fileName";
				$zapytanie = "INSERT INTO stylizacja(opis,zdjecie) VALUES ('$opis', '$path')";

				$wynik_zapytania = mysqli_query($wot, $zapytanie);

				if($wynik_zapytania)
				{
					$_SESSION["komunikat"] = "udalo się dodac nową stylizację";
					header("Location: stylizacja.php");
				}
				else
				{
					$_SESSION["komunikat"] = "jakis blad - nwm jaki";
					header("Location: stylizacja.php");
				}
			}
 		}
 	}
 	else  
	{
		$_SESSION["komunikat"] = "niezalogowany";
		header("Location: stylizacja.php");
	}

if(isSet($_POST["czolgUsun"]) && !empty($_POST["czolgUsun"]))
{
	$idCzolgu = $_POST["id"];
	$zapytanie = "DELETE FROM czolg WHERE nazwa = '$idCzolgu';";
	$zap = mysqli_query($wot, $zapytanie);
	if($zap)
	{
		$_SESSION["komunikat"] = "udało się usunąć czołg " . $idCzolgu ;
		header("Location: czolgi_wypisz.php");
	}
	else
	{
		$_SESSION["komunikat"] = "nie udało się usunąć czołgu " . $idCzolgu ;
		header("Location: czolgi_wypisz.php");		
	}
}

if(isSet($_POST["modyfikujCzolg"]) && isSet($_POST["idCzolguModyfikuj"]) )
	if($_SESSION["user"] == "admin")
	{
		$odpowiedz_dobra = "<b>zmieniono pomyślnie:</b><br> ";
		$odpowiedz_zla = "<b>nie udało się zmienić:</b><br> ";
		$nick = $_SESSION["user"];
		$stare_id = $_POST["idCzolguModyfikuj"];

		if(isSet($_POST["nazwaCzolgu"])  && !empty($_POST["nazwaCzolgu"]))
		{	
			$wart_nazwa = $_POST["nazwaCzolgu"];
			$zapytanie = "SELECT count(*) as liczba FROM czolg where nazwa = '$wart_nazwa'";
			$zap = mysqli_query($wot, $zapytanie);
			$flaga = 1;		
			while($wiersz = mysqli_fetch_assoc($zap)) 
			{
				if($wiersz["liczba"] > 0)
				{
					$odpowiedz_zla .=  " nazwa - $wart_nazwa  jest już zajęta. Wybierz inną <br>";
					$flaga = 0;
				}
			}
			if($flaga)
			{
				$zapytanie = "UPDATE czolg SET nazwa = '$wart_nazwa' WHERE nazwa = '$stare_id';";
				$zap = mysqli_query($wot, $zapytanie);
				if($zap)
				{
					$odpowiedz_dobra .= "nazwa<br> ";
					$stare_id = $wart_nazwa;
				}
				else 
					$odpowiedz_zla .= "nazwa<br> ";
			}
		}

		if(isSet($_POST["pancerz"])  && !empty($_POST["pancerz"]))
		{	
			$pancerz = $_POST["pancerz"];
			$zapytanie = "UPDATE czolg SET pancerz = '$pancerz' WHERE nazwa = '$stare_id';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				$odpowiedz_dobra .= "pancerz<br> ";
			else 
				$odpowiedz_zla .= "pancerz<br> ";
		}

		if(isSet($_POST["zycie"]) && !empty($_POST["zycie"]))
		{	
			$zycie = $_POST["zycie"];
			$zapytanie = "UPDATE czolg SET zycie = '$zycie' WHERE nazwa = '$stare_id';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				$odpowiedz_dobra .= "życie<br> ";
			else 
				$odpowiedz_zla .= "życie<br> ";
		}

		if(isSet($_POST["sila_ognia"]) && !empty($_POST["sila_ognia"]))
		{	
			$silaOgnia	= $_POST["sila_ognia"];
			$zapytanie = "UPDATE czolg SET sila_ognia = '$silaOgnia' WHERE nazwa = '$stare_id';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				$odpowiedz_dobra .= "siła ognia<br> ";
			else 
				$odpowiedz_zla .= "siła ognia<br> ";
		}

		if(isSet($_POST["tier"]) && !empty($_POST["tier"]))
		{	
			$tier = $_POST["tier"];
			$zapytanie = "UPDATE czolg SET tier = '$tier' WHERE nazwa = '$stare_id';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				$odpowiedz_dobra .= "tier<br> ";
			else 
				$odpowiedz_zla .= "tier<br> ";
		}

		if(isSet($_POST["nacja"]) && !empty($_POST["nacja"]))
		{	
			$nacja = $_POST["nacja"];
			$zapytanie = "UPDATE czolg SET nacja = '$nacja' WHERE nazwa = '$stare_id';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				$odpowiedz_dobra .= "nacja<br> ";
			else 
				$odpowiedz_zla .= "nacja<br> ";
		}
		if(isSet($_POST["typ"]) && !empty($_POST["typ"]))
		{	
			$typ = $_POST["typ"];
			$zapytanie = "UPDATE czolg SET typ = '$typ' WHERE nazwa = '$stare_id';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				$odpowiedz_dobra .= "typ<br> ";
			else 
				$odpowiedz_zla .= "typ<br> ";
		}

		$fileName = $_FILES['myfile']['name'];
		
		if(!empty ($fileName))
		{
			$zapytanie = "SELECT tier, typ FROM czolg WHERE nazwa = '$stare_id';";
			$zap = mysqli_query($wot, $zapytanie);
			while($wiersz = mysqli_fetch_assoc($zap)) 
			{
				$tier = $wiersz["tier"];
				$typ =$wiersz["typ"];
			}
			$flaga = 1;
			$currentDir = getcwd();
			$uploadDirectory = "/../obrazy/czolgi/" . $tier . "/" . $typ ."/";
			$fileSize = $_FILES['myfile']['size'];
			$fileTmpName = $_FILES['myfile']['tmp_name'];
			$fileType = $_FILES['myfile']['type'];
			if($fileName != "" )
			{
				$uploadPath = $currentDir . $uploadDirectory . $fileName;
				if(move_uploaded_file($fileTmpName, $uploadPath))
				{
					$odpowiedz_dobra .= "upload ";
				}
				else
				{
					$flaga = 0;
					$odpowiedz_zla	.= "upload ";
				}
			}
			else
			{
				$flaga = 0;
				$odpowiedz_zla	.= "zly format pliku";	
			}

			if($flaga == 1)
			{
				$path = "obrazy/czolgi/$tier/$typ/$fileName";
				$zapytanie = "UPDATE czolg SET model = '$path' WHERE nazwa = '$stare_id';";

				$zap = mysqli_query($wot, $zapytanie);
				if($zap)
					$odpowiedz_dobra .= "obrazek<br> ";
				else
					$odpowiedz_zla .= "obrazek<br> ";
			}		
		}

			
		if ($odpowiedz_dobra != "<b>zmieniono pomyślnie:</b><br> ")
			$_SESSION["udane"] = $odpowiedz_dobra;
		else unset($_SESSION["udane"]);
		if ($odpowiedz_zla != "<b>nie udało się zmienić:</b><br> ")
			$_SESSION["nieudane"] = $odpowiedz_zla;
		else unset($_SESSION["nieudane"]);
		$_SESSION["czolg"] = $stare_id;
		header("Location: modyfikujCzolg.php");
	}	
	else  
	{
		$_SESSION["komunikat"] = "niezalogowany";
		header("Location: ../index.php");
	}

	if (!empty($_GET["idStylizacji"]))
	{
		$nick = $_SESSION["user"];
		$idStyliacji = $_GET["idStylizacji"];
		$zapytanie = "UPDATE garaz SET id_stylizacji = '$idStyliacji' WHERE nick = '$nick';";

		$zap = mysqli_query($wot, $zapytanie);
		if($zap)
		{
			$_SESSION["stylizacja"]	= "udało się dodać stylizację";
			header("Location: gracz.php");
		}
		else
		{
			$_SESSION["stylizacja"]	= "udało się dodać stylizację";
			header("Location: gracz.php");
		}	

	}

	if(isSet($_POST["wybranoStylizacjaDoCzolgu"]))
	{
		if(isSet($_POST["idCzolguDoStylizacji"]) && !empty($_POST["idCzolguDoStylizacji"])
			&& isSet($_POST["stylizacjaDoDodania"]) && !empty($_POST["stylizacjaDoDodania"]))
		{
			$nick = $_SESSION["user"];
			$idCzolgu = $_POST["idCzolguDoStylizacji"];
			$idStyliacji = $_POST["stylizacjaDoDodania"];
			$zapytanie = "UPDATE garaz SET id_stylizacji = '$idStyliacji' WHERE nick = '$nick' and czolg_id = '$idCzolgu';";

			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
			{
				$_SESSION["stylizacjaDodanie"]	= "udało się dodać stylizację";
				header("Location: dodajStylizacje.php");
			}
			else
			{
				$_SESSION["stylizacjaDodanie"]	= "nie udało się dodać stylizacji";
				header("Location: dodajStylizacje.php");
			}	
		}
	}

	if(isSet($_POST["usunGarazStylizacja"]))
	{
		echo $_POST["idCzolguStylizacjaUsun"];
		if(isSet($_POST["idCzolguStylizacjaUsun"]) && !empty($_POST["idCzolguStylizacjaUsun"]))
		{
			$nick = $_SESSION["user"];
			$idCzolgu = $_POST["idCzolguStylizacjaUsun"];

			$zapytanie = "UPDATE garaz SET id_stylizacji = null WHERE nick = '$nick' and czolg_id = '$idCzolgu';";
			$zap = mysqli_query($wot, $zapytanie);
			
			if($zap)
			{
				$_SESSION["idCzolguUsunieto"] = $idCzolgu;
				$_SESSION["usunStylizajce"]	= "udało się usunąć stylizację";
				header("Location: ../gracz.php");
			}
			else
			{
				$_SESSION["idCzolguUsunieto"] = $idCzolgu;
				$_SESSION["usunStylizajce"]	= "udało się usunąć stylizacji";
				header("Location: ../gracz.php");
			}	
		}
	}
?>

</body>
</html>