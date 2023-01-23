<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSAssetCategoryCollector extends AbstractOCSCollector
{
    public function checkToLaunch():bool
    {
	    if (Utils::GetConfigurationValue('CategoryCollection', 'no') == 'yes') {
            return true;
		}
		return false;
    }
}