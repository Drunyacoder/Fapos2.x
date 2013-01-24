<?php
/*-----------------------------------------------\
| 												 |
| @Author:       Andrey Brykin (Drunya)          |
| @Email:        drunyacoder@gmail.com           |
| @Site:         http://fapos.net                |
| @Version:      1.3                             |
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




/**
 * Return current module which we editing
 */
function getCurrMod() {
	$ModulesManager = new ModulesManager();
	$allow_mods = $ModulesManager->getCategoriesAllowedModules();
	if (empty($_GET['mod'])) redirect('/admin/category.php?mod=news');
	
	$mod = trim($_GET['mod']);
	if (!in_array($mod, $allow_mods)) redirect('/admin/category.php?mod=news');
	return $mod;
}




/**
 * Try find collision
 */
function deleteCatsCollision() 
{
	global $FpsDB;
	$collision = $FpsDB->select(getCurrMod() . '_sections', DB_ALL, array(
		'joins' => array(
			array(
				'type' => 'LEFT',
				'table' => getCurrMod() . '_sections',
				'alias' => 'b',
				'cond' => '`b`.`id` = `a`.`parent_id`',
			),
		),
		'fields' => array('COUNT(`b`.`id`) as cnt', '`a`.*'),
		'alias' => 'a',
		'group' => '`a`.`parent_id`',
	));
	
	if (count($collision)) {
		foreach ($collision as $key => $cat) {
			if (!empty($cat['parent_id']) && empty($cat['cnt'])) {
				$FpsDB->save(getCurrMod() . '_sections', 
				array(
					'parent_id' => 0,
				), 
				array(
					'id' => $cat['id']
				));
			}
		}
	}
}
deleteCatsCollision();





$head = file_get_contents('template/header.php');
$ptitles = array(
	'news' => __('News'),
	'stat' => __('Article'),
	'loads' => __('Loads'),
	'foto' => __('Photo'),
);
$page_title = $ptitles[getCurrMod()];


if (!isset($_GET['ac'])) $_GET['ac'] = 'index';
$permis = array('add', 'del', 'index', 'edit', 'off_home', 'on_home');
if (!in_array($_GET['ac'], $permis)) $_GET['ac'] = 'index';

switch($_GET['ac']) {
	case 'index':
		$content = index($page_title);
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
	case 'on_home':
		$content = on_home();
		break;
	case 'off_home':
		$content = off_home();
		break;
	default:
		$content = index();
}




$pageTitle = $page_title;
$pageNav = $page_title;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
?>



<div class="fps-win">
<?php echo __('If you delete a category, all the materials in it will be removed') ?><br /><br />

</div>
<?php
echo $content;



function getTreeNode($array, $id = false) {
	$out = array();
	foreach ($array as $key => $val) {
		if ($id === false && empty($val['parent_id'])) {
			$out[$val['id']] = array(
				'category' => $val,
				'subcategories' => getTreeNode($array, $val['id']),
			);
			unset($array[$key]);
		} else {
		
			if ($val['parent_id'] == $id) {
				$out[$val['id']] = array(
					'category' => $val,
					'subcategories' => getTreeNode($array, $val['id']),
				);
				unset($array[$key]);
			}
		}
	}
	return $out;
}


function buildCatsList($catsTree, $catsList, $indent = '') {
    $Register = Register::getInstance();
	$FpsDB = $Register['DB'];
    $acl_groups = $Register['ACL']->get_group_info();
	$out = '';
	
	foreach ($catsTree as $id => $node) {
		$cat = $node['category'];
		$no_access = ($cat['no_access'] !== '') ? explode(',', $cat['no_access']) : array();

		
		$_catList = (count($catsList)) ? $catsList : array();
		$cat_selector = '<select style="width:130px;" name="id_sec" id="cat_secId">';
		if (empty($cat['parent_id'])) {
			$cat_selector .= '<option value="0" selected="selected">&nbsp;</option>';
		} else {
			$cat_selector .= '<option value="0">&nbsp;</option>';
		}
		foreach ($_catList as $selector_result) {
			if ($selector_result['id'] == $cat['id']) continue;
			if ($cat['parent_id'] == $selector_result['id']) {
				$cat_selector .= '<option value="' . $selector_result['id'] 
				. '" selected="selected">' . $selector_result['title'] . '</option>';
			} else {
				$cat_selector .= '<option value="' . $selector_result['id'] 
				. '">' . $selector_result['title'] . '</option>';
			}
		}
		$cat_selector .= '</select>';
		
		
		$out .= '<div class="category_row">' . $indent . '<div class="category"><b>' 
			. h($cat['title']) . '</b>( ' . $cat['cnt'] 
			. ' )<div class="tools"><a href="javascript://" onClick="wiOpen(\'' . $cat['id'] . '_cat\');">' 
			. '<img src="template/img/edit_16x16.png"  /></a>&nbsp;<a href="?ac=del&id=' . $cat['id'] 
			. '&mod='.getCurrMod().'" onClick="return _confirm();"><img src="template/img/del.png"  /></a>&nbsp;';
		

		if (getCurrMod() != 'foto') {
			if ($cat['view_on_home'] == 1) {
				$out .=  '<a href="?ac=off_home&id=' . $cat['id'] . '&mod='.getCurrMod().'" onClick="return _confirm();">'
					. '<img title="' . __('On home') . '" src="template/img/round_ok.png" /></a>';
			} else {
				$out .=  '<a href="?ac=on_home&id=' . $cat['id'] . '&mod='.getCurrMod().'" onClick="return _confirm();">'
					. '<img title="' . __('On home') . '" src="template/img/round_not_ok.png" /></a>';
			}
		}
			  
		$out .= '<div id="' . $cat['id'] . '_cat_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
			<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
			</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
			<form action="category.php?mod=' . getCurrMod() . '&ac=edit&id=' . $cat['id'] . '" method="POST">
			
			<div class="form-item2">
			' . __('Parent section') . ':<br />
			' . $cat_selector . '
			<div style="clear:both;"></div></div>
			
			<div class="form-item2">
			' . __('Title') . ':<br />
			<input type="text" style="width:130px" name="title" value="' . h($cat['title']) . '" />
			<div style="clear:both;"></div></div>
			
			<div class="form-item2">
			' . __('Access for') . ':<br /><table><tr>';
		$n = 1;
		foreach ($acl_groups as $id => $group) {
			if (($n % 3) == 0) $out .= '</tr><tr>';
			$checked = (in_array($id, $no_access)) ? '' : ' checked="checked"';
			$out .= '<td><input type="checkbox" name="access[' . $id . ']" value="' . $id 
			. '"' . $checked . '  />&nbsp;' . h($group['title']) . '</td>';
			$n++;
		}
		$out .= '</tr></table><div style="clear:both;"></div></div>
			
			<div class="form-item2 center">
			<input type="submit" name="send" value="' . __('Save') . '" />
			<input type="button" onClick="hideWin(\'' . $cat['id'] . '_cat\')" value="' . __('Cancel') . '" />
			<div style="clear:both;"></div></div>
			</form>
			</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
			<div class="xw-footer"></div></div></div></div>
			</div>';	  
		$out .= '</div></div></div>';
		
		if (count($node['subcategories'])) {
			$out .= buildCatsList($node['subcategories'], $catsList, $indent . '<div class="cat-indent">&nbsp;</div>');
		}
	}
	
	return $out;
}



function index(&$page_title) {
    $Register = Register::getInstance();
	$FpsDB = $Register['DB'];
    $acl_groups = $Register['ACL']->get_group_info();


	$page_title .= ' - ' . __('Sections editor');
	$cat_selector = '<select style="width:130px;" name="id_sec" id="cat_secId">';
	$cat_selector .= '<option value="0">&nbsp;</option>';
	$all_sections = $FpsDB->select(getCurrMod() . '_sections', DB_ALL, array(
		'joins' => array(
			array(
				'alias' => 'b',
				'type' => 'LEFT',
				'table' => getCurrMod(),
				'cond' => 'a.`id` = b.`category_id`',
			),
		),
		'fields' => array('a.*', 'COUNT(b.`id`) as cnt'),
		'alias' => 'a',
		'group' => 'a.`id`',
	));
	foreach ($all_sections as $result) {
		$cat_selector .= '<option value="' . $result['id'] . '">' . h($result['title']) . '</option>';
	}
	$cat_selector .= '</select>';
	
	$html = '';
	if (!empty($_SESSION['errors'])) {
		$html .= '<ul style="color:red;list-style-type:none;">' . $_SESSION['errors'] . '</ul>';
		unset($_SESSION['errors']);
	}
	
	
	
	$cats_tree = getTreeNode($all_sections);
	if (count($cats_tree)) {
		foreach ($cats_tree as $catid => $cat) {
		
		}
	}
	
	
	
	$html .= '<table width="100%"><tr><td></td>';
	$html .= '<td align="right">
				<div align="right" class="topButtonL" id="cat_view"><input type="button" name="add" value="' . __('Add section') . '" onClick="wiOpen(\'cat\');" /></div></td></tr></table>
				
		<div id="cat_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
		<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
		</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
		<form action="category.php?mod=' . getCurrMod() . '&ac=add" method="POST">
		
		<div class="form-item2">
		' . __('Parent section') . ':<br />
		' . $cat_selector . '
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		' . __('Title') . ':<br />
		<input type="hidden" name="type" value="cat" />
		<input type="text" style="width:130px" name="title" />
		<div style="clear:both;"></div></div>
		
		<div class="form-item2">
		Доступно:<br /><table><tr>';
		$n = 1;
		foreach ($acl_groups as $id => $group) {
			if (($n % 3) == 0) $html .= '</tr><tr>';
			$html .= '<td><input type="checkbox" name="access[' . $id . ']" value="' . $id 
			. '"  checked="checked" />&nbsp;' . h($group['title']) . '</td>';
			$n++;
		}
		$html .= '</tr></table><div style="clear:both;"></div></div>
		
		<div class="form-item2 center">
		<input type="submit" name="send" value="' . __('Save') . '" />
		<input type="button" onClick="hideWin(\'cat\')" value="' . __('Cancel') . '" />
		<div style="clear:both;"></div></div>
		</form>
		</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
		<div class="xw-footer"></div></div></div></div>
		</div>';
	
	

	
	if (count($all_sections) > 0) {
		$html .= '<div class="cat_list_container">';
		$html .= buildCatsList($cats_tree, $all_sections); 	
		$html .= '</div>';
	} else {
		$html .= __('Sections not found');
	}
	return $html;
}




function edit() {
	global $FpsDB, $acl_groups;
	if (!isset($_GET['id'])) redirect('/admin/category.php?mod=' . getCurrMod());
	if (!isset($_POST['title'])) redirect('/admin/category.php?mod=' . getCurrMod());
	$id = intval($_GET['id']);
	if ($id < 1) redirect('/admin/category.php?mod=' . getCurrMod());
	$error = '';

	if (empty($_POST['title'])) $error .= '<li>' . __('Empty field "title"') . '</li>';
	


	$parent_id = intval($_POST['id_sec']);
	$changed_cat = $FpsDB->select(getCurrMod() . '_sections', DB_FIRST, array('cond' => array('id' => $id)));
	if (empty($changed_cat)) $error .= '<li>' . __('Edited section not found') . '</li>';

	
	/* we must know changed parent section or not changed her. And check her  */
	if (!empty($parent_id) && $changed_cat[0]['parent_id'] != $parent_id) {
		$target_section = $FpsDB->select(getCurrMod() . '_sections', DB_COUNT, array('cond' => array('id' => $parent_id)));
		if ($target_section < 1) $error .= '<li>' . __('Parent section not found') . '</li>';
	}
	/* if errors exists */
	if (!empty($error)) {
		$_SESSION['errors'] = $error;
		redirect('/admin/category.php?mod=' . getCurrMod());
	}
	
	
	$no_access = array();
	foreach ($acl_groups as $gid => $group) {
		if (!array_key_exists($gid, $_POST['access'])) {
			$no_access[] = $gid;
		}
	}
	$no_access = (count($no_access)) ? implode(',', $no_access) : '';
	if ($no_access !== '') $no_access = New Expr($no_access);
	
	
	/* prepare data to save */
	$data = array(
		'id' => $id, 
		'title' => substr($_POST['title'], 0, 100), 
		'no_access' => $no_access,
	);
	if (!empty($parent_id)) $data['parent_id'] = (int)$parent_id;
	$FpsDB->save(getCurrMod() . '_sections', $data);
		

	redirect('/admin/category.php?mod=' . getCurrMod());
}



function add() {
	global $FpsDB, $acl_groups;
	if (empty($_POST['title'])) redirect('/admin/category.php?mod=' . getCurrMod());
	$error = '';
	$title = mysql_real_escape_string($_POST['title']);
	$in_cat = intval($_POST['id_sec']);
	if ($in_cat < 0) $in_cat = 0;
	
	
	if (empty($title)) $error .= '<li>' . __('Empty field "title"') . '</li>';
	
	$no_access = array();
	foreach ($acl_groups as $id => $group) {
		if (!array_key_exists($id, $_POST['access'])) {
			$no_access[] = $id;
		}
	}
	$no_access = (count($no_access)) ? implode(',', $no_access) : '';
	if ($no_access !== '') $no_access = New Expr($no_access);
	
	/* if errors exists */
	if (!empty($error)) {
		$_SESSION['errors'] = $error;
		redirect('/admin/category.php?mod=' . getCurrMod());
	}
	
	
	if (empty($error)) {
		$FpsDB->save(getCurrMod() . '_sections', array(
			'title' => $title,
			'parent_id' => $in_cat,
			'no_access' => $no_access,
		));
	}
		
	redirect('/admin/category.php?mod=' . getCurrMod());
}


function delete() {	
	global $FpsDB;
	$id = (!empty($_GET['id'])) ? intval($_GET['id']) : 0;
	if ($id < 1) redirect('/admin/category.php?mod=' . getCurrMod());
	
	
	$childrens = $FpsDB->select(getCurrMod() . '_sections', DB_ALL, array('cond' => array('parent_id' => $id)));

	
	if (!count($childrens)) {
		delete_category($id);
	} else {
		foreach ($childrens as $category) {
			delete_category($category['id']);
			delete($category['id']);
		}
		mysql_query("DELETE FROM `" . $FpsDB->getFullTableName(getCurrMod() . '_sections') . "` WHERE `id`='{$id}'");
	}
	redirect('/admin/category.php?mod=' . getCurrMod());
}


function delete_category($id) {
	global $FpsDB;
	$records = $FpsDB->select(getCurrMod(), DB_ALL, array('cond' => array('category_id' => $id)));
	if (count($records) > 0) {
		foreach ($records as $record) {
			mysql_query("DELETE FROM `" . $FpsDB->getFullTableName(getCurrMod()) . "` WHERE `id`='{$record['id']}'");
			
			
			$hlufile = ROOT . '/sys/tmp/hlu_' . getCurrMod() . '/' . $record['id'] . '.dat';
			if (file_exists($hlufile)) {
				$fname = file_get_contents($hlufile);
				_unlink($hlufile);
				_unlink(ROOT . '/sys/tmp/hlu_' . getCurrMod() . '/' . $fname . '.dat');
			}
			
			
			
			if (getCurrMod() == 'foto') {
				if (file_exists(ROOT . '/sys/files/foto/full/' . $record['filename'])) 
					_unlink(ROOT . '/sys/files/foto/full/' . $record['filename']);
				if (file_exists(ROOT . '/sys/files/foto/preview/' . $record['filename'])) 
					_unlink(ROOT . '/sys/files/foto/preview/' . $record['filename']);

					
			} else {
				$attaches = $FpsDB->select(getCurrMod() . '_attaches', DB_ALL, array('cond' => array('entity_id' => $record['id'])));
				if (count($attaches)) {
					foreach ($attaches as $attach) {
						mysql_query("DELETE FROM `" . $FpsDB->getFullTableName(getCurrMod() . '_attaches') 
						. "` WHERE `id`='{$attach['id']}'");
						if (file_exists(ROOT . '/sys/files/' . getCurrMod() . '/' . $attach['filename']))
							_unlink(ROOT . '/sys/files/' . getCurrMod() . '/' . $attach['filename']);
					}
				}
				
				if (getCurrMod() == 'loads') {
					if (file_exists(ROOT . '/sys/files/loads/' . $record['download'])) 
						_unlink(ROOT . '/sys/files/loads/' . $record['download']);
				}
			} 
		}
	}
	mysql_query("DELETE FROM `" . $FpsDB->getFullTableName(getCurrMod() . '_sections') . "` WHERE `id`='{$id}'");
	return true;
}



function on_home($cid = false) {
	global $FpsDB;
	if (getCurrMod() == 'foto') redirect('/admin/category.php?mod=' . getCurrMod());
	
	
	if ($cid === false) {
		$id = (!empty($_GET['id'])) ? intval($_GET['id']) : 0;
		if ($id < 1) redirect('/admin/category.php?mod=' . getCurrMod());
	} else {
		$id = $cid;
	}

	
	$childs = $FpsDB->select(getCurrMod() . '_sections', DB_ALL, array('cond' => array('parent_id' => $id)));
	if (count($childs)) {
		foreach ($childs as $child) {
			on_home($child['id']);
		}
	} 
	
	$FpsDB->save(getCurrMod() . '_sections', array('id' => $id, 'view_on_home' => 1));
	$FpsDB->save(getCurrMod(), array('view_on_home' => 1), array('category_id' => $id));

		
	if ($cid === false) redirect('/admin/category.php?mod=' . getCurrMod());
}



function off_home($cid = false) {
	global $FpsDB;
	if (getCurrMod() == 'foto') redirect('/admin/category.php?mod=' . getCurrMod());
	
	
	if ($cid === false) {
		$id = (!empty($_GET['id'])) ? intval($_GET['id']) : 0;
		if ($id < 1) redirect('/admin/category.php?mod=' . getCurrMod());
	} else {
		$id = $cid;
	}

	
	$childs = $FpsDB->select(getCurrMod() . '_sections', DB_ALL, array('cond' => array('parent_id' => $id)));
	if (count($childs)) {
		foreach ($childs as $child) {
			off_home($child['id']);
		}
	} 
	
	$FpsDB->save(getCurrMod() . '_sections', array('id' => $id, 'view_on_home' => 0));
	$FpsDB->save(getCurrMod(), array('view_on_home' => 0), array('category_id' => $id));

		
	if ($cid === false) redirect('/admin/category.php?mod=' . getCurrMod());
}


include_once 'template/footer.php';
