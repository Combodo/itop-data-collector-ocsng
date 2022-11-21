<?php

/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

class OCSDBServerCollector extends AbstractOCSSoftwareCollector
{

	public function AttributeIsOptional($sAttCode)
	{
		if ($this->GetOCSCollectionPlan()->IsTeemIpInstalled()) {
			if ($sAttCode == 'providercontracts_list') return true;
			if ($sAttCode == 'services_list') return true;
			if ($sAttCode == 'tickets_list') return true;
		}

		return parent::AttributeIsOptional($sAttCode);
	}
    protected function GetTargetClass()
    {
        return 'DBServer';
    }
}