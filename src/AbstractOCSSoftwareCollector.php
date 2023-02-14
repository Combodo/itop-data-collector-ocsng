<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
abstract class AbstractOCSSoftwareCollector extends AbstractOCSCollector
{

	protected function GetSQLQueryName()
    {
        $sSQLQueryName = "_query";
        if (Utils::GetConfigurationValue("use_software_categories", 'no') == 'yes') {
            $sSQLQueryName = "_with_categories_query";
        }
        return $sSQLQueryName;
    }


    abstract protected function GetTargetClass();

	protected function AddOtherParams(&$sQuery)
    {
		$sSQLQueryName =  '_getListFromItop';
	    $sClass = "Software";

	    if (Utils::GetConfigurationValue("use_software_categories", 'no') == 'yes') {
		    $sSQLQueryName =  '_with_categories'.$sSQLQueryName;
			$sClass = "OCSSoftwareCategory";
	    }

	    $sQueryITop = Utils::GetConfigurationValue(get_class($this) .$sSQLQueryName, '');
	    if ($sQueryITop == '') {
		    // Try all lowercase
		    $sQueryITop = Utils::GetConfigurationValue(strtolower(get_class($this)) . $sSQLQueryName, '');
	    }

	    $oRestClient = new RestClient();
        $aResult = $oRestClient->Get($sClass, $sQueryITop, "name, type");
        if(is_null($aResult['objects']))
        {
            Utils::Log(LOG_NOTICE, "No $sClass found in iTop with query: ".$sQueryITop);
            return;
        }

	    $aListSoftware = [];
	    foreach ($aResult['objects'] as $idx => $aAttDef) {
		    $sType = $aAttDef['fields']['type'];
		    if (is_null($sType)) {
			    $sType = 'OtherSoftware';
		    }
		    $aListSoftware[$sType][] = $aAttDef['fields']['name'];
	    }

        $sInitialQuery = $sQuery;
        $bIsFirst = true;
        foreach ($aListSoftware as $sType => $aName) {
            $sQueryByType = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_softwarelist#', implode("','", $aName), $sInitialQuery);
            $sQueryByType = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_type_id#', $sType, $sQueryByType);
            if ($bIsFirst) {
                $sQuery = $sQueryByType;
                $bIsFirst = false;
            } else {
                $sQuery = $sQuery . ' UNION ' . $sQueryByType;
            }
        }
	    Utils::Log(LOG_DEBUG, $sQuery);
    }

	public function CheckToLaunch(array $aOrchestratedCollectors): bool
	{
		if (Utils::GetConfigurationValue('SoftwareCollection', 'no') == 'yes') {
			return true;
		}
		return false;
	}
}