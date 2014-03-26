<?php

class Application_Model_Connection {

    private $arrayConfig;
    private $arrayDsnMySql00;

    public function getInitDsnMySql00() {
        defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
        $this->arrayConfig = parse_ini_file(APPLICATION_PATH."/configs/application.ini", true);

        $this->arrayDsnMySql00['dbtype'] = $this->arrayConfig['production']['dbtype'];
        $this->arrayDsnMySql00['dbhost'] = $this->arrayConfig['production']['dbhost'];
        //$this->arrayDsnInformix00['dbservices'] = $this->arrayConfig['server_236']['dbservices'];
        $this->arrayDsnMySql00['dbname'] = $this->arrayConfig['production']['dbname'];
        //$this->arrayDsnInformix00['dbserver'] = $this->arrayConfig['server_236']['dbserver'];
        //$this->arrayDsnInformix00['protocolo'] = $this->arrayConfig['server_236']['protocolo'];
        //$this->arrayDsnInformix00['scrolle'] = $this->arrayConfig['server_236']['scrolle'];
        $this->arrayDsnMySql00['dbuser'] = $this->arrayConfig['production']['dbuser'];
        $this->arrayDsnMySql00['dbpass'] = $this->arrayConfig['production']['dbpass'];
        return $this->arrayDsnMySql00;
    }

}