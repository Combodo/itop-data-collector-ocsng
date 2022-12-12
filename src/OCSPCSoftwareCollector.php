<?php

// Copyright (C) 2018 Combodo SARL
//
//   This application is free software; you can redistribute it and/or modify
//   it under the terms of the GNU Affero General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.
//
//   iTop is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU Affero General Public License for more details.
//
//   You should have received a copy of the GNU Affero General Public License
//   along with this application. If not, see <http://www.gnu.org/licenses/>

class OCSPCSoftwareCollector extends AbstractOCSSoftwareCollector
{
    private function GetSQLQueryName()
    {
        $sSQLQueryName = "_query";
        if (Utils::GetConfigurationValue("use_software_categories", 'no') == 'yes') {
            $sSQLQueryName = "_with_categories".$sSQLQueryName;
        }
        return $sSQLQueryName;
    }

    protected function GetTargetClass()
    {
        return 'PCSoftware';
    }
    protected function AddOtherParams(&$sQuery)
    {
        $sQueryITop = Utils::GetConfigurationValue("OCSPCSoftware_getListFromItop", '');
        $oRestClient = new RestClient();
        $aResult = $oRestClient->Get("Software", $sQueryITop, "name");

        $aListSoftware = [];
        foreach ($aResult['objects'] as $aAttDef) {
            $aListSoftware[$aAttDef['fields']['name']] = $aAttDef['fields']['name'];
        }

        $sQuery = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_softwarelist#', implode("','", $aListSoftware), $sQuery);
    }
}