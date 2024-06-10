<?php

define('ERROR_FILE', 'data/errors.json');
$exepath = dirname(__FILE__);
define('BASE_PATH', dirname(realpath(dirname(__FILE__))));
chdir($exepath);

$content = "";

function displayLine($error) {
    $record = "";
    $record .= "<h2>" . $error->datetime->format('Y-m-d H:i:s') . "   " . $error->domain . "</h2>";
    $record .= "<ul><li>Action: " . $error->action . "</li>";
    $record .= "<li>Error: " . str_replace("&amp;", "&", $error->error) . "</li></ul>";
    $trace = $error->trace;
    if ($trace !== null) {
        $record .= "<details><summary>Trace</summary>";
        $record .= displayTrace($trace);
        $record .= "</details>";
    }

    return $record;
}

function displayTrace($trace) {
    $out = "";
    $out .= "<ol>";
    foreach ($trace as $item) {

        $out .= displayTraceItem($item);
    }
    $out .= "</ol>";
    return $out;
}

function displayTraceItem($line) {
    $out = "<li>";
    $out .= $line->function . " called at " . $line->file . " : " . $line->line;
    $out .= "</li>";
    return $out;
}

$errorsJson = file_get_contents(ERROR_FILE);
$errors = json_decode($errorsJson);
unset($errorsJson);
$i = 0;
foreach ($errors as $error) {
    $error->datetime = new DateTime($error->datetime->date);
    $content .= displayLine($error);
    $i += 1;
    if ($i > 100) {
        $content .= "<p>....</p>";
        break;
    }
}

$page = file_get_contents("html/template.html");

echo str_replace("//[CONTENT]", $content, $page);
