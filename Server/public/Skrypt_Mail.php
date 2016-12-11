<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include('../vendor/simple_html_dom.php');
include('../vendor/phpmailer/class.phpmailer.php');
error_reporting(E_ALL);


define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_DATABASE', 'mail'); // 'dlugi' - wierzyciele pf
define('DB_PASSWORD', 'rudolf');


$db = new PDO(
        "mysql:host=".DB_SERVER.";dbname=".DB_DATABASE, 
        DB_USERNAME, 
        DB_PASSWORD
    ); 

$mailer = new PHPMailer();
$mailer->IsSMTP(); // telling the class to use SMTP
$mailer->SMTPAuth      = true;				// enable SMTP authentication
$mailer->SMTPKeepAlive = true;        		// SMTP connection will not close after each email sent
$mailer->Host          = 'tts.poloniabytom.com.pl'; 		// sets the SMTP server
$mailer->Port          = '587';			// set the SMTP port for the GMAIL server
$mailer->Username      = 'biuro@tts.poloniabytom.com.pl';	// SMTP account username
$mailer->Password      = '195509m';	// SMTP account password
$mailer->CharSet       = 'UTF-8';
$mailer->SetFrom('biuro@tts.poloniabytom.com.pl', 'TTS POLONIA BYTOM');
$mailer->SMTPSecure = 'tls';
$mailer->AltBody  = "Towarzystwo Tenisa Stołowego Polonia Bytom poszukuje fim, osób, instytucji do współpracy i wsparcia finansowego klubu.";
$mailer->Subject = 'Oferta współpracy sponsorskiej z klubem sportowym tenisa stołowego Superligi.';

// $mails = $db->query('select * from `firmy` where `mail` is not null group by `mail`')->fetchAll();

//print_r($mails);

$i=0;


$tresc = '
<html>
    <head>
        <title>Oferta współpracy Towarzystwo Tenisa Stołowego Polonia Bytom</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            div {text-align: center; }
            h4 { font-family: helvetica; }
            p  { text-align: left; font-family: arial;  }
            p.content { text-align: justify; }
        </style>
    </head>
    <body>
        <div>
        
            <center><table style="width: 650px; border: 0;">
    <tr>
        <td>

            <H4><b>Szanowni Państwo,</b></H4>
            <p class="content">Mam zaszczyt zwrócić się do Państwa z propozycją współpracy z  klubem sportowym, którego pierwsza drużyna to jeden z najlepszych polskich zespołów tenisa stołowego - III m. w Polskiej Superlidze Tenisa Stołowego (najwyższej polskiej lidze). Towarzystwo Tenisa Stołowego Polonia Bytom to społeczna inicjatywa sportowa, która zrzesza dzieci, młodzież, rodziny, kibiców oraz profesjonalnych sportowców w rozgrywkach tenisowych poparta tradycjami sportowymi sięgającymi początków XXw (W 1912 roku powstała seksja tenisa stołowego w Bytomiu). </p>
            <p class="content">Współzawodnictwo wśród dzieci, młodzieży, dorosłych, uwydatnienie cech sportowych i propagowania zdrowego, sportowego stylu życia, integracja dzieci i młodzieży oraz rodzin województwa śląskiego, zachęca wszystkich do regularnego dbania o kondycję fizyczno-ruchową. Rozwijanie talentów sportowych oraz wywieranie motywacji do grania w tenisa stołowego poprzez rywalizację sportową młodzieży i dorosłych z różnych miast Górnego Śląska, a w szczególności organizacji turniejów i imprez sportowych, utrzymywanie sekcji sportowych  oraz zdobywanie najwyższych podium to podstawowe cele statutowe, które od lat konsekwentnie są realizowane odnosząc ogólnopolski sukces. </p>
            <p class="content">Podejmowane działania sportowe dostępne dla wszystkich wymagają właściwych nakładów finansowych oraz wsparcia ludzi dobrej woli, współpracy partnerów biznesowych, bez których nasze Towarzystwo nie mogłoby istnieć. Wskazując na pozytywne aspekty tenisa stołowego zwracamy się do Państwa z propozycją współpracy, która zapewni obopólne korzyści. Zalety partnerstwa przedstawiamy w dalszej części załączonej oferty.</p>
            <br>
            <p class="content"><b>Liczymy na Państwa wsparcie.</b></p>
            <p style="font-size: 13px;">
                <br><b>Prezentacja:</b><br>
                <a href="http://tts.poloniabytom.com.pl/oferta/">www.tts.poloniabytom.com.pl/oferta/</a><br>
                <a href="https://www.youtube.com/watch?v=Vfiq-N3kI30">http://www.youtube.com/watch?v=Vfiq-N3kI30</a>
                
                <br><br> <b>Dodatkowe imprezy towarzyszące: </b><br>- VI Bytomski Półmaraton
                <br><a href="https://www.youtube.com/watch?v=5WcdpCXr05g">https://www.youtube.com/watch?v=5WcdpCXr05g</a>
                
                <br><br>Wersja do pobrania:<br>
                <a href="http://tts.poloniabytom.com.pl/oferta/oferta-sponsorska.pdf">http://tts.poloniabytom.com.pl/oferta/oferta-sponsorska.pdf</a>
            </p>    
            <p style="text-align: left">Z wyrazami szacunku <br>i sportowymi pozdrowieniami
                <br><br>
                Michał Napierała<br>
                <img src="http://tts.poloniabytom.com.pl/oferta/image004.png">
            </p>            
            <p style="font-size: 13px;">
                <b>Towarzystwo Tenisa Stołowego Polonia Bytom</b><br>
                ul. Nickla 143A, 41-908 Bytom<br>
                tel. 502 617 335<br>
                <a href="http://tts.poloniabytom.com.pl">www.tts.poloniabytom.com.pl</a>
                <br>
                <a href="mailto:biuro@tts.poloniabytom.com.pl">biuro@tts.poloniabytom.com.pl</a><br>
                <a href="mailto:napierala.michal@interia.pl">napierala.michal@interia.pl</a><br>
                <br>
            </p>
        </td>
    </tr><tr>
        <td>
            <p style="font-size: 12px;">
                Zamieszczona oferta cenowa ma charakter informacyjny, nie stanowi oferty handlowej w rozumieniu Art.66 par.1 Kodeksu Cywilnego
            </p>
        </td>
    </tr> 
</table></center>
        
        </div>
    </body>
</html>';

$mailer->MsgHTML($tresc);
$mailer->AddBCC('marcin@zwiazek.net');
$mailer->AddAttachment('./Oferta-sponsorska-TTS-Polonia-Bytom.pdf', 'Oferta-Sponsorska-TTS-Polonia-Bytom.pdf');
/*
$ilosc = sizeof($mails);
$i=0;


foreach ($mails as $mail ){
  if($mail['mail']){
    //$mailer->AddAddress($mail['mail']);  
    $mailer->Send();
    $mailer->ClearAddresses(); 
    $mailer->ClearAttachments(); 
  }
  system('clear');
  $i++;
  echo ($i/$ilosc)*100;
  echo ' %';
}

*/
//$mailer->AddAddress('sekretariat@zabka.pl');
//$mailer->AddAddress('marketing@drutex.com.pl');
//$mailer->AddAddress('sekretariat@drutex.com.pl');
//$mailer->AddAddress('reklama@tesco.pl');
//$mailer->AddAddress('m.zarnowski@kghm.pl'); //Mariusz Żarnowski tel. 76 7478 227
//$mailer->AddAddress('regnon@regnon.com');\
//$mailer->AddAddress('lukasz.ostrowski@zmt.tarnow.pl');
//$mailer->AddAddress('justyna.zach@zmt.tarnow.pl');
//$mailer->AddAddress('sekretariat@uzdrowisko-konstancin.pl');
//$mailer->AddAddress('sekretariat@intraco.pl');
//$mailer->AddAddress('mkwiatek@bumar-mikulczyce.pl');
//$mailer->AddAddress('marketing.zapalki@pcc.eu');
//$mailer->AddAddress('sekretariat@polmos.bielsko.pl');
//$mailer->AddAddress('zgorniak@hutalab.com.pl'); // Huta Łabendy
//$mailer->AddAddress('blewandowski@hutalab.com.pl'); // Huta Łabendy
//$mailer->AddAddress('biuro@zmuw.eu');
//$mailer->AddAddress('biuro@ehnsa.eu');
//$mailer->AddAddress('gajdzinski.adam@gmail.com');//Wellux S.A.
//$mailer->AddAddress('kamilgajdzinski@gmail.com');//Wellux S.A.
//$mailer->AddAddress('azoty-adipol@azoty-adipol.pl');
//$mailer->AddAddress('sekretariat@pks-katowice.pl');
//$mailer->AddAddress('zarzad@grupaazoty.com');
//$mailer->AddAddress('zarzad.zak@grupaazoty.com');
//$mailer->AddAddress('zarzad.tarnow@grupaazoty.com');
//$mailer->AddAddress('ecbedzin@ecb.com.pl');
//$mailer->AddAddress('sekretariat@ecb.com.pl');
//$mailer->AddAddress('info@elektrocarbon.pl');
//$mailer->AddAddress('j.rzytka@stradom.com.pl');
//$mailer->AddAddress('info@hutapokoj.eu');
//$mailer->AddAddress('elzbieta.karcz@tauron-pe.pl');
//$mailer->AddAddress('partnerstwostrategiczne@pzu.pl');
//$mailer->AddAddress('oc@wars.pl');
//$mailer->AddAddress('ewa.sobczyk@azoty-adipol.pl');
//$mailer->AddAddress('marketing@m-w.com.pl');
//$mailer->AddAddress('m-w@m-w.com.pl');
//$mailer->AddAddress('biuro@ham.com.pl');
//$mailer->AddAddress('biuro@zmparuzel.pl');
//$mailer->AddAddress('csr@grupalotos.pl');
//$mailer->AddAddress('kontakt@grupaazoty.com');

$mailer->Send();


