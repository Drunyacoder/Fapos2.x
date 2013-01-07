<?php

##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      0.9                            ##
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

$Register = Register::getInstance();
$FpsDB = $Register['DB'];


$pageTitle = __('Admin Panel');
$pageNav = $pageTitle . __(' - General information');
$pageNavl = '';




$cnt_usrs = $FpsDB->select('users', DB_COUNT);;

$groups_info = array();
$users_groups = $ACL->get_group_info();
if (!empty($users_groups)) {
	foreach ($users_groups as $key => $group) {
		if ($key === 0) {
			$groups_info[0] = null;
			continue;
		}
		$groups_info[$group['title']] = $FpsDB->select('users', DB_COUNT, array('cond' => array('status' => $key)));
	}
}


$cnt_for = $FpsDB->select('themes', DB_COUNT);
$cnt_news = $FpsDB->select('news', DB_COUNT);
$cnt_load = $FpsDB->select('loads', DB_COUNT);
$cnt_stat = $FpsDB->select('stat', DB_COUNT);
$cnt_mat = $cnt_news + $cnt_for + $cnt_load + $cnt_stat;

$all_hosts = $FpsDB->query("
	SELECT 
	SUM(`views`) as hits_cnt 
	, SUM(ips) as hosts_cnt
	, (SELECT SUM(`views`) FROM `" . $FpsDB->getFullTableName('statistics') . "` WHERE `date` = '" . date("Y-m-d") . "') as today_hits
	, (SELECT ips FROM `" . $FpsDB->getFullTableName('statistics') . "` WHERE `date` = '" . date("Y-m-d") . "') as today_hosts
	FROM `" . $FpsDB->getFullTableName('statistics') . "`");

$tmp_datafile = ROOT . '/sys/logs/counter/' . date("Y-m-d") . '.dat';

if (file_exists($tmp_datafile) && is_readable($tmp_datafile)) {
	$stats = unserialize(file_get_contents($tmp_datafile));
	$today_hits = $stats['views'];
	$today_hosts = $stats['cookie'];
} else {
	$today_hits = 0;
	$today_hosts = 0;
}
$all_hosts[0]['hits_cnt'] += $today_hits;
$all_hosts[0]['hosts_cnt'] += $today_hosts;




	
//echo $header;
include 'template/header.php';
?>


<?php
if (!empty($_SESSION['clean_cache'])):
?>
<script type="text/javascript">showHelpWin('<?php echo __('Cache is clened'); ?>', 'Сообщение');</script>
<?php
	unset($_SESSION['clean_cache']);
endif;
?>

		
			<div class="iqblock">
				<!--************ GENERAL **********-->
				<div class="bef-lines">
				<table class="lines">
					<tr>
						<th><?php echo __('Name'); ?></th>
						<th><?php echo __('Value'); ?></th>
					</tr>
					<tr>	
						<td width="70%" align="left"><b><?php echo __('Current domain'); ?></b><br />
						<span class="comment"><?php echo __('Domain is your site address'); ?></span></td>
						<td width="50%"  align="left"><span style="color:blue;"><?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/' ?></span></td>
					</tr>
					
					<tr>	
						<td width="70%" align="left"><b><?php echo __('SQL inj state'); ?></b><br />
						<span class="comment"><?php echo __('Is the controll of SQL inj'); ?></span></td>
						<td width="50%"  align="left"><span style="color:green;"><?php echo (Config::read('antisql', 'secure') == 1) ? __('Yes') : __('No') ?></span></td>
					</tr>
					
					<tr>	
						<td width="70%" align="left"><b><?php echo __('Anti DDOS protection'); ?></b><br />
						<span class="comment"><?php echo __('Is the enable Anti DDOS'); ?></span></td>
						<td width="50%"  align="left"><span style="color:green;"><?php echo (Config::read('anti_ddos', 'secure') == 1) ? __('Yes') : __('No') ?></span></td>
					</tr>
					
					<tr>	
						<td width="70%" align="left"><b><?php echo __('Cache'); ?></b><br />
						<span class="comment"><?php echo __('The site will run faster'); ?></span></td>
						<td width="50%"  align="left"><span style="color:green;"><?php echo (Config::read('cache')) ? __('Yes') : __('No') ?></span></td>
					</tr>
					
					<tr>	
						<td width="70%" align="left"><b><?php echo __('SQL cache'); ?></b><br />
						<span class="comment"><?php echo __('SQL. Site will be run faster'); ?></span></td>
						<td width="50%"  align="left"><span style="color:green;"><?php echo (Config::read('cache_querys')) ? __('Yes') : __('No') ?></span></td>
					</tr>
					
				</table>
				</div>
			
				<!--************ USERS **********-->
				<div class="bef-lines">
				<table class="lines">
					<tr>	
						<th>Группа</th>
						<th>Кол-во</th>
					</tr>
					<tr>	
						<td width="70%" align="left"><b>Всего пользователей:</b></td>
						<td width="50%"  align="left"><span style="color:blue;"><?php echo $cnt_usrs ?></span></td>
					</tr>
					
				<?php if (!empty($groups_info)):
						  foreach ($groups_info as $key => $group_info):
				?>
				
					<tr>	
						<?php if($key === 0): ?>
						<td width="70%" align="left">
							<b>Гости:</b>
							<span class="comment">*Гость - абстрактная группа</span>
						</td>
						<td width="50%"  align="left"><span style="color:blue;"></span></td>
						<?php else: ?>
						<td width="70%" align="left">
							<b><?php echo $key ?>:</b>
						</td>
						<td width="50%"  align="left"><span style="color:blue;"><?php echo $group_info ?></span></td>
						<?php endif; ?>
					</tr>
				
				<?php     endforeach;
					  endif;
				?>
					
				</table>
				</div>

				<!--************ STATISTIC **********-->
				<div class="bef-lines">
				<table class="lines">
					<tr>
						<th>Параметр</th>
						<th>Значение</th>
					</tr>
					<tr>	
						<td width="70%" align="left"><b>Хостов за все время:</b><br />
						<span class="comment">*Хост - это уникальный посетитель, фактически - это<br />
						заход на сайт с разных компьютеров или IP адресов</span></td>
						<td width="50%"  align="left"><span style="color:blue;"><?php echo $all_hosts[0]['hosts_cnt'] ?></span></td>
					</tr>
					
					
					<tr>	
						<td width="70%" align="left"><b>Хитов за все время:</b><br />
						<span class="comment">*Хиты(hits) - это просмотры, фактически - это любой<br />
						просмотрт страницы, даже с одного IP. На один хост может приходиться<br />
						любое кол-во хитов</span></td>
						<td width="50%"  align="left"><span style="color:green;"><?php echo $all_hosts[0]['hits_cnt'] ?></span></td>
					</tr>
					
					<tr>	
						<td width="70%" align="left"><b>Хостов сегодня:</b></td>
						<td width="50%"  align="left"><span style="color:green;"><?php echo $today_hosts ?></span></td>
					</tr>
					
					<tr>	
						<td width="70%" align="left"><b>Хитов за сегодня:</b></td>
						<td width="50%"  align="left"><span style="color:green;"><?php echo $today_hits ?></span></td>
					</tr>
					
				</table>
				</div>
			</div>
			
			<div  class="iqblock">
			
				<!--************ MODULES **********-->
				<div class="bef-lines">
				<?php $modules = glob(ROOT . '/modules/*'); ?>
				<table class="lines">
					<tr>
						<th>Модуль</th>
						<th>Состояние</th>
					</tr>
					<tr>	
						<td width="70%" align="left"><b>Всего модулей:</b><br />
						<span class="comment">*Модули, которые присутствуют у Вас на сайте</span></td>
						<td width="50%"  align="left"><span style="color:blue;"><?php echo count($modules); ?></span></td>
					</tr>
					
					
					<?php foreach ($modules as $modul): ?>
					<?php if (preg_match('#/(\w+)$#i', $modul, $modul_name)): ?>
					<?php if (is_dir($modul)): ?>
					
					<tr>	
						<td width="70%" align="left"><b><?php echo $modul_name[1] ?>:</b></td>
						<td width="50%"  align="left">
							<?php echo (Config::read('active', $modul_name[1])) ? '<span style="color:green;">Активен</span>' : '<span style="color:red;">Не активен</span>' ?>
						</td>
					</tr>
					
					<?php endif; ?>
					<?php endif; ?>
					<?php endforeach; ?>
					
				</table>
				</div>
				<div style="height:2px;"></div>

				<!--************ MATERIALS **********-->
				<div class="bef-lines">
				<table class="lines">
					<tr>
						<th>Материал</th>
						<th>Кол-во</th>
					</tr>
				
					<tr>	
						<td width="70 align="left"><b>Всего материалов:</b></td>
						<td width="50%"  align="left"><span style="color:blue;"><?php echo $cnt_mat ?></span></td>
					</tr>
					
					
					<tr>	
						<td width="70 align="left"><b>Новостей:</b></td>
						<td width="50%"  align="left"><span style="color:green;"><?php echo $cnt_news ?></span></td>
					</tr>
					
					<tr>	
						<td width="70%" align="left"><b>Загрузок:</b></td>
						<td width="50%"  align="left"><span style="color:green;"><?php echo $cnt_load ?></span></td>
					</tr>
					
					<tr>	
						<td width="70%" align="left"><b>Статей:</b></td>
						<td width="50%"  align="left"><span style="color:green;"><?php echo $cnt_stat ?></span></td>
					</tr>
					
					<tr>	
						<td width="70%" align="left"><b>Тем на форуме:</b></td>
						<td width="50%"  align="left"><span style="color:green;"><?php echo $cnt_for ?></span></td>
					</tr>
					
				</table>
				</div>
				<div style="height:2px;"></div>
			
			</div>
			
			
		<div style="clear:both;"></div>
	
		<!--
		<script language="JavaScript" type="text/javascript" src="/sys/js/jquery-1.5.2.min.js"></script>
		<script type="text/javascript">
		
		</script>
		-->



<?php
include_once 'template/footer.php';
?>



