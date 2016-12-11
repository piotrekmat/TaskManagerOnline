<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(0);
include('simple_html_dom.php');


define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_DATABASE', 'dlugi');
define('DB_PASSWORD', 'rudolf');


$db = new PDO(
        "mysql:host=".DB_SERVER.";dbname=".DB_DATABASE, 
        DB_USERNAME, 
        DB_PASSWORD
    ); 



$strona = 1;
$maxsite = 13;
$firm=0;

while($strona <= $maxsite){
    
    $link = 'http://panoramafirm.pl/d%C5%82ugi,_wierzytelno%C5%9Bci_-_obs%C5%82uga,_obr%C3%B3t/%C5%9Bl%C4%85skie/firmy,'.$strona.'.html';
    sprintf($link);
    $opts = array(
      'http'=>array(
        'method'=>"GET"
      )
    );
    
    $options = stream_context_create($opts);
    //$file = file_get_contents($link, false, $options);

    $sql = null;
    $file = file_get_html($link);

    $li = $file->find('li.vcard');
    $il = sizeof($li);
    $i=0;
    foreach ($li as $element){
        $nazwa      = "'".strip_tags($element->find('a.addax-cs_hl_hit_company_name_click',0)->plaintext)."'";
        $tab[$i]['nazwa']           = $nazwa;
        $tab[$i]['adres']           = "'".strip_tags($element->find('div.contacts',0)->plaintext)."'";
        $tab[$i]['mail']            = "'".strip_tags($element->find('a.icon-mail',0)->plaintext)."'";
        $tab[$i]['www']             = "'".strip_tags($element->find('a.icon-link-ext',0)->plaintext)."'";
        $tab[$i]['kategoria']       = "'".strip_tags($element->find('div.tradeName',0)->plaintext)."'";
        $i++;
    }
    $firm += $i;
    
    $data = array();
    foreach($tab as $d){
        $data[] = '('.implode(',', $d).')';
    }

    $sql = "INSERT INTO firmy ( nazwa, adres, mail, www, kategoria ) VALUES ". implode(',', $data);
    $db->query($sql);
    
    system('clear');
    echo 'Wykonano: [%]'.($strona/$maxsite)*100;
    echo ' [Ilosc firm: '.$firm.']';
    
    
    $strona++;
}

