<?php
define('ERROR_FILE', 'data/errors.json');
$test = false;
error_reporting(E_ALL);
ini_set('display_errors', 1);
$exepath = dirname(__FILE__);
define('BASE_PATH', dirname(realpath(dirname(__FILE__))));
chdir($exepath);
require 'classes/autoload.php';
spl_autoload_register('autoload');


Logfile::create("logfiles/errors");
Logfile::writeWhen("Start");
$opts = new Options();
Logfile::writeWhen('PHP Version: ' . \PHP_VERSION);
if (version_compare(PHP_VERSION, '8.2.0') < 0) {
    Logfile::writeError('You MUST be running on PHP version 8.2.0 or higher.');
    http_response_code(500);
    die();
}
if ($test) {
    $domain = $opts->gets("domain");
    $action = $opts->gets("action");
    $error = $opts->gets("error");
    $trace = null;
} else {
    $domain = $opts->posts("domain");
    $action = $opts->posts("action");
    $error = $opts->posts("error");
    $trace = $opts->posts("trace");
    if ($trace !== null) {
        $trace = json_decode(str_replace("&quot;", '"', $trace));
    }
}
Logfile::writeWhen("Domain: " . $domain);
Logfile::writeWhen("Action: " . $action);
Logfile::writeWhen("Error: " . $error);
Logfile::writeWhen("Trace: " . $trace);
if ($domain == NULL) {
    Logfile::writeError("Invalid options - no Domain specified");
    http_response_code(400);
    die();
}

if ($action == NULL) {
    Logfile::writeError("Invalid options - no Action specified");
    http_response_code(400);
    die();
}

if ($error == NULL) {
    Logfile::writeError("Invalid options - no Error specified");
    http_response_code(400);
    die();
}

try {

    $item = new erroritem($domain, $action, $error, $trace);

    if (file_exists(ERROR_FILE)) {
        Logfile::writeWhen("Error.json: file exists");
        $errorsJson = file_get_contents(ERROR_FILE);
        Logfile::writeWhen("Error.json: read");
        $errors = json_decode($errorsJson);
        Logfile::writeWhen("Error.json: decoded");
        while (count($errors) > 200) {
            Logfile::writeWhen("Error.json: removing excess items");
            array_pop($errors);
        }
    } else {
        Logfile::writeWhen("Error.json file does not exist, will create new file");
        $errors = [];
    }
    Logfile::writeWhen("Error.json: adding new item");
    array_unshift($errors, $item); // add to start of array
    Logfile::writeWhen("Error.json: writing new file");
    file_put_contents(ERROR_FILE, json_encode($errors));
    Logfile::writeWhen("Process complete");
    http_response_code(200);
} catch (Exception $exc) {
    Logfile::writeError("Exception occurred during processing");
    Logfile::writeError($exc->getMessage());
    Logfile::writeError($exc->getTraceAsString());
}
Logfile::writeWhen("Closing");
Logfile::close();
