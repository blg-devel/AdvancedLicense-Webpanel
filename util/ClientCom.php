<?php

class ClientCom
{
    private $sKey;
    private $rand;

    function __construct($v1, $key)
    {
        $this->sKey = $this->toBinary($key);
        $this->rand = $this->_xor($v1, $this->sKey);
    }


    function decrypt($encryptedBin)
    {
        return $this->fromBinary($this->decryptToBin($encryptedBin));
    }

    function decryptToBin($encryptedBin)
    {
        return $this->_xor($encryptedBin, $this->rand);
    }


    function encrypt($text)
    {
        return $this->encryptBin($this->toBinary($text));
    }

    function encryptBin($textBin)
    {
        return $this->_xor($this->rand, $this->_xor($textBin, $this->sKey));
    }

    function toBinary($value = 'none')
    {
        $str = "";
        $a = 0;
        while ($a < strlen($value)) {
            $str .= sprintf("%08d", decbin(ord(substr($value, $a, 1))));
            $a++;
        }
        return $str;
    }

    function fromBinary($value = '00100001')
    {
        $str = "";
        $a = 0;
        while ($a < strlen($value)) {
            $str .= chr(bindec(substr($value, $a, 8)));
            $a = $a + 8;
        }
        return $str;
    }

    private function _xor($text, $key)
    {
        $textLength = strlen($text);
        $keyLength = strlen($key);

        $length = min($textLength, $keyLength);

        for ($i = 0; $i < $length; $i++) {
            $text[$i] = intval($text[$i]) ^ intval($key[$i]);
        }

        return $text;
    }
}