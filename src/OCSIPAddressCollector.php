<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSIPAddressCollector extends AbstractOCSCollector
{
     public function AttributeIsOptional($sAttCode)
    {
        if (!$this->GetOCSCollectionPlan()->IsIpDiscoveryInstalled()) {
            if ($sAttCode == 'fqdn_from_iplookup') return true;
            if ($sAttCode == 'last_discovery_date') return true;
            if ($sAttCode == 'responds_to_iplookup') return true;
            if ($sAttCode == 'responds_to_ping') return true;
            if ($sAttCode == 'responds_to_scan') return true;
        }
        if (!$this->GetOCSCollectionPlan()->IsTeemIpZoneMgmtInstalled()) {
            if ($sAttCode == 'view_id') return true;
        }

        return parent::AttributeIsOptional($sAttCode);
    }
}