<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        echo "这是首尔";
    }

}
