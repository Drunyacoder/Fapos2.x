<?php
##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      1.0                            ##
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
$pageTitle = 'Пользователи - настройки';


$set = Config::read('all');
$TempSet = $set;
if (!isset($set['users']['fields'])) $set['users']['fields'] = array();
//pr($set);
if (isset($_POST['send'])) {

	$TempSet['users']['title'] = $_POST['title'];
	$TempSet['users']['active'] = $_POST['active'];
	$TempSet['users']['max_avatar_size'] = $_POST['max_avatar_size'];
	$TempSet['users']['users_per_page'] = $_POST['users_per_page'];
	$TempSet['users']['max_mail_lenght'] = $_POST['max_mail_lenght'];
	$TempSet['users']['max_count_mess'] = $_POST['max_count_mess'];
	$TempSet['users']['max_message_lenght'] = $_POST['max_message_lenght'];
	$TempSet['users']['rating_comment_lenght'] = $_POST['rating_comment_lenght'];
	$TempSet['users']['warnings_by_ban'] = intval($_POST['warnings_by_ban']);
	$TempSet['users']['autoban_interval'] = intval($_POST['autoban_interval']);
	
	// fields
	$TempSet['users']['fields'] = array();
	if (!empty($_POST['fields']['icq']))  		$TempSet['users']['fields'][] = 'icq';
	if (!empty($_POST['fields']['jabber'])) 	$TempSet['users']['fields'][] = 'jabber';
	if (!empty($_POST['fields']['pol']))		$TempSet['users']['fields'][] = 'pol';
	if (!empty($_POST['fields']['city']))		$TempSet['users']['fields'][] = 'city';
	if (!empty($_POST['fields']['byear']))		$TempSet['users']['fields'][] = 'byear';
	if (!empty($_POST['fields']['bmonth']))		$TempSet['users']['fields'][] = 'bmonth';
	if (!empty($_POST['fields']['bday']))		$TempSet['users']['fields'][] = 'bday';
	if (!empty($_POST['fields']['signature']))	$TempSet['users']['fields'][] = 'signature';
	if (!empty($_POST['fields']['timezone']))	$TempSet['users']['fields'][] = 'timezone';
	if (!empty($_POST['fields']['about']))		$TempSet['users']['fields'][] = 'about';
	if (!empty($_POST['fields']['url']))		$TempSet['users']['fields'][] = 'url';
	if (!empty($_POST['fields']['telephone']))	$TempSet['users']['fields'][] = 'telephone';
	
	Config::write($TempSet);
	redirect("/admin/settings_users.php");
}


$pageNav = $pageTitle;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
?>


<form method="POST" action="settings_users.php">
<table class="settings-tb">

<tr><td class="left">Заголовок модуля:</td>
<td><input type="text" name="title" value="<?php echo h($set['users']['title']); ?>"></td></tr>


<tr class="small"><td class="group" colspan="2">Ограничения</td></tr>
<tr><td class="left">Максимальный размер аватара:<br>
<span class="comment">Это картинка, которая отображаеться на форуме и в профиле пользователя</span></td>
<td><input type="text" name="max_avatar_size" value="<?php echo intval($set['users']['max_avatar_size']); ?>">&nbsp;<span class="comment">Байт</span></td></tr>

<tr><td class="left">Количество пользователей на одну страницу:<br>
<span class="comment">в списке зарегистрированных пользователей</span><br></td>
<td><input type="text" name="users_per_page" value="<?php echo intval($set['users']['users_per_page']); ?>"></td></tr>

<tr><td class="left">Максимальный размер письма:<br>
<span class="comment">которое один пользователь форума может написать другому</span><br></td>
<td><input type="text" name="max_mail_lenght" value="<?php echo intval($set['users']['max_mail_lenght']); ?>">&nbsp;<span class="comment">Символов</span></td></tr>

<tr><td class="left">Максимальное количество личных сообщений:<br>
<span class="comment">в папках "Входящие" и "Исходящие"</span><br></td>
<td><input type="text" name="max_count_mess" value="<?php echo intval($set['users']['max_count_mess']); ?>"></td></tr>

<tr><td class="left">Максимальная длина личного сообщения:<br></td>
<td><input type="text" name="max_message_lenght" value="<?php echo intval($set['users']['max_message_lenght']); ?>">&nbsp;<span class="comment">Символов</span></td></tr>

<tr><td class="left">Максимальная длина комментария к голосу:<br></td>
<td><input type="text" name="rating_comment_lenght" value="<?php echo intval($set['users']['rating_comment_lenght']); ?>">&nbsp;<span class="comment">Символов</span></td></tr>

<tr><td class="left">Кол-во предупреждений для наступления бана:<br></td>
<td><input type="text" name="warnings_by_ban" value="<?php echo intval($set['users']['warnings_by_ban']); ?>">&nbsp;<span class="comment">Штук</span></td></tr>

<tr><td class="left">Продолжительность бана, из-за предупреждений:<br></td>
<td><input type="text" name="autoban_interval" value="<?php echo intval($set['users']['autoban_interval']); ?>">&nbsp;<span class="comment">Секунд</span></td></tr>



<!--#################### FIELDS ###################-->
<tr class="small"><td class="group" colspan="2">Поля обязательные для заполнения</td></tr>

<tr class="small"><td class="left">Имя:<br></td><td>
<input type="checkbox" name="comment_active" value="1" checked="checked" disabled="disabled" /></td></tr>

<tr class="small"><td class="left">E-mail:<br></td><td>
<input type="checkbox" name="comment_active" value="1" checked="checked" disabled="disabled" /></td></tr>

<tr class="small"><td class="left">Пароль:<br></td><td>
<input type="checkbox" name="comment_active" value="1" checked="checked" disabled="disabled" /></td></tr>

<tr class="small"><td class="left">Код(каптча):<br></td><td>
<input type="checkbox" name="comment_active" value="1" checked="checked" disabled="disabled" /></td></tr>

<tr class="small"><td class="left">ICQ:<br></td><td>
<input type="checkbox" name="fields[icq]" value="1" <?php if (in_array('icq', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>

<tr class="small"><td class="left">Jabber:<br></td><td>
<input type="checkbox" name="fields[jabber]" value="1" <?php if (in_array('jabber', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>

<tr class="small"><td class="left">Пол:<br></td><td>
<input type="checkbox" name="fields[pol]" value="1" <?php if (in_array('pol', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>

<tr class="small"><td class="left">Город:<br></td><td>
<input type="checkbox" name="fields[city]" value="1" <?php if (in_array('city', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>

<tr class="small"><td class="left">Телефон:<br></td><td>
<input type="checkbox" name="fields[telephone]" value="1" <?php if (in_array('telephone', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>

<tr class="small"><td class="left">Год рождения:<br></td><td>
<input type="checkbox" name="fields[byear]" value="1" <?php if (in_array('byear', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>

<tr class="small"><td class="left">Месяц рождения:<br></td><td>
<input type="checkbox" name="fields[bmonth]" value="1" <?php if (in_array('bmonth', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>

<tr class="small"><td class="left">День рождения:<br></td><td>
<input type="checkbox" name="fields[bday]" value="1" <?php if (in_array('bday', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>

<tr class="small"><td class="left">Домашняя страничка:<br></td><td>
<input type="checkbox" name="fields[url]" value="1" <?php if (in_array('url', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>

<tr class="small"><td class="left">Интересы:<br></td><td>
<input type="checkbox" name="fields[about]" value="1" <?php if (in_array('about', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>

<tr class="small"><td class="left">Подпись:<br></td><td>
<input type="checkbox" name="fields[signature]" value="1" <?php if (in_array('signature', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>

<tr class="small"><td class="left">Временная зона:<br></td><td>
<input type="checkbox" name="fields[timezone]" value="1" <?php if (in_array('timezone', $set['users']['fields'])) echo 'checked="checked"' ?>></td></tr>




<tr class="small"><td class="group" colspan="2">Прочее</td></tr>
<tr><td class="left">Статус :<br />
<span class="comment">Активирован/Деактивирован</span></td>
<td>
<select name="active">
<option value="1" <?php if (Config::read('active', 'users') && Config::read('active', 'users') == 1) echo 'selected="selected"' ?>>Активирован</option>
<option value="0"<?php if (!Config::read('active', 'users') || Config::read('active', 'users') == 0) echo 'selected="selected"' ?>>Деактивирован</option>
</select>

</td></tr>

<tr><td colspan="2" align="center"><input type="submit" name="send" value="Сохранить"><br></td></tr>

</table>
</form>




<?php
include_once 'template/footer.php';
?>