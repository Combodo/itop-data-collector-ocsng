<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSOSLicenceCollector extends AbstractOCSCollector
{
    protected $oOSVersionLookup;

    protected function MustProcessBeforeSynchro()
    {
        // We must reprocess the CSV data obtained from the inventory script
        // to lookup the Brand/Model and OSFamily/OSVersion in iTop
        return true;
    }

    protected function InitProcessBeforeSynchro()
    {
        // Retrieve the identifiers of the OSVersion since we must do a lookup based on two fields: Family + Version
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
       $this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));
    }

    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
       $this->oOSVersionLookup->Lookup($aLineData,[ 'osversion_id'], 'osversion_id', $iLineIndex);
    }

	public function CheckToLaunch(array $aOrchestratedCollectors): bool
    {
	    if (Utils::GetConfigurationValue('MobilePhoneCollection', 'no') == 'yes'
		    || Utils::GetConfigurationValue('PCCollection', 'no') == 'yes'
		    || Utils::GetConfigurationValue('ServerCollection', 'no') == 'yes'
		    || Utils::GetConfigurationValue('VMCollection', 'no') == 'yes') {
				    return true;
	    }
	    return false;
    }
}