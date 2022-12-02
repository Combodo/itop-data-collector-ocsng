<?php

class OCSIPAddressCollector extends MySQLCollector
{
    static $bTeemIpIsInstalled;
    static $bIpDiscoveryIsInstalled;
    static $bTeemIpZoneMgmtIsInstalled;

    static function IsTeemIpInstalled()
    {
        // Detects if TeemIp is installed or not
        Utils::Log(LOG_DEBUG, 'Detecting if TeemIp is installed on remote iTop server');
        self::$bTeemIpIsInstalled = true;
        $oRestClient = new RestClient();
        try {
            $aResult = $oRestClient->Get('IPAddress', 'SELECT IPAddress WHERE id = 0');
            if ($aResult['code'] == 0) {
                $sMessage = 'TeemIp is installed on remote iTop server';
            } else {
                $sMessage = 'TeemIp is NOT installed on remote iTop server';
                self::$bTeemIpIsInstalled = false;
            }
        } catch (Exception $e) {
            self::$bTeemIpIsInstalled = false;
            $sMessage = 'TeemIp is considered as NOT installed due : ' . $e->getMessage();
            if (is_a($e, "IOException")) {
                Utils::Log(LOG_ERR, $sMessage);
                throw $e;
            }
        }

        Utils::Log(LOG_INFO, $sMessage);

        self::$bIpDiscoveryIsInstalled = false;
        self::$bTeemIpZoneMgmtIsInstalled = false;
        if (self::$bTeemIpIsInstalled) {
            // Detects if IP Discovery extension is installed or not
            Utils::Log(LOG_DEBUG, 'Detecting if IP Discovery extension is installed on remote iTop server');
            $oRestClient = new RestClient();
            try {
                $aResult = $oRestClient->Get('IPDiscovery', 'SELECT IPDiscovery WHERE id = 0');
                if ($aResult['code'] == 0) {
                    $sMessage = 'IP Discovery extension is installed on remote iTop server';
                    self::$bIpDiscoveryIsInstalled = true;
                } else {
                    $sMessage = 'IP Discovery extension is NOT installed on remote iTop server';
                }
            } catch (Exception $e) {
                $sMessage = 'IP TDiscovery extension is NOT installed on remote iTop server';
            }
            Utils::Log(LOG_INFO, $sMessage);

            // Detects if Zone Management extension is installed or not
            Utils::Log(LOG_DEBUG, 'Detecting if TeemIp Zone Management extension is installed on remote iTop server');
            $oRestClient = new RestClient();
            try {
                $aResult = $oRestClient->Get('Zone', 'SELECT Zone WHERE id = 0');
                if ($aResult['code'] == 0) {
                    $sMessage = 'TeemIp Zone Management is installed on remote iTop serve';
                    self::$bTeemIpZoneMgmtIsInstalled = true;
                } else {
                    $sMessage = 'TeemIp Zone Management is NOT installed on remote iTop server';
                }
            } catch (Exception $e) {
                $sMessage = 'TeemIp Zone Management is NOT installed on remote iTop server';
            }
            Utils::Log(LOG_INFO, $sMessage);
        }

        return self::$bTeemIpIsInstalled;
    }

    static function IsTeemIpZoneMgmtInstalled()
    {
        return self::$bTeemIpZoneMgmtIsInstalled;
    }

    public function AttributeIsOptional($sAttCode)
    {
        if (!self::$bIpDiscoveryIsInstalled) {
            if ($sAttCode == 'fqdn_from_iplookup') return true;
            if ($sAttCode == 'last_discovery_date') return true;
            if ($sAttCode == 'responds_to_iplookup') return true;
            if ($sAttCode == 'responds_to_ping') return true;
            if ($sAttCode == 'responds_to_scan') return true;
        }
        if (!self::$bTeemIpZoneMgmtIsInstalled) {
            if ($sAttCode == 'view_id') return true;
        }

        return parent::AttributeIsOptional($sAttCode);
    }
}