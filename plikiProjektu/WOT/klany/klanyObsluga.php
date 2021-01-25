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

if(isSet($_POST["nowyKlan"]))
	{

		if(isSet($_POST["nazwaKlanu"])  && !empty($_POST["nazwaKlanu"])
			&& ($_POST["maksGraczy"])  && !empty($_POST["maksGraczy"])
			&& ($_POST["prowincje"])  && !empty($_POST["prowincje"]))
		{	
			$nazwa = $_POST["nazwaKlanu"];
			$maks = $_POST["maksGraczy"];
			$prowincje = $_POST["prowincje"];
			//if (str_word_count($nazwa) == 1 && strpos($nazwa, ' ') == false)
			//{
				$zapytanie = "SELECT count(*) as liczba FROM Klan where nazwa_klanu = '$nazwa';";
				$zap = mysqli_query($wot, $zapytanie);

				while($wiersz = mysqli_fetch_assoc($zap)) 
				{
					if($wiersz["ile"] > 0)
					{
						$_SESSION["komunikat"] = "nieunikalna nazwa - wybierz inną!";
						header("Location: dodajKlan.php");
					}
				}

				
				$zapytanie = "INSERT INTO klan(nazwa_klanu, maks_liczba_graczy, ilosc_prowincji)  VALUES ('$nazwa','$maks','$prowincje');";

				$wynik_zapytania = mysqli_query($wot, $zapytanie);
				echo mysqli_errno($wot);
				

				if($wynik_zapytania)
				{
					$_SESSION["komunikat"] = "udalo się dodac nowy klan";
					header("Location: dodajKlan.php");
				}
				else
				{
					$_SESSION["komunikat"] = "nie udalo sie dodac nowego klanu";
					header("Location: dodajKlan.php");
				}
		//	}
			//else{

		//		$_SESSION["komunikat"] = "nazwa $nazwa - wymagany jeden wyraz zawierający co najmniej jedna literę<br>";
		//		header("Location: dodajKlan.php");
		//	}
			
			
 		}
 	}	

 	if(isSet($_POST["zmienKlan"]))
	if($_SESSION["user"] == "admin" && isSet($_POST["id"]) && !empty($_POST["id"]))
	{
		$odpowiedz_dobra = "<b>zmieniono pomyślnie:</b><br> ";
		$odpowiedz_zla = "<b>nie udało się zmienić:</b><br> ";
		$nick = $_SESSION["user"];
		$stare_id = $_POST["id"];
		$przekierowanie = $stare_id;

		if(isSet($_POST["nazwa_klanu"])  && !empty($_POST["nazwa_klanu"]))
		{	
			$wart_nazwa = $_POST["nazwa_klanu"];
			if (str_word_count($wart_nazwa) == 1 && strpos($wart_nazwa, ' ') == false)
			{

			$zapytanie = "SELECT count(*) as liczba FROM klan where nazwa_klanu = '$wart_nazwa'";
			$zap = mysqli_query($wot, $zapytanie);
			$flaga = 1;		
			while($wiersz = mysqli_fetch_assoc($zap)) 
			{
				if($wiersz["liczba"] > 0)
				{
					$odpowiedz_zla .=  "nazwa  $wart_nazwa  jest już zajęta. Wybierz inną <br>";
					$flaga = 0;
				}
			}
			if($flaga)
			{
				$zapytanie = "UPDATE klan SET nazwa_klanu = '$wart_nazwa' WHERE nazwa_klanu = '$stare_id';";
				$zap = mysqli_query($wot, $zapytanie);
				if($zap)
				{
					$_SESSION["klan"] = $_POST["nazwa_klanu"];
					$odpowiedz_dobra .= "nazwa<br> ";
					$stare_id = $wart_nazwa;
				}
				else 
					$odpowiedz_zla .= "nazwa<br> ";
			}
			}
			else{
				$odpowiedz_zla .= "nazwa $wart_nazwa - wymagany jeden wyraz zawierający co najmniej jedna literę<br>";
			}
		}

		if(isSet($_POST["maks_liczba_graczy"])  && !empty($_POST["maks_liczba_graczy"]))
		{	
			$maks_liczba_graczy = $_POST["maks_liczba_graczy"];
			$zapytanie = "UPDATE klan SET maks_liczba_graczy = '$maks_liczba_graczy' WHERE nazwa_klanu = '$stare_id';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				$odpowiedz_dobra .= "maksymalna liczba graczy<br> ";
			else 
				$odpowiedz_zla .= "maksymalna liczba graczy<br> ";
		}

		if(isSet($_POST["ilosc_prowincji"]) && !empty($_POST["ilosc_prowincji"]))
		{	
			$ilosc_prowincji = $_POST["ilosc_prowincji"];
			$zapytanie = "UPDATE klan SET ilosc_prowincji = '$ilosc_prowincji' WHERE nazwa_klanu = '$stare_id';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				$odpowiedz_dobra .= "ilość prowincji<br> ";
			else 
				$odpowiedz_zla .= "ilość prowincji<br> ";
		}
			
		if ($odpowiedz_dobra != "<b>zmieniono pomyślnie:</b><br> ")
			$_SESSION["udane"] = $odpowiedz_dobra;
		else unset($_SESSION["udane"]);
		if ($odpowiedz_zla != "<b>nie udało się zmienić:</b><br> ")
			$_SESSION["nieudane"] = $odpowiedz_zla;
		else unset($_SESSION["nieudane"]);

 
		$_SESSION["klan"] = $stare_id;
		header("Location: modyfikujKlan.php");
	}	
	else  
	{
		$_SESSION["komunikat"] = "niezalogowany";
		header("Location: ../index.php");
	}

if(!empty($_POST["usunięcieKlanu"]) && isSet($_POST["usunięcieKlanu"]))
{
	if(isSet($_POST["idKlanyUsuniecie"]) && !empty($_POST["idKlanyUsuniecie"]))
	{
		$idKlanu = $_POST["idKlanyUsuniecie"];
		$zapytanie = "DELETE FROM klan WHERE nazwa_klanu = '$idKlanu';";
		$zap = mysqli_query($wot, $zapytanie);
		if($zap)
		{
			$_SESSION["bladKlan"] = "udało się usunąć klan " . $idKlanu ;
			header("Location: klan.php");
		}
		else
		{
			$_SESSION["bladKlan"] = "nie udało się usunąć klanu " . $idKlanu ;
			header("Location: klan.php");		
		}
	}
}
 ?>

</body>
</html>

