<?php
class erroritem {

    // property declaration
    public $datetime;
    public $domain;
    public $action;
    public $error;
    public $trace;

    // method declaration
    public function __construct($domain, $action, $error, $trace) {
        $this->datetime = new DateTime("now");
        $this->domain = $domain;
        $this->action = $action;
        $this->error = $error;
        $this->trace = $trace;
    }
}
