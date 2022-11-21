<?php

/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

class OCSPCSoftwareCollector extends AbstractOCSSoftwareCollector
{

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
	        if (Utils::GetConfigurationValue("use_asset_categories", 'no') == 'yes')
	        {
		        $sSQLQueryName = '_with_2categories'.$sSQLQueryName;
	        }
	        else
	        {
		        $sSQLQueryName = '_with_categories'.$sSQLQueryName;
	        }
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
}