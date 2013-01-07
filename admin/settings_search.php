<?php
/*-----------------------------------------------\
| 												 |
|  Author:       Andrey Brykin (Drunya)          |
|  Version:      0.7                             |
|  Project:      CMS                             |
|  package       CMS Fapos                       |
|  subpackege    Admin Panel module              |
|  copyright     ©Andrey Brykin 2010-2011        |
\-----------------------------------------------*/

/*-----------------------------------------------\
| 												 |
|  any partial or not partial extension          |
|  CMS Fapos,without the consent of the          |
|  author, is illegal                            |
|------------------------------------------------|
|  Любое распространение                         |
|  CMS Fapos или ее частей,                      |
|  без согласия автора, является не законным     |
\-----------------------------------------------*/


include_once '../sys/boot.php';
include_once ROOT . '/admin/inc/adm_boot.php';
$pageTitle = 'Поиск - Настройки';


$set = Config::read('all');
$TempSet = $set;
if (isset($_POST['send'])) {
	$_POST = array_merge(array(
		'title' => null, 
		'description' => null, 
		'index_interval' => 1,
		'min_lenght' => 3, 
		'per_page' => 10, 
		'active' => 1), $_POST);
	$index_interval = (int)$_POST['index_interval'];
	
	$TempSet['search']['title'] = $_POST['title'];
	$TempSet['search']['description'] = $_POST['description'];
	$TempSet['search']['index_interval'] = (!empty($index_interval)) ? $index_interval : 1;
	$TempSet['search']['min_lenght'] = $_POST['min_lenght'];
	$TempSet['search']['per_page'] = $_POST['per_page'];
	$TempSet['search']['active'] = $_POST['active'];


	Config::write($TempSet);
	redirect('/admin/settings_search.php');
}


$pageNav = $pageTitle;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
?>



<form method="POST" action="settings_search.php">
<table class="settings-tb">

<tr><td class="left">Заголовок модуля:</td>
<td>
<input type="text" name="title" value="<?php echo (!empty($set['search']['title'])) ? h($set['search']['title']) : ''; ?>">
</td></tr><th>

<tr><td class="left">Описание модуля:<br></td>
<td>
<input type="text" name="description" value="<?php echo (!empty($set['search']['description'])) ? h($set['search']['description']) : ''; ?>">
</td></tr>


<tr class="small"><td class="group" colspan="2">Ограничения</td></tr>
<tr><td class="left">Частота обновления:<br>
<span class="comment">Через какое кол-во дней проводить переиндексацию сайта</span>
</td>
<td><input type="text" name="index_interval" value="<?php echo (!empty($set['search']['index_interval'])) ? intval($set['search']['index_interval']) : 1; ?>">&nbsp;<span class="comment">Дней</span></td></tr>


<tr><td class="left">Минимальная длина запроса:<br>
<span class="comment">Поиск будет вестись только 
по словам отвечающим этому требованию</span>
</td>
<td><input type="text" name="min_lenght" value="<?php echo (!empty($set['search']['min_lenght'])) ? h($set['search']['min_lenght']) : ''; ?>">&nbsp;<span class="comment">Символов</span></td></tr><th>

<tr><td class="left">Количество выводимых результатов:<br></td>
<td><input type="text" name="per_page" value="<?php echo (!empty($set['search']['per_page'])) ? h($set['search']['per_page']) : ''; ?>">&nbsp;<span class="comment">Шт.</span></td></tr>


<tr class="small"><td class="group" colspan="2">Прочее</td></tr>
<tr><td class="left">Статус :<br />
<span class="comment">(Активирован/Деактивирован)</span></td>
<td>
<select name="active">
<option value="1" <?php if (Config::read('active', 'search') && Config::read('active', 'search') == 1) echo 'selected="selected"' ?>>Активирован</option>
<option value="0"<?php if (!Config::read('active', 'search') || Config::read('active', 'search') == 0) echo 'selected="selected"' ?>>Деактивирован</option>
</select>
</td></tr>


<tr><td colspan="2" align="center"><input type="submit" name="send" value="Сохранить"><br></td></tr>

</table>
</form>


<?php
include_once 'template/footer.php';
?>