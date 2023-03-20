<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSIPAddressCollector extends AbstractOCSCollector
{
	protected function GetSQLQueryName()
	{
		$sSQLQueryName = "_query";
		if (Utils::GetConfigurationValue("use_asset_categories", 'no') == 'yes')
		{
			$sSQLQueryName = '_with_categories'.$sSQLQueryName;
		}
		return $sSQLQueryName;
	}


	public function AttributeIsOptional($sAttCode)
    {
        if (!$this->GetOCSCollectionPlan()->IsIpDiscoveryInstalled()) {
            if ($sAttCode == 'fqdn_from_iplookup') return true;
            if ($sAttCode == 'last_discovery_date') return true;
            if ($sAttCode == 'responds_to_iplookup') return true;
            if ($sAttCode == 'responds_to_ping') return true;
            if ($sAttCode == 'responds_to_scan') return true;
        }
        if (!$this->GetOCSCollectionPlan()->IsTeemIpZoneMgmtInstalled()) {
            if ($sAttCode == 'view_id') return true;
        }

        return parent::AttributeIsOptional($sAttCode);
    }

	protected function AddOtherParams(&$sQuery)
	{
		$aListAssetCategories =[];

		if (Utils::GetConfigurationValue("use_asset_categories", 'no') == 'yes')
		{
			//Get list of asset category
			$aListSynchronisedClasses = [];
			if (Utils::GetConfigurationValue("PCCollection", 'no') == 'yes') {
				$aListSynchronisedClasses[] = 'PC';
			}
			if (Utils::GetConfigurationValue("ServerCollection", 'no') == 'yes') {
				$aListSynchronisedClasses[] = 'Server';
			}
			if (Utils::GetConfigurationValue("VMCollection", 'no') == 'yes') {
				$aListSynchronisedClasses[] = 'VirtualMachine';
			}
			if (Utils::GetConfigurationValue("MobilePhoneCollection", 'no') == 'yes') {
				$aListSynchronisedClasses[] = 'MobilePhone';
			}

			$sQueryITopGetAssetCategory = Utils::GetConfigurationValue('OCSSoftwareCollector_getListAssetCategoryFromItop', '');
			$sQueryITopGetAssetCategory = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_targetlist#', implode("','", $aListSynchronisedClasses), $sQueryITopGetAssetCategory);

			$oRestClientAssetCategory = new RestClient();
			$aResultAssetCategory = $oRestClientAssetCategory->Get("OCSAssetCategory", $sQueryITopGetAssetCategory, "name");
			if (is_null($aResultAssetCategory['objects']))
			{
				Utils::Log(LOG_NOTICE, "No Asset category found in iTop with query: ".$sQueryITopGetAssetCategory);
				return;
			}
			foreach ($aResultAssetCategory['objects'] as $idx => $aAttDef)
			{
				$aListAssetCategories[$aAttDef['fields']['name']] = $aAttDef['fields']['name'];
			}

			$sQuery = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_categorielist#', implode("','", $aListAssetCategories), $sQuery);
			Utils::Log(LOG_DEBUG, "************".$sQuery);
		}
	}
}