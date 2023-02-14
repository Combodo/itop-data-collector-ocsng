<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

class OCSlnkIPInterfaceToIPAddressCollector extends AbstractOCSCollector
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

	public function CheckToLaunch(array $aOrchestratedCollectors): bool
    {
        if (($this->GetOCSCollectionPlan()->IsTeemIpInstalled()) && ( Utils::GetConfigurationValue('collect_ips', 'no') == 'yes')) {
            return true;
        }
        return false;
    }
}


