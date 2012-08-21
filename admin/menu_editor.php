<?php
##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      0.8                            ##
## Project:      CMS                            ##
## package       CMS Fapos                      ##
## subpackege    Admin Panel module             ##
## copyright     ©Andrey Brykin 2010-2012       ##
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





$pageTitle = 'Управление дизайном - меню';
$pageNav = $pageTitle;
$pageNavl = '';

$menu_conf_file = ROOT . '/sys/settings/menu.dat';	

	
if (!empty($_GET['ac']) && $_GET['ac'] === 'add') {
	$data = array();
	$data['title'] = (!empty($_POST['ankor'])) ? trim($_POST['ankor']) : '';
	$data['url'] = (!empty($_POST['url'])) ? trim($_POST['url']) : '';
	$data['prefix'] = (!empty($_POST['prefix'])) ? trim($_POST['prefix']) : '';
	$data['sufix'] = (!empty($_POST['sufix'])) ? trim($_POST['sufix']) : '';
	$data['newwin'] = (!empty($_POST['newwin'])) ? trim($_POST['newwin']) : '';
	
	if (!empty($data['title']) && !empty($data['url'])) {
		if (file_exists($menu_conf_file)) {
			$menu = unserialize(file_get_contents($menu_conf_file));
		} else {
			$menu = array();
		}
		$data['id'] = getMenuPointId($menu) + 1;
		$menu[] = $data;
		file_put_contents($menu_conf_file, serialize($menu));
		redirect('/admin/menu_editor.php');
	}
} else if (!empty($_GET['ac']) && $_GET['ac'] === 'edit' && !empty($_GET['id'])) {
	$id = intval($_GET['id']);
	if ($id < 1) redirect('/admin/menu_editor.php');
	
	$data = array();
	$data['id'] = $id;
	$data['title'] = (!empty($_POST['ankor'])) ? trim($_POST['ankor']) : '';
	$data['url'] = (!empty($_POST['url'])) ? trim($_POST['url']) : '';
	$data['prefix'] = (!empty($_POST['prefix'])) ? trim($_POST['prefix']) : '';
	$data['sufix'] = (!empty($_POST['sufix'])) ? trim($_POST['sufix']) : '';
	$data['newwin'] = (!empty($_POST['newwin'])) ? trim($_POST['newwin']) : '';
	
	if (!empty($data['title']) && !empty($data['url']) && !empty($data['id'])) {
		$menu = unserialize(file_get_contents($menu_conf_file));
		$menu = saveMenu($id, $data, $menu);
		file_put_contents($menu_conf_file, serialize($menu));
		redirect('/admin/menu_editor.php');
	}
}
	

function saveMenu($id, $data, $menu) {
	if (!empty($menu) && count($menu) > 0) {
		foreach ($menu as $key => $value) {
			if (!empty($value['id']) && $value['id'] == $id) {
				$menu[$key] = $data;
				if (isset($value['sub'])) $menu[$key]['sub'] = $value['sub'];
				break;
			}
			
			if (!empty($value['sub']) && count($value['sub']) > 0) {
				$menu[$key]['sub'] = saveMenu($id, $data, $value['sub']);
			}
		}
	}
	
	
	return $menu;
}	
	
	
function getMenuPointId($menu) {
	$n = 0;
	if (empty($menu)) return 0;
	foreach ($menu as $k => $v) {
		if (empty($v['id'])) continue;
		if ($n < $v['id']) $n = $v['id'];
		if (!empty($v['sub']) && is_array($v['sub'])) {
			$ns = getMenuPointId($v['sub']);
			if ($n < $ns) $n = $ns;
		}
	}
	
	
	return $n;
}
	
function parseNode($data) {
	$output = array();
	$n = 0;
	
	if (!empty($data) && is_array($data)) {	
		foreach ($data as $key => $value) {
			if (empty($value['url']) || empty($value['title']) || empty($value['id'])) {
				continue;
				$n++;
			}
			
			
			$output[$n] = array(
				'id' => trim($value['id']),
				'url' => trim($value['url']),
				'title' => trim($value['title']),
				'prefix' => (!empty($value['prefix'])) ? trim($value['prefix']) : '',
				'sufix' => (!empty($value['sufix'])) ? trim($value['sufix']) : '',
				'newwin' => (!empty($value['newwin'])) ? trim($value['newwin']) : '',
			);
			
			
			if (!empty($value['sub']) && is_array($value['sub'])) {
				$output[$n]['sub'] = parseNode($value['sub']);
			}
			
			$n++;
		}
	}
	

	return $output;
}


function buildMenu($node) {
	$out = '';
	$n = 0;
	
	if (!empty($node) && is_array($node)) {	
		foreach ($node as $key => $value) {
			if (empty($value['url']) || empty($value['title']) || empty($value['id'])) continue;
			$value['prefix'] = (!empty($value['prefix'])) ? trim($value['prefix']) : '';
			$value['sufix'] = (!empty($value['sufix'])) ? trim($value['sufix']) : '';
			$value['newwin'] = (!empty($value['newwin'])) ? 1 : 0;
			
			
			
			
			$out .= '<li>' . "\n";
			$out .= '<div class="item">' . h($value['title'])
				. '<input type="hidden" name="id" value="' . $value['id'] . '" />' . "\n" 
				. '<input type="hidden" name="url" value="' . h($value['url']) . '" />' . "\n" 
				. '<input type="hidden" name="ankor" value="' . h($value['title']) . '" />' . "\n"
				. '<input type="hidden" name="prefix" value="' . h($value['prefix']) . '" />' . "\n"
				. '<input type="hidden" name="sufix" value="' . h($value['sufix']) . '" />' . "\n" 
				. '<input type="hidden" name="newwin" value="' . h($value['newwin']) . '" />' . "\n" 
				. '<div style="float:right;"><img src="template/img/edit_16x16.png" ' 
				. 'title="Edit" onClick="wiOpen(\'' . md5($value['id']) . '\');" />' . "\n"
				. '<img src="template/img/del.png" title="Delete" '
				. 'onClick="deletePoint(this);" /><div style="clear:both;"></div></div>' . "\n"
				. '</div>' . "\n";
				
			$checked = (!empty($value['newwin'])) ? 'selected="selected"' : '';
			$out .= '<div id="' . md5($value['id']) . '_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
				<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
				</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
				<form action="menu_editor.php?ac=edit&id=' . $value['id'] . '" method="POST">

				<div class="form-item2">
				Текст ссылки:<br />
				<input type="text" name="ankor" value="' . h($value['title']) . '" />
				<div style="clear:both;"></div></div>

				<div class="form-item2">
				Сылка(URL):<br />
				<input type="text" name="url" value="' . h($value['url']) . '" />
				<div style="clear:both;"></div></div>

				<div class="form-item2">
				Префикс:<br />
				<textarea name="prefix">' . h($value['prefix']) . '</textarea>
				<div style="clear:both;"></div></div>

				<div class="form-item2">
				Суфикс:<br />
				<textarea name="sufix">' . h($value['sufix']) . '</textarea>
				<div style="clear:both;"></div></div>

				<div class="form-item2">
				В новом окне:<br />
				<input type="checkbox" value="1" name="newwin" ' . $checked . ' />
				<div style="clear:both;"></div></div>

				<div class="form-item2">
				<input type="submit" name="send" value="Сохранить" />
				<input type="button" onClick="hideWin(\'' . md5($value['id']) . '\')" value="Отмена" />
				<div style="clear:both;"></div></div>
				</form>
				</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
				<div class="xw-footer"></div></div></div></div>
				</div>';


			$out .= '<ul>' . "\n";	
			if (!empty($value['sub']) && is_array($value['sub'])) {
				$out .= buildMenu($value['sub']) . "\n";
			}
			$out .= '<li></li></ul>' . "\n";
			
			$out .= '</li>';
			$n++;
		}
	}
	
	return $out;
}



if (isset($_POST['data']) && is_array($_POST['data'])) {
	$array_menu = parseNode($_POST['data']);

	if (isset($array_menu) && is_array($array_menu)) {
		file_put_contents($menu_conf_file, serialize($array_menu));
	}
	die();
}



$menu = array();
if (file_exists($menu_conf_file)) {
	$menu = unserialize(file_get_contents($menu_conf_file));
}	

include_once ROOT . '/admin/template/header.php';
?>





<div class="fps-win">
	* Создавайте любые пункты меню и сортируйте их простым перетаскиванием.<br />
	* Удаляйте и редактируйте пункты, нажатием кнопочек с правой стороны.</b><br />
	* В конце не забудьте сохранить изменения, нажатием кнопки "Сохранить".<br />
	* Используйте специальную метку для отображения меню на сайте.<br />
	* В итоге пункты меню будут в таком формате 
	<span class="comment">[prefix]&lt;a href="[url]"&gt;[title]&lt;/a&gt;[sufix]</span>.<br />
</div>



<script type="text/javascript">
$(function(){
	$('#sort').sortable({
		items:"li",
		appendTo:"ul",
		placeholder:"plholder",
		update:function(){
			//alert(ui.item);
		},
	});
});

function deletePoint(obj) {
	var node = $(obj).parent("div").parent("div");
	node.remove();
	return true;
}

list = {};
function sortList(id, mlist) {
	var mlist = mlist;
	var points = id.find(">li");
	//var children = id.find(">li>ul");
	
	points.each(function(key){
		var point = points[key];
		point = $(point);
		mlist[key] = {};
		mlist[key]['url'] = point.find("div").find("input[name=url]").val();
		mlist[key]['title'] = point.find("div").find("input[name=ankor]").val();
		mlist[key]['prefix'] = point.find("div").find("input[name=prefix]").val();
		mlist[key]['sufix'] = point.find("div").find("input[name=sufix]").val();
		mlist[key]['newwin'] = point.find("div").find("input[name=newwin]").val();
		mlist[key]['id'] = point.find("div").find("input[name=id]").val();
		
		mlist[key]['sub'] = {};
		mlist[key]['sub'] = sortList(point.find("ul"), mlist[key]['sub']);
	});
	return mlist;
}

function form1() {
	$('#sendButton').attr("disabled","disabled");
	$('#sendButton').css("opacity","0.7");
	list = {};
	list = sortList($('#sort'), list);
	$.post('menu_editor.php', {data:list}, function(){
		$('#sendButton').removeAttr("disabled");
		$('#sendButton').css("opacity","1");
	});
}
</script>
<table class="lines">
	<tr>
		<td>
			Код для вставки: 
			<input style="width:100px; font-size:16px;" type="text" value="{MAINMENU}" onClick="" />
		</td>
	</tr>
	<tr>
		<td>
			<ul id="sort">
			<?php  echo buildMenu($menu); ?>
			</ul>

			<br />
			<br />
			<br />
			<br />
			<input type="submit" value="Сохранить" onClick="form1();" id="sendButton" />
			<input type="submit" value="Добавить" onClick="wiOpen('sec');" />
		</td>
	</tr>
</table>


<div id="sec_dWin" class="fps-win" style="position:absolute;top:200px;left:40%;display:none">
<div class="xw-tl"><div class="xw-tr"><div class="xw-tc xw-tsps"></div>
</div></div><div class="xw-ml"><div class="xw-mr"><div align="center" class="xw-mc">
	<form action="menu_editor.php?ac=add" method="POST">

	<div class="form-item2">
	Текст ссылки:<br />
	<input type="text" name="ankor" value="" />
	<div style="clear:both;"></div></div>

	<div class="form-item2">
	Сылка(URL):<br />
	<input type="text" name="url" />
	<div style="clear:both;"></div></div>

	<div class="form-item2">
	Префикс:<br />
	<textarea name="prefix"></textarea>
	<div style="clear:both;"></div></div>

	<div class="form-item2">
	Суфикс:<br />
	<textarea name="sufix"></textarea>
	<div style="clear:both;"></div></div>

	<div class="form-item2">
	В новом окне:<br />
	<input type="checkbox" value="1" name="newwin" />
	<div style="clear:both;"></div></div>

	<div class="form-item2">
	<input type="submit" name="send" value="Сохранить" />
	<input type="button" onClick="hideWin('sec')" value="Отмена" />
	<div style="clear:both;"></div></div>
	</form>
</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc">
<div class="xw-footer"></div></div></div></div>
</div>

<?php include_once 'template/footer.php';