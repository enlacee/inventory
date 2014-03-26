<?php
session_start();

/*echo ini_get('cookie_lifetime');
echo " :: ";
echo ini_get('session.gc_maxlifetime');
echo " :: ";
echo ini_get('date.timezone');
echo " :: ";

if(!empty($_GET['lima']))
	ini_set('date.timezone', 'America/Lima');

print_r($_SESSION);

echo " :: ";
*/

if(!empty($_SESSION)){
	if(empty($_SESSION['seg']))
		$_SESSION['seg']=60*20;
	else
		$_SESSION['seg']++;

	$hora1=date('H:i:s');
	$hora2=date('H:i:s') + $_SESSION['seg'];
}else{
	$hora1=$_GET['hora1'];
	$hora2=$_GET['hora2'];
}

echo "$hora1 - $hora2";

if(!empty($_SESSION)){
	$seg = $_SESSION['seg'];
?>
<script type="text/javascript">
	setInterval(function(){ document.location.href="initset.php?hora1=<?php echo $hora1;?>&hora2=<?php $hora2;?>" }, <?php echo $seg;?>);
</script>
<?php
}?>
