<?php
##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      1.1                            ##
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
$pageTitle = 'Чат - Настройки';


$set = Config::read('all');
$TempSet = $set;
if (isset($_POST['send'])) {

	$TempSet['chat']['title'] = $_POST['title'];
	$TempSet['chat']['max_lenght'] = $_POST['max_lenght'];
	$TempSet['chat']['active'] = $_POST['active'];


	Config::write($TempSet);
	redirect("/admin/settings_chat.php");
}



$pageNav = $pageTitle;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
?>




<form method="POST" action="settings_chat.php">
<table class="settings-tb">

<tr><td class="left">Заголовок чата:</td>
<td><input type="text" name="title" value="<?php echo $set['chat']['title'] ?>"></td></tr>

<tr><td class="left">Максимальная длина сообщения:<br></td>
<td><input type="text" name="max_lenght" value="<?php echo $set['chat']['max_lenght'] ?>">&nbsp;<span class="comment">Символов</span></td></tr>


<tr><td class="left">Статус :<br />
<span class="comment">(Активирован/Деактивирован)</span></td>
<td>
<select name="active">
<option value="1" <?php if (Config::read('active', 'chat') && Config::read('active', 'chat') == 1) echo 'selected="selected"' ?>>Активирован</option>
<option value="0"<?php if (!Config::read('active', 'chat') || Config::read('active', 'chat') == 0) echo 'selected="selected"' ?>>Деактивирован</option>
</select>
</td></tr>


<tr><td colspan="2" align="center"><input type="submit" name="send" value="Сохранить"><br></td></tr>

</table>
</form>




<?php
include_once 'template/footer.php';
?>