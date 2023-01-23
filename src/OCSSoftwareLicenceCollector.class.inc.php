<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSSoftwareLicenceCollector extends AbstractOCSCollector
{
	protected function AddOtherParams(&$sQuery)
    {
        $sQueryITop = Utils::GetConfigurationValue("OCSSoftware_getListFromItop", '');
        $oRestClient = new RestClient();
        $aResult = $oRestClient->Get("Software", $sQueryITop, "name, type");

        $aListSoftware = [];
        foreach ($aResult['objects'] as $idx => $aAttDef) {
            $sType = $aAttDef['fields']['type'];
            if (is_null($sType)) {
                $sType = 'OtherSoftware';
            }
            $aListSoftware[$sType][$aAttDef['fields']['name']] = $aAttDef['fields']['name'];
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
	    Utils::Log(LOG_DEBUG,$sQuery);
      }

    protected function GetTargetClass()
    {
        return 'SoftwareLicence';
    }

    public function checkToLaunch():bool
    {
        if ( (Utils::GetConfigurationValue('SoftwareCollection', 'no') == 'yes') && (Utils::GetConfigurationValue('LicenceCollection', 'no') == 'yes') ) {
            return true;
        }
        return false;
    }
}