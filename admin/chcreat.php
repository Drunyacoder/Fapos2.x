<?php
/*-----------------------------------------------\
| 												 |
| @Author:       Andrey Brykin (Drunya)          |
| @Email:        drunyacoder@gmail.com           |
| @Site:         http://fapos.net                |
| @Version:      1.0                             |
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


// Clean chancks Cache
$cache = new Cache;
$cache->prefix = 'block';
$cache->cacheDir = ROOT . '/sys/cache/blocks/';
$cache->clean();



$pageTitle = 'Глобальные блоки. Чанки.';
$pageNav = $pageTitle;
$pageNavl = '';



$error = '';
if (isset($_GET['a']) && $_GET['a'] == 'ed') {
    $pageNavl = 'Чанки &raquo; [редактирование] &raquo; <a href="chcreat.php">создание</a>';
	$id = (!empty($_GET['id'])) ? intval($_GET['id']) : '';
	if (isset($_POST['save'])) {
		if (empty($_POST['text_edit'])) $error = '<li>Текст пуст</li>';
		if (empty($_POST['my_title'])) $error = '<li>Заголовок пуст</li>';
		
		if(empty($error)) {
			$FpsDB->save('chank', array(
				'chanck' => $_POST['text_edit'],
				'id' => $id,
			));
			$mess = 'Чанк успешно сохранен!';
		}
	}
	if(!$id) {
		$content = 'Change the chanck';
		$name = '';
	}

	if(isset($_GET['delete'])) {
		$sql = $FpsDB->query ("DELETE FROM `" . $FpsDB->getFullTableName('chank') . "` WHERE id='".$id."';");
		$mess = 'Чанк успешно удален!';
		redirect('/admin/chcreat.php?a=ed');
	}
	$sql = $FpsDB->select('chank', DB_FIRST, array('cond' => array('id' => $id)));
	if(count($sql) > 0) {
		$content = h($sql[0]['chanck']);
		$name = h($sql[0]['name']);
	}

    include_once ROOT . '/admin/template/header.php';
	?>
	<!--
	<script language="javascript" type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
	<script language="javascript" type="text/javascript">
	tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	theme_advanced_buttons1_add : "fontselect,fontsizeselect",
	theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom,forecolor,backcolor",
	theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace",
	theme_advanced_buttons3_add_before : "tablecontrols,separator",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	external_link_list_url : "example_data/example_link_list.js",
	external_image_list_url : "example_data/example_image_list.js",
	flash_external_list_url : "example_data/example_flash_list.js",
	convert_urls : false,
	relative_urls : false,
	remove_script_host : false,
	force_br_newlines : true,
	language : "en"
	});
	</script>
	-->

	
	
	
	
	
	
	
	<table width="100%">
		<tr>
			<td valign="top">
				<table width="30%">
					<tr>
						<td valign="top">
							<div class="tmplsuDiv" id="tmplsuDiv" style="min-height:100px;width:100%;overflow:auto;">

							<?php
							$sql = $FpsDB->select('chank', DB_ALL);
								foreach ($sql as $record) {
									print '<div class="tba"><a href="chcreat.php?a=ed&id='.$record['id'].'">'.$record['name'].'</a></div>';
								}
							?>
							</div>
						</td>
					</tr>
				</table>
			
			</td>
		</tr>
		<tr>
			<td width="100%">
				<form action="<?php print $_SERVER['REQUEST_URI']?>" method="post">
				
				<?php if (isset($mess)) : ?>
					<tr><td colspan='2' align="center" style="color:green"><b><?php echo $mess ?></b></td></tr>
				<?php endif; ?>
				<?php if (isset($error)) : ?>
					<tr><td colspan='2' align="center" style="color:red"><ul><?php echo $error ?></ul></td></tr>
				<?php endif; ?>
				
				<table width="100%">
					<tr>
						<td valign="top">
						<b>Имя чанка:</b>
						<input name="my_title" style="width:90%; float:right;" type="text" value="<?php print $name; ?>">
						
						<?php if(isset($id) && $id != null) : ?> 
							<a href="chcreat.php?a=ed&id=<?php echo $id ?>&delete=y" onClick="return confirm('Are you sure?')">
							<img src="template/img/del.png" title="Удалить">
							</a>
						<?php endif; ?>
						
						</td>
					</tr>
					<tr>
						<td valign="top">
						<div align="center" class="dis-textarea">
						<textarea class="global_block" style="width:99%; margin: 0px auto;" name="text_edit" rows="20"><?php print $content;?></textarea>
						</div>
						</td>
					</tr>
					<tr>
						<td align="center"><input name="save" type="submit" value="Сохранить" /></td>
					</tr> 
					<tr>
						<td colspan="2" align="center">
							<span class="comment">Чанки позволяют создать блоки html кода и подключать их в любом месте сайта, прямо в шаблонах.<br /> 
							Вызвать чанк из шаблона можно так <strong>{GLOBAL_ИМЯ ЧАНКА}</strong><br />
							После того, как Вы добавите метку в шаблон, она будет заменена на HTML код чанка.<br /> 
							Вверху приведен список, уже созданных, чанков. Вы можете их просматривать и редактировать.</span>
						</td>
					</tr>
				</table>
				</form>




<?php




} else {
    $pageNavl = 'Чанки &raquo; <a href="chcreat.php?a=ed">редактирование</a> &raquo; [создание]';
    include_once ROOT . '/admin/template/header.php';
	if (isset($_POST['send'])) {
		if (empty($_POST['my_title'])) $error .= '<li>Заголовок пуст</li>';
		if (empty($_POST['my_text'])) $error .= '<li>Текст чанка пуст</li>';
		
		$countchank = $FpsDB->select('chank', DB_COUNT, array('cond' => array('name' => $_POST['my_title']))); 
		if (count($countchank) > 1) {
			$error .= '<li>Чанк с таким именем уже есть</li>';
		}
		if (empty($error)) {
			$sql = $FpsDB->save('chank', array(
				'name' => $_POST['my_title'],
				'chanck' => $_POST['my_text'],
			));
			$mess = "Глобальный блок создан! Применяйте его так: {GLOBAL_" . h($_POST['my_title']) . "}";
		}
	}
	?>
	<!--
	<script language="javascript" type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
	<script language="javascript" type="text/javascript">
	tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	theme_advanced_buttons1_add : "fontselect,fontsizeselect",
	theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom,forecolor,backcolor",
	theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace",
	theme_advanced_buttons3_add_before : "tablecontrols,separator",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	external_link_list_url : "example_data/example_link_list.js",
	external_image_list_url : "example_data/example_image_list.js",
	flash_external_list_url : "example_data/example_flash_list.js",
	language : "en"
	});
	</script>
	-->



	<form action="chcreat.php" method="post">
	<table width="100%">
	<?php if (isset($error)) : ?>
		<tr><td colspan='2' align="center" style="color:red; font-size:11px; font-weight:none; font-family: Tahoma, Arial, serif;"><ul><?php echo $error ?></ul></td></tr>
	<?php endif; ?>
	<?php if (isset($mess)) : ?>
		<tr><td colspan='2' align="center" style="color:green; font-size:11px; font-weight:none; font-family: Tahoma, Arial, serif;"><b><?php echo $mess ?></b></td></tr>
	<?php endif; ?>
	  <tr>
		<td valign="top"><span style="padding: 3px; float: left; color: rgb(112, 112, 112); font-weight: bold;">Имя чанка:</span>
		<input name="my_title" type="text" style="width:90%;float:right;" /></td>
	  </tr>
	  <tr>
		<td valign="top" >
		<div align="center" class="dis-textarea">
		<textarea class="global_block" name="my_text" style="width:99%" rows="20" >&nbsp;</textarea>
		</div>
		</td>
	  </tr>
	  <tr>
		<td  align="center"><input name="send" type="submit" value="Сохранить" /></td>
	  </tr> 
		<tr>
			<td colspan="2" align="center">
				<span class="comment">Чанки позволяют создать блоки html кода и подключать их в любом месте сайта, прямо в шаблонах.<br /> 
				Вызвать чанк из шаблона можно так <strong>{GLOBAL_ИМЯ ЧАНКА}</strong><br />
				После того, как Вы добавите метку в шаблон, она будет заменена на HTML код чанка.<br /> 
				Вверху приведен список, уже созданных, чанков. Вы можете их просматривать и редактировать.</span>
			</td>
		</tr>
	</table>
	</form>





<?php } ?>

<?php
include_once 'template/footer.php';
?>

