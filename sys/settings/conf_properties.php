<?php



// properties for system settings and settings that not linked to module
$settingsInfo = array(
	/* HLU */
	'hlu' => array(
		'hlu' => array(
			'type' => 'checkbox',
			'title' => 'Включить ЧПУ',
			'description' => '',
			'checked' => '1',
		),
		'hlu_extention' => array(
			'title' => 'Окончание URL',
			'description' => 'Например .html',
		),
		'hlu_understanding' => array(
			'type' => 'checkbox',
			'title' => 'Разбор ЧПУ',
			'description' => 'Новые ссылки будут обычными, но обращение через ЧПУ будет поддерживаться для работоспособности старых ссылок',
			'checked' => '1',
		),
	),

	/* SYS */
	'sys' => array(
		'template' => array(
			'type' => 'select',
			'title' => 'Шаблон',
			'description' => '',
			'options' => $templateSelect,
			'options_attr' => array(
				'onClick' => 'showScreenshot(\'%s\');',
			),
			'input_sufix' => '<img id="screenshot" style="border:1px solid #A3BAE9;" width="200px" height="200px" src="' . getImgPath($config['template']) . '" />',
		),
		'site_title' => array(
			'type' => 'text',
			'title' => 'Название сайта',
			'description' => 'можно использовать в шаблонах как {SITE_TITLE}',
		),
		'title' => array(
			'type' => 'text',
			'title' => 'Заголовок сайта',
			'description' => 'можно использовать в шаблонах как {TITLE}',
		),
		'title' => array(
			'type' => 'text',
			'title' => 'Заголовок сайта',
			'description' => 'можно использовать в шаблонах как {TITLE}',
		),
		'meta_keywords' => array(
			'type' => 'text',
			'title' => 'Ключевые слова сайта',
			'description' => 'можно использовать в шаблонах как {META_KEYWORDS}',
		),
		'meta_description' => array(
			'type' => 'text',
			'title' => 'Описание сайта',
			'description' => 'можно использовать в шаблонах как {META_DESCRIPTION}',
		),
		'cookie_time' => array(
			'type' => 'text',
			'title' => 'Время "жизни" cookies в днях',
			'description' => 'в cookies сохраняются логин и пароль пользователя,
 если была выбрана опция "Автоматически входить при каждом посещении"',
		),
		'redirect' => array(
			'type' => 'text',
			'title' => 'Автоматическая переадресация',
			'description' => 'используйте эту опцию что бы перевести пользователя с главной страницы, например, на форум или каталог файлов, или на другой сайт',
		),
		'start_mod' => array(
			'type' => 'text',
			'title' => 'Точка входа',
			'description' => 'Это что-то похожее на переадресацию, но самой переадресации
не происходит. Другими словами сдесь Вы вводите адрес точки входа
и страница по этому адресу будет являться главной страницей сайта.
Вводите сюда только рабочие ссылки и только в пределах сайта. Пример "<b>news/view/1</b>"',
		),
		'max_file_size' => array(
			'type' => 'text',
			'title' => 'Максимальный размер файла вложения',
			'help' => 'Байт',
			'description' => 'которые пользователи смогут выгружать на сайте
Используется во всех модулях где нет собственой подобной настройки',
		),
		
		'img_preview_size' => array(
			'type' => 'text',
			'title' => 'Размер превью',
			'help' => 'Px',
			'description' => 'Автоматически уменьшеных изображений',
		),
		
		'min_password_lenght' => array(
			'type' => 'text',
			'title' => 'Минимальная длина пароля пользователя',
			'description' => '',
		),
		'admin_email' => array(
			'type' => 'text',
			'title' => 'Адрес электронной почты администратора',
			'description' => 'этот e-mail будет указан в поле FROM писем, которое один пользователь напишет
другому; этот же e-mail будет указан в письмах с просьбой активировать учетную
 запись или пароль (в случае его утери)',
		),
		'redirect_delay' => array(
			'type' => 'text',
			'title' => 'Задержка перед редиректом',
			'description' => 'когда пользователь выполняет какое-то действие (например, добавляет сообщение)
 ему выдается сообщение, что "Ваше сообщение было успешно добавлено" и делается
редирект на нужную страницу',
		),
		'time_on_line' => array(
			'type' => 'text',
			'title' => 'Время , в течение которого считается, что пользователь "on-line"',
			'description' => '',
		),
		'open_reg' => array(
			'type' => 'select',
			'title' => 'Режим регистрации',
			'description' => 'Определяет разрешена ли регистрация у Вас на сайте',
			'options' => array(
				'1' => 'Разрешена',
				'0' => 'Запрещена',
			),
		),
		'email_activate' => array(
			'type' => 'checkbox',
			'checked' => '1',
			'value' => '1',
			'title' => 'Требуется ли активация аккаунта по E-mail',
			'description' => '',
		),
		'debug_mode' => array(
			'type' => 'checkbox',
			'checked' => '1',
			'value' => '1',
			'title' => 'Вывод ошибок',
			'description' => '',
		),



		'Какие из последних материалов выводить на главной' => 'Какие из последних материалов выводить на главной',
		'sub_news' => array(
			'type' => 'checkbox',
			'title' => 'Новости',
			'description' => '',
			'checked' => '1',
			'value' => 'news',
			'fields' => 'latest_on_home',
		),
		'sub_stat' => array(
			'type' => 'checkbox',
			'title' => 'Статьи',
			'description' => '',
			'checked' => '1',
			'value' => 'stat',
			'fields' => 'latest_on_home',
		),
		'sub_loads' => array(
			'type' => 'checkbox',
			'title' => 'Загрузки',
			'description' => '',
			'checked' => '1',
			'value' => 'loads',
			'fields' => 'latest_on_home',
		),
		'cnt_latest_on_home' => array(
			'type' => 'text',
			'title' => 'Кол-во материалов на главной',
			'description' => '',
		),
		'announce_lenght' => array(
			'type' => 'text',
			'title' => 'Размер анонса на главной',
			'description' => '',
		),


		'Прочее' => 'Прочее',
		'cache' => array(
			'type' => 'checkbox',
			'title' => 'Кэш',
			'description' => '(Кешировать ли содержимое сайта? Если кэш включен сайт будет работать быстрее
при большой нагрузке, но при маленькой его лучше выключить.)',
			'checked' => '1',
			'value' => '1',
		),
		'cache_querys' => array(
			'type' => 'checkbox',
			'title' => 'Кэш SQl запросов',
			'description' => '(Кешировать ли результаты SQL запросов? Если кэш включен сайт будет работать быстрее
при большой нагрузке, но при маленькой его лучше выключить.)',
			'checked' => '1',
			'value' => '1',
		),
		'use_additional_fields' => array(
			'type' => 'checkbox',
			'title' => 'Использовать ли дополнительные поля на сайте',
			'description' => 'Замедлит работу сайта. Используйте только если знаете что это и как этим пользоваться.',
			'checked' => '1',
			'value' => '1',
		),
		'allow_html' => array(
			'type' => 'checkbox',
			'title' => 'Разрешить использование HTML в сообщениях',
			'description' => 'Таит угрозу. Включая эту возможность, настройте ее в правах групп.',
			'checked' => '1',
			'value' => '1',
		),
		'allow_smiles' => array(
			'type' => 'checkbox',
			'title' => 'Разрешить использование Смайлов в сообщениях',
			'description' => 'Использовать ли на сайте замену специальных меток на изображения(smiles).',
			'checked' => '1',
			'value' => '1',
		),
	),

	/* SECURE */
	'secure' => array(
		'antisql' => array(
			'type' => 'checkbox',
			'title' => 'Отслеживать попытки SQL иньекций через адресную строку',
			'description' => '(запись ведеться в /sys/logs/antisql.dat)',
			'checked' => '1',
			'value' => '1',
		),
		'anti_ddos' => array(
			'type' => 'checkbox',
			'title' => 'Анти DDOS',
			'description' => 'Анти DDOS защита: (Позволяет снизить риск DDOS атаки)',
			'checked' => '1',
			'value' => '1',
		),
		'request_per_second' => array(
			'type' => 'text',
			'title' => '(DDOS)Максимально допустимое кол-во запросов',
			'description' => '(за одну секунду, с одного диапазона IP адресов)',
		),
		'system_log' => array(
			'type' => 'checkbox',
			'title' => 'Лог действий',
			'description' => 'Вести ли лог действий: (фиксируются действия пользователей)',
			'checked' => '1',
			'value' => '1',
		),
		'max_log_size' => array(
			'type' => 'text',
			'title' => 'Максимально допустимый объем логов',
			'description' => 'Предел занимаемого логами дискового пространства',
		),
		'autorization_protected_key' => array(
			'type' => 'checkbox',
			'title' => 'Защита от перебора пароля',
			'description' => 'Посредством передачи защитного ключа',
			'checked' => '1',
			'value' => '1',
		),
		'session_time' => array(
			'type' => 'text',
			'title' => 'Длительность сессии в админ-панели',
			'description' => 'Если бездействовать в админ-панели больше отведеного времени, придется заново авторизоваться',
		),
	),

    /* COMMON */
    'common' => array(
        'rss_lenght' => array(
            'type' => 'text',
            'title' => 'Максимальная длина анонса RSS',
            'description' => '',
      	),
        'rss_cnt' => array(
            'type' => 'text',
            'title' => 'Количество материалов в RSS',
            'description' => '',
      	),

        'Для каких модулей включить RSS' => 'Для каких модулей включить RSS',
        'rss_news' => array(
             'type' => 'checkbox',
             'title' => 'Новости',
             'description' => '',
             'checked' => '1',
             'value' => '1',
       	),
        'rss_stat' => array(
             'type' => 'checkbox',
             'title' => 'Статьи',
             'description' => '',
             'checked' => '1',
             'value' => '1',
       	),
        'rss_loads' => array(
             'type' => 'checkbox',
             'title' => 'Каталог файлов',
             'description' => '',
             'checked' => '1',
             'value' => '1',
       	),
    ),
);
$sysMods = array(
	'sys',
	'hlu',
	'secure',
	'common',
);
$noSub = array(
	'sys',
	'hlu',
);