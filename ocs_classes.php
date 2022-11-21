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

class OCSBrandCollector extends MySQLCollector
{
}

class OCSOSFamilyCollector extends MySQLCollector
{
}

class OCSOSVersionCollector extends MySQLCollector
{
}

class OCSServerModelCollector extends MySQLCollector
{
}

class OCSServerCollector extends MySQLCollector
{
	protected $oOSVersionLookup;
	protected $oModelLookup;

    public function AttributeIsOptional($sAttCode)
    {
        // If the module Service Management for Service Providers is selected during the setup
        // there is no "services_list" attribute on VirtualMachines. Let's safely ignore it.
        if ($sAttCode == 'enclosure_id') return true;
        if ($sAttCode == 'rack_id') return true;
        if ($sAttCode == 'powerA_id') return true;
        if ($sAttCode == 'powerB_id') return true;
        
        // Backward comptability with previous versions which were adding an ocsid field
        if ($sAttCode == 'ocsid') return true;
        
        // For information
        if ($sAttCode == 'tickets_list')
        {
            Utils::Log(LOG_INFO, "[".__CLASS__."] The column tickets_list is used for storing the OCS ID in order to display the OCS tab on Servers. You can safely ignore the warning about it.");
        }
        
        return parent::AttributeIsOptional($sAttCode);
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
        $this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));
        
        // Retrieve the identifiers of the Model since we must do a lookup based on two fields: Brand + Model
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        $this->oModelLookup = new LookupTable('SELECT Model', array('brand_id_friendlyname', 'name'), false /* non-case sensitive */);
    }
    
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        $this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex);
        $this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex);
    }
}

class OCSServerPhysicalInterfaceCollector extends MySQLCollector
{
}

class OCSPCModelCollector extends MySQLCollector
{
}

class OCSPCCollector extends MySQLCollector
{
	protected $oOSVersionLookup;
	protected $oModelLookup;

	public function AttributeIsOptional($sAttCode)
    {
        // For backward comptability with previous versions which were adding an ocsid field
        if ($sAttCode == 'ocsid') return true;
        
        // For information
        if ($sAttCode == 'tickets_list')
        {
            Utils::Log(LOG_INFO, "[".__CLASS__."] The column tickets_list is used for storing the OCS ID in order to display the OCS tab on PCs. You can safely ignore the warning about it.");
        }
        
        return parent::AttributeIsOptional($sAttCode);
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
        $this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));
        
        // Retrieve the identifiers of the Model since we must do a lookup based on two fields: Brand + Model
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        $this->oModelLookup = new LookupTable('SELECT Model', array('brand_id_friendlyname', 'name'));
    }
    
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        $this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex);
        $this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex);
    }
}

class OCSPCPhysicalInterfaceCollector extends MySQLCollector
{
}

class OCSVirtualMachineCollector extends MySQLCollector
{
	protected $oOSVersionLookup;

	public function AttributeIsOptional($sAttCode)
    {
        // For backward comptability with previous versions which were adding an ocsid field
        if ($sAttCode == 'ocsid') return true;
        
        // For information
        if ($sAttCode == 'tickets_list')
        {
            Utils::Log(LOG_INFO, "[".__CLASS__."] The column tickets_list is used for storing the OCS ID in order to display the OCS tab on Virtual Machines. You can safely ignore the warning about it.");
        }
        
        return parent::AttributeIsOptional($sAttCode);
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
        $this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));        
    }
    
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        $this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex);
    }
}
class OCSLogicalInterfaceCollector extends MySQLCollector
{
}

class OCSMobilePhoneCollector extends MySQLCollector
{
    protected $oOSVersionLookup;
    protected $oModelLookup;

    public function AttributeIsOptional($sAttCode)
    {
        // For backward comptability with previous versions which were adding an ocsid field
        if ($sAttCode == 'ocsid') return true;

       return parent::AttributeIsOptional($sAttCode);
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
        $this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));

        // Retrieve the identifiers of the Model since we must do a lookup based on two fields: Brand + Model
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        $this->oModelLookup = new LookupTable('SELECT Model', array('brand_id_friendlyname', 'name'));
    }

    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        $this->oOSVersionLookup->Lookup($aLineData, array('ocs_osfamily_id', 'ocs_osversion_id'), 'ocs_osversion_id', $iLineIndex);
        $this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex);
    }
}

class OCSPrinterCollector extends MySQLCollector
{
}
class OCSSoftwareCollector extends MySQLCollector
{
    protected function AddQqch(&$sQuery)
    {
        $sQueryITop =  Utils::GetConfigurationValue("OCSSoftware_getListFromItop", '');
        $oRestClient = new RestClient();
        $aResult = $oRestClient->Get( "Software", $sQueryITop,"name, type");

        $aListSoftware = [];
        foreach ($aResult['objects'] as $idx =>$aAttDef) {
            $sType =$aAttDef['fields']['type'];
            if (is_null($sType )){
                $sType =  'OtherSoftware';
            }
            $aListSoftware[$sType][$aAttDef['fields']['name']]= $aAttDef['fields']['name'];
        }

        $sInitialQuery =  $sQuery;
        $bIsFirst=true;
        foreach ($aListSoftware as $sType =>$aName) {
            echo('##############################################');
            echo($sType.'::'. implode("','",$aName));
            echo('##############################################');
            $sQueryByType = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_softwarelist#', implode("','", $aName), $sInitialQuery);
            $sQueryByType = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_type_id#', $sType, $sQueryByType);
            if ($bIsFirst) {
                $sQuery = $sQueryByType;
                $bIsFirst=false;
            } else   {
                $sQuery = $sQuery . ' UNION ' . $sQueryByType;
            }
        }
        echo('##############################################');
        echo( $sQuery);
        echo('##############################################');
    }
}

class OCSPCSoftwareCollector extends MySQLCollector
{
    protected function AddQqch(&$sQuery)
    {
        $sQueryITop =  Utils::GetConfigurationValue("OCSPCSoftware_getListFromItop", '');
        $oRestClient = new RestClient();
        $aResult = $oRestClient->Get( "Software", $sQueryITop,"name");

        $aListSoftware = [];
        foreach ($aResult['objects'] as $idx =>$aAttDef) {
            $aListSoftware[$aAttDef['fields']['name']]= $aAttDef['fields']['name'];
        }
        echo('##############################################');
        echo( implode("','",$aListSoftware));
        echo('##############################################');
        $sQuery = str_replace('#ERROR_UNDEFINED_PLACEHOLDER_softwarelist#', implode("','",$aListSoftware), $sQuery);
    }
}