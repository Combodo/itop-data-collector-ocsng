<?php
// Copyright (C) 2014 Combodo SARL
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


class OCSBrandCollector extends SQLCollector
{
}

class OCSOSFamilyCollector extends SQLCollector
{
}
class OCSOSVersionCollector extends SQLCollector
{
}

class OCSServerModelCollector extends SQLCollector
{
}

class OCSServerCollector extends SQLCollector
{
	protected function MustProcessBeforeSynchro()
	{
		// We must reprocess the CSV data obtained from the inventory script
		// to lookup the Brand/Model and OSFamily/OSVersion in iTop
		return true;
	}
 
	protected function InitProcessBeforeSynchro()
	{
		// Retrieve the identifiers of the OSVersion since we must do a lookup based on two fields: Family + Version
		// which is not supported by the iTop Data Synchro... so let's do the job of an ETL
		$this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));		
 
		// Retrieve the identifiers of the Model since we must do a lookup based on two fields: Brand + Model
		// which is not supported by the iTop Data Synchro... so let's do the job of an ETL
		$this->oModelLookup = new LookupTable('SELECT Model', array('brand_id_friendlyname', 'name'));		
	}
 
	protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
	{
		// Process each line of the CSV
		$this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex);
		$this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex);
	}
}

class OCSServerPhysicalInterfaceCollector extends SQLCollector
{
}

class OCSPCModelCollector extends SQLCollector
{
}

class OCSPCCollector extends SQLCollector
{
	protected function MustProcessBeforeSynchro()
	{
		// We must reprocess the CSV data obtained from the inventory script
		// to lookup the Brand/Model and OSFamily/OSVersion in iTop
		return true;
	}
 
	protected function InitProcessBeforeSynchro()
	{
		// Retrieve the identifiers of the OSVersion since we must do a lookup based on two fields: Family + Version
		// which is not supported by the iTop Data Synchro... so let's do the job of an ETL
		$this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));		
 
		// Retrieve the identifiers of the Model since we must do a lookup based on two fields: Brand + Model
		// which is not supported by the iTop Data Synchro... so let's do the job of an ETL
		$this->oModelLookup = new LookupTable('SELECT Model', array('brand_id_friendlyname', 'name'));		
	}
 
	protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
	{
		// Process each line of the CSV
		$this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex);
		$this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex);
	}
}

class OCSPCPhysicalInterfaceCollector extends SQLCollector
{
}




class OCSVirtualMachineCollector extends SQLCollector
{
	protected function MustProcessBeforeSynchro()
	{
		// We must reprocess the CSV data obtained from the inventory script
		// to lookup the Brand/Model and OSFamily/OSVersion in iTop
		return true;
	}
 
	protected function InitProcessBeforeSynchro()
	{
		// Retrieve the identifiers of the OSVersion since we must do a lookup based on two fields: Family + Version
		// which is not supported by the iTop Data Synchro... so let's do the job of an ETL
		$this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));		
	}
 
	protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
	{
		// Process each line of the CSV
		$this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex);
	}
}
class OCSLogicalInterfaceCollector extends SQLCollector
{
}


$index = 1;

Orchestrator::AddCollector($index++, 'OCSBrandCollector');
Orchestrator::AddCollector($index++, 'OCSOSFamilyCollector');
Orchestrator::AddCollector($index++, 'OCSOSVersionCollector');

if (Utils::GetConfigurationValue('ServerCollection', 'yes') == 'yes')
{
	Orchestrator::AddCollector($index++, 'OCSServerModelCollector');
	Orchestrator::AddCollector($index++, 'OCSServerCollector');
	Orchestrator::AddCollector($index++, 'OCSServerPhysicalInterfaceCollector');
}

if (Utils::GetConfigurationValue('PCCollection', 'yes') == 'yes')
{
	Orchestrator::AddCollector($index++, 'OCSPCModelCollector');
	Orchestrator::AddCollector($index++, 'OCSPCCollector');
	Orchestrator::AddCollector($index++, 'OCSPCPhysicalInterfaceCollector');
}

if (Utils::GetConfigurationValue('VMCollection', 'yes') == 'yes')
{
	Orchestrator::AddCollector($index++, 'OCSVirtualMachineCollector');
	Orchestrator::AddCollector($index++, 'OCSLogicalInterfaceCollector');
}

