<?php

require_once __DIR__ . "/RandomUtil.php";
require_once __DIR__ . "/LicenseInfo.php";
require_once __DIR__ . "/../scripts/connect.php";

const TOKEN_EXPIRY_TIME = 30;

function getNewToken($license, $userIP)
{
    removeExpired();
    return createToken($license, $userIP);
}

function createToken($license, $userIP)
{
    $token = generateTokenString();
    $expiry = time() + TOKEN_EXPIRY_TIME;
    saveToken($token, $license, $userIP, $expiry);
    return $token;
}

function saveToken($token, $license, $userIP, $expiry)
{
    global $link;
    $stmt = $link->prepare("INSERT INTO `user_tokens` (`token`, `ip`, `license`, `expiry`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $token, $userIP, $license, $expiry);
    $stmt->execute();
}

function validateToken($token, $ip, $product)
{
    $token = getTokenInfo($token);

    if ($token == null) return "TOKEN_NOT_FOUND";
    if (!$token->validateExpiry()) return "TOKEN_EXPIRED";
    if (!$token->validateIP($ip)) return "INVALID_IP";
    if (!$token->getAssociatedLicenseInfo()->validateBound($product)) return "INVALID_PRODUCT";

    return "VALID";
}

function removeExpired()
{
    global $link;
    $stmt = $link->prepare("DELETE FROM `user_tokens` WHERE `expiry` < ?");
    $expiredTime = time() - 1;
    $stmt->bind_param("i", $expiredTime);
    $stmt->execute();
}

function generateTokenString()
{
    return generateRandomString(31);
}

function getTokenInfo($token)
{
    global $link;
    $stmt = $link->prepare("SELECT * FROM `user_tokens` WHERE `token` = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        return new TokenInfo($res, $token);
    }
    return null;
}

class TokenInfo
{
    private $result;
    private $token;

    function __construct($result, $token)
    {
        $this->result = $result;
        $this->token = $token;
    }

    function getAssociatedLicenseInfo()
    {
        return getLicenseInfo($this->fetchLicense());
    }

    function validateIP($ip)
    {
        return $this->fetchIP() == $ip;
    }

    function validateExpiry()
    {
        return $this->fetchExpiry() > time();
    }

    private function fetch($field)
    {
        return mysqli_result($this->result, 0, $field);
    }

    function fetchExpiry()
    {
        return $this->fetch("expiry");
    }

    function fetchIP()
    {
        return $this->fetch("ip");
    }

    function fetchLicense()
    {
        return $this->fetch("license");
    }

}