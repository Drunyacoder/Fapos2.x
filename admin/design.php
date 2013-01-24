<?php
/*-----------------------------------------------\
| 												 |
|  @Author:       Andrey Brykin (Drunya)         |
|  @Email:        drunyacoder@gmail.com          |
|  @Site:         http://fapos.net               |
|  @Version:      1.5.4                          |
|  @Project:      CMS                            |
|  @package       CMS Fapos                      |
|  @subpackege    Template redactor              |
|  @copyright     ©Andrey Brykin 2010-2013       |
|  @last mod.     2013/01/20                     |
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


$pageTitle = __('Design - templates');
$pageNav = $pageTitle;
$pageNavl = '';


$allowedFiles = array(
    'news' => array(
        'addform',
        'main',
        'editform',
        'material',
        'list',
    ),
    'stat' => array(
        'addform',
        'main',
        'editform',
        'material',
        'list',
    ),
    'loads' => array(
        'addform',
        'main',
        'editform',
        'material',
        'list',
    ),
    'foto' => array(
        'addform',
        'main',
        'editform',
        'material',
        'list',
    ),
    'chat' => array(
        'addform',
        'main',
        'list',
    ),
    'search' => array(
        'search_form',
        'search_row',
    ),
    'users' => array(
        'addnewuserform',
        'main',
        'edituserform',
        'loginform',
        'baned',
        'showuserinfo',
    ),
    'forum' => array(
        'addthemeform',
        'editthemeform',
        'main',
        'replyform',
        'editpostform',
        'get_stat',
        'posts_list',
        'themes_list',
    ),
    'default' => array(
        'main',
    ),
);



$entities = array(
    'addform' 			=> __('Add form'),
    'main' 				=> __('Layout'),
    'editform' 			=> __('Edit form'),
    'material' 			=> __('Material view'),
    'list' 				=> __('List of materials'),
    'addnewuserform' 	=> __('Add form'),
    'edituserform' 		=> __('Edit form'),
    'loginform' 		=> __('Login form'),
    'baned' 			=> __('Ban page'),
    'showuserinfo' 		=> __('Profile info'),
    'style' 			=> __('Style(CSS)'),
    'addthemeform' 		=> __('Add theme form'),
    'editthemeform' 	=> __('Edit theme form'),
    'replyform' 		=> __('Reply form'),
    'editpostform' 		=> __('Edit post form'),
    'get_stat' 			=> __('Statistic'),
    'posts_list' 		=> __('Posts list'),
    'themes_list' 		=> __('Themes list'),
    'search_form' 		=> __('Search form'),
    'search_row' 		=> __('Search results'),
);

if (empty($_GET['m']) || !is_string($_GET['m'])) $_GET['m'] = 'default';
if (empty($_GET['t']) || !is_string($_GET['t'])) $_GET['t'] = 'main';
if (empty($_GET['d']) || !is_string($_GET['d'])) $_GET['d'] = 'default';



$module = trim($_GET['m']);
if (!array_key_exists($_GET['m'], $allowedFiles)) {
	$modInstaller = new FpsModuleInstaller();
	$extentionParams = $modInstaller->getTemplateParts($module);
	if (!empty($extentionParams)) {
		$allowedFiles[$module] = $extentionParams;
	}
}


$module = (array_key_exists($_GET['m'], $allowedFiles)) ? $_GET['m'] : 'default';
$filename = (in_array($_GET['t'], $allowedFiles[$module])) ? $_GET['t'] : 'main';
$type = (in_array($_GET['d'], array('css', 'default'))) ? $_GET['d'] : 'default';
if ('css' == $type) $file = 'style';



if(isset($_POST['send']) && isset($_POST['templ'])) {
	if ($type == 'css') {
		$template_file = ROOT . '/template/' . Config::read('template') . '/css/style.css';
		if (!is_file($template_file . '.stand')) {
			copy($template_file, $template_file . '.stand');
		}
		$file = fopen($template_file, 'w+');


	} else {
		$template_file = ROOT . '/template/' . Config::read('template') . '/html/' . $module . '/' . $filename . '.html';
		if (!is_file($template_file . '.stand')) {
			copy($template_file, $template_file . '.stand');
		}
		$file = fopen($template_file, 'w+');
	}
	if(fputs($file, $_POST['templ'])) {
		$mess = __('Templates is saved');
	} else {
		$mess = __('Templates is not saved');
	}
	fclose($file);
}
if (!empty($_SESSION['message'])) {
    $mess = $_SESSION['message'];
    unset($_SESSION['message']);
}





if ($_GET['d'] == 'css') {
    $path = ROOT .'/template/' . Config::read('template') . '/css/style.css';
} else {
    clearstatcache();
    $path = ROOT .'/template/' . Config::read('template') . '/html/' . $module . '/' . $filename . '.html';
    if (!file_exists($path)) {
        $path = ROOT .'/template/' . Config::read('template') . '/html/default/' . $filename . '.html';
        if (!file_exists($path)) {
            $_SESSION['message'] = __('Requested file is not found');
            redirect('/admin/design.php');
        }
    }
}
$template = file_get_contents($path);


include_once ROOT . '/admin/template/header.php';
echo '<form action="' . $_SERVER['REQUEST_URI'] . '" method="POST">';

?>
<div class="disign-page">






<table style="z-index:30;" border="0" cellpadding="1" cellspacing="0" width="100%">
<tr>
<td class="dis-list" valign="top" rowspan="3"><div id="listener" class="tmplsuDiv">

<?php foreach ($allowedFiles as $mod => $files):
    $title = ('default' == $mod) ? __('Default') : Config::read('title', $mod);
    if (!empty($title)):
?>

    <div class="tbn"><?php echo $title; ?></div>
        <?php foreach ($files as $file): ?>
        <div class="tba1">
        <a href="design.php?d=default&t=<?php echo $file; ?>&m=<?php echo $mod; ?>"><?php echo $entities[$file]; ?></a>
        </div>
        <?php endforeach; ?>
        <?php if ('default' == $mod): ?>
            <div class="tba1">
            <a href="design.php?d=css&t=style"><?php echo __('Style(CSS)') ?></a>
            </div>
        <?php endif; ?>
        <br />
    <?php endif; ?>
<?php endforeach; ?>

</div>
</td>
<td colspan="2" valign="top"><div style="float:right">
<div>
<a href="set_default_dis.php" onClick="return confirm('Система востановит стандартный шаблон. Вы уверенны?')"><?php echo __('Return to default template') ?></a>
&nbsp;|&nbsp;
<a href="backup_dis.php" onClick="return confirm('Система сделает резервную копию шаблона. Вы уверенны?')"><?php echo __('Save current state of template') ?></a>
</div>
      </div></td>
</tr>
<tr><td colspan="2" align="right"></td></tr>

<tr>
<td valign="bottom"><div id="eMessage" style="padding:3px;" class="myBdTop myBdRight myBdBottom myBdLeft" align="center">
<?php
if(isset($mess) && $mess != NULL) {
	echo '<b>'.$mess.'</b>';
} else {
	echo __('Change template and save');
}
?>

</div></td></tr>
</table>
<script type="text/javascript">
$('#listener').hover(function(){
    $(this).stop().animate({height:'200px', overflow:'auto'}, 400);
    $(this).css('overflow','auto');
},
function(){
    $(this).stop().animate({height:'90px', opacity:'0.3'}, 300, function(){
        $(this).css('opacity','1');
    });
    $(this).css('overflow','auto');
});
</script>








<table class="settings-tb">
	<tr>
		<td>
			<div align="center"  class="dis-textarea" >
			<textarea title="Код шаблона" style="width:99%;height:380px;" wrap="off" name="templ" id="tmpl"><?php print htmlspecialchars($template); ?></textarea>
			</div>
		</td>
	</tr>
	<tr>
		<td align="center"><input type="submit" name="send" value="<?php echo __('Save') ?>"></td>
	</tr>
</table>
</form>




<!--
<table class="lines">
	<tr>
		<td>
			<div>
			<ul class="uz">
				<li><div class="global-marks">{CONTENT}</div> - Основной контент страницы</li>
				<li><div class="global-marks">{TITLE}</div> - Заголовок страницы</li>
				<li><div class="global-marks">{DESCRIPTION}</div> - Содержание Мета-тега description</li>
				<li><div class="global-marks">{FPS_WDAY}</div> - День кратко</li>
				<li><div class="global-marks">{FPS_DATE}</div> - Дата</li>
				<li><div class="global-marks">{FPS_TIME}</div> - Время</li>
				<li><div class="global-marks">{HEADMENU}</div> - Верхнее меню</li>
				<li><div class="global-marks">{FPS_USER_NAME}</div> - Ник текущего пользователя (Для не авторизованного - Гость)</li>
				<li><div class="global-marks">{FPS_USER_GROUP}</div> - Группа текущего пользователя (Для не авторизованного - Гости)</li>
				<li><div class="global-marks">{CATEGORIES}</div> - Список категорий раздела</li>
				<li><div class="global-marks">{COUNTER}</div> - Встроенный счетчик посещаемости CMS Fapos</li>
				<li><div class="global-marks">{FPS_YEAR}</div> - Год</li>
				<li><div class="global-marks">{POWERED_BY}</div> - CMS Fapos</li>
				<li><div class="global-marks">{COMMENTS}</div> - Комментарии к материалу и форма добавления комментариев <b>(если предусмотренно)</b></li>
				<li><div class="global-marks">{PERSONAL_PAGE_LINK}</div> - URL на свою персональную страницу или на страницу регистрации, если не авторизован</li>
			</ul>
			</div>
		</td>
	</tr>
</table>
-->

</div>

<?php
if (!empty($_SESSION['info_message'])):
?>
<script type="text/javascript">showHelpWin('<?php echo h($_SESSION['info_message']) ?>', '<?php echo __('Message') ?>');</script>
<?php
	unset($_SESSION['info_message']);
endif;
?>

<?php include_once 'template/footer.php'; ?>

