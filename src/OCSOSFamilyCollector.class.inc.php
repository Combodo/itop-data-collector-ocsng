<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSOSFamilyCollector extends AbstractOCSCollector
{
	public function CheckToLaunch(array $aOrchestratedCollectors): bool
    {
	    if (Utils::GetConfigurationValue('MobilePhoneCollection', 'no') == 'yes'
		    || Utils::GetConfigurationValue('PCCollection', 'no') == 'yes'
		    || Utils::GetConfigurationValue('ServerCollection', 'no') == 'yes'
		    || Utils::GetConfigurationValue('VMCollection', 'no') == 'yes') {
		    return true;
	    }
	    return false;
    }
}