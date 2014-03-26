<?php

class LoginController extends Zend_Controller_Action
{
    private $objDatos;
    private $auth;
    private $sessName;
	
    public function init()
    {
        //Zend_Session::start();
        $this->sessName = new Zend_Session_Namespace('inventory');		
        $this->objDatos=new Application_Model_Login();
        $this->auth=new Application_Model_Auth();
    }

    public function indexAction($p = array())
    {

    }
	
    public function hora_local($zona_horaria = 0)
    {
        if ($zona_horaria > -12.1 and $zona_horaria < 12.1) {
            $hora_local = time() + ($zona_horaria * 3600);
            return $hora_local;
        }
        return 'error';
    }

    public function obtenerUsuarioAction()
    {
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
    public function validarUsuarioAction()
    {
        $this->_helper->viewRenderer->setNoRender();		
        $p["usuario"] = $_POST["user"];
        setcookie("cookie_usuario", $_POST["user"]);
        $p["clave"] = $_POST["password"];
        $rs = $this->objDatos->sp_usuarios_login($p);
        
        if ($rs[0]["usr_id"] > 0) {			
            $this->sessName->se_usr_id = $rs[0]["usr_id"];
            $this->sessName->se_usp_id = $rs[0]["usp_id"];
            $this->sessName->se_nombres = $rs[0]["nombres"];
            $this->sessName->se_apellidos = $rs[0]["apellidos"];
            $this->sessName->se_cargo = $rs[0]["cargo"];
            $this->sessName->se_area = $rs[0]["area"];
            $this->sessName->se_age_id = $rs[0]["tie_id"];
            $this->sessName->se_tienda = $rs[0]["tienda"];
            $this->sessName->se_valida_stock = $rs[0]["valida_stock"];
            //$this->sessName->se_tc_c=200;
            $rstData = $this->objDatos->sp_obtenerdatasql("select * from configuracion where tie_id = ".$rs[0]["tie_id"]);
            if ($rstData[0]["con_id"] > 0) {
                $this->sessName->se_apertura_stock = $rstData[0]["apertura_stock"];
                $this->sessName->se_apertura_clientes = $rstData[0]["apertura_clientes"];
                $this->sessName->se_apertura_proveedores = $rstData[0]["apertura_proveedores"];
                $this->sessName->se_moneda_almacen = $rstData[0]["moneda_almacen"];
                $this->sessName->se_utilidad = $rstData[0]["utilidad"];
                $this->sessName->se_desc1 = $rstData[0]["desc1"];
                $this->sessName->se_desc2 = $rstData[0]["desc2"];
                $this->sessName->se_desc3 = $rstData[0]["desc3"];
                $this->sessName->se_desc4 = $rstData[0]["desc4"];
                $this->sessName->se_calculo_precios = $rstData[0]["calculo_precios"];
                $this->sessName->se_igv = $rstData[0]["igv"];			
                $this->sessName->se_anio_inicial = $rstData[0]["anio_inicial"];			
            }

            $rstData = $this->objDatos->sp_obtenerdatasql("select * from tipo_cambio where fecha = '".date('Y-m-d')."'");
            if ($rstData[0]["valor_compra"]>0 and $rstData[0]["valor_venta"] > 0) {
                $this->sessName->se_tc_c = $rstData[0]["valor_compra"];
                $this->sessName->se_tc_v = $rstData[0]["valor_venta"];
            } else {
                $this->sessName->se_tc_c = 0;
                $this->sessName->se_tc_v = 0;
            }            
            $this->auth->defines();
            $this->_redirect("/");
        } else {
            $this->_redirect("login?msn=1");
        }
    }

    public function getStatusSessionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        if ($this->sessName->time === true) {
            $successTime = true;
        } else {
            $successTime = false;
        }
        $array = array();
        $array['status_time'] = $successTime;
        echo json_encode($array);
    }
    
    public function expireAction()
    {
        $this->_helper->viewRenderer->setNoRender();
			
		$rs=$this->objDatos->sp_eventos_guardar(array('usr_id'=>$this->sessName->se_usr_id, 'evento'=>'S', 'tabla'=>'login', 'registro'=>$this->sessName->se_usr_id, 'detalle'=>'SALIDA DEL SISTEMA DE '.$this->sessName->se_nombres.' '.$this->sessName->se_apellidos.', TIENDA: '.$this->sessName->se_tienda));
		
        Zend_Session::namespaceUnset("inventory");
		$this->_redirect(PUBLIC_PATH);
		
    }
}

?>