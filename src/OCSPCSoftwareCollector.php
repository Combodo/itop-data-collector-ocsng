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
    protected $oSoftwareLicence;

    private function GetSQLQueryName()
    {
        $sSQLQueryName = "_query";
        if (Utils::GetConfigurationValue("LicenceCollection", 'no') == 'yes') {
            $sSQLQueryName = "_with_licence".$sSQLQueryName;
        }
        if (Utils::GetConfigurationValue("use_software_categories", 'no') == 'yes') {
            $sSQLQueryName = "_with_categories".$sSQLQueryName;
        }
        return $sSQLQueryName;
    }
    protected function MustProcessBeforeSynchro()
    {
        // We must reprocess the CSV data obtained from the inventory script
        // to lookup the Brand/Model and OSFamily/OSVersion in iTop
        return true;
    }

    protected function InitProcessBeforeSynchro()
    {
        // Retrieve the identifiers of the OSVersion since we must do a lookup based on two fields: Family + Version
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        $this->oSoftwareLicence = new LookupTable('SELECT SoftwareLicence', array('software_name', 'org_id','softwarelicence_id'));

    }

    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        $this->oSoftwareLicence->Lookup($aLineData, array('software_name', 'org_id', 'softwarelicence_id'), 'softwarelicence_id', $iLineIndex);
    }

    protected function GetTargetClass()
    {
        return 'PCSoftware';
    }
    protected function AddOtherParams(&$sQuery)
    {
        $sQueryITop = Utils::GetConfigurationValue("OCSPCSoftware_getListFromItop", '');
        $oRestClient = new RestClient();
        echo('#####################' . $sQueryITop . '#########################');
        $aResult = $oRestClient->Get("Software", $sQueryITop, "name");

        $aListSoftware = [];
        foreach ($aResult['objects'] as $aAttDef) {
            $aListSoftware[$aAttDef['fields']['name']] = $aAttDef['fields']['name'];
        }
        echo('##############################################');
        echo(implode("','", $aListSoftware));
        echo('##############################################');
        $sQuery = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_softwarelist#', implode("','", $aListSoftware), $sQuery);
    }
}