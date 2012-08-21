<?php
/** 
* repair forums, themes, messages count 
*
*/
include_once '../sys/boot.php';
include_once ROOT . '/admin/inc/adm_boot.php';




$forums = $FpsDB->select('forums', DB_ALL, array());
if (!empty($forums)) {
	foreach ($forums as $forum) {
		$themes = $FpsDB->select('themes', DB_ALL, array('cond' => array('id_forum' => $forum['id'])));
		if (!empty($themes)) {
			foreach ($themes as $theme) {
				$FpsDB->query("UPDATE `" . $FpsDB->getFullTableName('themes') . "` SET 
								`posts` = (SELECT COUNT(*) FROM `" . $FpsDB->getFullTableName('posts') . "` WHERE `id_theme` = '" . $theme['id'] . "')
								WHERE `id` = '" . $theme['id'] . "'");
			}
		}
		$FpsDB->query("UPDATE `" . $FpsDB->getFullTableName('forums') . "` SET 
						`themes` = (SELECT COUNT(*) FROM `" . $FpsDB->getFullTableName('themes') . "` WHERE `id_forum` = '" . $forum['id'] . "')
						, `posts` = (SELECT SUM(posts) FROM `" . $FpsDB->getFullTableName('themes') . "` WHERE `id_forum` = '" . $forum['id'] . "')
						WHERE `id` = '" . $forum['id'] . "'");
	}
}



$pageTitle = 'Пересчет данных форума';
$pageNav = $pageTitle;
$pageNavl = '';

include_once ROOT . '/admin/template/header.php';
?>


<div class="info-str">Все готово.</div>


<?php
include_once 'template/footer.php';
?>