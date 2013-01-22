<?php
##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      0.8                            ##
## Project:      CMS                            ##
## package       CMS Fapos                      ##
## subpackege    Admin Panel module             ##
## copyright     ©Andrey Brykin 2010-2011       ##
##################################################


##################################################
##												##
## any partial or not partial extension         ##
## CMS Fapos,without the consent of the         ##
## author, is illegal                           ##
##################################################
## Любое распространение                        ##
## CMS Fapos или ее частей,                     ##
## без согласия автора, является не законным    ##
##################################################

include_once '../sys/boot.php';
include_once ROOT . '/admin/inc/adm_boot.php';

 
$pageTitle = 'Баны по IP адресам';
 
 
if ( !isset( $_GET['ac'] ) ) $_GET['ac'] = 'index';
$actions = array( 'index',
					'del',
					'add');
					
if ( !in_array( $_GET['ac'], $actions ) ) $_GET['ac'] = 'index';

switch ( $_GET['ac'] )
{
	case 'index':  // главная страница 
		$content = index($pageTitle);
		break;
	case 'add':         //смотреть новость
		$content = add();
		break;
	case 'del':         //добавить новость
		$content = delete();
		break;
	default:
		$content = index($pageTitle);
}



$pageNav = $pageTitle;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
?>

		<div style="float:right;">
		<a href="javascript://" onClick="wiOpen('addIp')">Добавить IP</a>
		</div>
		<div style="clear:right;"></div>
		</div></div></div>   <div class="xw-bl"></div> 
		<div class="xw-tl1"><div class="xw-tr1"><div class="xw-tc1"></div></div></div>
		<div class="xw-ml"><div class="xw-mr"><div id="mainContent" class="xw-mc topBlockM">
<?php
echo $content;
include_once ROOT . '/admin/template/footer.php';

	
function index(&$page_title) {
	$content = null;
	if (file_exists(ROOT . '/sys/logs/ip_ban/baned.dat')) {
		$data = file(ROOT . '/sys/logs/ip_ban/baned.dat');
		if (!empty($data)) {
			foreach($data as $key => $row) {
				$content .= '<tr><td>' . $row . '</td><td width="30px"><a onClick="return confirm(\'Are you sure?\');" href="ip_ban.php?ac=del&id=' . $key . '">
							<img src="' . get_url('/sys/img/delete_16x16.png') . '" /></a></td></tr>';
			}
		}
	}
	if (empty($content)) $content = '<div class="info-str">Записей пока нет</div>';
	else $content = '<table class="lines">' . $content . '</table>';
	
	//add form
	$content .= '<div id="addIp_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
			<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
			</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
			<form action="ip_ban.php?ac=add" method="POST">
			
			<div class="form-item2">
			IP:<br />
			<input type="text" name="ip" />
			<div style="clear:both;"></div></div>
			
			<div class="form-item2">
			<input type="submit" name="send" value="Сохранить" />
			<input type="button" onClick="hideWin(\'addIp\')" value="Отмена" />
			<div style="clear:both;"></div></div>
			</form>
			</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
			<div class="xw-footer"></div></div></div></div>
			</div>';
	
	if (isset($_SESSION['add']['errors'])) {
		$content = $_SESSION['add']['errors'] . $content;
		unset($_SESSION['add']);
	}
	
	return $content;
}



/**
* adding IP to ban list
*/
function add() {
	if (empty($_POST['ip'])) redirect('/admin/ip_ban.php');
	$ip = trim($_POST['ip']);
	$error = null;
	
	
	if (!preg_match('#^\d{1,3}\.\d{1,3}.\d{1,3}.\d{1,3}$#', $ip)) $error = '<li>Не верный формат IP адреса</li>';
	if (!empty($error)) {
		$_SESSION['add']['errors'] = '<ul class="uz_err">' . $error . '</ul>';
		redirect('/admin/ip_ban.php');
	}
	
	if (empty($error)) {
		touchDir(ROOT . '/sys/logs/ip_ban/');
		$f = fopen(ROOT . '/sys/logs/ip_ban/baned.dat', 'a+');
		fwrite($f, $ip . "\n");
		fclose($f);
	}
	
	redirect('/admin/ip_ban.php');
}



/**
* deleting ip
*/
function delete() {
	if (!isset($_GET['id'])) redirect('ip_ban.php');
	if (file_exists(ROOT . '/sys/logs/ip_ban/baned.dat')) {
		$data = file(ROOT . '/sys/logs/ip_ban/baned.dat');
		if (!empty($data)) {

			if (array_key_exists($_GET['id'], $data)) {
				$_data = array();
				foreach ($data as $key => $val) {
					if (empty($val) || $key == $_GET['id']) continue;
					$_data[$key] = $val;
				}
				$data = implode("", $_data);
				file_put_contents(ROOT . '/sys/logs/ip_ban/baned.dat', $data);
			} else {
				$_SESSION['add']['errors'] = '<ul class="uz_err"><li>Записи с таким ключом не найдено</li></ul>';
			}
		}
	}
	
	redirect('/admin/ip_ban.php');
}

?>