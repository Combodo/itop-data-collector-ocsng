<?php
// Copyright (C) 2018 Combodo SARL
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

class OCSPCTeemIpCollector extends OCSPCCollector
{
	protected $oIPAddressLookup;

	public function AttributeIsOptional($sAttCode)
	{
		// If the collector is connected to TeemIp standalone, there is no "providercontracts_list" on PCs. Let's safely ignore it.
		if ($sAttCode == 'providercontracts_list') return true;
		if ($sAttCode == 'services_list') return true;

		return parent::AttributeIsOptional($sAttCode);
	}

	protected function InitProcessBeforeSynchro()
	{
		parent::InitProcessBeforeSynchro();

		$this->oIPAddressLookup = new LookupTable('SELECT IPAddress', array('org_name', 'friendlyname'));
	}

	protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
	{
		// Process each line of the CSV
		parent::ProcessLineBeforeSynchro($aLineData, $iLineIndex);

		$this->oIPAddressLookup->Lookup($aLineData, array('org_id', 'ipaddress_id'), 'ipaddress_id', $iLineIndex);
	}
}

class OCSPCPhysicalInterfaceTeemIpCollector extends MySQLCollector
{
}

class OCSServerTeemIpCollector extends OCSServerCollector
{
	protected $oIPAddressLookup;

	public function AttributeIsOptional($sAttCode)
	{
		// If the collector is connected to TeemIp standalone, there is no "providercontracts_list" on Servers. Let's safely ignore it.
		if ($sAttCode == 'providercontracts_list') return true;
		if ($sAttCode == 'services_list') return true;

		return parent::AttributeIsOptional($sAttCode);
	}

	protected function InitProcessBeforeSynchro()
	{
		parent::InitProcessBeforeSynchro();

		$this->oIPAddressLookup = new LookupTable('SELECT IPAddress', array('org_name', 'friendlyname'));
	}

	protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
	{
		// Process each line of the CSV
		parent::ProcessLineBeforeSynchro($aLineData, $iLineIndex);

		$this->oIPAddressLookup->Lookup($aLineData, array('org_id', 'managementip_id'), 'managementip_id', $iLineIndex);
	}
}

class OCSServerPhysicalInterfaceTeemIpCollector extends MySQLCollector
{
}

class OCSVirtualMachineTeemIpCollector extends OCSVirtualMachineCollector
{
	protected $oIPAddressLookup;

	public function AttributeIsOptional($sAttCode)
	{
		// If the collector is connected to TeemIp standalone, there is no "providercontracts_list" on VMs. Let's safely ignore it.
		if ($sAttCode == 'providercontracts_list') return true;
		if ($sAttCode == 'services_list') return true;

		return parent::AttributeIsOptional($sAttCode);
	}

	protected function InitProcessBeforeSynchro()
	{
		parent::InitProcessBeforeSynchro();

		$this->oIPAddressLookup = new LookupTable('SELECT IPAddress', array('org_name', 'friendlyname'));
	}

	protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
	{
		// Process each line of the CSV
		parent::ProcessLineBeforeSynchro($aLineData, $iLineIndex);

		$this->oIPAddressLookup->Lookup($aLineData, array('org_id', 'managementip_id'), 'managementip_id', $iLineIndex);
	}
}

class OCSLogicalInterfaceTeemIpCollector extends MySQLCollector
{
}

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
		try
		{
			$aResult = $oRestClient->Get('IPAddress', 'SELECT IPAddress WHERE id = 0');
			if ($aResult['code'] == 0)
			{
				$sMessage = 'TeemIp is installed on remote iTop server';
			}
			else
			{
				$sMessage = 'TeemIp is NOT installed on remote iTop server';
				self::$bTeemIpIsInstalled = false;
			}
		}
		catch(Exception $e)
		{
            self::$bTeemIpIsInstalled = false;
            $sMessage = 'TeemIp is considered as NOT installed due to below issue: ' . $e->getMessage();
            if(is_a($e, "IOException"))
            {
                Utils::Log(LOG_ERR, $sMessage);
                throw $e;
            }
		}

        Utils::Log(LOG_INFO, $sMessage);

		self::$bIpDiscoveryIsInstalled = false;
		self::$bTeemIpZoneMgmtIsInstalled = false;
		if (self::$bTeemIpIsInstalled)
		{
			// Detects if IP Discovery extension is installed or not
			Utils::Log(LOG_DEBUG, 'Detecting if IP Discovery extension is installed on remote iTop server');
			$oRestClient = new RestClient();
			try
			{
				$aResult = $oRestClient->Get('IPDiscovery', 'SELECT IPDiscovery WHERE id = 0');
				if ($aResult['code'] == 0)
				{
					$sMessage = 'IP Discovery extension is installed on remote iTop server';
					self::$bIpDiscoveryIsInstalled = true;
				}
				else
				{
					$sMessage = 'IP Discovery extension is NOT installed on remote iTop server';
				}
			}
			catch(Exception $e)
			{
				$sMessage = 'IP TDiscovery extension is NOT installed on remote iTop server';
			}
			Utils::Log(LOG_INFO, $sMessage);

			// Detects if Zone Management extension is installed or not
			Utils::Log(LOG_DEBUG, 'Detecting if TeemIp Zone Management extension is installed on remote iTop server');
			$oRestClient = new RestClient();
			try
			{
				$aResult = $oRestClient->Get('Zone', 'SELECT Zone WHERE id = 0');
				if ($aResult['code'] == 0)
				{
					$sMessage = 'TeemIp Zone Management is installed on remote iTop serve';
					self::$bTeemIpZoneMgmtIsInstalled = true;
				}
				else
				{
					$sMessage = 'TeemIp Zone Management is NOT installed on remote iTop server';
				}
			}
			catch(Exception $e)
			{
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
		if (!self::$bIpDiscoveryIsInstalled)
		{
			if ($sAttCode == 'fqdn_from_iplookup') return true;
			if ($sAttCode == 'last_discovery_date') return true;
			if ($sAttCode == 'responds_to_iplookup') return true;
			if ($sAttCode == 'responds_to_ping') return true;
			if ($sAttCode == 'responds_to_scan') return true;
		}
		if (!self::$bTeemIpZoneMgmtIsInstalled)
		{
			if ($sAttCode == 'view_id') return true;
		}

		return parent::AttributeIsOptional($sAttCode);
	}
}

class OCSIPv4AddressCollector extends OCSIPAddressCollector
{
}

class OCSIPv4AddressWithZoneCollector extends OCSIPAddressCollector
{
}

class OCSIPv6AddressCollector extends OCSIPAddressCollector
{
}

class OCSIPv6AddressWithZoneCollector extends OCSIPAddressCollector
{
}

class OCSlnkIPInterfaceToIPAddressCollector extends MySQLCollector
{
	protected $oIPAddressLookup;
	protected $oIPInterfaceLookup;

	protected function InitProcessBeforeSynchro()
	{
		parent::InitProcessBeforeSynchro();

		$this->oIPAddressLookup = new LookupTable('SELECT IPAddress', array('org_name', 'friendlyname'));
		$this->oIPInterfaceLookup = new LookupTable('SELECT IPInterface', array('macaddress'));
	}

	protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
	{
		// Process each line of the CSV
		parent::ProcessLineBeforeSynchro($aLineData, $iLineIndex);

		$this->oIPAddressLookup->Lookup($aLineData, array('org_id', 'ipaddress_id'), 'ipaddress_id', $iLineIndex);
		$this->oIPInterfaceLookup->Lookup($aLineData, array('macaddress'), 'ipinterface_id', $iLineIndex);
	}
}


