<?
    if ( !defined("_NB_VALID_") ) exit;


    class admin_controller extends controller {
        public function index() {
            return $this->controller_name;
        }
    }