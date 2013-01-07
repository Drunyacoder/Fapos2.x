<?php
##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      1.4.7.2                        ##
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
$pageTitle = 'Системные настройки';

function getImgPath($template) {
	$path = '../template/' . $template . '/screenshot.png';
	if (file_exists($path)) {
		return $path;
	}
	return '../sys/img/noimage.jpg';
}


$set = Config::read('all');
$TempSet = $set;

if (isset($_POST['send'])) {

	$TempSet['template']   			  = $_POST['template'];
	$TempSet['title']   			  = $_POST['title'];
	$TempSet['site_title']   		  = $_POST['site_title'];
	$TempSet['meta_keywords']   	  = $_POST['meta_keywords'];
	$TempSet['meta_description']      = $_POST['meta_description'];
	$TempSet['cms']         		  = $_SERVER['DOCUMENT_ROOT'];
	$TempSet['cookie_time'] 		  = $_POST['cookie_time'];
	$TempSet['start_mod']   		  = $_POST['start_mod'];
	$TempSet['open_reg']       		  = $_POST['open_reg'];
	$TempSet['email_activate']        = (!empty($_POST['email_activate'])) ? 1 : 0;
	$TempSet['debug_mode']            = $_POST['debug_mode'];
	$TempSet['max_file_size'] 		  = $_POST['max_file_size'];
	$TempSet['min_password_lenght']   = $_POST['min_password_lenght'];
	$TempSet['admin_email'] 		  = $_POST['admin_email'];
	$TempSet['redirect_delay'] 		  = $_POST['redirect_delay'];
	$TempSet['time_on_line'] 		  = $_POST['time_on_line'];
	$TempSet['cache'] 				  = $_POST['cache'];
	$TempSet['cache_querys'] 		  = $_POST['cache_querys'];
	$TempSet['redirect'] 			  = $_POST['redirect'];
	$TempSet['cnt_latest_on_home']	  = $_POST['cnt_latest_on_home'];
	$TempSet['news_on_home']       	  = (!empty($_POST['latest_on_home'])) ? 1 : 0;
	$TempSet['use_additional_fields'] = (!empty($_POST['use_additional_fields'])) ? 1 : 0;
	$TempSet['allow_html'] 			  = (!empty($_POST['allow_html'])) ? 1 : 0;
	$TempSet['allow_smiles'] 		  = (!empty($_POST['allow_smiles'])) ? 1 : 0;
	
	/* LATEST ON HOME PAGE */
	$TempSet['latest_on_home'] = array();
	if (isset($_POST['latest_on_home']['news']))      	$TempSet['latest_on_home'][] = 'news';
	if (isset($_POST['latest_on_home']['loads'])) 		$TempSet['latest_on_home'][] = 'loads';
	if (isset($_POST['latest_on_home']['stat']))     	$TempSet['latest_on_home'][] = 'stat';
	
	$TempSet['announce_lenght']        = (!empty($_POST['announce_lenght'])) ? (int)$_POST['announce_lenght'] : 0;
	
	
	//save data
	Config::write($TempSet);
	//clean Cache
	$Cache = new Cache; 
	$Cache->clean();
	redirect("/admin/settings_sys.php");
}

$sourse = glob(ROOT . '/template/*', GLOB_ONLYDIR);
if (!empty($sourse) && is_array($sourse)) {
	$templates = array();
	foreach ($sourse as $dir) {
		if (preg_match('#.*/(\w+)$#', $dir, $match)) {
			$templates[] = $match[1];
		}
	}
}



$pageNav = $pageTitle;
$pageNavl = '';
include_once ROOT . '/admin/template/header.php';
?>


<form method="POST" action="settings_sys.php">
<table class="settings-tb">

<tr><td class="left">Шаблон дизайна:<br></td>
	<td>
	<select name="template">
	<?php if (!empty($templates)): ?>
		<?php foreach ($templates as $value): ?>
			<?php if (!empty($set['template']) && $value == $set['template']): ?>
		<option value="<?php echo $value ?>" onClick="showScreenshot('<?php echo getImgPath($value) ?>');" selected="selected"><?php echo ucfirst($value) ?></option>
			<?php else: ?>
		<option onClick="showScreenshot('<?php echo getImgPath($value) ?>');" value="<?php echo $value ?>"><?php echo ucfirst($value) ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
	</select>
	</td>
	<td>
	<img id="screenshot" style="border:1px solid #A3BAE9;" width="200px" height="200px" src="<?php echo getImgPath($set['template']) ?>" />
	</td>
</tr>



<tr><td class="left">Название сайта:<br>
<span class="comment">можно использовать в шаблонах как {SITE_TITLE}</span><br></td><td colspan="2">
<input type="text" name="site_title" value="<?php echo (!empty($set['site_title'])) ? h($set['site_title']) : ''; ?>"><br></td></tr>

<tr><td class="left">Заголовок сайта:<br>
<span class="comment">можно использовать в шаблонах как {TITLE}</span><br></td><td colspan="2">
<input type="text" name="title" value="<?php echo (!empty($set['title'])) ? h($set['title']) : ''; ?>"><br></td></tr>

<tr><td class="left">Ключевые слова сайта:<br>
<span class="comment">можно использовать в шаблонах как {META_KEYWORDS}</span><br></td><td colspan="2">
<input type="text" name="meta_keywords" value="<?php echo (!empty($set['meta_keywords'])) ? h($set['meta_keywords']) : ''; ?>"><br></td></tr>

<tr><td class="left">Описание сайта:<br>
<span class="comment">можно использовать в шаблонах как {META_DESCRIPTION}</span><br></td><td colspan="2">
<input type="text" name="meta_description" value="<?php echo (!empty($set['meta_description'])) ? h($set['meta_description']) : ''; ?>"><br></td></tr>




<tr><td class="left">Время "жизни" cookies в днях:<br>
<span class="comment">в cookies сохраняются логин и пароль пользователя,<br>
 если была выбрана опция "Автоматически входить при каждом посещении"</span><br></td><td colspan="2">
<input type="text" name="cookie_time" value="<?php echo $set['cookie_time'] ?>"><br></td></tr>

<tr><td class="left">Автоматическая переадресация:<br>
<span class="comment">используйте эту опцию что бы перевести пользователя с <br>
главной страницы, например, на форум или каталог файлов,<br>
или на другой сайт</span><br></td><td colspan="2">
<input type="text" name="redirect" value="<?php echo $set['redirect'] ?>"><br></td></tr>


<tr><td class="left">Точка входа:<br>
<span class="comment">Это что-то похожее на переадресацию, но самой переадресации
не происходит. Другими словами сдесь Вы вводите адрес точки входа
и страница по этому адресу будет являться главной страницей сайта.
Вводите сюда только рабочие ссылки и только в пределах сайта. Пример "<b>news/view/1</b>"</span><br></td><td colspan="2">
<input type="text" name="start_mod" value="<?php echo $set['start_mod'] ?>"><br></td></tr>



<tr><td class="left">Максимальный размер файла вложения:<br>
<span class="comment">которые пользователи смогут выгружать на сайте<br />
Используется во всех модулях где нет собственой подобной настройки</span><br></td><td colspan="2">
<input type="text" name="max_file_size" value="<?php echo $set['max_file_size'] ?>">&nbsp;<span class="comment">Байт</span><br></td></tr>

<tr><td class="left">Минимальная длина пароля пользователя:<br></td><td colspan="2">
<input type="text" name="min_password_lenght" value="<?php echo $set['min_password_lenght'] ?>">&nbsp;<span class="comment">Символов</span><br></td></tr>

<tr><td class="left">Адрес электронной почты администратора:<br>
<span class="comment">этот e-mail будет указан в поле FROM писем, которое один пользователь напишет <br>
другому; этот же e-mail будет указан в письмах с просьбой активировать учетную<br>
 запись или пароль (в случае его утери)</span><br></td><td colspan="2">
<input type="text" name="admin_email" value="<?php echo $set['admin_email'] ?>"><br></td></tr>

<tr><td class="left">Задержка перед редиректом:<br>
<span class="comment">когда пользователь выполняет какое-то действие (например, добавляет сообщение)<br>
 ему выдается сообщение, что "Ваше сообщение было успешно добавлено" и делается<br>
редирект на нужную страницу</span><br></td><td colspan="2">
<input type="text" name="redirect_delay" value="<?php echo $set['redirect_delay'] ?>">&nbsp;<span class="comment">Секунд</span><br></td></tr>

<tr><td class="left">Время , в течение которого считается, что пользователь "on-line":<br></td><td colspan="2">
<input type="text" name="time_on_line" value="<?php echo $set['time_on_line'] ?>">&nbsp;<span class="comment">Минут</span><br></td></tr>

<tr><td class="left">Режим регистрации:<br>
<span class="comment">Определяет разрешена ли регистрация у Вас на сайте</span><br></td><td colspan="2">
<select name="open_reg" width="30">
<?php if ($set['open_reg'] == 1): ?>
	<option selected value="1">Разрешена</option><option value="0">Запрещена</option>
<?php else: ?>
	<option selected value="0">Запрещена</option><option value="1">Разрешена</option>
<?php endif; ?>
</select><br></td></tr>

<tr><td class="left">Требуется ли активация аккаунта по E-mail:<br></td><td colspan="2">
<input type="checkbox" name="email_activate" value="1"<?php if (!empty($set['email_activate'])) echo ' checked="checked"' ?> /><br></td></tr>

<tr><td class="left">Вывод ошибок:<br></td><td colspan="2">
<select name="debug_mode">
<?php if ($set['debug_mode'] == 1): ?> 
	<option selected value="1">Выводить</option><option value="0">НЕ Выводить</option><br>
<?php else: ?>
	<option selected value="0">НЕ Выводить</option><option value="1">Выводить</option><br>
<?php endif; ?>
</select><br></td></tr>



<!-- LATEST MATERIALS ON HOME PAGE -->
<tr><td class="left">Какие из последних материалов выводить на главной:<br></td><td colspan="2">
<input type="checkbox" name="latest_on_home[news]" value="0" <?php 
echo (!empty($set['latest_on_home']) && in_array('news', $set['latest_on_home'])) ? 'checked="checked"' : '' ?>>Новости<br>
<input type="checkbox" name="latest_on_home[loads]" value="1" <?php 
echo (!empty($set['latest_on_home']) && in_array('loads', $set['latest_on_home'])) ? 'checked="checked"' : '' ?>>Загрузки<br>
<input type="checkbox" name="latest_on_home[stat]" value="2" <?php 
echo (!empty($set['latest_on_home']) && in_array('stat', $set['latest_on_home'])) ? 'checked="checked"' : '' ?>>Статьи<br>
</td></tr>


<tr><td class="left">Кол-во материалов на главной:<br></td><td colspan="2">
<input type="text" name="cnt_latest_on_home" value="<?php echo (!empty($set['cnt_latest_on_home'])) ? $set['cnt_latest_on_home'] : ''; ?>"><br></td></tr>


<tr><td class="left">Размер анонса на главной:<br></td><td colspan="2">
<input type="text" name="announce_lenght" value="<?php echo (!empty($set['announce_lenght'])) ? $set['announce_lenght'] : ''; ?>">&nbsp;<span class="comment">Символов</span><br></td></tr>

<!-- END LATEST MATERIALS ON HOME PAGE -->



<tr><td class="left">Кэш:<br>
<span class="comment">(Кешировать ли содержимое сайта? Если кэш включен сайт будет работать быстрее
при большой нагрузке, но при маленькой его лучше выключить.)</span></td>
<td colspan="2">
<select name="cache">
<?php if (!empty($set['cache']) && $set['cache'] == 1): ?> 
	<option selected value="1">Включено</option><option value="0">Выключено</option><br>
<?php else: ?>
	<option selected value="0">Выключено</option><option value="1">Включено</option><br>
<?php endif; ?>
</select><br>
</td></tr>



<tr><td class="left">Кэш SQl запросов:<br>
<span class="comment">(Кешировать ли результаты SQL запросов? Если кэш включен сайт будет работать быстрее
при большой нагрузке, но при маленькой его лучше выключить.)</span></td>
<td colspan="2">
<select name="cache_querys">
<?php if (!empty($set['cache_querys']) && $set['cache_querys'] == 1): ?> 
	<option selected value="1">Включено</option><option value="0">Выключено</option><br>
<?php else: ?>
	<option selected value="0">Выключено</option><option value="1">Включено</option><br>
<?php endif; ?>
</select><br>
</td></tr>


<tr><td class="left">Использовать ли дополнительные поля на сайте:<br>
<span class="comment">Замедлит работу сайта. 
<br />Используйте только если знаете что это и как этим пользоваться.</span></td><td colspan="2">
<input type="checkbox" name="use_additional_fields" value="1"<?php if (!empty($set['use_additional_fields'])) echo ' checked="checked"' ?> /><br></td></tr>


<tr><td class="left">Разрешить использование HTML в сообщениях:<br>
<span class="comment">Таит угрозу. 
<br />Включая эту возможность, настройте ее в правах групп.</span></td><td colspan="2">
<input type="checkbox" name="allow_html" value="1"<?php if (!empty($set['allow_html'])) echo ' checked="checked"' ?> /><br></td></tr>


<tr><td class="left">Разрешить использование Смайлов в сообщениях:<br>
<span class="comment">Использовать ли на сайте замену специальных меток на изображения(smiles).</span></td><td colspan="2">
<input type="checkbox" name="allow_smiles" value="1"<?php if (!empty($set['allow_smiles'])) echo ' checked="checked"' ?> /><br></td></tr>


<tr><td colspan="3" align="center"><input type="submit" name="send" value="Сохранить"><br></td></tr>

</table>
</form>



<?php

include_once 'template/footer.php';
?>