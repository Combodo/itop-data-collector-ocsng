<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSIPv4AddressCollector extends OCSIPAddressCollector
{
    public function checkToLaunch():bool
    {
        if ($this->GetOCSCollectionPlan()->IsTeemIpInstalled() && Utils::GetConfigurationValue('IPCollection', 'no') == 'yes') {
                Utils::Log(LOG_INFO, 'IPs will be collected');
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