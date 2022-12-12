<?php

abstract class AbstractOCSAssetCollector extends AbstractOCSCollector
{
    private function GetSQLQueryName()
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
            $sQueryITop = "SELECT  OCSAssetCategorie WHERE target_class='" . $this->GetTargetClass() ;
            $oRestClient = new RestClient();
            $aResult = $oRestClient->Get("OCSAssetCategorie", $sQueryITop, "name");
             if(is_null($aResult['objects']))
            {
                Utils::Log(LOG_ERR, "No OCSAssetCategorie found in iTop.");
                return;
            }
            $aListCategories = [];
            foreach ($aResult['objects'] as $idx => $aAttDef) {
                $aListCategories[$aAttDef['fields']['name']] = $aAttDef['fields']['name'];
            }

            $sQuery = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_categorielist#', implode("','", $aListCategories), $sQuery);
        }
    }

    abstract protected function GetTargetClass();
}