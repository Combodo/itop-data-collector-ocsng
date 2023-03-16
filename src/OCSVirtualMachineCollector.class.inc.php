<?php

class OCSVirtualMachineCollector extends AbstractOCSAssetCollector
{
    protected $oOSVersionLookup;
    protected $oOSLicenceLookup;
    protected $oIPAddressLookup;

    public function AttributeIsOptional($sAttCode)
    {
        // For backward comptability with previous versions which were adding an ocsid field
        if ($sAttCode == 'ocsid') return true;
	    if ($sAttCode == 'cvss') return true;

	    if ($sAttCode == 'logicalvolumes_list') return true;

	    if ($this->GetOCSCollectionPlan()->IsTeemIpInstalled()) {
            if ($sAttCode == 'providercontracts_list') return true;
            if ($sAttCode == 'services_list') return true;
            if ($sAttCode == 'managementip') return true;
		    if ($sAttCode == 'tickets_list') return true;
        } else {
            if ($sAttCode == 'managementip_id') return true;
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
        // Retrieve the identifiers of the OSVersion since we must do a lookup based on two fields: Family + Version
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        $this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));
        $this->oOSLicenceLookup = new LookupTable('SELECT OSLicence', array('osversion_id', 'name'));
        if ($this->GetOCSCollectionPlan()->IsTeemIpInstalled()) {
            $this->oIPAddressLookup = new LookupTable('SELECT IPAddress', array('org_name', 'friendlyname'));
        }
    }

    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        $this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex, true);
        $this->oOSLicenceLookup->Lookup($aLineData, array('osversion_id', 'oslicence_id'), 'oslicence_id', $iLineIndex, true);
        if ($this->GetOCSCollectionPlan()->IsTeemIpInstalled()) {
            $this->oIPAddressLookup->Lookup($aLineData, array('org_id', 'managementip_id'), 'managementip_id', $iLineIndex);
        }
    }

    protected function GetTargetClass()
    {
        return 'VirtualMachine';
    }

	public function CheckToLaunch(array $aOrchestratedCollectors): bool
    {
        if (Utils::GetConfigurationValue('VMCollection', 'no') == 'yes') {
            return true;
        }
        return false;
    }
}