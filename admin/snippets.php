<?php
##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      1.2                            ##
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

// Clean snippets Cache
$cache = new Cache;
$cache->prefix = 'block';
$cache->cacheDir = ROOT . '/sys/cache/blocks/';
$cache->clean();



$pageTitle = 'Глобальные блоки. Сниппеты.';
$pageNav = $pageTitle;
if (isset($_GET['a']) && $_GET['a'] == 'ed') {
    $pageNavl = 'Сниппеты &raquo; [редактирование] &raquo; <a href="snippets.php">создание</a>';
} else {
    $pageNavl = 'Сниппеты &raquo; <a href="snippets.php?a=ed">редактирование</a> &raquo; [создание]';
}



if (isset($_GET['a']) && $_GET['a'] == 'ed') {

	$id = (!empty($_GET['id'])) ? intval($_GET['id']) : '';
	if(isset($_POST['save']) && isset($_POST['text_edit'])) {
		$sql = $FpsDB->save('snippets', array(
			'body' => $_POST['text_edit'],
			'id' => $id,
		));
		$_SESSION['mess'] = 'Сниппет успешно сохранен!';
		redirect('/admin/snippets.php?a=ed&id=' . $id);
	}


	if(isset($_GET['delete'])) {
		$sql = $FpsDB->query("DELETE FROM `" . $FpsDB->getFullTableName('snippets') . "` WHERE id='" . $id . "'");
		$_SESSION['mess'] = 'Сниппет успешно удален!';
		redirect('/admin/snippets.php?a=ed');
	}
	if (!empty($id)) {
		$sql = $FpsDB->select('snippets', DB_FIRST, array('cond' => array('id' => $id)));
		if(count($sql) > 0) {
			$content = h($sql[0]['body']);
			$name = h($sql[0]['name']);
		 }
	} else {
		$content = 'Выберите сниппет';
	}

    include_once ROOT . '/admin/template/header.php';
?>	




	<table width="100%">
		<tr>
			<td>
			
			
			
				<table style="z-index:30;" border="0" cellpadding="1" cellspacing="0" width="100%">
				<tr>
				<td class="dis-list" valign="top" rowspan="3"><div id="listener" class="tmplsuDiv">

				<?php
				$sql = $FpsDB->select('snippets', DB_ALL);
					foreach ($sql as $record) {
						echo '<div id="mItem48"  class="tba"><a href="snippets.php?a=ed&id='
						 . ($record['id']) . '">'
						 . h($record['name']) . '</a></div>';
					}
				?>

				</div>
					</td>
					<td colspan="2" valign="top">
						<div style="margin-left:15px;" class="fps-win">
						Снипеты позволяют создать блоки php кода и подключать их в любом месте сайта, прямо в шаблонах.<br />
						Вызвать снипет из шаблона можно так <strong>{[ИМЯ СНИППЕТА]}</strong><br />
						После того, как Вы добавите метку в шаблон, она будет заменена на результат выполнения кода сниппета.<br />
						Тут приведен список, уже созданных, сниппетов. Вы можете их просматривать и редактировать.<br />
						Для то, что бы создавать и редактировать сниппеты, желательно, обладать, хотя бы, базовыми знаниями PHP
						</div>
					</td>
				</tr>
				</table>
			</td>
			
		</tr>
		<tr>
			<td>
				<form action="<?php echo $_SERVER['REQUEST_URI']?>" method="post">
				<table class="settings-tb">
				
				<?php if (isset($_SESSION['mess'])) : ?>
					<tr><td align="center" colspan="2" style="color:green; font-size:11px; font-weight:none; font-family: Tahoma, Arial, serif;"><b><?php echo $_SESSION['mess'] ?></b></td></tr>
				<?php unset($_SESSION['mess']); endif; ?>

					<tr>
						<td class="left"><b>Имя сниппета:</b></td>
						<td  class="right">
						<input disabled="disabled" name="my_title" type="text" style="" value="<?php echo (!empty($name)) ? $name : '';?>">
						<?php if(isset($id) && $id != null) : ?> 
							<a href="snippets.php?a=ed&id=<?php echo $id ?>&delete=y" onClick="return confirm('Are you sure?')">
							<img src="template/img/del.png" title="Удалить">
							</a>
						<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td class="left"><b>Код сниппета</b></td>
						<td class="right">
						<textarea name="text_edit" style="width:99%" rows="25"><?php echo $content;?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input name="save" type="submit" value="Сохранить" /></td>
					</tr> 

				</table>
				</form>
			</td>

		</tr>
	</table>
	

<?php

} else {
	
	 
	if (isset($_POST['send'])) {
		if (empty($_POST['my_title']) || mb_strlen($_POST['my_text']) < 3 || empty($_POST['my_title'])) $_SESSION['mess'] = 'Заполните все поля';
		if (empty($_SESSION['mess'])) {
			$countchank = $FpsDB->select('snippets', DB_COUNT, array('cond' => array('name' => $_POST['my_title'])));
			if ($countchank == 0) {
				$sql = $FpsDB->save('snippets', array(
					'name' => $_POST['my_title'],
					'body' => $_POST['my_text'],
				));
				
				$_SESSION['mess'] = "Сниппет создан! Применяйте его так: {[" . h($_POST['my_title']) . "]}";
				redirect('/admin/snippets.php?a=ed&id=' . mysql_insert_id());
			} else {
				$_SESSION['mess'] = 'Такой сниппет уже есть! Измените имя блока.';
			}
		}
	}
    include_once ROOT . '/admin/template/header.php';
	?>



	<form action="snippets.php" method="post">
	<table width="100%">
		<tr>
			<td>
				<table width="100%">
					<tr>
						<td>
							<div class="fps-win">
							Снипеты позволяют создать блоки php кода и подключать их в любом месте сайта, прямо в шаблонах.<br />
							Вызвать снипет из шаблона можно так <strong>{[ИМЯ СНИППЕТА]}</strong><br />
							После того, как Вы добавите метку в шаблон, она будет заменена на результат выполнения кода сниппета.<br />
							На странице редактирования приведен список, уже созданных, сниппетов. Вы можете их просматривать и редактировать.<br />
							Для то, что бы создавать и редактировать сниппеты, желательно, обладать, хотя бы, базовыми знаниями PHP
							</div>
						</td>
					</tr>
				</table>
			</td>
			
		</tr>
		<tr>
			<td>
			
				<table class="settings-tb">
				<?php if (isset($_SESSION['mess'])) : ?>
					<tr><td colspan="2" align="center" style="color:green; font-size:11px; font-weight:none; font-family: Tahoma, Arial, serif;"><b><?php echo $_SESSION['mess'] ?></b></td></tr>
				<?php unset($_SESSION['mess']); endif; ?>

				  <tr>
					<td class="left">Имя сниппета:</td>
					<td>
					<input name="my_title" type="text" style="" value="<?php if (!empty($_POST['my_title'])) echo h($_POST['my_title']) ?>" /></td>
				  </tr>
				  <tr>
					<td class="left">Код сниппета</td>
					<td>
					<div align="center" class="dis-textarea">
					<textarea name="my_text" style="width:99%" rows="25" ><?php if (!empty($_POST['my_text'])) echo h($_POST['my_text']) ?></textarea>
					</div></td>
				  </tr>
				  <tr>
					<td colspan="2"  align="center"><input name="send" type="submit" value="Сохранить" /></td>
				  </tr> 
				</table>
			</td>

		</tr>
	</table>
	</form>


<?php } ?> 

<?php
include_once 'template/footer.php';
?>