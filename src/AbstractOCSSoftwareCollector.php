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
            $sQueryITop = "SELECT  OCSAssetSoftware WHERE type='" . $this->GetTargetClass() . "'";
            $oRestClient = new RestClient();
            $aResult = $oRestClient->Get("OCSAssetSoftware", $sQueryITop, "name");

            $aListCategories = [];
            foreach ($aResult['objects'] as $idx => $aAttDef) {
                $aListCategories[$aAttDef['fields']['name']] = $aAttDef['fields']['name'];
            }

            $sQuery = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_categorielist#', implode("','", $aListCategories), $sQuery);
        }
    }
}