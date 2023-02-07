<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
abstract class AbstractOCSCollector extends MySQLCollector
{
    protected $oOCSCollectionPlan;
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function Init(): void
    {
        parent::Init();
        $this->oOCSCollectionPlan = OCSCollectionPlan::GetPlan();
    }

    public function GetOCSCollectionPlan() {
        return $this->oOCSCollectionPlan;
    }

    /**
     * Runs the configured query to start fetching the data from the database
     *
     * @see Collector::Prepare()
     */
    public function Prepare()
    {
        $bRet = parent::Prepare();
        if (!$bRet) {
            return false;
        }

        $bRet = $this->Connect(); // Establish the connection to the database
        if (!$bRet) {
            return false;
        }

        // Read the SQL query from the configuration
        $sSQLQueryName =  $this->GetSQLQueryName(); // by default "_query"
        $sQuery = Utils::GetConfigurationValue(get_class($this) .$sSQLQueryName, '');
        if ($sQuery == '') {
            // Try all lowercase
            $sQuery = Utils::GetConfigurationValue(strtolower(get_class($this)) . $sSQLQueryName, '');
        }

        if ($sQuery == '') {
            // No query at all !!
            Utils::Log(LOG_ERR, "[" . get_class($this) . "] no SQL query configured! Cannot collect data. The query was expected to be configured as '" . strtolower(get_class($this)) . "_query' in the configuration file.");

            return false;
        }

        //replace others params in query specially used when a request is used in order to replace some fields
        $this->AddOtherParams($sQuery);

        $this->oStatement = $this->oDB->prepare($sQuery);
        if ($this->oStatement === false) {
            $aInfo = $this->oDB->errorInfo();
            Utils::Log(LOG_ERR, "[" . get_class($this) . "] Failed to prepare the query: '$sQuery'. Reason: " . $aInfo[0] . ', ' . $aInfo[2]);

            return false;
        }

        $this->oStatement->execute();
        if ($this->oStatement->errorCode() !== '00000') {
            $aInfo = $this->oStatement->errorInfo();
            Utils::Log(LOG_ERR, "[" . get_class($this) . "] Failed to execute the query: '$sQuery'. Reason: " . $aInfo[0] . ', ' . $aInfo[2]);

            return false;
        }

        $this->idx = 0;

        return true;
    }

	protected function TestIfTableExistsInOCS($sTableName){
		$oExistsStatement = $this->oDB->query("SHOW TABLES LIKE '$sTableName'");
		if ($oExistsStatement === false) {
			$aInfo = $this->oDB->errorInfo();
			Utils::Log(LOG_ERR, "[" . get_class($this) . "] Failed to prepare the query: SHOW TABLES LIKE '$sTableName'. Reason: " . $aInfo[0] . ', ' . $aInfo[2]);
			return false;
		}

		if ($oExistsStatement->rowCount()>0){
			return true;
		} else {
			return false;
		}
	}

    /**
     * Get the end of SQL parameter name (prefixed by class name) in the config file
     *
     * @return mixed|string
     * @throws Exception
     */
	protected function GetSQLQueryName()
    {
       return "_query";
    }

	protected function AddOtherParams(&$sQuery)
    {
    }
}