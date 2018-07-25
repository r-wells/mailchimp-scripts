<?php

function memory($size) {
    $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

function save_as($path, $file_content) {
    $file = fopen($path, "w");
    fwrite($file, $file_content);
    fclose($file);
}

function load_models() {
    $models = scandir(ABSPATH . '/app/models');
    foreach ($models as $model) {

        if (strlen($model) > 3) {
            $file = ABSPATH . '/app/models/' . $model;
            //pretty("Loading {$file}");
            include($file);
        }
    }
}

function pretty($thing = false) {
    if ($thing) {
        if (is_array($thing) || is_object($thing)) {
            print "<pre>\n";
            print_r($thing);
            print "</pre>\n";
        } else {
            print "<pre>\n";
            print $thing;
            print "</pre>\n";
        }
    }
}

function convert_to_array($data) {

    if (is_object($data)) {
        $data = get_object_vars($data);
    }

    if (is_array($data)) {
        return array_map('convert_to_array', $data);
    }
    return $data;
}

function safe_redirect($url) {
    ob_start();
    ob_clean();
    ob_end_clean();
    header('Location: ' . $url);
    exit;
}

function is_session_started() {
    if (php_sapi_name() !== 'cli') {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            return session_status() === PHP_SESSION_ACTIVE ? true : false;
        } else {
            return session_id() === '' ? false : true;
        }
    }
    return false;
}
