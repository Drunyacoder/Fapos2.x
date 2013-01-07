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

 
$pageTitle = 'Пользователи';
$pageNav = $pageTitle;
$pageNavl = '<a href="javascript://" onClick="wiOpen(\'addGroup\')">Добавить группу</a>&nbsp;|&nbsp;<a href="users_rules.php">Редактор прав</a>';




$dp = $Register['DocParser'];
$acl_groups = $Register['ACL']->get_group_info();

//create tmp array with groups and cnt users in them.
$errors = array();
$groups = array();
if (!empty($acl_groups)) {
	$groups = $acl_groups;
	foreach ($acl_groups as $key => $value) {
		$groups[$key] = array();
		$groups[$key]['title'] = $value['title'];
		$groups[$key]['color'] = $value['color'];
		$groups[$key]['cnt_users'] = $FpsDB->select('users', DB_COUNT, array('cond' => array('status' => $key)));
	}
}


//move users into other group
if (!empty($_GET['ac']) && $_GET['ac'] == 'move') {
	if (isset($_POST['id']) && is_numeric($_POST['id']) && (int)$_POST['id'] !== 0) {
		$from = (int)$_POST['id'];
		if (!empty($_POST['to']) && is_numeric($_POST['to'])) {
			if (key_exists($_POST['to'], $acl_groups)) {
				$FpsDB->save('users', array('status' => $_POST['to']), array('status' => $from));
			}
		}
	}
	if(empty($errors)) redirect('/admin/users_groups.php');
	
//edit group
} else if (!empty($_GET['ac']) && $_GET['ac'] == 'edit') {
	if (isset($_POST['id']) && is_numeric($_POST['id'])) {
		$id = (int)$_POST['id'];
		if (!empty($_POST['title'])) {
			$allowed_colors = array('000000', 'EF1821', '368BEB', '959385', 'FBCA0B', '00AA2B', '9B703F', 'FAAA3C');
			if (!in_array($_POST['color'], $allowed_colors)) $errors[] = 'Не допустимый цвет';
			if (mb_strlen($_POST['title']) > 100 || mb_strlen($_POST['title']) < 2) {
				$errors[] = 'Поле "имя группы" должно быть в пределах 2-100 символов';
			} 

			if (!preg_match('#^[\w\d-_a-zа-я0-9 ]+$#ui', trim($_POST['title']))) {
				$errors[] = 'Поле "имя группы" содержит недопустимые символы';
			}
			if (empty($errors)) {
				if (key_exists($id, $acl_groups)) {
					$acl_groups[$id] = array('title' => h($_POST['title']), 'color' => h($_POST['color']));
					$ACL->save_groups($acl_groups);
				}
			}
		} else {
			$errors[] = 'Не заполненно поле "имя группы"';
		}
	}
	if(empty($errors)) redirect('/admin/users_groups.php');

//delete group
} else if (!empty($_GET['ac']) && $_GET['ac'] == 'delete') {
	if (isset($_GET['id']) && is_numeric($_GET['id']) && (int)$_GET['id'] !== 0 && (int)$_GET['id'] !== 1) {
		$id = (int)$_GET['id'];
		if ($groups[$_GET['id']]['cnt_users'] > 0) {
			$errors[] = 'Группа не пуста. Сперва перенесите пользователей';
		} else {
			unset($acl_groups[$_GET['id']]);
			$ACL->save_groups($acl_groups);
		}
	}
	if(empty($errors)) redirect('/admin/users_groups.php');

//add group	
} else if (!empty($_GET['ac']) && $_GET['ac'] == 'add') {
	if (!empty($_POST['title']) && !empty($_POST['color'])) {
		$allowed_colors = array('000000', 'EF1821', '368BEB', '959385', 'FBCA0B', '00AA2B', '9B703F', 'FAAA3C');
		if (!in_array($_POST['color'], $allowed_colors)) $errors[] = 'Не допустимый цвет';
		if (mb_strlen($_POST['title']) > 100 || mb_strlen($_POST['title']) < 2) {
			$errors[] = 'Поле "имя группы" должно быть в пределах 2-100 символов';
		}
		if (!preg_match('#^[\w\d-_a-zа-я0-9 ]+$#ui', $_POST['title'])) {
			$errors[] = 'Поле "имя группы" содержит недопустимые символы';
		}
		if (empty($errors)) {
			$acl_groups[] = array('title' => h($_POST['title']), 'color' => h($_POST['color']));
			$ACL->save_groups($acl_groups);
		}
	} else {
		$errors[] = 'Не заполненно поле "имя группы"';
	}
	if(empty($errors)) redirect('/admin/users_groups.php');



}


include_once ROOT . '/admin/template/header.php';

?>
 

<?php
if (!empty($errors)) {
	foreach ($errors as $error) {
?>
<span style="color:red;"><?php echo $error  ?></span><br />
<?php
	}
	unset($errors);
}
?>
	
	<div id="addGroup_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
	<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
	</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
	<form action="users_groups.php?ac=add" method="POST">
	<div class="form-item2">
	Имя Группы:<br />
	<input type="text" name="title" />
	<div style="clear:both;"></div></div>
	
	<div class="form-item2">
	Цвет для группы:<br />
	<select name="color">
		<option style="color:#000000;" value="000000">Черный</option>
		<option style="color:#EF1821;" value="EF1821">Красный</option>
		<option style="color:#368BEB;" value="368BEB">Синий</option>
		<option style="color:#959385;" value="959385">Серый</option>
		<option style="color:#FBCA0B;" value="FBCA0B">Желтый</option>
		<option style="color:#00AA2B;" value="00AA2B">Зеленый</option>
		<option style="color:#9B703F;" value="9B703F">Коричневый</option>
		<option style="color:#FAAA3C;" value="FAAA3C">Оранж</option>
	</select>
	<div style="clear:both;"></div></div>
	
	<div class="form-item2 center">
	<input type="submit" name="send" value="Сохранить" />
	<input type="button" onClick="hideWin('addGroup')" value="Отмена" />
	<div style="clear:both;"></div></div>
	</form>
	</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
	<div class="xw-footer"></div></div></div></div>
	</div>



	<table class="lines" width="100%" cellspacing="0">
		<tr>
			<th width="5%">ID</th>
			<th>Группа</th>
			<th width="10%">Пользователей</th>
			<th width="7%">Действия</th>
		</tr>

		
	<?php

	if (!empty($groups)) {
		foreach ($groups as $key => $value) {
			if ($key !== 0) {
	?>
		<tr>
			<td><?php echo h($key); ?></td>
			<td><?php echo h($value['title']); ?></td>
			<td><?php echo h($value['cnt_users']); ?></td>
			<td>
				<a href="javascript://" onClick="wiOpen('<?php echo h($key) ?>')"><img src="template/img/edit_16x16.png" title="Edit" /></a>
				<a href="javascript://" onClick="wiOpen('<?php echo h($key) ?>_move')">
				<img src="template/img/right_arrow.png" title="Move users" /></a>
				<?php if ($key !== 0 && $key !== 1): ?>
				<a href="users_groups.php?ac=delete&id=<?php echo h($key) ?>" onClick="return confirm('Are you sure?')">
				<img src="template/img/del.png" title="Delete" />
				</a>
				<?php endif; ?>
				<!-- FOR EDIT -->
				<div id="<?php echo h($key) ?>_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
				<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
				</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
				<form action="users_groups.php?ac=edit" method="POST">
				
				<div class="form-item2">
				Имя Группы:<br />
				<input type="hidden" name="id" value="<?php echo $key ?>" />
				<input type="text" name="title"  value="<?php echo $value['title'] ?>" />
				<div style="clear:both;"></div></div>
				
				<div class="form-item2">
				Цвет для группы:<br />
				<select name="color">
					<option style="color:#000000;" value="000000" <?php if($value['color'] == '000000') echo 'selected="selected"' ?>>Черный</option>
					<option style="color:#EF1821;" value="EF1821" <?php if($value['color'] == 'EF1821') echo 'selected="selected"' ?>>Красный</option>
					<option style="color:#368BEB;" value="368BEB" <?php if($value['color'] == '368BEB') echo 'selected="selected"' ?>>Синий</option>
					<option style="color:#959385;" value="959385" <?php if($value['color'] == '959385') echo 'selected="selected"' ?>>Серый</option>
					<option style="color:#FBCA0B;" value="FBCA0B" <?php if($value['color'] == 'FBCA0B') echo 'selected="selected"' ?>>Желтый</option>
					<option style="color:#00AA2B;" value="00AA2B" <?php if($value['color'] == '00AA2B') echo 'selected="selected"' ?>>Зеленый</option>
					<option style="color:#9B703F;" value="9B703F" <?php if($value['color'] == '9B703F') echo 'selected="selected"' ?>>Коричневый</option>
					<option style="color:#FAAA3C;" value="FAAA3C" <?php if($value['color'] == 'FAAA3C') echo 'selected="selected"' ?>>Оранж</option>
				</select>
				<div style="clear:both;"></div></div>
				
				<div class="form-item2 center">
				<input type="submit" name="send" value="Сохранить" />
				<input type="button" onClick="hideWin('<?php echo h($key) ?>')" value="Отмена" />
				<div style="clear:both;"></div></div>
				</form>
				</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
				<div class="xw-footer"></div></div></div></div>
				</div>
				
				<!-- FOR MOVE -->
				<div id="<?php echo h($key) ?>_move_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
				<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
				</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
				<form action="users_groups.php?ac=move" method="POST">
				<div class="form-item2">
				Куда перенести:<br />
				<input type="hidden" name="id" value="<?php echo $key ?>" />
				<?php
				$select = '<select name="to">';
				if (!empty($groups)) {
					foreach($groups as $sk => $sv) { 
						if ($sk != $key) {
							$select .= '<option value="' . $sk . '">' . h($sv['title']) . '</option>';
						}
					}
				}
				$select .= '</select>';
				?>
				<?php echo $select; ?>
				<div style="clear:both;"></div></div>
				
				<div class="form-item2 center">
				<input type="submit" name="send" value="Сохранить" />
				<input type="button" onClick="hideWin('<?php echo h($key) ?>_move')" value="Отмена" />
				<div style="clear:both;"></div></div>
				</form>
				</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
				<div class="xw-footer"></div></div></div></div>
				</div>
			</td>
		</tr>
		
		

		
	<?php	} else { ?>
			
		<tr>
			<td><?php echo h($key); ?></td>
			<td><?php echo h($value['title']); ?></td>
			<td> - </td>
			<td>
				-
			</td>
		</tr>
			
	<?php		
			}
		}
	} else {
	?>
		<tr>
			<td colspan="4">Нет групп</td>
		</tr>

	<?php
	}

	?>

		
		

	</table>
	</form>


<?php 
include_once ROOT . '/admin/template/footer.php';
?>