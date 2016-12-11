<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">

    </head>
    <body>
        <?php
        /**
         * 
         * @project: System partnerski SIFT
         * @author: Marcin Związek
         * 
         */
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', '1');


        include('../../vendor/simple_html_dom.php');
        include('../../vendor/phpmailer/class.phpmailer.php');


        $wyslane = [
            'm.pieczka@krakow-adwokat.com',
            'biuro@kancelariadyduch.pl',
            'kancelaria@adwokat-agacka.pl',
            'kancelaria@adwokatgrzywacz.pl',
            'kancelaria@lisicki.com',
            'kancelaria@hirszberg.pl',
            'kancelaria.mietelska@gmail.com',
            'biuro@omnia-kancelaria.pl',
            'kancelaria@kdw-adwokat.pl',
            'kancelaria@karonprawo.pl',
            'kancelaria@mglegal.pl',
            'rolicz@adwokat-slask.com',
            'biuro@dkk-kancelaria.pl',
            'mk@adwokat-korzeniewska.pl',
            'sekretariat@kancelaria-wylag.com',
            'kancelaria@radcaprawny-tychy.pl',
            'biuro@kancelariachorzow.pl',
            'kancelaria@grupalextax.pl',
            'katowice@zubek-gatner.pl',
            'kancelaria@adwokatbaranowski.pl',
            'biuro@kancelaria-szczepanski.com',
            'kancelaria@adwokatmusielak.pl',
            'kancelaria@adwokacitychy.pl',
            'adwokat.majagalas@gmail.com',
            'kancelaria@adwokatcebo-kubiczek.pl',
            'jan24@interia.eu',
            'm.knefel@gazeta.pl',
            'radcaprawny@kancelaria-bjk.pl',
            'ruchniak7@gmail.com',
            'g.wojcik@kancelaria-wojcik.pl',
            'ep@piotrowskaewa.pl',
            'sekretariat@bkmiw.pl',
            'adwokat@kidawa.pl',
            'sekretariat@kancelariapiotrowscy.pl',
            'adwokat@danielewska.pl',
            'jadwiga.pazdan@adwokatura.pl',
            'kancelaria@legali.pl',
            'sekretariat@radcy-prawni.com.pl',
            'magdalena@durczok.eu',
            'cholderny@kancelaria-cognitor.pl',
            'biuro@adwokatAGK.pl',
            'kancelaria@adwokatbytom.com.pl',
            'msz@adwokatbedzin.pl',
            'kancelaria@sluzalek.pl',
            'kancelaria@malegal.pl',
            'adwokat@murbanska.pl',
            'nocta1@wp.pl adw.sokolowska@gmail.com',
            'LCG.mdudala@onet.eu',
            'kancelaria@adwokathonorowicz.pl',
            'oskar@zlotowski.com.pl',
            'kancelaria.rmachecki@gmail.com',
            'amkrasuska@madonet.com.pl',
            'modus@modus.wroc.pl',
            'zkasznia@kancelariamkk.eu',
            'abprawnik@tlen.pl',
            'pawlowski@advokat.pl',
            'zelaznyj@poczta.onet.pl',
            'adw.janina.dudek@neostrada.pl',
            'Mitas@mitas-ka.pl',
            'apogoda@pzlegal.pl',
            'cz.kotas@op.pl',
            'k.kwiatkowska-kozik@adwokat.lex.pl',
            'blachtaj1@poczta.onet.pl',
            'kancelaria@kancelaria-mss.eu',
            'b.jakubczyk@sobien-jakubczyk.pl',
            'kancelaria@kzpa.pl',
            'sekretariat@kancelarialekston.pl',
            'aleksander.franik@adwokatura.pl',
            'adwokat_kumor_agnieszka@poczta.onet.pl',
            'kozlowska.kancelaria@op.pl',
            'urbanik@katowce.home.pl',
            'sekretariat@cwkh.pl',
            'kancelaria@mamyprawo.pl',
            'kkp@kkplegal.com',
            'prawobiznesu@prawobiznesu.com.pl',
            'krk@lawchambers.pl',
            'kancelaria_barda@poczta.onet.pl',
            'kancelaria@hot.pl',
            'kancelaria@rusecki.pl',
            'kancelaria@adwokaci-radcyprawni.pl',
            'kancelaria@durajreck.com',
            'adwokat.czechowicz@op.pl',
            'biuro@bonafides.com.pl',
            'kancelaria_adwokacka@op.pl',
            'wnielacny@nielacny.pl',
            'adw.karina.fajer@kancelaria-adwokacka.slask.pl',
            'zgryzek.mateusz@gmail.com',
            'adw@vp.pl',
            'kancelaria.mgorski@gmail.com',
            'maria.kaszyk@adwokatura.pl',
            'zygmut.ociepka@neostrada.pl',
        ];

        try {

            $mailer = new PHPMailer();
            $mailer->IsSMTP(); // telling the class to use SMTP
            $mailer->SMTPAuth = true;    // enable SMTP authentication
            $mailer->SMTPKeepAlive = true;          // SMTP connection will not close after each email sent
            $mailer->Host = 'biuro.comescon.pl';   // sets the SMTP server
            $mailer->Port = '587';   // set the SMTP port for the GMAIL server
            $mailer->Username = 'biuro'; // SMTP account username
            $mailer->Password = 'zaq1@wsx'; // SMTP account password
            $mailer->CharSet = 'UTF-8';
            $mailer->SetFrom('biuro@comescon.pl', 'Comes Consulting Group Wycena przedsiębiorstw');
            //$mailer->SMTPSecure = 'tls';
            $mailer->AltBody = "Głównym zadaniem firmy Comes Consulting Group jest wycena przedsiębiorstw nieruchomości ruchomości i innych rzeczy w zależności od potrzeb Zleceniodawcy. Wyceny są dokonywane dla potrzeb osób fizycznych prawnych. Szacujemy wszystkie rodzaje nieruchomości  przedsiębiorstw środków trwałych od 2003r. ";
            $mailer->Subject = 'Oferta współpracy dla wyceny przedsiębiorstw spółek akcji i nieruchomości.';

// $mails = $db->query('select * from `firmy` where `mail` is not null group by `mail`')->fetchAll();
//print_r($mails);
//$csv = str_getcsv('./file.csv' ';');
            $data = array_map('str_getcsv', file('file2.csv'));
            foreach ($data as $row) {
                $csv = explode(';', $row[0]);
                if (isset($csv[2]) && !empty($csv[2]) && !in_array($csv[2], $wyslane)) {
                    ob_start();
                    ob_implicit_flush(0);
                    include('./index.phtml');
                    $tresc = ob_get_contents();
                    ob_end_clean();
                    var_dump($csv[2]);
                    //echo $tresc;
                    //$mailer->MsgHTML($tresc);
                    //$mailer->AddAddress($csv[2]);
                    //$mailer->AddBCC('angelika@comescon.pl');
                    //$mailer->Send();
                    //$mailer->ClearAllRecipients();
                }
            }
            echo 'KONIEC';
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }



        