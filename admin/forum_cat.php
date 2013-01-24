<?php
/*-----------------------------------------------\
| 												 |
| @Author:       Andrey Brykin (Drunya)          |
| @Email:        drunyacoder@gmail.com           |
| @Site:         http://fapos.net                |
| @Version:      1.4                             |
| @Project:      CMS                             |
| @package       CMS Fapos                       |
| @subpackege    Admin Panel module  			 |
| @copyright     ©Andrey Brykin 2010-2013        |
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




$pageTitle = __('Forum');



if (!isset($_GET['ac'])) $_GET['ac'] = 'index';
$permis = array('add', 'del', 'index', 'edit');
if (!in_array($_GET['ac'], $permis)) $_GET['ac'] = 'index';

switch($_GET['ac']) {
	case 'index':
		$content = index($pageTitle);
		break;
	case 'del':
		$content = delete();
		break;
	case 'add':
		$content = add();
		break;
	case 'edit':
		$content = edit();
		break;
	default:
		$content = index();
}


$pageNav = $pageTitle;
$pageNavl = '';

include_once ROOT . '/admin/template/header.php';
 ?>


<div class="fps-win">
<span class="comment">
<?php echo __('If you delete a category, all the materials in it will be removed') ?><br /><br />

<?php echo __('Each forum should be inherited from the section') ?>
</span>
</div>

<?php
echo $content;



function index(&$page_title) {
	global $FpsDB;
	deleteCollisions();

	$page_title = __('Forum - sections editor');
	
	$query = $FpsDB->select('forum_cat', DB_ALL, array('order' => 'previev_id'));
	
	//cats and position selectors for ADD
	if (count($query) > 0) {
		$cat_selector = '<select style="width:130px;" name="in_cat" id="cat_secId">';	
		foreach ($query as $key => $result) {
			$cat_selector .= '<option value="' . $result['id'] . '">' . h($result['title']) . '</option>';
		}
		$cat_selector .= '</select>';
	} else {
		$cat_selector = '<b>' . __('First, create a section') . '</b>';
	}
	
	$forums = $FpsDB->select('forums', DB_ALL);

	
	//selector for subforums
	$sub_selector = '<select style="width:130px;" name="parent_forum_id">';
	$sub_selector .= '<option value=""></option>';
	if (!empty($forums)) {
		foreach($forums as $forum) {
			$sub_selector .= '<option value="' . $forum['id'] . '">' . h($forum['title']) . '</option>';
		}
	}
	$sub_selector .= '</select>';
	
	
	$html = '';

	$html .= '<table width="100%"><tr><td>
		<input type="button" name="add" value="' . __('Add section') . '" onClick="wiOpen(\'sec\');" /></td>';
	$html .= '<td align="right">
				<div align="right" class="topButtonL" id="cat_view"><input type="button" name="add" value="' . __('Create forum') . '" onClick="wiOpen(\'cat\');" /></div></td></tr></table>
				
		<div id="cat_dWin" class="fps-win" style="position:absolute;top:100px;left:40%;display:none">
		<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
		</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
		<form action="forum_cat.php?ac=add" method="POST" enctype="multipart/form-data">
		
		<div class="form-item2">
		' . __('Parent section') . ':<br />
		' . $cat_selector . '
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		' . __('Title of forum') . ':<br />
		<input type="hidden" name="type" value="forum" />
		<input type="text" style="width:130px" name="title" />
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		' . __('Forum position') . ':<br /><span class="comment">' . __('Numeric') . '</span><br />
		<input type="text" name="in_pos" />
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		' . __('Parent forum') . ':<br /><span class="comment">' . __('For which this will be sub-forum') . '</span><br />
		' . $sub_selector . '
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		' . __('Icon') . ':<br /> <span class="comment">(' . __('Empty field - no icon') . ')<br />
		' . __('The desired size 16x16 px') . '</span><br />
		<input type="file" style="margin-right:50px;width:130px" name="icon" />
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		' . __('Description') . ':<br />
		<textarea name="description" cols="30" rows="3" /></textarea><br />
		<div style="clear:both;"></div></div>
		
		<hr />
		
		<div class="form-item2">
		' . __('Lock on passwd') . ':<br />
		<input type="text" name="lock_passwd"/><br />
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		' . __('Lock on posts count') . ':<br />
		<input type="text" name="lock_posts"/><br />
		<div style="clear:both;"></div></div>
		
		<div class="form-item2 center">
		<input type="submit" name="send" value="' . __('Save') . '" />
		<input type="button" onClick="hideWin(\'cat\')" value="' . __('Cancel') . '" />
		<div style="clear:both;"></div></div>
		</form>
		</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
		<div class="xw-footer"></div></div></div></div>
		</div>
		
		<div id="sec_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
		<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
		</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
		<form action="forum_cat.php?ac=add" method="POST">
		
		<div class="form-item2">
		' . __('Title') . ':<br />
		<input type="hidden" name="type" value="section" />
		<input type="text" name="title" />
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		' . __('Section position') . ':<br /><span class="comment">' . __('Numeric') . '</span><br />
		<input type="text" name="in_pos" />
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		<input type="submit" name="send" value="' . __('Save') . '" />
		<input type="button" onClick="hideWin(\'sec\')" value="' . __('Cancel') . '" />
		<div style="clear:both;"></div></div>
		</form>
		</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
		<div class="xw-footer"></div></div></div></div>
		</div>';
		
		
		
	if (count($query) > 0) {
		$html .= '<div class="cat_list_container">';
		
		if (!empty($_SESSION['addErrors'])) {
			$html .= '<ul class="error" style="list-style-type:none;color:red;">' . $_SESSION['addErrors'] . '</ul>';
			unset($_SESSION['addErrors']);
		}
		
		
		foreach ($query as $result) {

			$html .= '
				<div class="section">
				<b>'.$result['title'].'</b>
				<div class="tools"><a href="javascript://" onClick="wiOpen(\'' . $result['id'] . '_section\')"><img src="template/img/edit_16x16.png"  /></a>
				<a href="?ac=del&id=' . $result['id'] . '&&section" onClick="return _confirm();"><img src="template/img/del.png"  /></a>';
			/* EDIT SECTION FORM */
			$html .= '		
				<div id="' . $result['id'] . '_section_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
				<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
				</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
				<form action="forum_cat.php?ac=edit&id=' . $result['id'] . '" method="POST">
				
				<div class="form-item2">
				' . __('Title') . ':<br />
				<input type="hidden" name="type" value="section" />
				<input type="text" name="title" value="' . $result['title'] . '" />
				<div style="clear:both;"></div></div>
				
				<div class="form-item2">
				' . __('Section position') . ':<br /><span class="comment">' . __('Numeric') . '</span><br />
				<input type="text" name="in_pos" value="' . $result['previev_id'] . '" />
				<div style="clear:both;"></div></div>
				
				<div class="form-item2 center">
				<input type="submit" name="send" value="' . __('Save') . '" />
				<input type="button" onClick="hideWin(\'' . $result['id'] . '_section\')" value="' . __('Cancel') . '" />
				<div style="clear:both;"></div></div>
				</form>
				</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
				<div class="xw-footer"></div></div></div></div>
				</div>
				</div></div>';
			/* END EDIT SECTION FORM */
			$queryCat = $FpsDB->query("
				SELECT a.*, COUNT(b.`id`) as cnt FROM `" . $FpsDB->getFullTableName('forums') . "` a 
				LEFT JOIN `" . $FpsDB->getFullTableName('themes') . "` b ON b.`id_forum` = a.`id` 
				WHERE a.`in_cat` = '" . $result['id'] . "' GROUP BY a.`id` ORDER BY a.`pos`");
			
			if (count($queryCat) > 0) {
				foreach ($queryCat as $cat) {

					
					//cat selector and position selector for EDIT FRORUMS
					$cat_selector = '<select style="width:130px;" name="in_cat" id="cat_secId">';	
					foreach ($query as $key => $category) {
						if ($cat['in_cat'] == $category['id']) {
							$cat_selector .= '<option value="' . $category['id'] . '" selected="selected">' . $category['title'] . '</option>';
						} else {
							$cat_selector .= '<option value="' . $category['id'] . '">' . $category['title'] . '</option>';
						}
					}
					$cat_selector .= '</select>';

					//selector for subforums
					$sub_selector = '<select style="width:130px;" name="parent_forum_id">';
					$sub_selector .= '<option value=""></option>';
					if (!empty($forums)) {
						foreach($forums as $forum) {
							if ($cat['id'] == $forum['id']) continue; 
							$selected = ($cat['parent_forum_id'] == $forum['id']) ? 'selected="selected"' : ''; 
							$sub_selector .= '<option value="' . $forum['id'] . '" ' . $selected . '>' 
							. $forum['title'] . '</option>';
						}
					}
					$sub_selector .= '</select>';
					
					$issubforum = (!empty($cat['parent_forum_id'])) 
					? '&nbsp;<span style="color:#0373FE;">' . __('Under forum with ID') . ' ' . $cat['parent_forum_id'] . '</span>' : '';
					
					$html .= '<div class="category_row"><div class="category">[' . $cat['id'] . ']&nbsp;<b>' . $cat['title'] 
					. '</b>&nbsp;( ' . $cat['cnt'] . ' ) ' . $issubforum . ' 
						<div class="tools"><a href="javascript://" onClick="wiOpen(\'' . $cat['id'] . '_forum\')">
						<img src="template/img/edit_16x16.png"  /></a>
						<a href="?ac=del&id='
						. $cat['id'] . '" onClick="return _confirm();"><img src="template/img/del.png"  /></a>';
					/* EDIT FORUM FORM */
					$html .= '
						<div id="' . $cat['id'] . '_forum_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
						<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
						</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
						<form action="forum_cat.php?ac=edit&id=' . $cat['id'] . '" method="POST" enctype="multipart/form-data">
						
						<div class="form-item2">
						' . __('Parent section') . ':<br />
						' . $cat_selector . '
						<div style="clear:both;"></div></div>
						
						
						<div class="form-item2">
						' . __('Title of forum') . ':<br />
						<input type="hidden" name="type" value="forum" />
						<input type="text" style="width:130px" name="title" value="' . $cat['title'] . '" />
						<div style="clear:both;"></div></div>
						
						<div class="form-item2">
						' . __('Forum position') . ':<br /><span class="comment">' . __('Numeric') . '</span><br />
						<input type="text" name="in_pos" value="' . $cat['pos'] . '" />
						<div style="clear:both;"></div></div>
						
						<div class="form-item2">
						' . __('Parent forum') . ':<br /><span class="comment">' . __('For which this will be sub-forum') . '</span><br />
						' . $sub_selector . '<div style="clear:both;"></div></div>
						
						<div class="form-item2">
						' . __('Icon') . ':<br /> <span class="comment">(' . __('Empty field - no icon') . ')<br />
						' . __('The desired size 16x16 px') . '</span><br />
						<input type="file" style="margin-right:50px;width:130px" name="icon" />
						<div style="clear:both;"></div></div>
						
						<div class="form-item2">
						' . __('Description') . ':<br />
						<textarea name="description" cols="30" rows="3" />' . $cat['description'] . '</textarea>
						<div style="clear:both;"></div></div>
						
						<hr />
						
						<div class="form-item2">
						' . __('Lock on passwd') . ':<br />
						<input type="text" name="lock_passwd" value="' . $cat['lock_passwd'] . '" /><br />
						<div style="clear:both;"></div></div>
						
						<div class="form-item2">
						' . __('Lock on posts count') . ':<br />
						<input type="text" name="lock_posts" value="' . $cat['lock_posts'] . '" /><br />
						<div style="clear:both;"></div></div>
						
						<div class="form-item2 center">
						<input type="submit" name="send" value="' . __('Save') . '" />
						<input type="button" onClick="hideWin(\'' . $cat['id'] . '_forum\')" value="' . __('Cancel') . '" />
						<div style="clear:both;"></div></div>
						</form>
						</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
						<div class="xw-footer"></div></div></div></div>
						</div></div></div></div>';
					/* END EDIT FORUM FORM */
				}
			} else {
				$html .= '<div class="category_row"><div>' . __('Empty') . '</div></div>';
			}
			
		}
		$html .= '</div>';
	} else {
		$html .= __('While empty');
	}
	return $html;
}


function edit() {
	global $FpsDB;
	
	if (!isset($_POST['title']) || !isset($_POST['type']) || empty($_GET['id'])) {
		redirect('/admin/forum_cat.php');
	}
	if ($_POST['type'] == 'forum' && 
	(!isset($_POST['in_cat']) || !isset($_POST['description']) || !isset($_FILES['icon']))) {
		redirect('/admin/forum_cat.php');
	}
	$id = (int)$_GET['id'];
	if ($id < 1) {
		redirect('/admin/forum_cat.php');
	}
	if (!isset($_POST['in_pos'])) redirect('/admin/forum_cat.php');
	$in_pos = (int)$_POST['in_pos']; 
	if ($in_pos < 1)  redirect('/admin/forum_cat.php');
	$error = '';
	$title = $_POST['title'];
	if (mb_strlen($title) > 200) $error .= '<li>' . __('Title more than 200 symbol') . '</li>';
	
	
	
	if ($_POST['type'] == 'forum') {
		$in_cat = (int)$_POST['in_cat'];
		$description = $_POST['description'];
		if (!empty($_FILES['icon']['name'])) {
			if ($_FILES['icon']['size'] > 100000) $error = $error . '<li>' . __('Max icon size 100Kb') . '</li>';
			if ($_FILES['icon']['type'] != 'image/gif'
			&& $_FILES['icon']['type'] != 'image/jpeg'
			&& $_FILES['icon']['type'] != 'image/png') $error = $error . '<li>' . __('Wrong icon format') . '</li>';
			if (!empty($error)) {
				$_SESSION['addErrors'] = $error;
				redirect('/admin/forum_cat.php');
			}
		}
		
		
		
		// Lock forum
		$lock_passwd = '';
		$lock_posts = 0;
		if (!empty($_POST['lock_passwd'])) {
			$lock_passwd = $_POST['lock_passwd'];
			if (mb_strlen($lock_passwd) > 100) $error = $error . '<li>' . __('Forum passwd more than 100 sym.') . '</li>';
		}
		if (!empty($_POST['lock_posts'])) {
			$lock_posts = $_POST['lock_posts'];
			if (mb_strlen($lock_posts) > 100) $error = $error . '<li>' . __('Posts cnt must be numeric') . '</li>';
		}
		
		
		
		//if isset errors
		if (!empty($error)) {
			$_SESSION['addErrors'] = $error;
			redirect('/admin/forum_cat.php');
		}
		
		//busy position
		$busy = $FpsDB->select('forums', DB_COUNT, array('cond' => array('pos' => $in_pos, 'in_cat' => $in_cat)));
		if ($busy > 0) {
			$FpsDB->query("UPDATE `" . $FpsDB->getFullTableName('forums') . "` SET `pos` = `pos` + 1 WHERE `pos` >= '" . $in_pos . "'");
		}
		//default position ON BOTTOM
		if ($in_pos < 1) {
			$last = $FpsDB->query("SELECT MAX(`pos`) AS last FROM `" . $FpsDB->getFullTableName('forums') . "` WHERE `in_cat` = '" . $in_cat . "' LIMIT 1");
			if (!empty($last[0]['last'])) {
				$in_pos = ((int)$last[0]['last'] + 1);
			} else {
				$in_pos = 1;
			}
		}
		
		
		$parent_forum_id = (int)$_POST['parent_forum_id'];
		$parent_forum_id = (!empty($parent_forum_id)) ? $parent_forum_id : '';
		
		//if allright - saving data
		$query = $FpsDB->save('forums', array(
			'id' => $id,
			'description' => $description,
			'title' => $title,
			'in_cat' => $in_cat,
			'pos' => $in_pos,
			'parent_forum_id' => $parent_forum_id,
			'lock_passwd' => $lock_passwd,
			'lock_posts' => $lock_posts,
		));
		if ($query) {
			if (move_uploaded_file($_FILES['icon']['tmp_name'], ROOT . '/sys/img/forum_icon_' . $id . '.jpg')) {
				chmod(ROOT . '/sys/img/forum_icon_' . $id . '.jpg', 0755);
			}
		}
	
	
	} else if ($_POST['type'] == 'section') {
		
		//if isset errors
		if (!empty($error)) {
			$_SESSION['addErrors'] = $error;
			redirect('/admin/forum_cat.php');
		}
		
		//busy position
		$busy = $FpsDB->select('forum_cat', DB_COUNT, array('cond' => array('previev_id' => $in_pos)));
		if ($busy > 0) {
			$FpsDB->query("UPDATE `" . $FpsDB->getFullTableName('forum_cat') . "` SET `previev_id` = `previev_id` + 1 WHERE `previev_id` >= '" . $in_pos . "'");
		}
		//default position ON BOTTOM
		if ($in_pos < 1) {
			$last = $FpsDB->query("SELECT MAX(`previev_id`) AS last FROM `" . $FpsDB->getFullTableName('forum_cat') . "` LIMIT 1");
			if (!empty($last[0]['last'])) {
				$in_pos = ((int)$last[0]['last'] + 1);
			} else {
				$in_pos = 1;
			}
		}
		
		$FpsDB->save('forum_cat', array(
			'id' => $id, 
			'title' => $title, 
			'previev_id' => $in_pos,
		));
	}
	redirect('/admin/forum_cat.php');
}





function add() {
	global $FpsDB;
	if (empty($_POST['type'])) redirect('/admin/forum_cat.php');
	if (!isset($_POST['title'])) redirect('/admin/forum_cat.php');
	if (!isset($_POST['in_pos'])) redirect('/admin/forum_cat.php');
	
	$in_pos = (int)$_POST['in_pos'];
	if ($_POST['type'] == 'forum' && (!isset($_FILES['icon']) || !isset($_POST['in_cat']))) redirect('/admin/forum_cat.php');
	$title = $_POST['title'];
	$error = '';
	if (empty($title)) $error .= '<li>' . __('Empty field "title"') . '</li>';
	
	
	if ($_POST['type'] == 'section') {
		if (mb_strlen($title) > 200) $error .= '<li>' . __('Title more than 200 symbol') . '</li>';
		//if isset errors
		if (!empty($error)) {
			$_SESSION['addErrors'] = $error;
			redirect('/admin/forum_cat.php');
		}
		
		//busy position
		$busy = $FpsDB->select('forum_cat', DB_COUNT, array('cond' => array('previev_id' => $in_pos)));
		if ($busy > 0) {
			$FpsDB->query("UPDATE `" . $FpsDB->getFullTableName('forum_cat') . "` SET `previev_id` = `previev_id` + 1 WHERE `previev_id` >= '" . $in_pos . "'");
		}
		//default position ON BOTTOM
		if ($in_pos < 1) {
			$last = $FpsDB->query("SELECT MAX(`previev_id`) AS last FROM `" . $FpsDB->getFullTableName('forum_cat') . "` LIMIT 1");
			if (!empty($last[0]['last'])) {
				$in_pos = ((int)$last[0]['last'] + 1);
			} else {
				$in_pos = 1;
			}
		}
		$FpsDB->save('forum_cat', array('title' => $title, 'previev_id' => $in_pos));
	
	
	} elseif ($_POST['type'] == 'forum') {
		$in_cat = (int)$_POST['in_cat'];
		if (!empty($_FILES['icon']['name'])) {
			if ($_FILES['icon']['size'] > 100000) $error = $error . '<li>' . __('Max icon size 100Kb') . '</li>';
			if ($_FILES['icon']['type'] != 'image/gif'
			&& $_FILES['icon']['type'] != 'image/jpeg'
			&& $_FILES['icon']['type'] != 'image/png') $error = $error . '<li>' . __('Wrong icon format') . '</li>';
		}
		
		
		// Lock forum
		$lock_passwd = '';
		$lock_posts = 0;
		if (!empty($_POST['lock_passwd'])) {
			$lock_passwd = $_POST['lock_passwd'];
			if (mb_strlen($lock_passwd) > 100) $error = $error . '<li>' . __('Forum passwd more than 100 sym.') . '</li>';
		}
		if (!empty($_POST['lock_posts'])) {
			$lock_posts = $_POST['lock_posts'];
			if (mb_strlen($lock_posts) > 100) $error = $error . '<li>' . __('Posts cnt must be numeric') . '</li>';
		}
		
		
		if (!empty($error)) {
			$_SESSION['addErrors'] = $error;
			redirect('/admin/forum_cat.php');
		}
		
		//busy position
		$busy = $FpsDB->select('forums', DB_COUNT, array('cond' => array('pos' => $in_pos, 'in_cat' => $in_cat)));
		if ($busy > 0) {
			$FpsDB->query("UPDATE `" . $FpsDB->getFullTableName('forums') . "` SET `pos` = `pos` + 1 WHERE `pos` >= '" . $in_pos . "'");
		}
		//default position ON BOTTOM
		if ($in_pos < 1) {
			$last = $FpsDB->query("SELECT MAX(`pos`) AS last FROM `" . $FpsDB->getFullTableName('forums') . "` WHERE `in_cat` = '" . $in_cat . "' LIMIT 1");
			if (!empty($last[0]['last'])) {
				$in_pos = ((int)$last[0]['last'] + 1);
			} else {
				$in_pos = 1;
			}
		}
		
		$parent_forum_id = (int)$_POST['parent_forum_id'];
		$parent_forum_id = (!empty($parent_forum_id)) ? $parent_forum_id : '';
		
		$description = $_POST['description'];
		$FpsDB->save('forums', array(
			'description' => $description,
			'title' => $title,
			'in_cat' => $in_cat,
			'pos' => $in_pos,
			'parent_forum_id' => $parent_forum_id,
			'lock_passwd' => $lock_passwd,
			'lock_posts' => $lock_posts,
		));
		$id = mysql_insert_id();
		if (!empty($_FILES['icon']['name'])) {
			if (move_uploaded_file($_FILES['icon']['tmp_name'], ROOT . '/sys/img/forum_icon_' . $id . '.jpg')) {
				chmod(ROOT . '/sys/img/forum_icon_' . $id . '.jpg', 0755);
			}
		}
	}
	redirect('/admin/forum_cat.php');
	
}





function delete() {
	global $FpsDB;
	if (empty($_GET['id']) || !is_numeric($_GET['id']))  header ('Location: /');
	$id = (int)$_GET['id']; 
	if ($id < 1) redirect('/admin/forum_cat.php');
	
	if (!isset($_GET['section'])) {
		$sql = $FpsDB->select('themes', DB_ALL, array('cond' => array('id_forum' => $id)));
		if (count($sql) > 0) {
			foreach ($sql as $result) {
				delete_theme($result['id']);
			}
		}
		$FpsDB->query("DELETE FROM `" . $FpsDB->getFullTableName('forums') . "` WHERE `id`='{$id}'");
		if (file_exists(ROOT . '/sys/img/forum_icon_' . $id . '.jpg')) 
			unlink(ROOT . '/sys/img/forum_icon_' . $id . '.jpg');
	} else {
		$sql = $FpsDB->select('forums', DB_ALL, array('cond' => array('in_cat' => $id)));
		if (count($sql) > 0) {
			foreach ($sql as $_result) {
				$sql = $FpsDB->select('themes', DB_ALL, array('cond' => array('id_forum' => $_result['id'])));
				if (count($sql) > 0) {
					foreach ($sql as $result) {
						delete_theme($result['id']);
					}
				}
				if (file_exists(ROOT . '/sys/img/forum_icon_' . $_result['id'] . '.jpg')) 
					unlink(ROOT . '/sys/img/forum_icon_' . $_result['id'] . '.jpg');
			}
		}
		$FpsDB->query("DELETE FROM `" . $FpsDB->getFullTableName('forums') . "` WHERE `in_cat`='{$id}'");
		$FpsDB->query("DELETE FROM `" . $FpsDB->getFullTableName('forum_cat') . "` WHERE `id`='{$id}'");
	}
	redirect('/admin/forum_cat.php');
}

// Функция удаляет тему; ID темы передается методом GET
function delete_theme($id_theme) {
	global $FpsDB;
	// Если не передан ID темы, которую надо удалить
	if (empty($id_theme)) {
		redirect('/admin/forum_cat.php');
	}
	$id_theme = (int)$id_theme;
	if ( $id_theme < 1 ) {
		redirect('/admin/forum_cat.php');
	}
	
	// delete colision ( this is paranoia )
	$FpsDB->query("DELETE FROM `" . $FpsDB->getFullTableName('themes') . "` WHERE id NOT IN (SELECT DISTINCT id_theme FROM `" . $FpsDB->getFullTableName('posts') . "`)");
	$FpsDB->query("DELETE FROM `" . $FpsDB->getFullTableName('posts') . "` WHERE id_theme NOT IN (SELECT id FROM `" . $FpsDB->getFullTableName('themes') . "`)");

	
	
	// Сперва мы должны удалить все сообщения (посты) темы;
	// начнем с того, что удалим файлы вложений
	$res = $FpsDB->select('posts', DB_ALL, array('cond' => array('id_theme' => $id_theme)));
	if (count($res) > 0) {
		foreach ($res as $file) {
			// Удаляем файл, если он есть
			$attach_files = $FpsDB->select('forum_attaches', DB_ALL, array('cond' => array('post_id' => $file['id'])));
			if (count($attach_files) > 0) {
				foreach ($attach_files as $attach_file) {
					if (file_exists(ROOT . '/sys/files/forum/' . $attach_file['filename'])) {
						if (@unlink(ROOT . '/sys/files/forum/' . $attach_file['filename'])) {
							$FpsDB->query("DELETE FROM `" . $FpsDB->getFullTableName('forum_attaches') . "` WHERE `id`='" . $attach_file['id'] . "'");
						}
					}
				}
			}
			// заодно обновляем таблицу TABLE_USERS - надо обновить поле posts (кол-во сообщений)
			if ( $file['id_author'] ) {
				$FpsDB->query("UPDATE `" . $FpsDB->getFullTableName('users') . "` SET `posts` = `posts` - 1 WHERE `id` = '" . $file['id_author'] . "'");
			}
		}
	}
	
	
	$attach_files = $FpsDB->select('forum_attaches', DB_ALL, array('cond' => array('theme_id' => $id_theme)));
	if (count($attach_files) > 0) {
		foreach ($attach_files as $attach_file) {
			if (file_exists(ROOT . '/sys/files/forum/' . $attach_file['filename'])) {
				if (@unlink(ROOT . '/sys/files/forum/' . $attach_file['filename'])) {
					$FpsDB->query("DELETE FROM `" . $FpsDB->getFullTableName('forum_attaches') . "` WHERE `id`='" . $attach_file['id'] . "'");
				}
			}
		}
	}

	//we must know id_forum
	$theme = $FpsDB->select('themes', DB_FIRST, array('cond' => array('id' => $id_theme)));
	
	
	//delete posts and theme
	$p_res = $FpsDB->query("DELETE FROM `" . $FpsDB->getFullTableName('posts') . "` WHERE `id_theme` = '" . $id_theme . "'");
	$t_res = $FpsDB->query("DELETE FROM `" . $FpsDB->getFullTableName('themes') . "` WHERE `id` = '" . $id_theme . "'");
	
	if (!empty($theme[0]['id_author'])) {
		// Обновляем таблицу TABLE_USERS - надо обновить поле themes
		$u_res = $FpsDB->query("UPDATE `" . $FpsDB->getFullTableName('users') . "` SET `themes` = `themes` - 1
				WHERE `id` = '" . $theme[0]['id_author'] . "'");
	}
	//clean cache
	$Cache = new Cache;
	$Cache->clean(CACHE_MATCHING_ANY_TAG, array('theme_id_' . $id_theme,));
	$Cache->clean(CACHE_MATCHING_TAG, array('module_forum', 'action_index'));
}


//delete "0" values from forums pos AND forums_cat previev_id
function deleteCollisions() {
	global $FpsDB;
	$categories_err = $FpsDB->select('forum_cat', DB_COUNT, array('cond' => array('previev_id' => 0)));
	$forums_err = $FpsDB->select('forums', DB_COUNT, array('cond' => array('pos' => 0)));
	if ($categories_err > 0 || $forums_err > 0) {
		$categories = $FpsDB->select('forum_cat', DB_ALL);
		if (count($categories) > 0) {
			foreach ($categories as $cat_key => $cat) {
				$forums = $FpsDB->select('forums', DB_ALL, array('cond' => array('in_cat' => $cat['id'])));
				if (count($forums) > 0) {
					foreach ($forums as $key => $forum) {
						$FpsDB->save('forums', array(
							'id' => $forum['id'],
							'pos' => ($key + 1),
						));
					}
				}
				if ((int)$cat['previev_id'] < 1) {
					$FpsDB->save('forum_cat', array(
						'id' => $cat['id'],
						'previev_id' => ($cat_key + 1),
					));
				}
			}
		}
	}
	return;
}

include_once 'template/footer.php';
?>