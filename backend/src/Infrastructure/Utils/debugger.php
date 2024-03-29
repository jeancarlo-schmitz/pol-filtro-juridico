<?php

function pre($x, $titulo = '', $exit = false) {
    ob_implicit_flush();
    $pid = getmypid();
    echo "<fieldset style='min-width: 50%; word-wrap: break-word; background-color: #FAFAFA; border: 2px groove #ddd !important; padding: 1.4em 1.4em 1.4em 1.4em !important;'>";
    if (!empty($titulo)) {
        echo "<legend style='color:rgb(0, 0, 123); padding: 3px 10px 3px 10px; font-weight: bold; font-size: 14px; text-transform: uppercase; border: 1px groove #ddd !important;'> $titulo </legend>";
    }
    echo "<pre>";
    echo "----------------------------\r\nProcesso PID: {$pid}\r\n----------------------------\r\n\r\n";
    print_r($x);
    echo "</pre>";
    echo "</fieldset>";
    ob_flush();
    flush();
    if ($exit) {
        exit;
    }
}

function pred($x, $titulo = '') {
    pre($x, $titulo, true);
}