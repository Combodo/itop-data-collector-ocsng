<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSPCPhysicalInterfaceCollector extends AbstractOCSAssetCollector
{
	public function CheckToLaunch(array $aOrchestratedCollectors): bool
    {
        if (Utils::GetConfigurationValue('PCCollection', 'no') == 'yes') {
            return true;
        }
        return false;
    }
    public function AttributeIsOptional($sAttCode)
    {
        if ($this->GetOCSCollectionPlan()->IsTeemIpInstalled()) {
            if ($sAttCode == 'ipaddress') return true;
            if ($sAttCode == 'ipgateway') return true;
            if ($sAttCode == 'ipmask') return true;
        } else  {
            if ($sAttCode == 'ip_list') return true;
            if ($sAttCode == 'vrfs_list') return true;
        }
        return parent::AttributeIsOptional($sAttCode);
    }

	protected function GetTargetClass()
	{
		return 'PhysicalInterface';
	}
}