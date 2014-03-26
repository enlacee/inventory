<?php

class Application_Model_Adodb {

    private $arrayDsn = array();
    private $conex;
    private $parameter = array();
    private $nomProcedure = '';
    private $data = '';
    private $arrayData = array();
    private $query;
    private $arrayConfig;

    public function ConnectionOpen($_dsn=array(), $_sp) {
        //$this->arrayConfig = Util::read_ini(REALPATHAPP . 'config/config.ini');
        //$tipo = trim($this->arrayConfig['server']['type']);
        try {
            /*$this->arrayDsn = $_dsn;
            if ( $this->arrayDsn['dbname'] = $_db == '' )
                $this->arrayDsn['dbname'] = $_dsn['dbname'];
            else{
                if ( in_array( $_db, array( 'plus', 'pcll', 'maestra' ) ) && $tipo != 'desarrollo' )
                    $this->arrayDsn['dbname'] = $_dsn['dbname'];
                else
                    $this->arrayDsn['dbname'] = $_db;
            }*/
            //$this->arrayDsn['dbname'] = $_db == '' ? $_dsn['dbname'] : $_db;
            $this->nomProcedure = $_sp;
            //$dsn = $_dsn['dbtype'] . ':host=' . $_dsn['dbhost'] . ';service=' . $_dsn['dbservices'] . ';database=' . $_dsn['dbname'] . ';server=' . $_dsn['dbserver'] . ';protocol=' . $_dsn['protocolo'] . ';EnableScrollableCursors=' . $_dsn['scrolle'];
            $dsn = $_dsn['dbtype'] . ':host=' . $_dsn['dbhost'] . ';dbname=' . $_dsn['dbname'];
            $this->conex = new PDO($dsn, $_dsn['dbuser'], $_dsn['dbpass']);
        } catch (PDOException $e) {
            throw new Exception("Error:Conexion Fallida");
        }
    }
	
	public function setNomProcedure($nomProcedure){
		$this->nomProcedure = $nomProcedure;
	}

    public function SetParameterSP($pValue, $_tparam) {
        $_tparam = trim($_tparam) == '' ? 'VARCHAR' : trim($_tparam);
        switch (strtoupper($_tparam)) {
            case "NUMERIC": case "INT": case "INTEGER": case "DECIMAL":
                if ($pValue == "")
                    $pValue = "NULL";
                else if (strtoupper($pValue) == "NULL")
                    $pValue = "NULL";
                else
                    $pValue = "$pValue";
                break;
            case "VARCHAR":case "DATE": case "TEXT": default:
                if ($pValue == "")
                    $pValue = "''";
                else if (strtoupper($pValue) == "NULL")
                    $pValue = "NULL";
                else
                    $pValue = "'$pValue'";
                break;
        }
        $this->parameter[] = $pValue;
    }

    public function Prepare_Procedure() {
        $query = "call " . $this->nomProcedure . "(";
        if (count($this->parameter) > 0)
            foreach ($this->parameter as $value)
                $query.=$value . ",";
        $len = strlen($query);
        if (count($this->parameter) > 0)
            $len-=1;
        $this->query = substr($query, 0, $len) . ")";
        return $this->query;
    }

    public function getSql() {
        return $this->Prepare_Procedure();
    }

    public function ExecuteSP() {
        $query = $this->Prepare_Procedure();
		//echo $query;
                //die();
        $this->data = $this->conex->query($query);
        //var_dump($this->data);
        $this->arrayData = $this->data->fetchAll();
        //var_dump($this->arrayData);
        return $this->arrayData;
    }

    public function ExecuteSPArray() {
        /* $this->ExecuteSP();
          while($data = @$this->data->fetch_array(MYSQLI_BOTH)){
          $this->arrayData[]=$data;
          }
          return count(@$this->arrayData)>0?@$this->arrayData:array(); */
    }

    public function ReiniciarSQL() {
        unset($this->query);
        unset($this->parameter);
        unset($this->arrayData);
    }

    public function Close() {
        //$this->data->close();
        //$this->conex->close();
    }

    public function setQuery($_query='') {
        $this->query = $_query;
    }

    public function getColumnCount() {
        return $this->data->columnCount();
    }
	
	public function obtenerDataSQL($query) {
        $this->data = $this->conex->query($query);
        $this->arrayData = $this->data->fetchAll();
        return $this->arrayData;
    }
	
	public function ejecutarSQL($query) {
        $this->data = $this->conex->exec($query);
        return $this->data;
    }
	
	public function iniciaTransaccion() {
        return $this->ejecutarSQL("SET AUTOCOMMIT=0; START TRANSACTION;");
    }
	
	public function finalizaTransaccion() {
        return $this->ejecutarSQL("COMMIT;");
    }
	
	public function abortaTransaccion() {
        return $this->ejecutarSQL("ROLLBACK;");
    }
	
}

?>
