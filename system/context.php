<?
    if ( !defined("_NB_VALID_") ) exit;

    class Context {
        private static $_instance = null;

        private $frags     = [];

        private $_get      = [];
        private $_post     = [];
        private $_files    = [];
        private $cookies   = [];
        private $sess      = [];
        private $serv      = [];
        private $headers   = [];

        public  $is_ajax   = false;
        public  $is_https  = false;

        public static function instance() {
            if (Context::$_instance !== null)
                return Context::$_instance;

            Context::$_instance = new Context();
        }

        function deserialize_url($url) {
            $frags = explode("/", $url);
            $ret   = [];

            foreach ($frags as $frag) {
                $frag = trim($frag);

                if (($frag) === null) {
                    http_response_code(400);
                    exit;
                }

                array_push($ret, $frag);
            }

            return $ret;
        }

        function deserialize_query($params) {
            $ret       = [];

            // begin parse query params from query string
            if ($params !== NULL) {
                $params = explode("&", $params);
                foreach ($params as $param) {
                    if (!strpos($param, "="))
                        continue;

                    list($key, $val) = explode("=", $param, 2);
                    $param_key = $key;

                    if ($param_key === null)
                        continue;

                    $ret[$param_key] = $val;
                }
            }

            return $ret;
        }

        function deserialize_body() {
            $body = file_get_contents("php://input");
            return json_decode($body,
                                associative: true,
                                flags: JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        function deserialize_files() {
            if (count($_FILES) === 0)
                return null;

            $ret = [];

            foreach($_FILES as $key => $value) {
                $file = [];

                if (is_array($value)) {
                    foreach ($value as $subkey => $subvalue) {
                        $file[$key][$subkey] = $subvalue;
                    }
                } else {
                    $file[$key] = $value;
                }
            }

            return $ret;
        }

        function __construct() {
            if (key_exists("QUERY_STRING", $_SERVER)) {
                $query_src = $_SERVER['QUERY_STRING'];

                if (strpos($query_src, "?")) {
                    list($url, $params) = explode("?", $query_src, 2);
                } else {
                    list($url, $params) = [ $query_src, NULL ];
                }

                $this->frags   = $this->deserialize_url($url);
                $this->_get    = $this->deserialize_query($params);
            }

            $this->_post   = $this->deserialize_body();
            $this->_files  = $this->deserialize_files();
            $this->cookies = $_COOKIE;
            $this->sess    = $_SESSION;
            $this->headers = getallheaders();

            $this->serv     = $_SERVER;
            $this->is_ajax  = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            $this->is_https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

            // disable superglobals
            unset($_GET, $_POST, $_REQUEST,
                  $_FILES, $HTTP_RAW_POST_DATA, $_COOKIE,
                  $_SERVER, $_SESSION);
        }

        public function url_frag($idx) {
            if ($idx > count($this->frags) - 1) {
                return null;
            }

            return $this->frags[$idx];
        }

        public function get($name) {
            if ($name === null)
                return null;

            return key_exists($name, $this->_get) ? $this->_get[$name] : null;
        }

        public function post($name) {
            if ($name === null)
                return null;

            return key_exists($name, $this->_post) ? $this->_post[$name] : null;
        }

        public function file($name) {
            if ($name === null || !key_exists($name, $this->_files))
                return null;

            // TODO: check if requested file field contains multiple files or not
            return key_exists($name, $this->_files) ? $this->_files[$name] : null;
        }

        public function getCookie($name) {
            if ($name === null)
                return null;

            return key_exists($name, $this->cookies) ? $this->cookies[$name] : null;
        }

        public function setCookie($name, $value, $secured) : bool {
            return $name !== null && setcookie($name, $value, httponly: $secured, secure: $secured);
        }

        public function setCookieExpired($name) : bool {
            return $name !== null && setcookie($name, expires_or_options: time());
        }
    }