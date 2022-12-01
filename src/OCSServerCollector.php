<?php

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
        if ($sAttCode == 'tickets_list') {
            Utils::Log(LOG_INFO, "[" . __CLASS__ . "] The column tickets_list is used for storing the OCS ID in order to display the OCS tab on Servers. You can safely ignore the warning about it.");
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