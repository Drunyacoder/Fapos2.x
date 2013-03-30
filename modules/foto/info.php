<?php

$menuInfo = array(
    'url' => 'settings.php?m=foto',
    'ankor' => 'Фото-каталог',
	'sub' => array(
        'settings.php?m=foto' => 'Настройки',
        'design.php?m=foto' => 'Дизайн',
        'category.php?mod=foto' => 'Управление категориями',
	),
);


$settingsInfo = array(
	'title' => array(
		'type' => 'text',
		'title' => 'Заголовок',
		'description' => 'Заголовок, который подставится в блок <title></title>',
	),
	'description' => array(
		'type' => 'text',
		'title' => 'Описание',
		'description' => 'То, что подставится в мета тег description',
	),
	
	
	'Ограничения' => 'Ограничения',
	'max_file_size' => array(
		'type' => 'text',
		'title' => 'Максимальный размер картинки',
		'description' => '',
		'help' => 'Байт',
	),
	'per_page' => array(
		'type' => 'text',
		'title' => 'Материалов на странице',
		'description' => '',
		'help' => '',
	),
	'description_lenght' => array(
		'type' => 'text',
		'title' => 'Максимальная длина описания',
		'description' => '',
		'help' => '',
	),
	
	
	'Поля обязательные для заполнения' => 'Поля обязательные для заполнения',
	'category_field' => array(
		'type' => 'checkbox',
		'title' => 'Категория',
		'attr' => array(
			'disabled' => 'disabled',
			'checked' => 'checked',
		),
	),
	'title_field' => array(
		'type' => 'checkbox',
		'title' => 'Заголовок',
		'attr' => array(
			'disabled' => 'disabled',
			'checked' => 'checked',
		),
	),
	'file_field' => array(
		'type' => 'checkbox',
		'title' => 'Файл',
		'attr' => array(
			'disabled' => 'disabled',
			'checked' => 'checked',
		),
	),
	'sub_description' => array(
		'type' => 'checkbox',
		'title' => 'Описание',
		'value' => 'description',
		'fields' => 'fields',
		'checked' => '1',
	),
	
	
	'Прочее' => 'Прочее',
	'use_watermarks' => array(
		'type' => 'checkbox',
		'title' => 'Водяные знаки',
		'value' => '1',
		'checked' => '1',
	),
	'watermark_img' => array(
		'type' => 'file',
		'title' => 'Водяной знак',
		'input_sufix_func' => 'fotoShowWaterMarkImage',
		'onsave' => array(
			'func' => 'fotoSaveWaterMark',
		),
	),
	'active' => array(
		'type' => 'checkbox',
		'title' => 'Статус',
		'description' => '(Активирован/Деактивирован)',
		'value' => '1',
		'checked' => '1',
	),
);


if (!function_exists('fotoSaveWaterMark')) {
	function fotoSaveWaterMark(&$settings)
	{
		if ($_FILES['watermark_img']['type'] == 'image/jpg'
		|| $_FILES['watermark_img']['type'] == 'image/gif'
		|| $_FILES['watermark_img']['type'] == 'image/jpeg'
		|| $_FILES['watermark_img']['type'] == 'image/png') {
			$ext = strchr($_FILES['watermark_img']['name'], '.');
			if (move_uploaded_file($_FILES['watermark_img']['tmp_name'], ROOT . '/sys/img/watermark'.$ext)) {
				$settings['watermark_img'] = 'watermark'.$ext;
			}
		}
	}
}

if (!function_exists('fotoShowWaterMarkImage')) {
	function fotoShowWaterMarkImage(&$settings)
	{
		$params = array(
			'style' => 'max-width:200px; max-height:200px;',
		);

		if (!empty($settings['watermark_img']) 
		&& file_exists(ROOT . '/sys/img/' . $settings['watermark_img'])) {
			return get_img('/sys/img/' . $settings['watermark_img'], $params);
		}
		return '';
	}
}
