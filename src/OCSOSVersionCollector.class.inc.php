<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSOSVersionCollector extends AbstractOCSCollector
{
    public function checkToLaunch():bool
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