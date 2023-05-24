<?php
require_once __DIR__ . "/../scripts/connect.php";

function getLicenseInfo($key)
{
    global $link;
    $stmt = $link->prepare("SELECT * FROM `licenses` WHERE `key`= ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        return new LicenseInfo($res, $key);
    } else {
        return null;
    }
}

class LicenseInfo
{
    private $res;
    private $key;

    function __construct($result, $key)
    {
        $this->res = $result;
        $this->key = $key;
    }

    function handleIp($usrIP)
    {
        global $link;
        $passed = false;

        $currIPs = $this->fetchCurrIPs();
        $lastRef = $this->fetchLastRef();
        $ips = $this->fetchIPs();

        $arrIPs = array();
        $arrRef = array();

        if ($currIPs) {
            #echo "<br/> Found CurrIPs";
            $arrIPs = explode('#', $currIPs);
            $arrRef = explode('#', $lastRef);

            for ($entryId = count($arrIPs) - 1; $entryId >= 0; $entryId--) {
                if ($arrRef[$entryId] < (time() - 900)) {
                    #echo "<br/> Deleted outdated IP ".$entryId." - ".$arrIPs[$entryId];
                    unset($arrRef[$entryId]);
                    unset($arrIPs[$entryId]);
                } else {
                    #echo "<br/>Diff of IP ".$arrIPs[$entryId]." is ".((time()-900));
                }
            }

            for ($entryId = 0; $entryId < count($arrIPs); $entryId++) {
                if ($arrIPs[$entryId] == $usrIP) {
                    #print_r($arrRef);
                    #echo "<br/> Updated IP-Time";
                    $arrRef[$entryId] = time();
                    #print_r($arrRef);
                    $passed = true;
                }
            }


            if (!$passed and count($arrIPs) < $ips) {
                #echo "<br/> Added user-ip";
                array_unshift($arrIPs, $usrIP);
                array_unshift($arrRef, time());
                $passed = true;
            }
        } else {
            #echo "<br/> Force added user-ip";
            array_unshift($arrIPs, $usrIP);
            array_unshift($arrRef, time());
            $passed = true;
        }

        #echo "<br/> Passed = ".$passed;

        $stmt = $link->prepare("UPDATE `licenses`
                   SET `currIPs` = ?,
                       `lastRef` = ?
                   WHERE `key`= ?");

        $ipsImp = implode("#", $arrIPs);
        $refImp = implode("#", $arrRef);

        $stmt->bind_param("sss", $ipsImp, $refImp, $this->key);
        $stmt->execute();

        return $passed;
    }

    function validateExpiry()
    {
        $expiry = $this->fetchExpiry();
        return time() < $expiry or $expiry == -1;
    }

    function validateBound($pluginName)
    {
        return $this->fetchPlBound() == 0 or $this->fetchPlName() == $pluginName;
    }

    private function fetch($field)
    {
        return mysqli_result($this->res, 0, $field);
    }

    function fetchExpiry()
    {
        return $this->fetch('expiry');
    }

    function fetchPlBound()
    {
        return $this->fetch('plBound');
    }

    function fetchPlName()
    {
        return $this->fetch('plName');
    }

    function fetchCurrIPs()
    {
        return $this->fetch('currIPs');
    }

    function fetchLastRef()
    {
        return $this->fetch('lastRef');
    }

    function fetchIPs()
    {
        return $this->fetch('ips');
    }

}