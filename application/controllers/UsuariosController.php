<?php
class UsuariosController extends Zend_Controller_Action
{
    private $objDatos;
    private $objDatos_index;
    private $auth;
	private $sessName;
    
    public function init()
    { 
		//Zend_Session::start();
        $this->sessName = new Zend_Session_Namespace('inventory');
		
		if($this->sessName->se_usr_id=='' or $this->sessName->se_usr_id == NULL){
			$data = array('success' => false, 'mensaje' => 'Se ha cerrado la sesion, inicie de nuevo la sesion');
			echo json_encode($data);
		}
		
		$_POST['se_usr_id']=$this->sessName->se_usr_id;
		$_POST['se_age_id']=$this->sessName->se_age_id;
        $this->objDatos=new Application_Model_Usuarios();
        $this->objDatos_index=new Application_Model_Index();
        $this->auth=new Application_Model_Auth();
        $this->auth->validar_session();
        $this->auth->defines();
		
    }

    public function usuariosDescribeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos_index->sp_table_describe($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function usuariosListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_usuarios_lista($_POST);
        
        $array=Array();
        foreach($rs as $fila)
        {
            $fila["nombres"]=utf8_encode($fila["nombres"]);
            $fila["apellidos"]=utf8_encode($fila["apellidos"]);
            $array[]=$fila;
        }
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $array);
        echo json_encode($data);
    }
	
	public function usuariosListaImpresionAction()
    {
		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Usuarios',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);
		
		$col_names = array(
			'usr_id' => 'Codigo',
			'nombres' => 'Nombre',
			'apellidos' => 'Apellidos',
			'cargo' => 'Cargo',
			'usu_per' => 'Perfil',
			'telefono' => 'Telefono',
			'email' => 'E-Mail',
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'usr_id' => array('justification'=>'center'),
				'nombres' => array('justification'=>'left'),
				'apellidos' => array('justification'=>'left'),
				'cargo' => array('justification'=>'center'),
				'usu_per' => array('justification'=>'center'),
				'telefono' => array('justification'=>'center'),
				'email' => array('justification'=>'center'),
			)
		);
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		$_POST['sort']=$_POST['txtsort'];
		$_POST['dir']=$_POST['txtdir'];
		
		$db_data = $this->objDatos->sp_usuarios_lista($_POST);
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($db_data, $col_names, 'Listado de Usuarios '.$_POST['txtfiltro'], $options);
		$pdf->ezStream();
		
    }
	
    public function usuariosEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_usuarios_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }

    public function usuariosGuardarAction()
    {
		
		
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from usuarios where nombres = '".$_POST['nombres']."' and apellidos = '".$_POST['apellidos']."' ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Usuario con el nombre '.$_POST['nombres']." ".$_POST['apellidos'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from usuarios where usuario = '".$_POST['usuario']."' ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Usuario con el inicio de sesion '.$_POST['usuario'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_usuarios_guardar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }

    public function usuariosActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from usuarios where nombres = '".$_POST['nombres']."' and apellidos = '".$_POST['apellidos']."' and usr_id <> ".$_POST['usr_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Usuario con el nombre '.$_POST['nombres']." ".$_POST['apellidos'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from usuarios where usuario = '".$_POST['usuario']."' and usr_id <> ".$_POST['usr_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Usuario con el inicio de sesion '.$_POST['usuario'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}

        $rs = $this->objDatos->sp_usuarios_actualizar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }

    public function tiendaListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_tienda_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function usuariosAction()
    {		
        $rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
        $perfil = $rs[0][0];
echo $perfil;
        $rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 1 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
    
    public function usuariosTipoListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_usuarios_tipo_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
	
	public function usuariosTipoListaImpresionAction()
    {
        ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		//$pdf = new ZendCPdf_Cezpdf();
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Tipos de Usuarios',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);
		
		$col_names = array(
			'ust_id' => 'Codigo',
			'nombre' => 'Nombre',
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'ust_id' => array('justification'=>'center'),
				'nombre' => array('justification'=>'left'),
			)
		);
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		if($_POST["campo"]=="" || $_POST["query"]==""){
			$filtro = "";
		}else{
			$sql = $sql." where usuarios_perfil.".$_POST["campo"]." like '%".$_POST["query"]."%'";
			$filtro = "FILTRO : ".$_POST["campo"]." = ".$_POST["query"];
		}
                
		$db_data = $this->objDatos->sp_usuarios_tipo_lista($_POST);
                
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($db_data, $col_names, $filtro, $options);
		$pdf->ezStream();
		
    }
    
    public function usuariosTipoEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_usuarios_tipo_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }

    public function usuariosTipoGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from usuarios_tipo where nombre = '".$_POST['nombre']."' ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Usuario con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_usuarios_tipo_guardar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }

    public function usuariosTipoActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from usuarios_tipo where nombre = '".$_POST['nombre']."' and ust_id <> ".$_POST['ust_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Tipo de Usuario con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_usuarios_tipo_actualizar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function usuariosTipoAction()
    {
        $rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 3 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
    
    public function usuariosPerfilListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_usuarios_perfil_lista($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function usuariosPerfilEliminarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $rs = $this->objDatos->sp_usuarios_perfil_eliminar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }

    public function usuariosPerfilGuardarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from usuarios_perfil where nombre = '".$_POST['nombre']."' ");
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Perfil con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_usuarios_perfil_guardar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }

    public function usuariosPerfilActualizarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		
		$rs = $this->objDatos->sp_obtenerdatasql("select * from usuarios_perfil where nombre = '".$_POST['nombre']."' and usp_id <> ".$_POST['usp_id']);
		if(count($rs)>0){
			$data = array('success' => false, 'mensaje' => 'Ya existe un Perfil con el nombre '.$_POST['nombre'].', por favor modifique los datos');
			echo json_encode($data);
			exit();
		}
		
        $rs = $this->objDatos->sp_usuarios_perfil_actualizar($_POST);
        
        $data = array('success' => true, 'total' => count($rs), 'data' => $rs);
        echo json_encode($data);
    }
    
    public function usuariosPerfilMenuListaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
	
        $rs = $this->objDatos->sp_usuarios_perfil_menu_lista($_POST);
        
        $array=Array();
        foreach($rs as $fila)
        {
            $fila["nombre_modulo"]=utf8_encode($fila["nombre_modulo"]);
            $fila["nombre_menu"]=utf8_encode($fila["nombre_menu"]);
			$fila["activo"]=utf8_encode($fila["activo"]);
            $array[]=$fila;
        }

        $data = array('success' => true, 'total' => count($rs), 'data' => $array);
        echo json_encode($data);
    }
	
	public function usuariosPerfilListaImpresionAction()
    {

		ini_set('memory_limit', '512M'); //Raise to 512 MB 
		ini_set('max_execution_time', '10000'); //Raise to 512 MB
        $this->_helper->viewRenderer->setNoRender();
		
		//$pdf = new ZendCPdf_Cezpdf();
		$pdf = new ZendCPdf_Cezpdf('a4', 'portrait','Listado de Perfiles y Accesos',0,$this->sessName->se_nombres.' '.$this->sessName->se_apellidos);
		
		$col_names = array(
			'usp_id' => 'Codigo',
			'perfil' => 'Nombre',
			'menu' => 'Menu',
			'leer' => 'Leer',
			'agregar' => 'Agregar',
			'modificar' => 'Modificar',
			'eliminar' => 'Eliminar'
		);
		
		$options = array(
			'width' => 550,
			'cols' => array(
				'usp_id' => array('justification'=>'center'),
				'perfil' => array('justification'=>'left'),
				'menu' => array('justification'=>'left'),
				'leer' => array('justification'=>'left'),
				'agregar' => array('justification'=>'left'),
				'modificar' => array('justification'=>'left'),
				'eliminar' => array('justification'=>'left')
			)
		);
		$_POST['campo']=$_POST['txtpar1'];
		$_POST['query']=$_POST['txtpar2'];
		//$db_data = $this->objDatos->sp_usuarios_perfil_lista($_POST);
		
		$sql = "select usuarios_perfil.usp_id,  usuarios_perfil.nombre as perfil, usuarios_menu.nombre as menu, usuarios_perfil_detalle.`READ` as leer, usuarios_perfil_detalle.`ADD` as agregar, usuarios_perfil_detalle.`UPDATE` as modificar, usuarios_perfil_detalle.`DELETE` as eliminar
from usuarios_perfil
	inner join usuarios_perfil_detalle on usuarios_perfil.usp_id = usuarios_perfil_detalle.usp_id
	inner join usuarios_menu on usuarios_perfil_detalle.usm_id = usuarios_menu.usm_id";
		
		
		

		if($_POST["campo"]=="" || $_POST["query"]==""){
			$filtro = "";
		}else{
			$sql = $sql." where usuarios_perfil.".$_POST["campo"]." like '%".$_POST["query"]."%'";
			$filtro = "FILTRO : ".$_POST["campo"]." = ".$_POST["query"];
		}
                
		$db_data = $this->objDatos->sp_obtenerdatasql($sql." order by usuarios_perfil.nombre, usuarios_menu.nombre");
		
                foreach($db_data as $key => $row){
                    
                    if(is_array($row)){
                        foreach($row as $k => $d){
                            $db_data[$key][$k] = utf8_decode($d);
                        }
                    }else{
                        $db_data[$key] = $row;
                    }                    
                }
                
		$pdf->ezTable($db_data, $col_names, $filtro, $options);
		$pdf->ezStream();
		
    }
    
    public function usuariosPerfilAction()
    {
        $rs = $this->objDatos->sp_obtenerdatasql("select usp_id from usuarios where usr_id = ".$this->sessName->se_usr_id);
		$perfil = $rs[0][0];
		
		$rs = $this->objDatos->sp_obtenerdatasql("select `read` as leer, `add` as agregar, `update` as editar, `delete` as eliminar from usuarios_perfil_detalle where usm_id = 2 and usp_id = ".$perfil);		
        $this->view->acceso = $rs[0];
    }
	
	public function seleccionAction()
    {
        $this->view->sesion = $this->sessName;
    }
	
	public function selecciontcAction()
    {
        $this->view->sesion = $this->sessName;
    }
	
	public function seleccionGuardarAction()
    {		
		
		if($_POST['tie_id']>0){
			
			$rstData = $this->objDatos->sp_obtenerdatasql("select * from configuracion where tie_id = ".$_POST['tie_id']);
			if($rstData[0]["con_id"]>0){
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
			
			$rs = $this->objDatos->sp_obtenerdatasql("select valida_stock from tiendas where tie_id = ".$_POST['tie_id']);
			$this->sessName->se_valida_stock = $rs[0][0];	
			define(VAR_VALIDA_STOCK, $rs[0][0]);
			
			$rs=$this->objDatos->sp_eventos_guardar(array('usr_id'=>$this->sessName->se_usr_id, 'evento'=>'R', 'tabla'=>'login', 'registro'=>$this->sessName->se_usr_id, 'detalle'=>'CAMBIO DE TIENDA POR ('.$this->sessName->se_valida_stock.VAR_VALIDA_STOCK.")".$this->sessName->se_nombres.' '.$this->sessName->se_apellidos.', DE: '.$this->sessName->se_tienda.' A '.$_POST['tienda']));
			
			$this->sessName->se_age_id = $_POST['tie_id'];
			$this->sessName->se_tienda = $_POST['tienda'];
			
			
				
		}
        if($_POST['valor_compra']>0 and $_POST['valor_venta']>0){
			$rs=$this->objDatos->sp_obtenerdatasql("CALL sp_tipo_cambio_actualizar (0,'".$_POST['fecha']."',".$_POST['valor_compra'].",".$_POST['valor_venta'].")");
		}
		$this->_helper->viewRenderer->setNoRender();
        
        $data = array('success' => true, 'total' => 1, 'data' => 'Direccionado correcto'."CALL sp_tipo_cambio_actualizar (0,'".$_POST['fecha']."',".$_POST['valor_compra'].",".$_POST['valor_venta'].")", 'fechahora'=>gmdate('d/m/Y H:i:s', $this->objDatos->hora_local_int(-5)));
        echo json_encode($data);
		//$this->_redirect("/");     
    }
	
	public function claveAction()
    {
        
    }
	
	public function claveGuardarAction()
    {		
		
		$rs=$this->objDatos->sp_eventos_guardar(array('usr_id'=>$this->sessName->se_usr_id, 'evento'=>'C', 'tabla'=>'login', 'registro'=>$this->sessName->se_usr_id, 'detalle'=>'CAMBIO DE CLAVE de '.$this->sessName->se_nombres.' '.$this->sessName->se_apellidos.', EN: '.$this->sessName->se_tienda.' '));
		
		$this->sessName->se_age_id = $_POST['tie_id'];
		$this->sessName->se_tienda = $_POST['tienda'];
		
		$this->_helper->viewRenderer->setNoRender();
		
		$rs=$this->objDatos->sp_obtenerdatasql("select * from usuarios where usr_id = ".$this->sessName->se_usr_id." and clave = '".$_POST['clave_anterior']."' ");
		
		if(count($rs)>0){
			$rs=$this->objDatos->sp_obtenerdatasql("update usuarios set clave = '".$_POST['clave_nueva']."' where usr_id = ".$this->sessName->se_usr_id."");
			$data = array('success' => true, 'total' => 1, 'data' => 'Clave cambiada correctamente');
		}else{
			$data = array('success' => false, 'mensaje' => 'Clave anterior no coincide');
		}        
        echo json_encode($data);    
    }
	
	public function printAction(){
		$this->_helper->viewRenderer->setNoRender();
		//echo "HOLA";
		
		$prueba = new ZendCPdf_Cezpdf();
		
		//$prueba->configuraPagina("portrait","Titulo");
		//echo $prueba;
		$prueba->ezText('CODIGO :', 12, array('justification' => 'left'));			
		$prueba->ezStream();
			
	}
	
}