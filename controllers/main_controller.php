<?
    if ( !defined("_NB_VALID_") ) exit;

    include_once _NB_ROOT_ . "/models/sample.php";

    class main_controller extends controller {
        public function index() {
            return $this->controller_name;
        }
    }