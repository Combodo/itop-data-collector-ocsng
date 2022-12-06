<?php

abstract class AbstractOCSCollector extends MySQLCollector
{
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

    /**
     * Get the end of SQL parameter name (prefixed by class name) in the config file
     *
     * @return mixed|string
     * @throws Exception
     */
    private function GetSQLQueryName()
    {
       return "_query";
    }

    protected function AddOtherParams(&$sQuery)
    {
    }
}