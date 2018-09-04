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

require_once(APPROOT.'collectors/ocs_classes.php');

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

