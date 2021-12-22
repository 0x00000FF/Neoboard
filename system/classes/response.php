<?
    if ( !defined("_NB_VALID_") ) exit;

    class response {
        public $status_code = 200;
        public $headers     = [];
        public $body        = NULL;

        public static function dispatch_action($context, $controller, $action) {
            $response = new response();

            try {
                if (!class_exists($controller) || !is_subclass_of($controller, "controller_base")) {
                    $response->status_code = 404;
                } else {
                    $ctrl_instance   = new $controller($context);
                    $response->body  = $ctrl_instance->$action();
                }
            } catch (Exception $ex) {
                $response->status_code = 500;
            }

            return $response;
        }

        public function middleware($class) {
            if (!class_exists($class) || !is_subclass_of($class, "middleware")) {
                $this->status_code = 500;
            } else {
                $middleware = new $class($this);
                $middleware->pass();
            }

            return $this;
        }

        public function emit() {
            http_response_code($this->status_code);

            foreach ($this->headers as $name => $val) {
                header("$name: $val");
            }

            if ($this->body !== NULL)
                echo $this->body;
        }
    }