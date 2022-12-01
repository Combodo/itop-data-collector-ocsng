<?php

class OCSSoftwareCollector extends AbstractOCSSoftwareCollector
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
            echo('##############################################');
            echo($sType . '::' . implode("','", $aName));
            echo('##############################################');
            $sQueryByType = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_softwarelist#', implode("','", $aName), $sInitialQuery);
            $sQueryByType = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_type_id#', $sType, $sQueryByType);
            if ($bIsFirst) {
                $sQuery = $sQueryByType;
                $bIsFirst = false;
            } else {
                $sQuery = $sQuery . ' UNION ' . $sQueryByType;
            }
        }
        echo('##############################################');
        echo($sQuery);
        echo('##############################################');
    }

    protected function GetTargetClass()
    {
        return 'PCSoftware';
    }
}