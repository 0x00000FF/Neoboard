<?
    if ( !defined("_NB_VALID_") ) exit;

    define("KDF_ITERATION", 100000);

    function create_password($plaintext, $salt = NULL) {
        if ($salt !== NULL) {
            $salt = base64_decode($salt);
        } else {
            $salt = random_bytes(16);
        }

        $hash = hash_pbkdf2('sha256', $plaintext, $salt, KDF_ITERATION, 32, binary: true);
        return [
            base64_encode($hash),
            base64_encode($salt)
        ];
    }