<?php

/*
 * Creado por Luis Remicio
 * Verifica que el usuario de sistema de encuentre logueado correctamente
 */

class Application_Model_Auth
{

    private $objSess;

    public function defines()
    {
        //Zend_Session::start();
        $this->objSess = new Zend_Session_Namespace('inventory');

        define(VAR_USR_ID, $this->objSess->se_usr_id);
        define(VAR_NOMBRES, $this->objSess->se_nombres);
        define(VAR_APELLIDOS, $this->objSess->se_apellidos);
        define(VAR_CARGO, $this->objSess->se_cargo);
        define(VAR_AREA, $this->objSess->se_area);
    }
    
    public function validar_session()
    {
        //Zend_Session::start();
        $sessName = new Zend_Session_Namespace('inventory');
        
        if ($sessName->se_usr_id <= 0 or $sessName->se_usr_id == "") {
            Zend_Session::namespaceUnset("inventory");
            header("location: ".PUBLIC_PATH."/login");
        }
    }
}