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

if(isSet($_POST["nowaMapa"]))
		if($_SESSION['user'] == "admin")
	{

		if(isSet($_POST["nazwa_mapy"])  && !empty($_POST["nazwa_mapy"])
			&& isSet($_POST["opis"])  && !empty($_POST["opis"])
			&& isSet($_POST["rozmiar"])  && !empty($_POST["rozmiar"])
			&& isSet($_POST["typMapy"]) && !empty($_POST["typMapy"]))


		{	
			$nazwa = $_POST["nazwa_mapy"];
			$opis = $_POST["opis"];
			$rozmiar = $_POST["rozmiar"];
			$typ = $_POST["typMapy"];
			$flaga = 0;

			//if (str_word_count($nazwa) == 1 && strpos($nazwa, ' ') == false)
			//{

				$zapytanie = "SELECT count(*) as liczba FROM mapa where nazwa_mapy = '$nazwa';";
				$zap = mysqli_query($wot, $zapytanie);

				while($wiersz = mysqli_fetch_assoc($zap)) 
				{
					if($wiersz["ile"] > 0)
					{
						$_SESSION["komunikat"] = "nieunikalny opis";
						header("Location: dodajMape.php");
					}
				}

				$flaga = 1;
				$currentDir = getcwd();
				$uploadDirectory = "/../obrazy/mapa/";
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
						header("Location: dodajMape.php");
					}
				}
				else 
				{
					$flaga = 0;
					$_SESSION["komunikat"] = "zly typ lub pusta nazwa";
					header("Location: dodajMape.php");
				}

				if($flaga == 1)
				{
					$path = "obrazy/mapa/" . $fileName;
					$zapytanie = "INSERT INTO mapa(nazwa_mapy, opis, rozmiar, zdjecie, typ_bitwy)  VALUES ('$nazwa','$opis','$rozmiar','$path', '$typ');";

					$wynik_zapytania = mysqli_query($wot, $zapytanie);
			

					if($wynik_zapytania)
					{
						$_SESSION["komunikat"] = "udalo się dodac nową mape";
						header("Location: dodajMape.php");
					}
					else
					{
						$_SESSION["komunikat"] = "Jakis blad - nwm jaki" . $path;
						header("Location: dodajMape.php");
					}
				}
		//	}
	//		else
//			{
//				$_SESSION["komunikat"] = "nazwa " . $_POST["nazwa_mapy"] . " - wymagany jeden wyraz zawierający co najmniej jedna literę<br>";
//				header("Location: dodajMape.php");
//			}	
 		}
 	}
	else  
	{
		$_SESSION["komunikat"] = "niezalogowany";
		header("Location: ../index.php");
	}


	if(isSet($_POST["zmienMape"]))
	if($_SESSION["user"] == "admin" && isSet($_POST["id"]))
	{
		$odpowiedz_dobra = "<b>zmieniono pomyślnie:</b><br> ";
		$odpowiedz_zla = "<b>nie udało się zmienić:</b><br> ";
		$nick = $_SESSION["user"];
		$stare_id = $_POST["id"];
		$przekierowanie = $stare_id;

		if(isSet($_POST["nazwa_mapy"])  && !empty($_POST["nazwa_mapy"]))
		{	
			$wart_nick = $_POST["nazwa_mapy"];
			if (str_word_count($wart_nick) == 1 && strpos($wart_nick, ' ') == false)
			{
				$zapytanie = "SELECT count(*) as liczba FROM mapa where nazwa_mapy = '$wart_nick'";
				$zap = mysqli_query($wot, $zapytanie);
				$flaga = 1;		
				while($wiersz = mysqli_fetch_assoc($zap)) 
				{
					if($wiersz["liczba"] > 0)
					{
						$odpowiedz_zla .=  "nazwa $wart_nick  jest już zajęta. Wybierz inną <br>";
						$flaga = 0;
					}
				}
				if($flaga)
				{
					$zapytanie = "UPDATE mapa SET nazwa_mapy = '$wart_nick' WHERE nazwa_mapy = '$stare_id';";
					$zap = mysqli_query($wot, $zapytanie);
					if($zap)
					{
						$_SESSION["idMapyZmien"] = $wart_nick;
						$odpowiedz_dobra .= "nazwa<br> ";
						$stare_id = $wart_nick;
					}
					else {
						$odpowiedz_zla .= "nazwa<br> ";
						

					}
				}
			}
			else
			{
				$odpowiedz_zla .= "nazwa " . $_POST["nazwa_mapy"] . " - wymagany jeden wyraz zawierający co najmniej jedna literę<br>";
			}
		}

		if(isSet($_POST["opis"])  && !empty($_POST["opis"]))
		{	
			$opis = $_POST["opis"];
			$zapytanie = "SELECT opis FROM  mapa  WHERE nazwa_mapy = '$stare_id';";
			$zap = mysqli_query($wot, $zapytanie);
			$wiersz = mysqli_fetch_assoc($zap);

			if($wiersz["opis"] != $opis){
				$zapytanie = "UPDATE mapa SET opis = '$opis' WHERE nazwa_mapy = '$stare_id';";
				$zap = mysqli_query($wot, $zapytanie);
				if($zap){
					$odpowiedz_dobra .= "opis<br> ";
				}
				else 
					$odpowiedz_zla .= "opis<br> ";
			}
		
		}

		if(isSet($_POST["rozmiar"]) && !empty($_POST["rozmiar"]))
		{	
			$rozmiar = $_POST["rozmiar"];
			$zapytanie = "UPDATE mapa SET rozmiar = '$rozmiar' WHERE nazwa_mapy = '$stare_id';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				$odpowiedz_dobra .= "rozmiar<br> ";
			else 
				$odpowiedz_zla .= "rozmiar<br> ";
		}

		$fileName = $_FILES['myfile']['name'];
		
		if(!empty ($fileName))
		{
			$currentDir = getcwd();
			$uploadDirectory = "/../obrazy/mapa/";
			$fileSize = $_FILES['myfile']['size'];
			$fileTmpName = $_FILES['myfile']['tmp_name'];
			$fileType = $_FILES['myfile']['type'];

			if($fileName != "" )
			{
				$uploadPath = $currentDir. $uploadDirectory . $fileName;
				if(move_uploaded_file($fileTmpName, $uploadPath))
				{
					$_SESSION["udane"] = "upload";
				}
				else
				{
					$flaga = 0;
					$_SESSION["nieudane"] = "upload";
					header("Location: addMap.php");
				}

				$path = "obrazy/mapa/" . $fileName;
				$zapytanie = "UPDATE mapa SET zdjecie = '$path' WHERE nazwa_mapy = '$stare_id';";
				$zap = mysqli_query($wot, $zapytanie);
				if($zap)
					$odpowiedz_dobra .= "obrazek<br> ";
				else
					$odpowiedz_zla .= "obrazek<br> ";
			}		
		}
			
		if ($odpowiedz_dobra != "<b>zmieniono pomyślnie:</b><br> "){
			$_SESSION["udane"] = $odpowiedz_dobra;

		}
		else unset($_SESSION["udane"]);
		if ($odpowiedz_zla != "<b>nie udało się zmienić:</b><br> ")
			$_SESSION["nieudane"] = $odpowiedz_zla;
		else unset($_SESSION["nieudane"]);

		$_SESSION["idMapyZmien"] = $stare_id;

		header("Location: modyfikujMape.php?id=$stare_id");
	}	
	else  
	{
		$_SESSION["komunikat"] = "niezalogowany";
		header("Location: ../index.php");
	}

if(!empty($_POST["usunMape"]) && isSet($_POST["idMapyUsuniecie"])  && !empty($_POST["idMapyUsuniecie"]))
{
	$idMapy = $_POST["idMapyUsuniecie"];
	$zapytanie = "DELETE FROM mapa WHERE nazwa_mapy = '$idMapy';";
	$zap = mysqli_query($wot, $zapytanie);
	if($zap)
	{
		$_SESSION["komunikatMapa"] = "udało się usunąć mapę " . $idMapy ;
		header("Location: mapa.php");
	}
	else
	{
		$_SESSION["komunikatMapa"] = "nie udało się usunąć mapy " . $idMapy ;
		header("Location: mapa.php");		
	}
}
?>
</body>
</html>

