<?php
    echo "Podnosze";
    echo '<br>';
    $message=shell_exec("./system 2>&1");
    echo 'Result: <br>';
    print_r($message);
?>