<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSIPv6AddressCollector extends OCSIPAddressCollector
{
    public function checkToLaunch():bool
    {
        if ($this->GetOCSCollectionPlan()->IsTeemIpInstalled() && Utils::GetConfigurationValue('collect_ips', 'no') == 'yes') {
                Utils::Log(LOG_WARNING, "IPv6 creation and update is not supported in iTop <3.1.0");
                return true;
        }
        return false;
    }
	public function AttributeIsOptional($sAttCode)
	{
		if (!$this->GetOCSCollectionPlan()->IsTeemIpZoneMgmtInstalled()) {
			if ($sAttCode == 'view_id') return true;
		}
		return parent::AttributeIsOptional($sAttCode);
	}
}