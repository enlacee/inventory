<?php

class IndexController extends Zend_Controller_Action
{
    private $objDatos;
    private $auth;
    
    public function init()
    { 
        $this->objDatos=new Application_Model_Index();
        $this->auth=new Application_Model_Auth();
        $this->auth->validar_session();
        $this->auth->defines();
    }
    public function usuariosMenuAction()
    {
        $rs = $this->objDatos->sp_usuarios_menu($p);
        return $rs;
    }
    
    public function usuariosMenuArmadoAction($val = 0)
    {
        return $this->usuariosMenuAction();
    }

    public function indexAction()
    {
        //Zend_Session::start();
        $this->objSess = new Zend_Session_Namespace('inventory');		
        $rstData = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".date('Y-m-d')."'");

        if ($rstData[0]["valor_compra"] > 0 && $rstData[0]["valor_venta"] > 0) {
            $this->objSess->se_tc_c = $rstData[0]["valor_compra"];
            $this->objSess->se_tc_v = $rstData[0]["valor_venta"];
        }else{
            $this->objSess->se_tc_c = 0;
            $this->objSess->se_tc_v = 0;
        }
		
        $this->view->menu = $this->usuariosMenuArmadoAction();        
        $this->view->se_age_id = $this->objSess->se_age_id;
        $this->view->se_tienda = $this->objSess->se_tienda;
        $this->view->se_valida_stock = $this->objSess->se_valida_stock;
        $this->view->sesion = $this->objSess;
    }
	
	
}