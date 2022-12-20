<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

include(APPROOT . '/collectors/vendor/autoload.php'); // composer autoload

// Initialize collection plan
require_once(APPROOT . 'collectors/src/OCSCollectionPlan.class.inc.php');
$oOCSCollectionPlan = new OCSCollectionPlan();
$oOCSCollectionPlan->Init();
$oOCSCollectionPlan->AddCollectorsToOrchestrator();

