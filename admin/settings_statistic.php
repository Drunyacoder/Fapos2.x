<?php
##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      0.7                            ##
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
$pageTitle = 'Статистика - Настройки';

$set = Config::read('all');
$TempSet = $set;

if (isset($_POST['send'])) {

	$TempSet['statistics']['active'] = $_POST['active'];
	
	//save data
	Config::write($TempSet);
	//clean Cache
	$Cache = new Cache; 
	$Cache->clean();
	redirect("/admin/settings_statistic.php");
}


$pageNav = $pageTitle;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
?>


<form method="POST" action="settings_statistic.php">
<table class="settings-tb">

<tr><td class="left">Статус :<br />
<span class="comment">(Активирован/Деактивирован)</span></td>
<td>
<select name="active">
<option value="1" <?php if (Config::read('active', 'statistics') && Config::read('active', 'statistics') == 1) echo 'selected="selected"' ?>>Активирован</option>
<option value="0"<?php if (!Config::read('active', 'statistics') || Config::read('active', 'statistics') == 0) echo 'selected="selected"' ?>>Деактивирован</option>
</select>
</td></tr>

<tr><td colspan="2" align="center"><input type="submit" name="send" value="Сохранить"><br></td></tr>

</table>
</form>



<?php
include_once 'template/footer.php';
?>