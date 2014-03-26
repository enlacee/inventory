<?php

class ImpresionController extends Zend_Controller_Action
{
    private $objDatos;
    private $objDatos_index;
    private $auth;
    
    public function init()
    { 
        /*$this->objDatos=new Application_Model_Tablas();
        $this->objDatos_index=new Application_Model_Index();
        $this->auth=new Application_Model_Auth();
        $this->auth->validar_session();
        $this->auth->defines();*/
    }
	
	//Funciones para tipoCambio >>>	
	public function indexAction()
    {
        $this->view->title = "Impresion";
    }
    
}