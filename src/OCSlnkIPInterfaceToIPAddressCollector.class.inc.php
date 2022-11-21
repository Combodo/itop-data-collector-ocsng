<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

class OCSlnkIPInterfaceToIPAddressCollector extends AbstractOCSCollector
{
	protected $oIPAddressLookup;
	protected $oIPInterfaceLookup;

	protected function GetSQLQueryName()
	{
		$sSQLQueryName = "_query";
		if (Utils::GetConfigurationValue("use_asset_categories", 'no') == 'yes')
		{
			$sSQLQueryName = '_with_categories'.$sSQLQueryName;
		}
		return $sSQLQueryName;
	}

	protected function MustProcessBeforeSynchro()
	{
		// We must reprocess the CSV data obtained from the inventory script
		return true;
	}

	protected function InitProcessBeforeSynchro()
	{
		parent::InitProcessBeforeSynchro();

		$this->oIPAddressLookup = new LookupTable('SELECT IPAddress WHERE org_name = \''. Utils::GetConfigurationValue("default_org_id", '').'\'', array( 'friendlyname'));
	}

	protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
	{
		// Process each line of the CSV
		parent::ProcessLineBeforeSynchro($aLineData, $iLineIndex);
		$this->oIPAddressLookup->Lookup($aLineData, array('ipaddress_id'), 'ipaddress_id', $iLineIndex, false);
	}

	public function CheckToLaunch(array $aOrchestratedCollectors): bool
    {
        if (($this->GetOCSCollectionPlan()->IsTeemIpInstalled()) && ( Utils::GetConfigurationValue('IPCollection', 'no') == 'yes')) {
            return true;
        }
        return false;
    }

	protected function AddOtherParams(&$sQuery)
	{
		$aListAssetCategories = [];

		if (Utils::GetConfigurationValue("use_asset_categories", 'no') == 'yes')
		{
			//Get list of asset category
			$aListSynchronisedClasses = [];
			if (Utils::GetConfigurationValue("PCCollection", 'no') == 'yes')
			{
				$aListSynchronisedClasses[] = 'PC';
			}
			if (Utils::GetConfigurationValue("ServerCollection", 'no') == 'yes')
			{
				$aListSynchronisedClasses[] = 'Server';
			}
			if (Utils::GetConfigurationValue("VMCollection", 'no') == 'yes')
			{
				$aListSynchronisedClasses[] = 'VirtualMachine';
			}
			if (Utils::GetConfigurationValue("MobilePhoneCollection", 'no') == 'yes')
			{
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


