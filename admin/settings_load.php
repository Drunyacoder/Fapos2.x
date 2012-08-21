<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.2.2                         |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    Controll panel                |
| @copyright     ©Andrey Brykin 2010-2012      |
| @last mod      2011/12/12                    |
|----------------------------------------------|
|											   |
| any partial or not partial extension         |
| CMS Fapos,without the consent of the         |
| author, is illegal                           |
|----------------------------------------------|
| Любое распространение                        |
| CMS Fapos или ее частей,                     |
| без согласия автора, является не законным    |
\---------------------------------------------*/



include_once '../sys/boot.php';
include_once R . 'admin/inc/adm_boot.php';
$pageTitle = 'Файлы - Настройки';


$set = Config::read('all');
$TempSet = $set;
if (isset($_POST['send'])) {

	$TempSet['loads']['title'] = $_POST['title'];
	$TempSet['loads']['description'] = $_POST['description'];
	$TempSet['loads']['min_lenght'] = $_POST['min_lenght'];
	$TempSet['loads']['max_lenght'] = $_POST['max_lenght'];
	$TempSet['loads']['announce_lenght'] = $_POST['announce_lenght'];
	$TempSet['loads']['per_page'] = $_POST['per_page'];
	$TempSet['loads']['max_file_size'] = $_POST['max_file_size'];
	$TempSet['loads']['active'] = $_POST['active'];
	//IMAGES
	$TempSet['loads']['img_size_x'] = $_POST['img_size_x'];
	$TempSet['loads']['img_size_y'] = $_POST['img_size_y'];
	$TempSet['loads']['max_attaches_size'] = $_POST['max_attaches_size'];
	$TempSet['loads']['max_attaches'] = $_POST['max_attaches'];
	
	/* FIELDS */
	$TempSet['loads']['fields'] = array();
	if (!empty($_POST['fields']['description'])) $TempSet['loads']['fields'][] = 'description';
	if (!empty($_POST['fields']['tags'])) $TempSet['loads']['fields'][] = 'tags';
	if (!empty($_POST['fields']['sourse'])) $TempSet['loads']['fields'][] = 'sourse';
	if (!empty($_POST['fields']['sourse_email'])) $TempSet['loads']['fields'][] = 'sourse_email';
	if (!empty($_POST['fields']['sourse_site'])) $TempSet['loads']['fields'][] = 'sourse_site';
	if (!empty($_POST['fields']['sourse_site'])) $TempSet['loads']['fields'][] = 'sourse_site';
	if (!empty($_POST['fields']['download_url'])) $TempSet['loads']['fields'][] = 'download_url';
	if (!empty($_POST['fields']['download_url_size'])) $TempSet['loads']['fields'][] = 'download_url_size';
	$TempSet['loads']['require_file'] = (!empty($_POST['require_file'])) ? 1 : 0;
	
	/* COMMENTS */
	$TempSet['loads']['comment_lenght'] = $_POST['comment_lenght'];
	$TempSet['loads']['comment_per_page'] = $_POST['comment_per_page'];
	
	$TempSet['loads']['comments_order'] = (!empty($_POST['comments_order'])) ? 1 : 0;
	
	
	Config::write($TempSet);
	redirect("/admin/settings_load.php");
}



$pageNav = $pageTitle;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
?>


<form method="POST" action="">
<table class="settings-tb">

<tr><td class="left">Заголовок:</td>
<td><input type="text" name="title" value="<?php echo $set['loads']['title'] ?>"></td></tr>

<tr><td class="left">Описание:<br></td>
<td><input type="text" name="description" value="<?php echo $set['loads']['description'] ?>"></td></tr>

<tr class="small"><td class="group" colspan="2">Ограничения</td></tr>
<tr><td class="left">Минимальная длина описания:<br></td>
<td><input type="text" name="min_lenght" value="<?php echo $set['loads']['min_lenght'] ?>">&nbsp;<span class="comment">Символов</span></td></tr>

<tr><td class="left">Максимальная длина описания:<br></td>
<td><input type="text" name="max_lenght" value="<?php echo $set['loads']['max_lenght'] ?>">&nbsp;<span class="comment">Символов</span></td></tr>

<tr><td class="left">Длина анонса:<br></td>
<td><input type="text" name="announce_lenght" value="<?php echo $set['loads']['announce_lenght'] ?>">&nbsp;<span class="comment">Символов</span></td></tr>

<tr><td class="left">Количество файлов на странице:<br></td>
<td><input type="text" name="per_page" value="<?php echo $set['loads']['per_page'] ?>"></td></tr>


<tr><td class="left">Максимальный размер файла:<br></td>
<td><input type="text" name="max_file_size" value="<?php echo $set['loads']['max_file_size'] ?>">&nbsp;<span class="comment">Байт</span></td></tr>


<!--#################### ATTACHED IMG ###################-->
<tr class="small"><td class="group" colspan="2">Изображения</td></tr>

<tr><td class="left">Размер по оси Х:<br></td><td>
<input type="text" name="img_size_x" value="<?php echo $set['loads']['img_size_x'] ?>">&nbsp;<span class="comment">Пикселей(Число)</span><br></td></tr>

<tr><td class="left">Размер по оси Y:<br></td><td>
<input type="text" name="img_size_y" value="<?php echo $set['loads']['img_size_y'] ?>">&nbsp;<span class="comment">Пикселей(Число)</span><br></td></tr>

<tr><td class="left">Максимальный "вес":<br></td><td>
<input type="text" name="max_attaches_size" value="<?php echo $set['loads']['max_attaches_size'] ?>">&nbsp;<span class="comment">Кбайт</span><br></td></tr>

<tr><td class="left">Максимальное кол-во:<br></td><td>
<input type="text" name="max_attaches" value="<?php echo $set['loads']['max_attaches'] ?>">&nbsp;<span class="comment">Штук</span><br></td></tr>

<!--#################### END ATTACHED IMG ###################-->


<!--#################### FIELDS ###################-->
<tr class="small"><td class="group" colspan="2">Поля обязательные для заполнения</td></tr>

<tr class="small"><td class="left">Категория:<br></td><td>
<input type="checkbox" name="comment_active" value="1" checked="checked" disabled="disabled" /><br></td></tr>

<tr class="small"><td class="left">Заголовок:<br></td><td>
<input type="checkbox" name="comment_active" value="1" checked="checked" disabled="disabled" /><br></td></tr>

<tr class="small"><td class="left">Текст материала:<br></td><td>
<input type="checkbox" name="comment_active" value="1" checked="checked" disabled="disabled" /><br></td></tr>

<tr class="small"><td class="left">Краткое описание:<br></td><td>
<input type="checkbox" name="fields[description]" value="1" <?php if (in_array('description', $set['loads']['fields'])) echo 'checked="checked"' ?>><br></td></tr>

<tr class="small"><td class="left">Теги:<br></td><td>
<input type="checkbox" name="fields[tags]" value="1" <?php if (in_array('tags', $set['loads']['fields'])) echo 'checked="checked"' ?>><br></td></tr>

<tr class="small"><td class="left">Источник(автор):<br></td><td>
<input type="checkbox" name="fields[sourse]" value="1" <?php if (in_array('sourse', $set['loads']['fields'])) echo 'checked="checked"' ?>><br></td></tr>

<tr class="small"><td class="left">E-Mail автора:<br></td><td>
<input type="checkbox" name="fields[sourse_email]" value="1" <?php if (in_array('sourse_email', $set['loads']['fields'])) echo 'checked="checked"' ?>><br></td></tr>

<tr class="small"><td class="left">Сайт автора:<br></td><td>
<input type="checkbox" name="fields[sourse_site]" value="1" <?php if (in_array('sourse_site', $set['loads']['fields'])) echo 'checked="checked"' ?>><br></td></tr>

<tr class="small"><td class="left">Ссылка на файл:<br></td><td>
<input type="checkbox" name="fields[download_url]" value="1" <?php if (in_array('download_url', $set['loads']['fields'])) echo 'checked="checked"' ?>><br></td></tr>

<tr class="small"><td class="left">Размер удаленног файла:<br></td><td>
<input type="checkbox" name="fields[download_url_size]" value="1" <?php if (in_array('download_url_size', $set['loads']['fields'])) echo 'checked="checked"' ?>><br></td></tr>

<tr class="small"><td class="left">Файл:<br></td><td>
<input type="checkbox" name="require_file" value="1" <?php if (!empty($set['loads']['require_file'])) echo 'checked="checked"' ?>><br></td></tr>




<!--#################### END FIELDS ###################-->

<!--#################### COMMENTS ###################-->
<tr class="small"><td class="group" colspan="2">Комментарии</td></tr>
<tr><td class="left">Максимальный размер комментария:<br></td><td>
<input type="text" name="comment_lenght" value="<?php echo $set['loads']['comment_lenght'] ?>">&nbsp;<span class="comment">Символов</span><br></td></tr>

<tr><td class="left">Комментариев на страницу:<br></td><td>
<input type="text" name="comment_per_page" value="<?php echo $set['loads']['comment_per_page'] ?>"><br></td></tr>

<tr><td class="left">Новые сверху:<br></td><td>
<input type="checkbox" name="comments_order" value="1" <?php echo (!empty($set['loads']['comments_order'])) ? 'checked="checked"' : ''; ?> /><br></td></tr>
<!--#################### END COMMENTS ###################-->
<tr class="small"><td class="group" colspan="2">Прочее</td></tr>
<tr><td class="left">Статус :<br />
<span class="comment">(Активирован/Деактивирован)</span></td>
<td>
<select name="active">
<option value="1" <?php if (Config::read('active', 'loads') && Config::read('active', 'loads') == 1) echo 'selected="selected"' ?>>Активирован</option>
<option value="0"<?php if (!Config::read('active', 'loads') || Config::read('active', 'loads') == 0) echo 'selected="selected"' ?>>Деактивирован</option>
</select>
</td></tr>


<tr><td colspan="2" align="center"><input type="submit" name="send" value="Сохранить"><br></td></tr>

</table>
</form>




<?php
include_once 'template/footer.php';
?>