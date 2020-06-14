<?php

namespace Core;

class Encrypt{

    protected $key;
    protected $hash = 'AES-128-CBC';


    public function getKey(){
        require_once(ROOT.DS.'App'.DS.'Config'.DS.'application.php');
        
        $this->key = md5(ENCODE_KEY);
    }

    public function encode($string){
        
        $this->getKey();

        $ivlen = openssl_cipher_iv_length($this->hash);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($string, $this->hash, $this->key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->key, $as_binary=true);
        $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
        
        return $ciphertext;
    }

    public function decode($string){
    
        $c = base64_decode($string);
        $ivlen = openssl_cipher_iv_length($this->hash);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $this->hash, $this->key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->key, $as_binary=true);
        
        if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
        {
            return $original_plaintext."\n";
        }

    }
}