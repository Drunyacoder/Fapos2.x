<?php

##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      1.1                            ##
## Project:      CMS                            ##
## package       CMS Fapos                      ##
## subpackege    Admin Panel module             ##
## copyright     ©Andrey Brykin 2010-2011       ##
## last mod.     2011/11/13                     ##
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




$pageTitle = 'Массовая рассылка писем';
$pageNav = $pageTitle;
$pageNavl = '';





$ACL = $Register['ACL'];
$FpsDB = $Register['DB'];
$users_groups = $ACL->get_group_info();
$count_usr = $FpsDB->select('users', DB_ALL, array(
	'group' => 'status',
	'fields' => array(
		'COUNT(*) as cnt',
		'status'
	),
));
foreach ($users_groups as $id => $gr) {
	$users_groups[$id]['cnt'] = 0;
	foreach ($count_usr as $key => $val) {
		if ($id == $val['status']) {
			$users_groups[$id]['cnt'] = $val['cnt'];
			break;
		}
	}
}
if (isset($users_groups[0])) unset($users_groups[0]);

$all_users_cnt = 0;
foreach ($count_usr as $val) {
	$all_users_cnt += $val['cnt'];
}


	
if (isset($_POST['send'])) {
	if (!empty($_POST['message']) 
	&& !empty($_POST['subject']) 
	&& !empty($_POST['groups'])
	&& count($_POST['groups']) > 0) {
		
		$status_ids = array();
		foreach ($_POST['groups'] as $group) {
			$status_ids[] = intval($group);
		}
		$status_ids = array_unique($status_ids);
		$status_ids = implode(', ', $status_ids);
		
		
		$mail_list = $FpsDB->select('users', DB_ALL, array(
			'cond' => array(
				'`status` IN (' . $status_ids . ')',
			),
		));
		
		if (count($mail_list) > 0) {
			$from = (!empty($_POST['from'])) ? trim($_POST['from']) : Config::read('admin_email');
			$headers  = "Content-type: text/plain; charset=utf-8 \r\n";
			$headers .= "From: " . $from . "\r\n";
			
			$subject = trim($_POST['subject']);

			$n = 0;
			foreach ($mail_list as $result) {
				$patterns = array(
					'{USERNAME}' => $result['name'],
					'{USERMAIL}' => $result['email'],
					'{SITEDOMAIN}' => $_SERVER['HTTP_HOST'],
				);
				$message = str_replace(array_keys($patterns), $patterns, $_POST['message']);
				
				if (mail($result['email'], $subject, $message, $headers)) {
					$n++;
				}
			}
			
			if (empty($error)) $_SESSION['info_message'] =  'Писем отправленно: ' . $n;
		} else {
			$_SESSION['info_message'] = '<span style="color:red;">Не найдено пользователей с заданными параметрами</span>';
		}
	} else {
		$_SESSION['info_message'] = '<span style="color:red;">Заполните поля.</span>';
	}
	
	redirect('/admin/users_sendmail.php');
}



include_once ROOT . '/admin/template/header.php';
?>

<div class="fps-win">
<span class="greytxt">Email-ов доступно:</span> <?php echo $all_users_cnt; ?><br /><br />


<span class="greytxt">Максимальный размер письма:</span> 10000 символов<br /><br />


<span class="greytxt"><b>В теле письма доступны следующие метки</b></span><br />
{USERNAME}<span class="greytxt"> - Имя получателя</span><br />
{USERMAIL}<span class="greytxt"> - Почтовый адрес получателя</span><br />
{SITEDOMAIN}<span class="greytxt"> - Домен вашего сайта</span><br />
</div>

<form action="" method="POST">
<table class="settings-tb">
		<tr>
			<td class="left">Отправить группам</td>
			<td class="right">
				<table>
					<tr>
					<?php foreach ($users_groups as $id => $group): ?>
						<td style="text-align:center;">
							<input type="checkbox" name="groups[<?php echo (int)$id; ?>]" value="<?php echo (int)$id; ?>" checked="checked" /><br />
							<?php echo h($group['title']) . ' (' . $group['cnt'] . ')'; ?>
						</td>
					<?php endforeach; ?>
					</tr>
				</table>
			</td>
		</tr>	
		<tr>
			<td class="left">Тема</td>
			<td class="right">
				<input size="120" type="text" name="subject" />
			</td>
		</tr>	
		<tr>
			<td class="left">Обратный адрес</td>
			<td class="right">
				<input size="120" type="text" name="from" value="<?php 
				echo (Config::read('admin_email')) ? Config::read('admin_email') : ''; ?>" />
			</td>
		</tr>
		<tr>
			<td class="left">Текст письма</td>
			<td class="right">
				<textarea name="message" cols="100" rows="12"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;"><input type="submit" value="Разослать" name="send" /></td>
		</tr>
</table>
</form>

<?php
if (!empty($_SESSION['info_message'])):
?>
<script type="text/javascript">showHelpWin('<?php echo $_SESSION['info_message'] ?>', 'Сообщение');</script>
<?php
	unset($_SESSION['info_message']);
endif;
?>


<?php

include_once ROOT . '/admin/template/footer.php';
?>

