<?php

  $sqlite = 'teleinfo.sqlite';

  //
  //  renvoie une trame teleinfo complete sous forme d'array
  //
  function getTeleinfo () {

    if(!$handle = fopen ('/dev/ttyAMA0', "r")){
         throw new \RuntimeException('Unable to open tty');
    } // ouverture du flux

    while (fread($handle, 1) != chr(2)); // on attend la fin d'une trame pour commencer a avec la trame suivante

    $char  = '';
    $trame = '';
    $datas = '';

    while ($char != chr(2)) { // on lit tous les caracteres jusqu'a la fin de la trame
      $char = fread($handle, 1);
      if ($char != chr(2)){
        $trame .= $char;
      }
    }

    fclose ($handle); // on ferme le flux

    $trame = chop(substr($trame,1,-1)); // on supprime les caracteres de debut et fin de trame

    $messages = explode(chr(10), $trame); // on separe les messages de la trame

    foreach ($messages as $key => $message) {
      $message = explode (' ', $message, 3); // on separe l'etiquette, la valeur et la somme de controle de chaque message
      if(!empty($message[0]) && !empty($message[1])) {
        $etiquette = $message[0];
        $valeur    = $message[1];
        $datas[$etiquette] = $valeur; // on stock les etiquettes et les valeurs de l'array datas
      }
    }

    return $datas;

  }

  //
  //  enregistre la puissance instantanée en V.A et en W
  //
  function handlePuissance () {
    global $sqlite;
    $db = new SQLite3($sqlite);
    $db->exec('CREATE TABLE IF NOT EXISTS puissance (timestamp INTEGER, hchp TEXT, va REAL, iinst REAL, watt REAL);'); // cree la table puissance si elle n'existe pas

    $trame = getTeleinfo (); // recupere une trame teleinfo

    $datas = array();
    $datas['timestamp'] = time();
    $datas['hchp']      = substr($trame['PTEC'],0,2); // indicateur heure pleine/creuse, on garde seulement les carateres HP (heure pleine) et HC (heure creuse)
    $datas['va']        = preg_replace('`^[0]*`','',$trame['PAPP']); // puissance en V.A, on supprime les 0 en debut de chaine
    $datas['iinst']     = preg_replace('`^[0]*`','',$trame['IINST']); // intensité instantanée en A, on supprime les 0 en debut de chaine
    $datas['watt']      = $datas['iinst']*220; // intensite en A X 220 V

    if($db->busyTimeout(5000)){ // stock les donnees
      $db->exec("INSERT INTO puissance (timestamp, hchp, va, iinst, watt) VALUES (".$datas['timestamp'].", '".$datas['hchp']."', ".$datas['va'].", ".$datas['iinst'].", ".$datas['watt'].");");
    }

    return 1;
  }

  //
  //  enregistre la consommation en Wh
  //
  function handleConso () {
    global $sqlite;
    $db = new SQLite3($sqlite);
    $db->exec('CREATE TABLE IF NOT EXISTS conso (timestamp INTEGER, total_hc INTEGER, total_hp INTEGER, daily_hc REAL, daily_hp REAL);'); // cree la table conso si elle n'existe pas

    $trame     = getTeleinfo (); // recupere une trame teleinfo

    $today     = strtotime('today 00:00:00');
    $yesterday = strtotime("-1 day 00:00:00");

    // recupere la conso totale enregistree la veille pour pouvoir calculer la difference et obtenir la conso du jour
    if($db->busyTimeout(5000)){
      $previous = $db->query("SELECT * FROM conso WHERE timestamp = '".$yesterday."';")->fetchArray(SQLITE3_ASSOC);
    }
    if(empty($previous)){
      $previous = array();
      $previous['timestamp'] = $yesterday;
      $previous['total_hc']  = 0;
      $previous['total_hp']  = 0;
      $previous['daily_hc']  = 0;
      $previous['daily_hp']  = 0;
    }

    $datas = array();
    $datas['query']     = 'hchp';
    $datas['timestamp'] = $today;
    $datas['total_hc']  = preg_replace('`^[0]*`','',$trame['HCHC']); // conso total en Wh heure creuse, on supprime les 0 en debut de chaine
    $datas['total_hp']  = preg_replace('`^[0]*`','',$trame['HCHP']); // conso total en Wh heure pleine, on supprime les 0 en debut de chaine

    if($previous['total_hc'] == 0){
      $datas['daily_hc'] = 0;
    }
    else{
      $datas['daily_hc']  = ($datas['total_hc']-$previous['total_hc'])/1000; // conso du jour heure creuse = total aujourd'hui - total hier, on divise par 1000 pour avec un resultat en kWh
    }

    if($previous['total_hp'] == 0){
      $datas['daily_hp'] = 0;
    }
    else{
      $datas['daily_hp']  = ($datas['total_hp']-$previous['total_hp'])/1000; // conso du jour heure pleine = total aujourd'hui - total hier, on divise par 1000 pour avec un resultat en kWh
    }

    if($db->busyTimeout(5000)){ // stock les donnees
      $db->exec("INSERT INTO conso (timestamp, total_hc, total_hp, daily_hc, daily_hp) VALUES (".$datas['timestamp'].", ".$datas['total_hc'].", ".$datas['total_hp'].", ".$datas['daily_hc'].", ".$datas['daily_hp'].");");
    }
  }

  //
  //  recupere les donnees de puissance des $nb_days derniers jours et les met en forme pour les affichers sur le graphique
  //
  function getDatasPuissance ($nb_days) {
    global $sqlite;
    $months    = array('01' => 'janv', '02' => 'fev', '03' => 'mars', '04' => 'avril', '05' => 'mai', '06' => 'juin', '07' => 'juil', '08' => 'aout', '09' => 'sept', '10' => 'oct', '11' => 'nov', '12' => 'dec');
    $now  = time();
    $past = strtotime("-$nb_days day", $now);

    $db = new SQLite3($sqlite);
    $results = $db->query("SELECT * FROM puissance WHERE timestamp > $past ORDER BY timestamp ASC;");

    $sums = array();
    $days = array();
    $datas = array();

    while($row = $results->fetchArray(SQLITE3_ASSOC)){
      $year   = date("Y", $row['timestamp']);
      $month  = date("n", $row['timestamp']-1);
      $day    = date("j", $row['timestamp']);
      $hour   = date("G", $row['timestamp']);
      $minute = date("i", $row['timestamp']);
      $second = date("s", $row['timestamp']);
      $datas[] = "[{v:new Date($year, $month, $day, $hour, $minute, $second), f:'".date("j", $row['timestamp'])." ".$months[date("m", $row['timestamp'])]." ".date("H\hi", $row['timestamp'])."'}, {v:".$row['va'].", f:'".$row['va']." V.A'}, {v:".$row['watt'].", f:'".$row['watt']." kW'}]";

    }

    return implode(', ', $datas);
  }

  //
  //  recupere les donnees de consommation des $nb_days derniers jours et les met en forme pour les affichers sur le graphique
  //
  function getDatasConso ($nb_days) {
    global $sqlite;
    $months    = array('01' => 'janv', '02' => 'fev', '03' => 'mars', '04' => 'avril', '05' => 'mai', '06' => 'juin', '07' => 'juil', '08' => 'aout', '09' => 'sept', '10' => 'oct', '11' => 'nov', '12' => 'dec');
    $now  = time();
    $past = strtotime("-$nb_days day", $now);

    $db = new SQLite3($sqlite);
    $results = $db->query("SELECT * FROM conso WHERE timestamp > $past ORDER BY timestamp ASC;");

    $datas = array();

    while($row = $results->fetchArray(SQLITE3_ASSOC)){
      $day    = date("j", $row['timestamp'])." ".$months[date("m", $row['timestamp'])];
      $datas[] = "['".$day."', {v:".$row['daily_hp'].", f:'".$row['daily_hp']." kWh'}, {v:".$row['daily_hc'].", f:'".$row['daily_hc']." kWh'}]";
    }

    return implode(', ', $datas);
  }

