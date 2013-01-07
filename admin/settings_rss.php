<?php
/*-----------------------------------------------\
| 												 |
|  Author:       Andrey Brykin (Drunya)          |
|  Version:      0.8                             |
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
$pageTitle = 'RSS - Настройки';


$set = Config::read('all');
$TempSet = $set;
if (isset($_POST['send'])) {

	$TempSet['common']['rss_news'] = (!empty($_POST['rss_news'])) ? 1 : 0;
	$TempSet['common']['rss_stat'] = (!empty($_POST['rss_stat'])) ? 1 : 0;
	$TempSet['common']['rss_loads'] = (!empty($_POST['rss_loads'])) ? 1 : 0;
	
	$TempSet['common']['rss_lenght'] = intval($_POST['rss_lenght']);
	$TempSet['common']['rss_cnt'] = intval($_POST['rss_cnt']);




	Config::write($TempSet);
	redirect("/admin/settings_rss.php");
}


$pageNav = $pageTitle;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
?>



<form method="POST" action="settings_rss.php">
<table class="settings-tb">

<tr><td class="left">Максимальная длина анонса RSS:</td>
<td><input type="text" name="rss_lenght" value="<?php echo (!empty($set['common']['rss_lenght'])) ? intval($set['common']['rss_lenght']) : 0; ?>"></td></tr>

<tr><td class="left">Количество материалов в RSS:<br></td>
<td><input type="text" name="rss_cnt" value="<?php echo (!empty($set['common']['rss_cnt'])) ? intval($set['common']['rss_cnt']) : 0; ?>"></td></tr>

<!--#################### FIELDS ###################-->
<tr class="small"><td class="group" colspan="2">Для каких модулей включить RSS</td></tr>
<tr class="small"><td class="left">Новости:<br></td><td>
<input type="checkbox" name="rss_news" value="1" <?php if (!empty($set['common']['rss_news'])) echo 'checked="checked"'; ?> /></td></tr>

<tr class="small"><td class="left">Статьи:<br></td><td>
<input type="checkbox" name="rss_stat" value="1" <?php if (!empty($set['common']['rss_stat'])) echo 'checked="checked"'; ?> /></td></tr>

<tr class="small"><td class="left">Каталог файлов:<br></td><td>
<input type="checkbox" name="rss_loads" value="1" <?php if (!empty($set['common']['rss_loads'])) echo 'checked="checked"'; ?> /></td></tr>






<tr><td colspan="2" align="center"><input type="submit" name="send" value="Сохранить"><br></td></tr>

</table>
</form>


<?php
include_once 'template/footer.php';
?>