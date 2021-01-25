# WoT_projekt

należy w phpMyAdmin importować plik  kkulesa_wot.sql a następnie skonfigurować plik WOT->config.php
Jedyna zmiana w config.php to nazwa bazy danych na taką, jaka jest w phpMyAdmin - u nas była to nazwa "projekt".

Do samego projektu można się też dostać przez link katarzynakulesa.pl/WOT 


BAZA DLA GRACZY WORLD OF TANKS
Gra World of Tanks skupia tysiące graczy z całego świata. Celem naszego projektu jest stworzenie miejsca, w którym swobodnie można przejrzeć mapy, czołgi oraz statystyki gry. 
Gracz jest określeniem użytkownika gry, który może posiadać dowolną liczbę czołgów, przy czym nie może posiadać dwóch identycznych. Posiada on unikalny nick, musi związać konto ze swoim adresem email oraz hasłem do logowania. Gracz może grać w klanie, ale tylko w jednym, podczas gdy klan może zawierać określoną liczbę graczy. Każdy klan ma unikalną nazwę oraz liczbę prowincji.
Plutony to nawiązywane przez graczy chwilowe sojusze. To znaczy że nie mają swojej nazwy a w pamięci przechowywana jest informacja o dacie ich wygaśnięcia, dodawana jest jednak dopiero w momencie rozwiązania plutonu. Jednocześnie gracz może być uczestnikiem tylko jednego plutonu.
Gracz posiada swój garaż, w którym przechowywane są jego czołgi oraz dane o ich stylizacji. Stylizacja to zestaw zmieniający zewnętrzny wygląd czołgu, ona również musi być przez gracza posiadana, aby móc ją nałożyć na pojazd. Stylizację określa zdjęcie oraz krótki opis. 
Każdy czołg jest opisany przez konkretny typ składający się z nazwy i ikony. Jeden typ może opisywać wiele czołgów. Czołg jako parametry własne posiada unikalną nazwę, model, pancerz, życie, siłę ognia , tier oraz nację. 
Statystyki są zbiorem danych opisujących każdego gracza oddzielnie. Przedstawiają średnie uszkodzenia zadawane przez gracza, ilość bitew wygranych, przegranych oraz remisu, a także współczynnik WN8 świadczący o ogólnym poziomie użytkownika w rankingu wszystkich graczy, który nie jest obowiązkowy ponieważ nowy gracz nie posiada jeszcze wartości WN8.
Bitwy to główna atrakcja gry. Dzielą się one na bitwy losowe oraz klanowe. Bitwy klanowe rozgrywane są przez dwa klany walczące ze sobą. Bitwy losowe skupiają zgodnie z nazwą losowych uczestników z serwera oraz losową liczbę plutonów. Bitwa nie posiada własnej nazwy, a tuż przed jej rozpoczęciem zawsze wyświetla się wskazówka do gry.
Każda bitwa rozgrywa się na konkretnej mapie. Mapa ma unikalna nazwę i może na niej rozgrywać się wiele bitew. Każda mapa posiada krótki opis, rozmiar oraz zdjęcie. Każda mapa posiada również swój typ. Typ określa pogodę, rodzaj bitwy oraz czas jej trwania.
Celem projektu jest stworzenie aplikacji webowej pomagającej skupić wszystkie informacje dotyczące gry World of Tanks w jednym uporządkowanym miejscu.

