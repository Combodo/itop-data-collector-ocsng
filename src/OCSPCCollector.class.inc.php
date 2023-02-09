<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSPCCollector extends AbstractOCSAssetCollector
{

    protected $oOSVersionLookup;
    protected $oOSLicenceLookup;
    protected $oModelLookup;

    public function AttributeIsOptional($sAttCode)
    {
        // For backward comptability with previous versions which were adding an ocsid field
        if ($sAttCode == 'ocsid') return true;
	    if ($sAttCode == 'cvss') return true;
        if ($sAttCode == 'softwarelicence_id') return true;
        if ($this->GetOCSCollectionPlan()->IsTeemIpInstalled()) {
            // If the collector is connected to TeemIp standalone, there is no "providercontracts_list" on PCs. Let's safely ignore it.
            if ($sAttCode == 'providercontracts_list') return true;
            if ($sAttCode == 'services_list') return true;
        } else {
            if ($sAttCode == 'ipaddress_id') return true;
        }
        return parent::AttributeIsOptional($sAttCode);
    }

	protected function MustProcessBeforeSynchro()
	{
		// We must reprocess the CSV data obtained from the inventory script
		// to lookup the Brand/Model and OSFamily/OSVersion in iTop
		return true;
	}

	protected function InitProcessBeforeSynchro()
	{
		// Retrieve the identifiers of the Model since we must do a lookup based on two fields: Brand + Model
		// which is not supported by the iTop Data Synchro... so let's do the job of an ETL
		$this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));
		$this->oOSLicenceLookup = new LookupTable('SELECT OSLicence', array('osversion_id', 'name'));
		$this->oModelLookup = new LookupTable('SELECT Model', array('brand_id_friendlyname', 'name'), false /* non-case sensitive */);
	}

	protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
	{

		Utils::Log(LOG_DEBUG, "ProcessLineBeforeSynchro line $iLineIndex. : ");
		// Process each line of the CSV
		$this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex);
		$this->oOSLicenceLookup->Lookup($aLineData, array('osversion_id', 'oslicence_id'), 'oslicence_id', $iLineIndex);
		$this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex);

	}

    protected function GetTargetClass()
    {
        return 'PC';
    }
    public function checkToLaunch():bool
    {
        if (Utils::GetConfigurationValue('PCCollection', 'no') == 'yes') {
            return true;
        }
        return false;
    }
}