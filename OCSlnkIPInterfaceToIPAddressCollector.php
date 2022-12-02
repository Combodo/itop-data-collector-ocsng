<?php
// Copyright (C) 2018 Combodo SARL
//
//   This application is free software; you can redistribute it and/or modify
//   it under the terms of the GNU Affero General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.
//
//   iTop is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU Affero General Public License for more details.
//
//   You should have received a copy of the GNU Affero General Public License
//   along with this application. If not, see <http://www.gnu.org/licenses/>

class OCSlnkIPInterfaceToIPAddressCollector extends MySQLCollector
{
	protected $oIPAddressLookup;
	protected $oIPInterfaceLookup;

	protected function InitProcessBeforeSynchro()
	{
		parent::InitProcessBeforeSynchro();

		$this->oIPAddressLookup = new LookupTable('SELECT IPAddress', array('org_name', 'friendlyname'));
		$this->oIPInterfaceLookup = new LookupTable('SELECT IPInterface', array('macaddress'));
	}

	protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
	{
		// Process each line of the CSV
		parent::ProcessLineBeforeSynchro($aLineData, $iLineIndex);

		$this->oIPAddressLookup->Lookup($aLineData, array('org_id', 'ipaddress_id'), 'ipaddress_id', $iLineIndex);
		$this->oIPInterfaceLookup->Lookup($aLineData, array('macaddress'), 'ipinterface_id', $iLineIndex);
	}
}


