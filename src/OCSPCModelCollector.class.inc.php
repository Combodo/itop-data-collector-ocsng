<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSPCModelCollector extends AbstractOCSAssetCollector
{
    protected function GetTargetClass()
    {
        return 'Model';
    }

	public function CheckToLaunch(array $aOrchestratedCollectors): bool
    {
        if (Utils::GetConfigurationValue('PCCollection', 'no') == 'yes') {
            return true;
        }
        return false;
    }
}