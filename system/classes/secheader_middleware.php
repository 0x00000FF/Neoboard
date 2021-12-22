<?
    if ( !defined("_NB_VALID_") ) exit;

    require_once(__DIR__ . "/config/sec_header.php");

    class secheader_middleware implements middleware {
        private $response;

        public function __construct($response) {
            $this->response = $response;
        }

        // Todo: load secrurity header information from sec_header.php and emit
        private function create_header_list() {
            return [];
        }

        public function pass() {
            if (!SEC_HEADER_ENABLED)
                return;

            $this->response->headers = array_merge(
                $this->response->headers,
                $this->create_header_list()
            );
        }
    }