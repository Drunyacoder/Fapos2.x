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


$set = Config::read('all');
$TempSet = $set;
if (isset($_POST['send'])) {

	$TempSet['secure']['antisql'] = (!empty($_POST['antisql'])) ? 1 : 0;
	$TempSet['secure']['anti_ddos'] = (!empty($_POST['anti_ddos'])) ? 1 : 0;
	$TempSet['secure']['request_per_second'] = (!empty($_POST['request_per_second'])) ? (int)$_POST['request_per_second'] : 5;
	$TempSet['secure']['system_log'] = (!empty($_POST['system_log'])) ? 1 : 0;
	$TempSet['secure']['autorization_protected_key'] = (!empty($_POST['autorization_protected_key'])) ? 1 : 0;
	$TempSet['secure']['max_log_size'] = (!empty($_POST['max_log_size'])) ? (int)($_POST['max_log_size'] * 1024) : 0;
	$TempSet['secure']['session_time'] = (!empty($_POST['session_time'])) ? (int)$_POST['session_time'] : 600;

	Config::write($TempSet);
	redirect("/admin/secure_settings.php");
}


$pageTitle = 'Настройки безопасности';
$pageNav = $pageTitle;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
//echo '<div valign="top" align="center" border="3" width="85%">';
?>
<form method="POST" action="secure_settings.php">
<table class="settings-tb">

<tr><td class="left">Отслеживать попытки SQL иньекций через адресную строку:<br>
<span class="comment">(запись ведеться в /sys/logs/antisql.dat)</span></td><td>
<input type="checkbox" name="antisql" value="1" <?php echo (!empty($set['secure']['antisql']) && $set['secure']['antisql'] == 1) ? 'checked="checked"' : ''; ?> />
<br></td></tr>

<tr class="small"><td class="group" colspan="2">Анти DDOS</td></tr>
<tr><td class="left">Анти DDOS защита:<br>
<span class="comment">(Позволяет снизить риск DDOS атаки)</span></td><td>
<input type="checkbox" name="anti_ddos" value="1" <?php echo (!empty($set['secure']['anti_ddos']) && $set['secure']['anti_ddos'] == 1) ? 'checked="checked"' : ''; ?> />
<br></td></tr>

<tr><td class="left">(DDOS)Максимально допустимое кол-во запросов:<br>
<span class="comment">(за одну секунду, с одного диапазона IP адресов)</span></td><td>
<input type="text" name="request_per_second" value="<?php echo (!empty($set['secure']['request_per_second'])) ? $set['secure']['request_per_second'] : ''; ?>"  />
<br></td></tr>


<tr class="small"><td class="group" colspan="2">Лог действий</td></tr>
<tr><td class="left">Вести ли лог действий:<br>
<span class="comment">(фиксируются действия пользователей)</span></td><td>
<input type="checkbox" name="system_log" value="1" <?php echo (!empty($set['secure']['system_log']) && $set['secure']['system_log'] == 1) ? 'checked="checked"' : ''; ?> />
<br></td></tr>


<tr><td class="left">Максимально допустимый объем логов:<br>
<span class="comment">Предел занимаемого логами дискового пространства</span></td><td>
<input type="text" name="max_log_size" value="<?php echo (!empty($set['secure']['max_log_size'])) ? (int)($set['secure']['max_log_size'] / 1024) : ''; ?>"  />
<span class="comment">Кб.</span>
<br></td></tr>

<tr class="small"><td class="group" colspan="2">Прочее</td></tr>
<tr><td class="left">Защита от перебора пароля:<br>
<span class="comment">Посредством передачи защитного ключа</span></td><td>
<input type="checkbox" name="autorization_protected_key" value="1" <?php echo (!empty($set['secure']['autorization_protected_key'])) ? 'checked="checked"' : ''; ?> />
<br></td></tr>


<tr><td class="left">Длительность сессии в админ-панели:<br>
<span class="comment">Если бездействовать в админ-панели больше отведеного времени, придется заново авторизоваться</span></td><td>
<input type="text" name="session_time" value="<?php echo (!empty($set['secure']['session_time'])) ? (int)$set['secure']['session_time'] : ''; ?>"  />
<br></td></tr>


<tr><td align="center" colspan="2"><input type="submit" name="send" value="Сохранить"><br></td>
</form>

</table>
<?php
//echo '</div>';

include_once 'template/footer.php';

?>