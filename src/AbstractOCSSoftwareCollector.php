<?php

abstract class AbstractOCSSoftwareCollector extends AbstractOCSCollector
{

    private function GetSQLQueryName()
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
        if (Utils::GetConfigurationValue("use_software_categories", 'no') == 'yes') {
            $sQueryITop = "SELECT  OCSSoftwareCategorie WHERE type='" . $this->GetTargetClass() . "'";
            $oRestClient = new RestClient();
            $aResult = $oRestClient->Get("OCSSoftwareCategorie", $sQueryITop, "name, type");
            if(is_null($aResult['objects']))
            {
                Utils::Log(LOG_ERR, "No OCSSoftwareCategorie found in iTop.");
                return;
            }
            $aListCategories = [];
            foreach ($aResult['objects'] as $idx => $aAttDef) {
                $aListCategories[$aAttDef['fields']['name']] = $aAttDef['fields']['name'];
            }

            $sQuery = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_categorielist#', implode("','", $aListCategories), $sQuery);
        }
        else {
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

    }
}