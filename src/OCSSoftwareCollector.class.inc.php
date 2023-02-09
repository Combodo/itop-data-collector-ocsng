<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
class OCSSoftwareCollector extends AbstractOCSSoftwareCollector
{
	public function AttributeIsOptional($sAttCode)
	{
		if ($sAttCode == 'cvss') return true;

		return parent::AttributeIsOptional($sAttCode);
	}

    protected function GetTargetClass()
    {
        return 'Software';
    }

}