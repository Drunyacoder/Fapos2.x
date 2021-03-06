<?php
/*-----------------------------------------------\
| 												 |
|  Author:       Andrey Brykin (Drunya)          |
|  Version:      0.3                             |
|  Project:      CMS                             |
|  package       CMS Fapos                       |
|  subpackege    Additional Fields (Admin Part)  |
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




// Know module
$ModulesManager = new ModulesManager();
$allow_modules = $ModulesManager->getAddFieldsAllowedModules();
$modules_titles = $ModulesManager->getAddFieldsAllowedModulesTitles();
if (empty($_GET['m']) || !in_array($_GET['m'], $allow_modules)) {
	$_GET['m'] = 'news';
	$_GET['ac'] = 'index';
}
$pageTitle = $modules_titles[$_GET['m']] . ' - Дополнительные поля';

// Know action
if (!isset($_GET['ac'])) $_GET['ac'] = 'index';
$permis = array('add', 'del', 'index', 'edit');
if (!in_array($_GET['ac'], $permis)) $_GET['ac'] = 'index';

switch($_GET['ac']) {
	case 'del':
		$content = FpsDelete();
		break;
	case 'add':
		$content = FpsAdd();
		break;
	case 'edit':
		$content = FpsEdit();
		break;
	default:
		
}




if ($_GET['ac'] == 'index'):
	$fields = $FpsDB->select($_GET['m'] . '_add_fields', DB_ALL);
	$AddFields = new FpsAdditionalFields;
	if (count($fields) > 0)
		$inputs = $AddFields->getInputs($fields, false, $_GET['m']);




	$pageNav = $pageTitle;
	$pageNavl = '';
	//echo $head
    include_once ROOT . '/admin/template/header.php';
    ?>
	<span style="float:right;"><input type="button" value="Добавить" onClick="wiOpen('add')" /></span>
	<div style="clear:both;"></div>
	</div></div></div>   <div class="xw-bl"></div> 
	<div class="xw-tl1"><div class="xw-tr1"><div class="xw-tc1"></div></div></div>
	<div class="xw-ml"><div class="xw-mr"><div id="mainContent" class="xw-mc topBlockM">


	<div id="add_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
	<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
	</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
	<form action="additional_fields.php?m=<?php echo $_GET['m'] ?>&ac=add" method="POST">
	<div class="form-item2">
	Тип поля:<br />
	<select name="type">
		<option value="text">text</option>
		<option value="checkbox">checkbox</option>
		<option value="textarea">textarea</option>
	</select>
	<div style="clear:both;"></div></div>
	
	<div class="form-item2">
	Видимое имя поля:<br />
	<span class="comment">Будет видно в сообщениях об ошибках</span><br />
	<input type="text" name="label" value="" />
	<div style="clear:both;"></div></div>
	
	<div class="form-item2">
	Максимальный размер:<br />
	<span class="comment">Сохраняемых данных</span><br />
	<input type="text" name="size" value="" />
	<div style="clear:both;"></div></div>
	
	<div class="form-item2">
	Параметры:<br />
	<span class="comment">Подробнее в доке</span><br />
	<input type="text" name="params" value="" />
	<div style="clear:both;"></div></div>
	
	<div class="form-item2">
	Обязательно к заполнению:<br />
	<input type="checkbox" name="required" value="1" />
	<div style="clear:both;"></div></div>
	
	<div class="form-item2 center">
	<input type="submit" name="send" value="Сохранить" />
	<input type="button" onClick="hideWin('add')" value="Отмена" />
	<div style="clear:both;"></div></div>
	</form>
	</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
	<div class="xw-footer"></div></div></div></div>
	</div>


	<?php if (!empty($fields)): ?>
	<?php foreach($fields as $field): ?>
		<?php
			$params = (!empty($field['params'])) ? unserialize($field['params']) : array();
			$values = (!empty($params['values'])) ? $params['values'] : '-';
			$field_market = 'add_field_' . $field['id'];
			$required = (!empty($params['required'])) ? '<span style="color:red;">ДА</span>' : '<span style="color:blue;">НЕТ</span>';
		?>
		<div class="fps-win lines2">
			<div class="items">
				<div class="item">Тип: <div><?php echo h($field['type']); ?></div></div>
				<div class="item">Название поля: <div><?php echo h($field['label']); ?></div></div>
				<div class="item">Max. Размер: <div><?php echo (!empty($field['size'])) ? h($field['size']) : '-'; ?></div></div>
				<div class="item">Доп. Параметры: <div><?php echo (!empty($values)) ? h($values) : ''; ?></div></div>
				<div class="item">Обязательное поле: <div><?php echo $required; ?></div></div>
				<div class="item">Маркет поля: <div><?php echo h(strtoupper('{' . $field_market . '}')); ?></div></div>
			</div>
			<div class="textarea-item">
				<textarea style="width:100%; height:103px;;"><?php echo (!empty($inputs[$field_market])) ? $inputs[$field_market] : ''; ?></textarea>
			</div>
			
			<div class="control-but">
				<a href="javascript://" onClick="wiOpen('edit_<?php echo $field['id'] ?>')">
				<img src="<?php echo get_url('/admin/template/img/edit_16x16.png'); ?>"  /></a>&nbsp;
				<a href="additional_fields.php?m=<?php echo $_GET['m'] ?>&ac=del&id=<?php echo $field['id'] ?>" onClick="return confirm('Are you sure?');">
				<img src="<?php echo get_url('/admin/template/img/del.png'); ?>"  /></a>
			</div>
			<div style="clear:both;"></div>
		</div>
		
		<div id="edit_<?php echo $field['id'] ?>_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
		<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
		</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
		<form action="additional_fields.php?m=<?php echo $_GET['m'] ?>&ac=edit&id=<?php echo $field['id'] ?>" method="POST">
		<div class="form-item2">
		Тип поля:<br />
		<select name="type">
			<option value="text"<?php if($field['type'] == 'text') echo ' selected="selected"' ?>>test</option>
			<option value="checkbox"<?php if($field['type'] == 'checkbox') echo ' selected="selected"' ?>>checkbox</option>
			<option value="textarea"<?php if($field['type'] == 'textarea') echo ' selected="selected"' ?>>textarea</option>
		</select>
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		Видимое имя поля:<br />
		<span class="comment">Будет видно в сообщениях об ошибках</span><br />
		<input type="text" name="label" value="<?php echo h($field['label']) ?>" />
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		Максимальный размер:<br />
		<span class="comment">Сохраняемых данных</span><br />
		<input type="text" name="size" value="<?php echo (!empty($field['size'])) ? h($field['size']) : ''; ?>" />
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		Параметры:<br />
		<span class="comment">Подробнее в доке</span><br />
		<input type="text" name="params" value="<?php echo ($values != '-') ? h($values) : ''; ?>" />
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		Обязательно к заполнению:<br />
		<input type="checkbox" name="required" value="1"<?php if(!empty($params['required'])) echo ' checked="checked"' ?>/>
		<div style="clear:both;"></div></div>
		
		<div class="form-item2 center">
		<input type="submit" name="send" value="Сохранить" />
		<input type="button" onClick="hideWin('edit_<?php echo $field['id'] ?>')" value="Отмена" />
		<div style="clear:both;"></div></div>
		</form>
		</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
		<div class="xw-footer"></div></div></div></div>
		</div>
		
		
	<?php endforeach; ?>
	<?php else: ?>
	<div class="fps-win"><div class="h3">Дополнительные поля отсутствуют!</div></div>
	<?php endif; ?>
	<?php if (!empty($_SESSION['FpsForm']['errors'])): ?>
		<script type="text/javascript">showHelpWin('<?php echo '<ul class="error">' . $_SESSION['FpsForm']['errors'] . '</ul>'; ?>', 'Ошибки');</script>
		<?php unset($_SESSION['FpsForm']); ?>
	<?php endif; ?>
<?php endif; ?>




<?php

function FpsEdit() {
	global $FpsDB;
	if (empty($_GET['id'])) redirect('/admin/additional_fields.php?m=' . $_GET['m']);
	$id = intval($_GET['id']);
	if ($id < 1) redirect('/admin/additional_fields.php?m=' . $_GET['m']);
	
	if (isset($_POST['send'])) {
		$error = null;
		$allow_types = array('text', 'checkbox', 'textarea');
		
		//type of field
		$type = (!empty($_POST['type']) && in_array(trim($_POST['type']), $allow_types))
		? trim($_POST['type']) : 'text';
		if (empty($_POST['label'])) $error .= '<li>Не заполнено поле "Видимое название"</li>';
		if (empty($_POST['size']) && $type != 'checkbox') $error .= '<li>Не заполнено поле "Макс. Размер"</li>';
		if (!empty($_POST['size']) && !is_numeric($_POST['size'])) $error .= '<li>"Макс. Размер" должен быть числом</li>';
		
		//params
		$params = array();
		$params['values'] = (!empty($_POST['params'])) ? trim($_POST['params']) : 'да|нет';
		if (!empty($_POST['required'])) $params['required'] = 1;
		if ($type != 'checkbox') unset($params['values']);
		$params = serialize($params);
		
		//label
		$label = (!empty($_POST['label'])) ? trim($_POST['label']) : 'Доп. поле';
		
		//size
		$size = (!empty($_POST['size'])) ? intval($_POST['size']) : 70;
		
		if (!empty($error)) {
			$_SESSION['FpsForm'] = array('errors' => $error);
			redirect('/admin/additional_fields.php?m=' . $_GET['m']);
		}
		$data = array(
			'type' => $type,
			'label' => $label,
			'size' => $size,
			'params' => $params,
			'id' => $id,
		);
		$FpsDB->save($_GET['m'] . '_add_fields', $data);
		
		//clean cache
		$Cache = new Cache;
		$Cache->clean(CACHE_MATCHING_ANY_TAG, array('module_' . $_GET['m']));
		redirect('/admin/additional_fields.php?m=' . $_GET['m']);
	}
}



function FpsAdd() {
	global $FpsDB;
	if (isset($_POST['send'])) {
		$error = null;
		$allow_types = array('text', 'checkbox', 'textarea');
		
		//type of field
		$type = (!empty($_POST['type']) && in_array(trim($_POST['type']), $allow_types))
		? trim($_POST['type']) : 'text';
		if (empty($_POST['label'])) $error .= '<li>Не заполнено поле "Видимое название"</li>';
		if (empty($_POST['size']) && $type != 'checkbox') $error .= '<li>Не заполнено поле "Макс. Размер"</li>';
		if (!empty($_POST['size']) && !is_numeric($_POST['size'])) $error .= '<li>"Макс. Размер" должен быть числом</li>';
		
		//params
		$params = array();
		$params['values'] = (!empty($_POST['params'])) ? trim($_POST['params']) : 'да|нет';
		if (!empty($_POST['required'])) $params['required'] = 1;
		if ($type != 'checkbox') unset($params['values']);
		$params = serialize($params);
		
		//label
		$label = (!empty($_POST['label'])) ? trim($_POST['label']) : 'Доп. поле';
		
		//size
		$size = (!empty($_POST['size'])) ? intval($_POST['size']) : 70;
		
		if (!empty($error)) {
			$_SESSION['FpsForm'] = array('errors' => $error);
			redirect('/admin/additional_fields.php?m=' . $_GET['m']);
		}
		$data = array(
			'type' => $type,
			'label' => $label,
			'size' => $size,
			'params' => $params,
		);
		$FpsDB->save($_GET['m'] . '_add_fields', $data);
		
		//clean cache
		$Cache = new Cache;
		$Cache->clean(CACHE_MATCHING_ANY_TAG, array('module_' . $_GET['m']));
		redirect('/admin/additional_fields.php?m=' . $_GET['m']);
	}
}




function FpsDelete() {
	global $FpsDB;
	if (empty($_GET['id'])) redirect('/admin/additional_fields.php?m=' . $_GET['m']);
	$id = intval($_GET['id']);
	if ($id < 1) redirect('/admin/additional_fields.php?m=' . $_GET['m']);
	
	$FpsDB->query("DELETE FROM `" . $FpsDB->getFullTableName($_GET['m'] . '_add_fields') 
	. "` WHERE `id` = '" . $id . "' LIMIT 1");
	redirect('/admin/additional_fields.php?m=' . $_GET['m']);
}



include_once 'template/footer.php';
?>