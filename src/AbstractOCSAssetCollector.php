<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
abstract class AbstractOCSAssetCollector extends AbstractOCSCollector
{
	protected function GetSQLQueryName()
    {
        $sSQLQueryName = "_query";
        if (Utils::GetConfigurationValue("use_asset_categories", 'no') == 'yes') {
            $sSQLQueryName = "_with_categories_query";
        }
        return $sSQLQueryName;
    }

	protected function AddOtherParams(&$sQuery)
    {
        if (Utils::GetConfigurationValue("use_asset_categories", 'no') == 'yes') {
	        $sSQLQueryName =   '_getCategoriesFromItop';
	        $sQueryITop = Utils::GetConfigurationValue(get_class($this) .$sSQLQueryName, '');
	        if ($sQueryITop == '') {
		        // Try all lowercase
		        $sQueryITop = Utils::GetConfigurationValue(strtolower(get_class($this)) . $sSQLQueryName, '');
	        }

            $oRestClient = new RestClient();
            $aResult = $oRestClient->Get("OCSAssetCategory", $sQueryITop, "name");

             if(is_null($aResult['objects']))
            {
                Utils::Log(LOG_INFO, "No OCSAssetCategory found in iTop with request: ".$sSQLQueryName);
                return;
            }
            $aListCategories = [];
            foreach ($aResult['objects'] as $idx => $aAttDef) {
                $aListCategories[$aAttDef['fields']['name']] = $aAttDef['fields']['name'];
            }

            $sQuery = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_categorielist#', implode("','", $aListCategories), $sQuery);
	        Utils::Log(LOG_DEBUG, "************".$sQuery);
        }
    }

    abstract protected function GetTargetClass();
}