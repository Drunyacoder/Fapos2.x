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
$pageTitle = 'Форум - Настройки';


$set = Config::read('all');
$TempSet = $set;
if (isset($_POST['send'])) {

	$TempSet['forum']['title'] = $_POST['title'];
	$TempSet['forum']['description'] = $_POST['description'];
	$TempSet['forum']['not_reg_user'] = $_POST['not_reg_user'];
	$TempSet['forum']['max_post_lenght'] = $_POST['max_post_lenght'];
	$TempSet['forum']['posts_per_page'] = $_POST['posts_per_page'];
	$TempSet['forum']['themes_per_page'] = $_POST['themes_per_page'];
	$TempSet['forum']['active'] = $_POST['active'];

	Config::write($TempSet);
	redirect("/admin/settings_for.php");
}


$pageNav = $pageTitle;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
?>



<form method="POST" action="settings_for.php">
<table class="settings-tb">

<tr><td class="left">Заголовок форума:</td>
<td><input type="text" name="title" value="<?php echo $set['forum']['title'] ?>"></td></tr>

<tr><td class="left">Описание форума:<br></td>
<td><input type="text" name="description" value="<?php echo $set['forum']['description'] ?>"></td></tr>


<tr><td class="left">Псевдоним гостя:<br>
<span class="comment">(Под этим именем будет показано сообщение (пост) <br>
 не зарегистрированного пользователя)</span><br></td>
<td><input type="text" name="not_reg_user" value="<?php echo $set['forum']['not_reg_user'] ?>"></td></tr>


<tr class="small"><td class="group" colspan="2">Ограничения</td></tr>
<tr><td class="left">Максимальная длина поста:<br></td>
<td><input type="text" name="max_post_lenght" value="<?php echo $set['forum']['max_post_lenght'] ?>">&nbsp;<span class="comment">Символов</span></td></tr>

<tr><td class="left">Количество постов на странице:<br></td>
<td><input type="text" name="posts_per_page" value="<?php echo $set['forum']['posts_per_page'] ?>"></td></tr>

<tr><td class="left"> Количество тем на одной странице:<br></td>
<td><input type="text" name="themes_per_page" value="<?php echo $set['forum']['themes_per_page'] ?>"></td></tr>



<tr class="small"><td class="group" colspan="2">Прочее</td></tr>
<tr><td class="left">Статус :<br />
<span class="comment">(Активирован/Деактивирован)</span></td>
<td>
<select name="active">
<option value="1" <?php if (Config::read('active', 'forum') && Config::read('active', 'forum') == 1) echo 'selected="selected"' ?>>Активирован</option>
<option value="0"<?php if (!Config::read('active', 'forum') || Config::read('active', 'forum') == 0) echo 'selected="selected"' ?>>Деактивирован</option>
</select>
</td></tr>

<tr><td colspan="2" align="center"><input type="submit" name="send" value="Сохранить"><br></td></tr>

</table>
</form>




<?php
include_once 'template/footer.php';
?>