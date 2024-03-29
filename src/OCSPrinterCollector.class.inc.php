<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSPrinterCollector extends AbstractOCSCollector
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

	public function CheckToLaunch(array $aOrchestratedCollectors): bool
    {
        if (Utils::GetConfigurationValue('PrinterCollection', 'no') == 'yes') {
            return true;
        }
        return false;
    }

	protected function GetSQLQueryName()
	{
		$sSQLQueryName = "_query";
		if ($this->TestIfTableExistsInOCS('listprinters')) {
			$sSQLQueryName = "_with_listprinters".$sSQLQueryName;
		}
		return $sSQLQueryName;
	}
}