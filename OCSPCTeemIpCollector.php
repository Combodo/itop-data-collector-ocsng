<?php

class OCSPCTeemIpCollector extends OCSPCCollector
{
    protected $oIPAddressLookup;

    public function AttributeIsOptional($sAttCode)
    {
        // If the collector is connected to TeemIp standalone, there is no "providercontracts_list" on PCs. Let's safely ignore it.
        if ($sAttCode == 'providercontracts_list') return true;
        if ($sAttCode == 'services_list') return true;

        return parent::AttributeIsOptional($sAttCode);
    }

    protected function InitProcessBeforeSynchro()
    {
        parent::InitProcessBeforeSynchro();

        $this->oIPAddressLookup = new LookupTable('SELECT IPAddress', array('org_name', 'friendlyname'));
    }

    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        parent::ProcessLineBeforeSynchro($aLineData, $iLineIndex);

        $this->oIPAddressLookup->Lookup($aLineData, array('org_id', 'ipaddress_id'), 'ipaddress_id', $iLineIndex);
    }

}