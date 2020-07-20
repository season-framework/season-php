<?php

class crypto {
    
    private $key;
    private $cipher;
    private $mode;

    public function encode($message) {
        $key = $this->key;
        $cipher = $this->cipher;

        $iv_length = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($iv_length);
    
        for( $i = 0 ; $i < $iv_length ; $i++ )
            $message = " " . $message;

        $ciphertext = openssl_encrypt($message, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($ciphertext); 
    }

    public function decode($message) {
        $key = $this->key;
        $cipher = $this->cipher;
        $message = base64_decode($message);

        $iv_size = openssl_cipher_iv_length($cipher);
        $iv = substr($message, 0, $iv_size);
        $message = substr($message, $iv_size);

        $message = openssl_decrypt($message, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        return $message; 
    }

    public function load($key, $cipher = "aes128") {
        $this->key = $key;
        $this->cipher = $cipher;
        return $this; 
    }

}

