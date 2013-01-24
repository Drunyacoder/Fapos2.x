<?php
/*-----------------------------------------------\
| 												 |
| @Author:       Andrey Brykin (Drunya)          |
| @Email:        drunyacoder@gmail.com           |
| @Site:         http://fapos.net                |
| @Version:      1.3                             |
| @Project:      CMS                             |
| @package       CMS Fapos                       |
| @subpackege    Authors list (Admin Part)       |
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



$pageTitle = $page_title = __('Dev. Team');
$pageNav = $page_title;
$pageNavl = '<span style="float:right;"><a href="javascript://" onClick="showHelpWin(\'Арбайтен! Арбайтен! Арбайтен!\', \'А никто и не мешает\')">' . __('I want to be here') . '</a></span>';
include_once ROOT . '/admin/template/header.php';
?>


	
<div class="fps-win">
	<ul class="authors">
		<li>
			<h3 style="padding: 0px; margin:0px;">Idea by</h3>
			<li class="comment">Andrey Brykin (Drunya)</li>
		</li>
		<br />
		<li>
			<h3 style="padding: 0px; margin:0px;">Programmers</h3>
			<li class="comment">Andrey Brykin (Drunya)</li>
			<li class="comment">Danilov Alexandr (modos189)</li>
		</li>
		<br />
		<li>
			<h3 style="padding: 0px; margin:0px;">Testers and audit</h3>
			<li class="comment">Andrey Konyaev (Ater)</li>
			<li class="comment">Laguta Dmitry (ARMI)</li>
			<li class="comment">Roman Maximov (r00t_san)</li>
			<li class="comment">Alexandr Verenik (Wasja)</li>
		</li>
		<br />
		<li>
			<h3 style="padding: 0px; margin:0px;">Marketing</h3>
			<li class="comment">Andrey Konyaev (Ater)</li>
		</li>
		<br />
		<li>
			<h3 style="padding: 0px; margin:0px;">Design and Templates</h3>
			<li class="comment">Andrey Brykin (Drunya)</li>
			<li class="comment">Alexandr Bognar (Krevedko)</li>
			<li class="comment">Roman Maximov (r00t_san)</li>
			<li class="comment">Laguta Dmitry (ARMI)</li>
		</li>
		<br />
		<li>
			<h3 style="padding: 0px; margin:0px;">Specialists by Security</h3>
			<li class="comment">Roman Maximov (r00t_san)</li>
		</li>
		<br />
		<li>
			<h3 style="padding: 0px; margin:0px;">Additional Software</h3>
			<li class="comment">Andrey Brykin (Drunya)</li>
			<li class="comment">Alexandr Verenik (Wasja)</li>
		</li>
		<br />
	</ul>
</div>
<?php
include_once 'template/footer.php';
?>