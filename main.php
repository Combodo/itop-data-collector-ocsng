<?php
// Copyright (C) 2014 Combodo SARL
//
//   This application is free software; you can redistribute it and/or modify	
//   it under the terms of the GNU Affero General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.
//
//   iTop is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU Affero General Public License for more details.
//
//   You should have received a copy of the GNU Affero General Public License
//   along with this application. If not, see <http://www.gnu.org/licenses/>

include(APPROOT . '/collectors/vendor/autoload.php'); // composer autoload
include(APPROOT . '/collectors/ocs_teemip_classes.php');

// Detects if TeemIp is installed or not
$bTeemIpIsInstalled = OCSIPAddressCollector::IsTeemIpInstalled();

// Register the collectors (one collector class per data synchro task to run)
// and tell the orchestrator in which order to run them
$iRank = 1;
Orchestrator::AddCollector($iRank++, 'OCSBrandCollector');
Orchestrator::AddCollector($iRank++, 'OCSOSFamilyCollector');
Orchestrator::AddCollector($iRank++, 'OCSOSVersionCollector');

if ($bTeemIpIsInstalled) {
    $bCollectIps = Utils::GetConfigurationValue('collect_ips', 'no');

    if ($bCollectIps == 'yes') {
        Utils::Log(LOG_INFO, 'IPs will be collected');
        $bTeemIpZoneMgmtIsInstalled = OCSIPAddressCollector::IsTeemIpZoneMgmtInstalled();
        $bManageIPv6 = Utils::GetConfigurationValue('manage_ipv6', 'no');
        if ($bTeemIpZoneMgmtIsInstalled) {
            Orchestrator::AddCollector($iRank++, 'OCSIPv4AddressWithZoneCollector');
            if ($bManageIPv6 == 'yes') {
                Utils::Log(LOG_WARNING, "IPv6 creation and update is not supported yet due to iTop limitation");
                Orchestrator::AddCollector($iRank++, 'OCSIPv6AddressWithZoneCollector');
            }
        } else {
            Orchestrator::AddCollector($iRank++, 'OCSIPv4AddressCollector');
            if ($bManageIPv6 == 'yes') {
                Utils::Log(LOG_WARNING, "IPv6 creation and update is not supported yet due to iTop limitation");
                Orchestrator::AddCollector($iRank++, 'OCSIPv6AddressCollector');
            }
        }
    } else {
        Utils::Log(LOG_INFO, 'IPs will NOT be collected');
    }
}

if (Utils::GetConfigurationValue('PCCollection', 'yes') == 'yes') {
    Orchestrator::AddCollector($iRank++, 'OCSPCModelCollector');
    if ($bTeemIpIsInstalled) {
        Orchestrator::AddCollector($iRank++, 'OCSPCTeemIpCollector');
        Orchestrator::AddCollector($iRank++, 'OCSPCPhysicalInterfaceTeemIpCollector');
    } else {
        Orchestrator::AddCollector($iRank++, 'OCSPCCollector');
        Orchestrator::AddCollector($iRank++, 'OCSPCPhysicalInterfaceCollector');
    }
}

if (Utils::GetConfigurationValue('ServerCollection', 'yes') == 'yes') {
    Orchestrator::AddCollector($iRank++, 'OCSServerModelCollector');
    if ($bTeemIpIsInstalled) {
        Orchestrator::AddCollector($iRank++, 'OCSServerTeemIpCollector');
        Orchestrator::AddCollector($iRank++, 'OCSServerPhysicalInterfaceTeemIpCollector');
    } else {
        Orchestrator::AddCollector($iRank++, 'OCSServerCollector');
        Orchestrator::AddCollector($iRank++, 'OCSServerPhysicalInterfaceCollector');
    }
}

if (Utils::GetConfigurationValue('VMCollection', 'yes') == 'yes') {
    if ($bTeemIpIsInstalled) {
        Orchestrator::AddCollector($iRank++, 'OCSVirtualMachineTeemIpCollector');
        Orchestrator::AddCollector($iRank++, 'OCSLogicalInterfaceTeemIpCollector');
    } else {
        Orchestrator::AddCollector($iRank++, 'OCSVirtualMachineCollector');
        Orchestrator::AddCollector($iRank++, 'OCSLogicalInterfaceCollector');
    }
}

if (($bTeemIpIsInstalled) && ($bCollectIps == 'yes')) {
    Orchestrator::AddCollector($iRank++, 'OCSlnkIPInterfaceToIPAddressCollector');
}
if (Utils::GetConfigurationValue('MobilePhoneCollection', 'yes') == 'yes') {
    Orchestrator::AddCollector($iRank++, 'OCSMobilePhoneCollector');
}
if (Utils::GetConfigurationValue('SoftwareCollection', 'yes') == 'yes') {
    Orchestrator::AddCollector($iRank++, 'OCSPrinterCollector');
}
if (Utils::GetConfigurationValue('SoftwareCollection', 'yes') == 'yes') {
    Orchestrator::AddCollector($iRank++, 'OCSSoftwareCollector');
    if (Utils::GetConfigurationValue('LicenceCollection', 'yes') == 'yes') {
        Orchestrator::AddCollector($iRank++, 'OCSSoftwareLicenceCollector');
        Orchestrator::AddCollector($iRank++, 'OCSPCSoftwareWithLicenceCollector');
    } else {
        Orchestrator::AddCollector($iRank++, 'OCSPCSoftwareCollector');
    }
}


