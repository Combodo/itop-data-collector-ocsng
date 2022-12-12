<?php


class OCSPCCollector extends AbstractOCSAssetCollector
{

    protected $oOSVersionLookup;
    protected $oOSLicenceLookup;
    protected $oModelLookup;

    public function AttributeIsOptional($sAttCode)
    {
        // For backward comptability with previous versions which were adding an ocsid field
        if ($sAttCode == 'ocsid') return true;

        return parent::AttributeIsOptional($sAttCode);
    }

    protected function InitProcessBeforeSynchro()
    {

        // Retrieve the identifiers of the Model since we must do a lookup based on two fields: Brand + Model
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        $this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));
        $this->oOSLicenceLookup = new LookupTable('SELECT OSLicence', array('osversion_id', 'name'));
        $this->oModelLookup = new LookupTable('SELECT Model', array('brand_id_friendlyname', 'name'), false /* non-case sensitive */);
    }

    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV

        $this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex);
        $this->oOSLicenceLookup->Lookup($aLineData, array('osversion_id', 'oslicence_id'), 'oslicence_id', $iLineIndex);
        $this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex);
    }

    protected function GetTargetClass()
    {
        return 'PC';
    }
}