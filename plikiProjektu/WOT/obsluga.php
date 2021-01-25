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
</head>
<body>

<?php
$sprawdz = 0;

	if(isSet($_POST["dodaj"]))
	{
		if(isSet($_POST["nick"]) && isSet($_POST["email"]) && isSet($_POST["haslo"]) &&
			!empty($_POST["nick"]) && !empty($_POST["email"]) && !empty($_POST["haslo"] ))
		{
			$nick = $_POST["nick"];
			$email = $_POST["email"];
			$haslo = $_POST["haslo"];
			$hash = password_hash($haslo, PASSWORD_DEFAULT);

			$flaga = 1;

			if (str_word_count($nick) == 1 && strpos($nick, ' ') == false){
				$zapytanie = "SELECT nick FROM gracz";
				$zap = mysqli_query($wot, $zapytanie);
							
				while($wiersz = mysqli_fetch_assoc($zap)) 
				{
					if($wiersz["nick"] == $nick)
						{
							$flaga = 0;
						}
				}
				if($flaga == 1)
				{
					
					$zapytanie = "INSERT INTO gracz(nick, email, haslo) VALUES ('$nick', '$email', '$hash')";

						if(mysqli_query($wot, $zapytanie))
						{
							$_SESSION["nowyUzytkownik"] = "dodano gracza: $nick";
							header("Location: nowy_uzytkownik.php");
						}
						else
	     				{
							$_SESSION["nowyUzytkownik"] = "jakis blad - nwm jaki";
							header("Location: nowy_uzytkownik.php");
						}
				}
				else
				{
					$_SESSION["nowyUzytkownik"] = "ten nick jest już zajęty - wybierz inny";
					header("Location: nowy_uzytkownik.php");
				}
			}
			else
			{
				$_SESSION["nowyUzytkownik"] = "nick musi składać się z jednego wyrazu<br>wyraz musi zawierać co najmniej jedną literę!";
				header("Location: nowy_uzytkownik.php");

			
		}
			
 		}
 		else
		{
			$_SESSION["nowyUzytkownik"] = "brak wszystkich danych";
			header("Location: nowy_uzytkownik.php");
		}
		$sprawdz = 1;
	}
	
 	
if( empty($_SESSION["user"]) and $sprawdz == 0) 
		header("Location: index.php");
	else 	session_regenerate_id();

	
	if(isSet($_POST["zaloguj"]))
	{
		if(isSet($_POST["nick"]) && isSet($_POST["haslo"]) &&
			!empty($_POST["nick"]) && !empty($_POST["haslo"]) && $_POST["edycja"]=="nie")
		{
			$zapytanie = "SELECT * FROM gracz";
			$zap = mysqli_query($wot, $zapytanie);
			$flaga = 0;
			while($wiersz = mysqli_fetch_assoc($zap)) 
			{
				$hash = 0;
				if (password_verify($_POST["haslo"], $wiersz["haslo"]))
					$hash = 1;
				$wart_nick = $wiersz["nick"];
				$wart_haslo = $wiersz["haslo"];
				if($wart_nick == $_POST["nick"] && $hash)
					{	
						$flaga = 1;
						$_SESSION["user"] = $wart_nick;
						if ($wart_nick == "admin")
							header("Location: admin.php");
						else
							header("Location: gracz.php");
						break;
					}
			}
			if ($flaga == 0)
				{
					$_SESSION["logowanie"] = "niepoprawne dane - spróbuj ponownie";
					header("Location: index.php");
				}
		}
		else if(isSet($_POST["nick"]) && isSet($_POST["haslo"]) &&
			!empty($_POST["nick"]) && !empty($_POST["haslo"])  && $_POST["edycja"]=="tak")
		{
			$wart_nick = $_POST["nick"];
			$wart_haslo = $_POST["haslo"];
			$hash = password_hash($wart_haslo, PASSWORD_DEFAULT);
			$zapytanie = "UPDATE gracz SET haslo = '$hash' WHERE nick = '$wart_nick';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				header("Location: index.php");
		}
	}


	if(isSet($_POST["zatwierdz"]))
	{
		$odpowiedz_dobra = "<b>zmieniono pomyślnie:</b><br> ";
		$odpowiedz_zla = "<b>nie udało się zmienić:</b><br> ";
		$nick = $_SESSION["user"];

		if(isSet($_POST["nowy_nick"]) && !empty($_POST["nowy_nick"]))
		{	
			$wart_nick = $_POST["nowy_nick"];
			if (str_word_count($wart_nick) == 1 && strpos($wart_nick, ' ') == false){
				$zapytanie = "SELECT nick FROM gracz";
				$zap = mysqli_query($wot, $zapytanie);
				$flaga = 1;		
				while($wiersz = mysqli_fetch_assoc($zap)) 
				{
					if($wiersz["nick"] == $wart_nick)
					{
						$odpowiedz_zla .=  "<br>  nick - $wart_nick  jest już zajęty. Wybierz inny <br>";
						$flaga = 0;
					}
				}
				if($flaga)
				{
					$zapytanie = "UPDATE gracz SET nick = '$wart_nick' WHERE nick = '$nick';";
					$zap = mysqli_query($wot, $zapytanie);
					if($zap)
					{
						$odpowiedz_dobra .= "nick ";
						$_SESSION["user"] =  "$wart_nick";
					}
					else 
						$odpowiedz_zla .= "nick ";
				}
			}
			else{
				$odpowiedz_zla .= "nick  $wart_nick - wymagany jeden wyraz zawierający co najmniej jedna literę<br>";
			}
			
		}

		$nick = $_SESSION["user"];

		if(isSet($_POST["nowe_haslo"]) && !empty($_POST["nowe_haslo"]))
		{	
			$wart_haslo = $_POST["nowe_haslo"];
			$hash = password_hash($wart_haslo, PASSWORD_DEFAULT);
			$zapytanie = "UPDATE gracz SET haslo = '$hash' WHERE nick = '$nick';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				$odpowiedz_dobra .= "hasło ";
			else 
				$odpowiedz_zla .= "hasło ";
		}

		if(isSet($_POST["nowy_email"]) && !empty($_POST["nowy_email"]))
		{	
			$wart_email = $_POST["nowy_email"];
			$zapytanie = "UPDATE gracz SET email = '$wart_email' WHERE nick = '$nick';";
			$zap = mysqli_query($wot, $zapytanie);
			if($zap)
				$odpowiedz_dobra .= "email ";
			else 
				$odpowiedz_zla .= "email ";
		}

		if ($odpowiedz_dobra != "<b>zmieniono pomyślnie:</b><br> ")
			$_SESSION["udaneModyfikujUzytkownika"] = $odpowiedz_dobra;
		else unset($_SESSION["udaneModyfikujUzytkownika"]);
		if ($odpowiedz_zla != "<b>nie udało się zmienić:</b><br> ")
			$_SESSION["nieudaneModyfikujUzytkownika"] = $odpowiedz_zla;
		else unset($_SESSION["nieudaneModyfikujUzytkownika"]);

		header("Location: modyfikuj.php");
	}	

	if(isSet($_POST["nowyCzolg"]))
	{
		if(isSet($_POST["nazwa"]) && !empty($_POST["nazwa"]))
		{
			$nazwa = $_POST["nazwa"];
			$nick = $_SESSION["user"];
			$flaga = 1;


			$zapytanie = "SELECT nazwa FROM garaz where nick = '$nick';";
			$zap = mysqli_query($wot, $zapytanie);
						
			while($wiersz = mysqli_fetch_assoc($zap)) 
			{
				if($wiersz["nazwa"] == $nazwa)
					{
						$flaga = 0;
					}
			}

			if($flaga == 1)
			{
				$zapytanie = "INSERT INTO garaz(nick, nazwa) VALUES ('$nick', '$nazwa')";
				$wynik_zapytania = mysqli_query($wot, $zapytanie);

					if($wynik_zapytania)
					{
						$_SESSION["bladDodaniaNowegoCzolgu"] = "udało się dodać " . $nazwa . " do garażu<br><br>";
						header("Location: dodajCzolg.php");
					}
					else
	 				{
						$_SESSION["bladDodaniaNowegoCzolgu"] = "jakis blad - nwm jaki" ;
						header("Location: dodajCzolg.php");
					}
			}
			else if ($flaga == 0)
			{
				$_SESSION["bladDodaniaNowegoCzolgu"] = "Masz juz ten czolg";
				header("Location: dodajCzolg.php");
			}
			else if ($flaga == -1)
			{
				$_SESSION["bladDodaniaNowegoCzolgu"] = "nie znaleziono id";
				header("Location: dodajCzolg.php");
			}
		}
				
 		//}
	}

 
	if(!empty($_POST["dolaczKlan"]) && !empty($_POST["dolaczKlan"]))
	{
		$nazwa = $_POST["nazwaKlanu"];
		$nick = $_SESSION["user"];
		$zapytanie = "UPDATE gracz SET nazwa_klanu = '$nazwa' WHERE nick = '$nick';";

		$wynik_zapytania = mysqli_query($wot, $zapytanie);

			if($wynik_zapytania)
			{
				$_SESSION["klan"] = $nazwa;
				$_SESSION["bladKlan"] = "dołączyłeś do klanu!";
				$sciezka = "Location: klany/klan.php" ;
				header($sciezka);
			}
			else
				{
				$_SESSION["bladKlan"] = "jakis blad - nwm jaki" . $nazwa;
				$sciezka = "Location: klany/klan.php";
				header($sciezka);
			}
	}

	if(!empty($_POST["opuscKlan"]) && isSet($_POST["opuscKlan"]))
	{	
		$nick = $_SESSION['user'];
		$zapytanie = "UPDATE gracz SET nazwa_klanu = null WHERE nick = '$nick';";

		$wynik_zapytania = mysqli_query($wot, $zapytanie);

			if($wynik_zapytania)
			{
				$_SESSION["bladKlan"] = "opuściłeś klan!";
				$sciezka = "Location: klany/klan.php?user=" . $nick;
				header($sciezka);
			}
			else
				{
				$_SESSION["bladKlan"] = "jakis blad - nwm jaki";
				$sciezka = "Location: klany/klan.php?user=" . $nazwa;
				header($sciezka);
			}
	}
 		

	if(isSet($_POST["nowyPluton"]))
	{

		if(!empty($_POST["plutonowy"]) && isSet($_POST["plutonowy"]))
		{	
			$plutonowy = $_POST["plutonowy"];
			$nick = $_SESSION['user'];
			$zapytanie = "SELECT COUNT(*) AS liczba FROM gracz WHERE nick = '$plutonowy' and isnull(id_plutonu); ";
			$zap = mysqli_query($wot, $zapytanie);

			while($wiersz = mysqli_fetch_assoc($zap)) 
			{
				if($wiersz["liczba"] != 1)
				{
					$_SESSION["bladPluton"] = "ten gracz już jest w plutonie - nie możesz nawiązać z nim sojuszu!";
					header("Location: pluton.php");
				}
			}	

			$zapytanie = "INSERT INTO pluton(nick1, nick2) VALUES ('$nick', '$plutonowy');";
			$wynik_zapytania = mysqli_query($wot, $zapytanie);
			
			if($wynik_zapytania)
			{
				$zapytanie = "SELECT max(id_plutonu) AS liczba FROM pluton;";
				$wynik_zapytania = mysqli_query($wot, $zapytanie);
				if($wynik_zapytania)
				{
					while($wiersz = mysqli_fetch_assoc($wynik_zapytania))
					{
						$id = $wiersz["liczba"];
						echo $id;
						$_SESSION["bladPluton"] = $id;
					}
					
					$zapytanie = "UPDATE gracz SET id_plutonu = '$id' where nick = '$plutonowy' or nick = '$nick';";
					$wynik_zapytania = mysqli_query($wot, $zapytanie);
					if($wynik_zapytania)
					{
						$_SESSION["bladPluton"] = "Jestes w plutonie z $plutonowy";
						$_SESSION["plutonowy"]  = $plutonowy;
						$sciezka = "Location: pluton.php";
						header($sciezka);
					}
					else
					{
						$_SESSION["bladPluton"] = "zepsul sie ostatni krok" ;
						header("Location: pluton.php");
					}
				}
				else 
				{
					$_SESSION["bladPluton"] = "nie udalo sie dostac do maks id";
					header("Location: pluton.php");
				}
			}
			else
			{
				$_SESSION["bladPluton"] = "nie udalo sie utworzyc plutonu";
				header("Location: pluton.php");
			}
 		}
 	}

 	if(isSet($_POST["usunPluton"]))
	{
		
			$nick = $_SESSION['user'];
			$zapytanie = "SELECT id_plutonu from gracz where nick = '$nick';";
			$wynik_zapytania = mysqli_query($wot, $zapytanie);
			while($wiersz = mysqli_fetch_assoc($wynik_zapytania)) 
			{
				$id = $wiersz['id_plutonu'];
			}	

			$zapytanie = "UPDATE pluton SET aktywnosc = 0 WHERE id_plutonu = '$id'";

			$wynik_zapytania = mysqli_query($wot, $zapytanie);

				if($wynik_zapytania)
				{
					unset($_SESSION["plutonowy"]);
					$_SESSION["bladPluton"] = "Rozwiązałeś pluton!";
					header("Location: pluton.php");
				}
				else
 				{
					$_SESSION["bladPluton"] = "jakis blad - nwm jaki";
					header("Location: pluton.php");
				}
 		
	}

	if(isSet($_POST["usunGaraz"]))
	{
		if(isSet($_POST["idCzolguUsun"]) && !empty($_POST["idCzolguUsun"]))
		{
			$nick = $_SESSION['user'];
			$id = $_POST["idCzolguUsun"];
			if($id == "")
			{
				$_SESSION["bladGaraz"] = "wybierz czołg, kóry chcesz usunąć";
				header("Location: gracz.php");
			}

			$zapytanie = "DELETE FROM garaz WHERE nick = '$nick' AND czolg_id='$id';";

			$wynik_zapytania = mysqli_query($wot, $zapytanie);

			if($wynik_zapytania)
			{
				$_SESSION["bladGaraz"] = "Usunąłeś czołg z garażu!";
				header("Location: gracz.php");
			}
			else
				{
				$_SESSION["bladGaraz"] = "Nie udało się usunąć";
				header("Location: gracz.php");
			}
 		}
	}
 

 	if(!empty($_GET["losuj"]))
	{
		
			$nick = $_SESSION["user"];
			$liczba = 0;
			$idBitwy = -1;
			$nazwaMapy = "";

			$zapytanie = "SELECT count(*) as liczba from gracz where isnull(id_bitwy) && isnull(id_bitwy_klanowej);";
			$wynik_zapytania = mysqli_query($wot, $zapytanie);
			
			while($wiersz = mysqli_fetch_assoc($wynik_zapytania)) 
			{
				$liczba = $wiersz["liczba"];
			}	
			if(!$wynik_zapytania)
			{
				$_SESSION["nowaBitwa"] = "liczba";
					header("Location: gracz.php");
			}

			$zapytanie = "SELECT max(id) +1 as maxId from bitwa;";
			$wynik_zapytania = mysqli_query($wot, $zapytanie);
			while($wiersz = mysqli_fetch_assoc($wynik_zapytania)) 
			{
				$idBitwy = $wiersz["maxId"];
			}	
			if(!$wynik_zapytania)
			{
				$_SESSION["nowaBitwa"] = "maxId";
					header("Location: gracz.php");
			}

			$zapytanie = "SELECT nazwa_mapy from mapa ORDER BY RAND() LIMIT 1;";
			$wynik_zapytania = mysqli_query($wot, $zapytanie);
			
			while($wiersz = mysqli_fetch_assoc($wynik_zapytania)) 
			{
				$nazwaMapy = $wiersz["nazwa_mapy"];
			}	
			if(!$wynik_zapytania)
			{
				$_SESSION["nowaBitwa"] = "nazwaMapy";
					header("Location: gracz.php");
			}


			if($liczba >= 10 && $nazwaMapy != "" && $idBitwy >= 0 )
			{

				$zapytanie = "INSERT INTO bitwa (id, nazwa_mapy) VALUES ('$idBitwy', '$nazwaMapy');";

				$wynik_zapytania = mysqli_query($wot, $zapytanie);

				if($wynik_zapytania)
				{

					$zapytanie = "call losowanie('$idBitwy', '1', '$nick');";
					$wynik_zapytania = mysqli_query($wot, $zapytanie);
					if($wynik_zapytania)
					{
						$zapytanie = "call losowanie('$idBitwy', '2', null);";
						$wynik_zapytania = mysqli_query($wot, $zapytanie);
						if($wynik_zapytania)
						{
							$_SESSION["nowaBitwa"] = "Wstawiono bitwe!";
							header("Location: gracz.php");
						}
						else 
						{
							$_SESSION["nowaBitwa"] = "DRUZYNA 2 - BLAD";
							header("Location: gracz.php");		
						}				
					}
					else 
					{
						$_SESSION["nowaBitwa"] = "DRUZYNA 1 - BLAD";
						header("Location: gracz.php");
					}
					
				}
				else
 				{
					$_SESSION["nowaBitwa"] = "nie udalo się wstawić bitwy!" . $idBitwy . $nazwaMapy . $liczba ;
					header("Location: gracz.php");
				}
			}
			else
			{
				$_SESSION["nowaBitwa"] ="za mało graczy w bazie na nową bitwę";
					header("Location: gracz.php");
			}
	}

	if(!empty($_GET["usunBitwe"]))
	{
		$nick = $_SESSION["user"];
		$zapytanie = "SELECT * FROM gracz WHERE nick = '$nick'";
		$wynik_zapytania = mysqli_query($wot, $zapytanie);
		while($wiersz = mysqli_fetch_assoc($wynik_zapytania)) 
			{
				$idBitwy = $wiersz["id_bitwy"];
				$liczbaWygranych = $wiersz["wygranych"];
				$zapytanie = "call koniecBitwy($idBitwy);";
				$wynik_zapytania = mysqli_query($wot, $zapytanie);
				if($wynik_zapytania)
					{
						$zapytanie2 = "SELECT * FROM gracz WHERE nick = '$nick'";
						$wynik_zapytania2 = mysqli_query($wot, $zapytanie2);
						$wynik = mysqli_fetch_assoc($wynik_zapytania2);
						
						$zwyciestwo = "wygrano bitwę!";
						if($wynik["wygranych"] == $liczbaWygranych){
							$zwyciestwo = "przegrano bitwę!";
						}
						$_SESSION["nowaBitwa"] = $zwyciestwo;
						header("Location: gracz.php");
					}
				else
					{
						$_SESSION["bitwa"] ="nie udalo się zakończyć bitwy";
						header("Location: bitwa.php");	
					}
			}
		
	}

	if(isSet($_POST["losujKlanowa"]))
	{

		if( isSet($_POST["klanPrzeciwny"]) && !empty($_POST["klanPrzeciwny"])
			&& isSet($_POST["mojKlan"]) && !empty($_POST["mojKlan"]))
		{
			echo "cos";
			$nick = $_SESSION["user"];
			$klan = $_POST["mojKlan"];
			$klanPrzeciwny = $_POST["klanPrzeciwny"];
			$liczba = 0;
			$idBitwy = -1;
			$nazwaMapy = "";
			$sciezka="Location: wybierzKlan.php?klan=" . $klan;

			//liczba klanu 1
			$zapytanie = "SELECT count(*) as liczbaKlan1 from gracz where  isnull(id_bitwy) && isnull(id_bitwy_klanowej) && nazwa_klanu = '$klan';";
			$wynik_zapytania = mysqli_query($wot, $zapytanie);
			
			while($wiersz = mysqli_fetch_assoc($wynik_zapytania)) 
			{
				$liczbaKlan1 = $wiersz["liczbaKlan1"];
			}	
			if(!$wynik_zapytania)
			{
				$_SESSION["nowaBitwaKlanowa"] = "liczba";
					header($sciezka);
			}
			echo $liczbaKlan1;
			//liczba klanu 2
			$zapytanie = "SELECT count(*) as liczbaKlan2 from gracz where  isnull(id_bitwy) && isnull(id_bitwy_klanowej) && nazwa_klanu = '$klanPrzeciwny';";
			$wynik_zapytania = mysqli_query($wot, $zapytanie);
			
			while($wiersz = mysqli_fetch_assoc($wynik_zapytania)) 
			{
				$liczbaKlan2 = $wiersz["liczbaKlan2"];
			}	
			if(!$wynik_zapytania)
			{
				$_SESSION["nowaBitwaKlanowa"] = "liczba";
					header("Location: wybierzKlan.php");
			}

			//id nowej bitwy klanowej
			$zapytanie = "SELECT max(id) +1 as maxId from bitwaKlanowa;";
			$wynik_zapytania = mysqli_query($wot, $zapytanie);
			while($wiersz = mysqli_fetch_assoc($wynik_zapytania)) 
			{
				$idBitwy = $wiersz["maxId"];
			}	
			if(!$wynik_zapytania)
			{
				$_SESSION["nowaBitwaKlanowa"] = "maxId";
					header($sciezka);
			}

			//wybor mapy
			$zapytanie = "SELECT nazwa_mapy from mapa ORDER BY RAND() LIMIT 1;";
			$wynik_zapytania = mysqli_query($wot, $zapytanie);
			
			while($wiersz = mysqli_fetch_assoc($wynik_zapytania)) 
			{
				$nazwaMapy = $wiersz["nazwa_mapy"];
			}	
			if(!$wynik_zapytania)
			{
				$_SESSION["nowaBitwaKlanowa"] = "nazwaMapy";
					header($sciezka);
			}

			echo $liczbaKlan1	. $liczbaKlan2	 . $nazwaMapy	 . $idBitwy;	
			if($liczbaKlan1 >= 5 && $liczbaKlan2 >= 5 && $nazwaMapy != "" && $idBitwy >= 0 )
			{

				$zapytanie = "INSERT INTO bitwaKlanowa(id, nazwaMapy) VALUES ('$idBitwy', '$nazwaMapy');";
				$wynik_zapytania = mysqli_query($wot, $zapytanie);

				if($wynik_zapytania)
				{

					$zapytanie = "call losujKlan2('$idBitwy', '$klan', '$nick');";
					$wynik_zapytania = mysqli_query($wot, $zapytanie);
					if($wynik_zapytania)
					{
						$zapytanie = "call losujKlan('$idBitwy', '$klanPrzeciwny');";
						$wynik_zapytania = mysqli_query($wot, $zapytanie);
						if($wynik_zapytania)
						{
							$_SESSION["nowaBitwaKlanowa"] = "Wstawiono bitwe!";
							header("Location: gracz.php");
						}
						else 
						{
							$_SESSION["nowaBitwaKlanowa"] = "DRUZYNA 2 - BLAD";
							header($sciezka);		
						}				
					}
					else 
					{
						$_SESSION["nowaBitwaKlanowa"] = "DRUZYNA 1 - BLAD";
						header($sciezka);
					}
					
				}
				else
 				{
					$_SESSION["nowaBitwaKlanowa"] = "nie udalo się wstawić bitwy!";
					header($sciezka);
				}
			}
			else
			{
				$_SESSION["nowaBitwaKlanowa"] = "za mało graczy w klanach!";
					header($sciezka);
			}
		}
	}


	if(!empty($_GET["usunBitweKlanowa"]))
	{
		$nick = $_SESSION["user"];
		$zapytanie = "SELECT id_bitwy_klanowej, nazwa_klanu FROM gracz WHERE nick = '$nick'";
		$wynik_zapytania = mysqli_query($wot, $zapytanie);
		while($wiersz = mysqli_fetch_assoc($wynik_zapytania)) 
			{
				$idBitwy = $wiersz["id_bitwy_klanowej"];
				$nazwa = $wiersz["nazwa_klanu"];
				$liczbaWygranych = $wiersz["wygranych"];
				$zapytanie = "call koniecBitwyKlanowej($idBitwy, '$nazwa');";
				$wynik_zapytania = mysqli_query($wot, $zapytanie);
				if($wynik_zapytania)
					{
						$zapytanie2 = "SELECT * FROM gracz WHERE nick = '$nick'";
						$wynik_zapytania2 = mysqli_query($wot, $zapytanie2);
						$wynik = mysqli_fetch_assoc($wynik_zapytania2);
						
						$zwyciestwo = "wygrano bitwę!";
						if($wynik["wygranych"] == $liczbaWygranych){
							$zwyciestwo = "przegrano bitwę!";
						}
						$_SESSION["nowaBitwaKlanowa"] = $zwyciestwo;
						header("Location: gracz.php");
					}
				else
					{
						$_SESSION["bitwa"] ="nie udalo się zakończyć bitwy";
						header("Location: bitwaKlanowa.php");	
					}
			}
		
	}
 
?>

</body>
</html>

