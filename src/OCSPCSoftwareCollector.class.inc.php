<?php

/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

class OCSPCSoftwareCollector extends AbstractOCSSoftwareCollector
{
    public function Init(): void
    {
        parent::Init();
        if (Utils::GetConfigurationValue('LicenceCollection', 'no') == 'yes' && $this->TestIfTableExistsInOCS('officepack')) {
            $this->aFields['softwarelicence_id']['update'] = true;
        }
    }

	public function AttributeIsOptional($sAttCode)
	{
		if ($this->GetOCSCollectionPlan()->IsTeemIpInstalled()) {
			if ($sAttCode == 'providercontracts_list') return true;
			if ($sAttCode == 'services_list') return true;
			if ($sAttCode == 'tickets_list') return true;
		}

		return parent::AttributeIsOptional($sAttCode);
	}

	protected function GetSQLQueryName()
    {
        $sSQLQueryName = "_query";
        if (Utils::GetConfigurationValue("use_software_categories", 'no') == 'yes') {
            $sSQLQueryName = "_with_categories".$sSQLQueryName;
        }
        if ($this->aFields['softwarelicence_id']['update']) {
            $sSQLQueryName = "_with_licence".$sSQLQueryName;
        }
        Utils::Log(LOG_DEBUG, 'sSQLQueryName'.$sSQLQueryName);
        return $sSQLQueryName;
    }

    protected function GetTargetClass()
    {
        return 'PCSoftware';
    }

	public function CheckToLaunch(array $aOrchestratedCollectors): bool
    {
        if (Utils::GetConfigurationValue('SoftwareCollection', 'no') == 'yes') {
            return true;
        }
        return false;
    }

    /*With Licence*/
    protected function MustProcessBeforeSynchro()
    {
        // We must reprocess the CSV data obtained from the inventory script
        // to lookup the Brand/Model and OSFamily/OSVersion in iTop
        if (Utils::GetConfigurationValue('LicenceCollection', 'no') == 'yes') {
            return true;
        } else {
            return false;
        }
    }

    protected function InitProcessBeforeSynchro()
    {
        // Retrieve the identifiers of the OSVersion since we must do a lookup based on two fields: Family + Version
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        if (Utils::GetConfigurationValue('LicenceCollection', 'no') == 'yes') {
            $this->oSoftwareLicence = new LookupTable('SELECT SoftwareLicence', array('software_name', 'org_id', 'name'));
        }
    }

    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        if (Utils::GetConfigurationValue('LicenceCollection', 'no') == 'yes') {
            $this->oSoftwareLicence->Lookup($aLineData, array('name', 'org_id', 'softwarelicence_id'), 'softwarelicence_id', $iLineIndex);
        }
    }
}