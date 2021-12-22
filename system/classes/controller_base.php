<?
    if ( !defined("_NB_VALID_") ) exit;

    class controller_base {
        protected $current_context = NULL;
        protected $controller_name;

        public function __construct($context) {
            $this->current_context = $context;
            $this->controller_name = get_class($this);
        }
    }