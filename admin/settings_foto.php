<?php
##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      0.9                            ##
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
$pageTitle = 'Фотокаталог - Настройки';


$set = Config::read('all');
$TempSet = $set;
if (isset($_POST['send'])) {

	$TempSet['foto']['title'] = $_POST['title'];
	$TempSet['foto']['description'] = $_POST['description'];
	$TempSet['foto']['max_file_size'] = $_POST['max_file_size'];
	$TempSet['foto']['per_page'] = $_POST['per_page'];
	$TempSet['foto']['active'] = $_POST['active'];
	$TempSet['foto']['description_lenght'] = $_POST['description_lenght'];
	$TempSet['foto']['description_requred'] = (!empty($_POST['description_requred']) && $_POST['description_requred'] == 1) ? 1 : 0;;
	
	
	if (!empty($_FILES['watermark_img']['name'])) {
		//pr($_FILES);
		if ($_FILES['watermark_img']['type'] == 'image/jpg'
		|| $_FILES['watermark_img']['type'] == 'image/gif'
		|| $_FILES['watermark_img']['type'] == 'image/jpeg'
		|| $_FILES['watermark_img']['type'] == 'image/png') {
			//pr($_FILES);
			$ext = strchr($_FILES['watermark_img']['name'], '.');
			if (move_uploaded_file($_FILES['watermark_img']['tmp_name'], ROOT . '/sys/img/watermark'.$ext)) {
				$TempSet['foto']['watermark_img'] = 'watermark'.$ext;
			}
		}
	}
	$TempSet['foto']['use_watermarks'] = (!empty($_POST['use_watermarks'])) ? 1 : 0;
	

	
	
	Config::write($TempSet);
	redirect("/admin/settings_foto.php");
}


$pageNav = $pageTitle;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
?>


<form method="POST" action="settings_foto.php" enctype="multipart/form-data">
<table class="settings-tb">

<tr><td class="left">Заголовок модуля:</td>
<td><input type="text" name="title" value="<?php echo $set['foto']['title'] ?>"></td></tr>

<tr><td class="left">Описание модуля:<br></td>
<td><input type="text" name="description" value="<?php echo $set['foto']['description'] ?>"></td></tr>


<tr class="small"><td class="group" colspan="2">Ограничения</td></tr>
<tr><td class="left">Максимальный размер картинки:<br></td>
<td><input type="text" name="max_file_size" value="<?php echo $set['foto']['max_file_size'] ?>">&nbsp;<span class="comment">Байт</span></td></tr>

<tr><td class="left"> Количество фото на странице:<br></td>
<td><input type="text" name="per_page" value="<?php echo $set['foto']['per_page'] ?>"></td></tr>

<tr><td class="left"> Максимальная длина описания:<br></td>
<td><input type="text" name="description_lenght" value="<?php echo $set['foto']['description_lenght'] ?>">&nbsp;<span class="comment">Символов</span></td></tr>


<tr class="small"><td class="group" colspan="2">Поля обязательные для заполнения</td></tr>
<tr class="small"><td class="left">Категория:<br></td><td>
<input type="checkbox" name="comment_active" value="1" checked="checked" disabled="disabled" /><br></td></tr>

<tr class="small"><td class="left">Заголовок:<br></td><td>
<input type="checkbox" name="comment_active" value="1" checked="checked" disabled="disabled" /><br></td></tr>

<tr class="small"><td class="left">Файл:<br></td><td>
<input type="checkbox" name="comment_active" value="1" checked="checked" disabled="disabled" /><br></td></tr>

<tr><td class="left"> Описание:<br></td>
<td><input type="checkbox" name="description_requred" value="1" <?php if($set['foto']['description_requred'] == 1) echo 'checked="checked"'; ?>></td></tr>


<tr class="small"><td class="group" colspan="2">Прочее</td></tr>

<tr><td class="left"> Водяные знаки:<br></td>
<td><input type="checkbox" name="use_watermarks" value="1" <?php if(!empty($set['foto']['use_watermarks']) && $set['foto']['use_watermarks'] == 1) echo 'checked="checked"'; ?>></td></tr>

<tr><td class="left"> Водяной знак:<br></td>
<td>
	<input type="file" name="watermark_img">
	<?php if (!empty($set['foto']['watermark_img']) && file_exists(ROOT . '/sys/img/'.$set['foto']['watermark_img'])): ?>
	<img src="<?php echo ROOT . '/sys/img/'.$set['foto']['watermark_img']; ?>" />
	<?php endif; ?>
</td></tr>


<tr><td class="left">Статус :<br />
<span class="comment">(Активирован/Деактивирован)</span></td>
<td>
<select name="active">
<option value="1" <?php if (Config::read('active', 'foto') && Config::read('active', 'foto') == 1) echo 'selected="selected"' ?>>Активирован</option>
<option value="0"<?php if (!Config::read('active', 'foto') || Config::read('active', 'foto') == 0) echo 'selected="selected"' ?>>Деактивирован</option>
</select>
</td></tr>

<tr><td colspan="2" align="center"><input type="submit" name="send" value="Сохранить"><br></td></tr>

</table>
</form>




<?php
include_once 'template/footer.php';
?>