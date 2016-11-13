<?php

if ($_GET['plik']) {
    $filehtml = 'files/' . $_GET['plik'] . '.html';
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($filehtml));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filehtml));
    readfile($filehtml);
    exit;
}


if (!$_POST) {
    include './form.html';
} else {
    if ($_POST['firma'] == "1") {
        $file = 'avoria.html';
    } elseif ($_POST['firma'] == "2") {
        $file = 'pflegerin.html';
    } elseif ($_POST['firma'] == "3") {
        $file = "sanvita.html";
    } else {
        echo "brak oznaczonej stopki / pliku";
        die;
    }

    ob_start();
    ob_implicit_flush(0);
    include $file;
    $contents = ob_get_contents();
    ob_end_clean();

    echo $contents;

    $namefile = md5(date('Y-m-d H:i:s'));
    $filehtml = 'files/' . $namefile . '.html';

    file_put_contents($filehtml, $contents);
    echo "<br>";
    echo '<a href="?plik=' . $namefile . '" style="font-size: 20px;"><button>POBIERZ STOPKE</button></a>';
}
?>