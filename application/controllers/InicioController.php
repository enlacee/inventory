<?php

class InicioController extends Zend_Controller_Action
{
    private $objDatos;
    private $auth;
	private $sessName;
	
    public function init()
    {
		//Zend_Session::start();
        $this->sessName = new Zend_Session_Namespace('inventory');
		$this->objDatos=new Application_Model_Inicio();
    }

    public function indexAction($p)
    {

    }
	
	public function obtenerAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		echo "HOLA";
	}
}

?>