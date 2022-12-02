<?php

class OCSVirtualMachineTeemIpCollector extends OCSVirtualMachineCollector
{
    protected $oIPAddressLookup;

    public function AttributeIsOptional($sAttCode)
    {
        // If the collector is connected to TeemIp standalone, there is no "providercontracts_list" on VMs. Let's safely ignore it.
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

        $this->oIPAddressLookup->Lookup($aLineData, array('org_id', 'managementip_id'), 'managementip_id', $iLineIndex);
    }
}