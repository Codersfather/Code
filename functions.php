<?php

if ( ! is_admin() ) { //Если страница не админка то
	//Перерегистрируем библиотеки Jquery
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', ( "https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" ), false, '1.12.4' );
}

// Инициализация действий
add_action( "inc_button-sidebar", "buttonHeaderToggle" );
add_action( 'wp_ajax_myfilter', 'true_filter_function' );
add_action( 'wp_ajax_nopriv_myfilter', 'true_filter_function' );
add_action( 'after_switch_theme', 'bt_flush_rewrite_rules' ); // Сбрасываем правила для произвольного типа записей.
add_action( 'wp_head', 'component_postviews' ); //Подсчет количества посещений страниц
add_action( 'wp_enqueue_scripts', 'Theme_includes' ); //Подключяем стили и скрипты
//add_action( 'wp_enqueue_scripts', 'Theme_icnludes_version' ); //Подключяем стили и скрипты с изменением версии

// Инициализация фильтров
add_filter( 'image_size_names_choose', 'true_new_image_sizes' );
add_filter( 'pre_option_link_manager_enabled', '__return_false' );
add_filter( 'nav_menu_css_class', 'custom_wp_nav_menu' );
add_filter( 'nav_menu_item_id', 'custom_wp_nav_menu' );
add_filter( 'page_css_class', 'custom_wp_nav_menu' );
add_filter( 'wp_nav_menu', 'current_to_active' );
add_filter( 'wp_nav_menu', 'strip_empty_classes' );
add_filter( "formatePrice", "formatePrice", 10, 1 );
add_filter( 'template_include', 'echo_cur_tplfile', 99 );//Какой шаблон используется в текущий момент

//Выполнение фильтров и действий
do_action( "inc_breadcrumbs" );

//Регистрируем меню
register_nav_menus( array(
	'main'   => 'Главное меню',
	'footer' => 'Нижнее меню'
) );

//Служебные фукнции

function echo_cur_tplfile( $template ){

	global $template_name;

	//Определяем название используемого шаблона
	$template_name = wp_basename( $template );

	return $template;
}

//Функция подключения стилей и скриптов


//Функции для работы с версией файла на базе последней даты изменения файла
function wp_enqueue_script_last( $handle, $src = false, $deps = array(), $in_footer = false ) {
	wp_enqueue_script( $handle, get_stylesheet_directory_uri() . $src, $deps, filemtime( get_stylesheet_directory() . $src ), $in_footer );
}

function wp_enqueue_style_last( $handle, $src = false, $deps = array(), $media = 'all' ) {
	wp_enqueue_style( $handle, get_stylesheet_directory_uri() . $src, $deps = array(), filemtime( get_stylesheet_directory() . $src ), $media );
}

function Theme_includes() {

	global $template_name;

	//Создаём рандомную переменную для версионности файлов
	function version($filter) {
		if($filter == 'WP') return random_int(10, 999); else return '?'.random_int(10, 999);
	}

	//CSS
	wp_enqueue_style('style', get_template_directory_uri()."/style.css",false,version("WP"));
	wp_enqueue_style( 'mediastyle-style', get_stylesheet_directory_uri() . "/css/mediastyle.css", false, version('WP') );
	wp_enqueue_style( 'RangeSlider-style', get_stylesheet_directory_uri() . "/css/RangeSlider.css" , false, version('WP') );
	wp_enqueue_style( 'Tooltips-style', get_stylesheet_directory_uri() . "/css/Tooltips.css", false, version('WP') );
	wp_enqueue_style( 'owltheme-style', get_stylesheet_directory_uri() . "/js/assets/owl.carousel.css", false, version('WP'));
	wp_enqueue_style( 'owlthemedefault-style', get_stylesheet_directory_uri() . "/js/assets/owl.theme.default.css", false, version('WP') );
	wp_enqueue_style( 'font-awesome', get_stylesheet_directory_uri() . "/css/font-awesome.css", false, version('WP') );
	wp_enqueue_style( 'jquery.custom-scroll-style', get_stylesheet_directory_uri() . "/js/jquery.custom-scroll.css" , false, version('WP'));
	wp_enqueue_style( 'nprogress-style', get_stylesheet_directory_uri() . "/css/nprogress.css" , false, version('WP'));
	wp_enqueue_style( 'border-ani-style', 'https://cdn.jsdelivr.net/gh/code-fx/Pure-CSS3-Animated-Border@V1.0/css/animated-border/animated-border.min.css',false, version('WP') ); //https://code-fx.github.io/Pure-CSS3-Animated-Border/

	//JS
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'theme-script', get_stylesheet_directory_uri() . "/js/script.js", false, version('WP') ); //Основной файл скриптов
	wp_enqueue_script( 'owlcarousel-script', get_stylesheet_directory_uri() . "/js/owl.carousel.js", false, version('WP') );
	wp_enqueue_script( 'jquery.custom-scroll', get_template_directory_uri() . '/js/jquery.custom-scroll.js', false, version('WP'));
	wp_enqueue_script( 'form-script', get_template_directory_uri() . '/js/form.js', false, version('WP'));
	wp_enqueue_script( 'nprogress', get_template_directory_uri() . "/js/nprogress.js", false, version('WP'));
	//wp_enqueue_script('dragdropp', get_template_directory_uri() . "/js/dragdropp.js", array());


	//Подключение скриптов и стилей по условию.
	//global $template_name - наименование выполняемого шаблона

	if ( in_array($template_name, array('archive-portfolio.php', 'single-portfolio.php') )) {
		wp_enqueue_script( 'portfolio-script', get_stylesheet_directory_uri() . "/js/portfolio-script.js", false, version('WP') );
	}
	if ( in_array($template_name, array('archive-project.php', 'taxonomy-project.php') )) {
		wp_enqueue_script( 'RangeSlider-script', get_stylesheet_directory_uri() . "/js/RangeSlider.js", false, version('WP') );
	}
	if ( in_array($template_name, array('single-project.php') )) {
		wp_enqueue_script( 'single-project', get_stylesheet_directory_uri() . "/js/single-project.js", false, version('WP') );
	}

}

//function Theme_includes_version () {
//
//	global $template_name;
//
//
//}

//Размеры миниатюр
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
	//set_post_thumbnail_size( 150, 150 ); // размер миниатюры поста по умолчанию
}

if ( function_exists( 'add_image_size' ) ) {
	//add_image_size( 'category-thumb', 300, 9999 ); // 300 в ширину и без ограничения в высоту
	//add_image_size( 'homepage-thumb', 220, 180, true ); // Кадрирование изображения
	add_image_size( 'selection', 320, 320, true );
	add_image_size( 'experts', 720, 960, true );
	add_image_size( 'true-fullwd', 700 );

	//Watermark
	add_image_size( 'wm-project-card', 640, 640, true );
	add_image_size( 'wm-project-max', 1600, 800, true );
	add_image_size( 'wm-project-plane', 640, 640, true );

	add_image_size( 'wm-portfolio-card', 640, 640, true );
	add_image_size( 'wm-portfolio-max', 1600, 800, true );

}

function true_new_image_sizes( $sizes ) {
	$addsizes = array(
		'selection'   => 'Подборки 1х1',
		'experts'   => 'Аватар эксперта',
		"true-fullwd" => 'По ширине контента',
		"wm-project-card" => 'Проект в каталоге',
		"wm-project-max" => 'Проект в слайдере',
		"wm-project-plane" => 'Планировки проекта',
		"wm-portfolio-max" => 'Готовый проект',
		"wm-portfolio-card" => 'Готовый проект в каталоге',
	);
	$newsizes = array_merge( $sizes, $addsizes );

	return $newsizes;
}

//Deletes all CSS classes and id's, except for those listed in the array below
function custom_wp_nav_menu( $var ) {
	return is_array( $var ) ? array_intersect( $var, array(
			//List of allowed menu classes
			'current_page_item',
			'current_page_parent',
			'current_page_ancestor',
			'first',
			'last',
			'vertical',
			'horizontal'
		)
	) : '';
}

//Replaces "current-menu-item" with "active"
function current_to_active( $text ) {
	$replace = array(
		//List of menu item classes that should be changed to "active"
		'current_page_item'     => 'active',
		'current_page_parent'   => 'active',
		'current_page_ancestor' => 'active',
	);
	$text    = str_replace( array_keys( $replace ), $replace, $text );

	return $text;
}

//Deletes empty classes and removes the sub menu class
function strip_empty_classes( $menu ) {
	$menu = preg_replace( '/ class=""| class="sub-menu"/', '', $menu );

	return $menu;
}

//Форматирование значения цены
function formatePrice( $param1 ) {

	$pricelength = strlen( (string) $param1 );

	if ( $pricelength > 6 ) {
		$param1 = $param1 / 1000000 . " МЛН ";
	} else {
		$param1 = number_format($param1, 0, ',', ' ');
	}

	return $param1;
}

//Подключаемые элементы
function buttonHeaderToggle() {
	require_once( get_template_directory() . "/includes/elements/button-sidebar.php" );
}

/* PROJECT - FILTER */

function go_filter() { // наша функция
	$args = array(); // подготовим массив
	$args['meta_query'] = array('relation' => 'AND'); // отношение между условиями, у нас это "И то И это", можно ИЛИ(OR)
	global $wp_query; // нужно заглобалить текущую выборку постов

	if ($_GET['project_type'] != '') { // если передана фильтрация по разделу
		$args['meta_query'][] = array( // пешем условия в meta_query
			'key' => 'project_type', // название произвольного поля
			'value' => $_GET['project_type'], // переданное значение произвольного поля
			'type' => 'CHAR', // тип поля, нужно указывать чтобы быстрее работало, у нас здесь число
			'compare' => 'IN'
		);
	}

	if ($_GET['project_seasonality'] != '') { // если передана фильтрация по разделу
		$args['meta_query'][] = array( // пешем условия в meta_query
			'key' => 'project_seasonality', // название произвольного поля
			'value' => $_GET['project_seasonality'], // переданное значение произвольного поля
			'type' => 'CHAR', // тип поля, нужно указывать чтобы быстрее работало, у нас здесь число
			'compare' => 'IN' // тип сравнения IN, т.е. значения поля комнат должно быть одним из значений элементов массива
		);
	}

	if ($_GET['project_square'] != '' || $_GET['project_square'] != '') { // если передано поле "Цена от" или "Цена до"
		$param = explode(';',$_GET['project_square']);
		if ($param[0] == '') $param[0] = 0; // если "Цена от" пустое, то значит от 0 и выше
		if ($param[1] == '') $param[1] = 500; // если "Цена до" пустое, то будет до 9999999
		$args['meta_query'][] = array( // пешем условия в meta_query
			'key' => 'project_square', // название произвольного поля
			'value' => array( (int)$param[0], (int)$param[1] ), // переданные значения ОТ и ДО для интервала передаются в массиве
			'type' => 'numeric', // тип поля - число
			'compare' => 'BETWEEN' // тип сравнения, здесь это BETWEEN - т.е. между "Цены от" и до "Цены до"
		);
	}

	if (!empty($_GET['project_floors'])) { // если передан массив с фильтром по комнатам
		$args['meta_query'][] = array( // пешем условия в meta_query
			'key' => 'project_floors', // название произвольного поля
			'value' => $_GET['project_floors'], // переданное значения, $_GET['rooms'] содержит массив со значениями отмеченных чекбоксов
			'type' => 'CHAR', // тип поля - строка не чувствительная
			'compare' => 'IN' // тип сравнения IN, т.е. значения поля комнат должно быть одним из значений элементов массива
		);
	}

	if (!empty($_GET['project_price'])) { // если передано поле "Цена от" или "Цена до"
		$param = explode(';',$_GET['project_price']);
		if ($param[0] == '') $param[0] = 0; // если "Цена от" пустое, то значит от 0 и выше
		if ($param[1] == '') $param[1] = 5000000; // если "Цена до" пустое, то будет до 9999999
		$args['meta_query'][] = array( // пешем условия в meta_query
			'key' => 'project_price', // название произвольного поля
			'value' => array( (int)$param[0], (int)$param[1] ), // переданные значения ОТ и ДО для интервала передаются в массиве
			'type' => 'numeric', // тип поля - число
			'compare' => 'BETWEEN' // тип сравнения, здесь это BETWEEN - т.е. между "Цены от" и до "Цены до"
		);
	}

	if ($_GET['photo'] != '') { // если передано поле "Только с фото"
		$args['meta_query'][] = array( // пешем условие в meta_query
			'key' => '_thumbnail_id', // поле _thumbnail_id должно быть, это зарезервированное имя wp
		);
	}

	if ($_GET['keyword'] != '') { // если передано поле "Ключевое слово"
		$args['s'] = $_GET['keyword']; // пешем значение в ключ "s" условий выборки, обратите внимание это уже не произвольное поле для meta_query, будет работать как обычный поиск + остальные условия
	}

	$args['tax_query'] = array('relation' => 'AND'); // можно AND // OR

	// фильтруем пост по таксономии с иерархией (как у категорий), причем пост должен принадлежать двум терминам таксономии одновременно
	if ($_GET['selections'] != '') { // если передано поле "Подборки"

		$args['tax_query'][] = array(
			'taxonomy'         => 'selection', // слаг таксономии, например category
			'field'            => 'term_id', // по какому полю таксономии фильтровать можно slug или id
			'terms'            => explode(',', $_GET['selections']), // пост должен принадлежать двум терминам с id 23 и 24
			'operator'         => 'IN', // одновременно, можно IN - тогда хотя бы одному термину
			'include_children' => false // чтобы работало для иерархических таксономий
		);
	}

	// более простой пример, пост должен принадлежать термину такосономии с указанным слагом
//	$args['tax_query'][] = array(
//		'taxonomy'  => 'slug_tax', // слаг таксономии
//		'field'     => 'slug', // по полю slug
//		'terms' => 'sug_term', // слаг термина
//	);


	query_posts(array_merge($args,$wp_query->query)); // сшиваем текущие условия выборки стандартного цикла wp с новым массивом переданным из формы и фильтруем
}

/*AJAX */

// Подключаем локализацию в самом конце подключаемых к выводу скриптов, чтобы скрипт
add_action( 'wp_enqueue_scripts', 'myajax_data', 99 );

function myajax_data(){

	// Первый параметр 'twentyfifteen-script' означает, что код будет прикреплен к скрипту с ID 'twentyfifteen-script'
	// 'twentyfifteen-script' должен быть добавлен в очередь на вывод, иначе WP не поймет куда вставлять код локализации
	// Заметка: обычно этот код нужно добавлять в functions.php в том месте где подключаются скрипты, после указанного скрипта
	wp_localize_script( 'theme-script', 'myajax',
		array(
			'url' => admin_url('admin-ajax.php')
		)
	);

}

function true_filter_function() {

	print_r( $_POST );

	$args = array(
		'orderby' => 'date', // сортировка по дате у нас будет в любом случае (но вы можете изменить/доработать это)
		'order'   => $_POST['date'] // ASC или DESC
	);

	// для таксономий
	if ( ! empty( $_POST['categoryfilter'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'id',
				'terms'    => $_POST['categoryfilter']
			)
		);
	}

	// создаём массив $args['meta_query'] если указана хотя бы одна цена или отмечен чекбокс
	if ( ! empty( $_POST['cena_min'] ) || isset( $_POST['cena_max'] ) || ( isset( $_POST['featured_image'] ) && $_POST['featured_image'] == 'on' ) ) {
		$args['meta_query'] = array( 'relation' => 'AND' );
	} // AND значит все условия meta_query должны выполняться

	// условие 1: цена больше $_POST['cena_min']
	if ( ! empty( $_POST['cena_min'] ) ) {
		$args['meta_query'][] = array(
			'key'     => 'cena',
			'value'   => $_POST['cena_min'],
			'type'    => 'numeric',
			'compare' => '>'
		);
	}

	// условие 2: цена меньше $_POST['cena_max']
	if ( ! empty( $_POST['cena_max'] ) ) {
		$args['meta_query'][] = array(
			'key'     => 'cena',
			'value'   => $_POST['cena_max'],
			'type'    => 'numeric',
			'compare' => '<'
		);
	}

	// условие 3: миниатюра имеется
	if ( ! empty( $_POST['featured_image'] ) && $_POST['featured_image'] == 'on' ) {
		$args['meta_query'][] = array(
			'key'     => '_thumbnail_id',
			'compare' => 'EXISTS'
		);
	}

	$query = new WP_Query( $args );

	//print_r($args);

	include_once( get_template_directory() . "/loop.php" );


	wp_die();

}

function bt_flush_rewrite_rules() {
	flush_rewrite_rules();
}

function component_postviews() {

	/* ------------ Настройки -------------- */
	$meta_key     = 'views';  // Ключ мета поля, куда будет записываться количество просмотров.
	$who_count    = 1;            // Чьи посещения считать? 0 - Всех. 1 - Только гостей. 2 - Только зарегистрированных пользователей.
	$exclude_bots = 1;            // Исключить ботов, роботов, пауков и прочую нечесть :)? 0 - нет, пусть тоже считаются. 1 - да, исключить из подсчета.

	global $user_ID, $post;
	if ( is_singular() ) {
		$id = (int) $post->ID;
		static $post_views = false;
		if ( $post_views ) {
			return true;
		} // чтобы 1 раз за поток
		$post_views   = (int) get_post_meta( $id, $meta_key, true );
		$should_count = false;
		switch ( (int) $who_count ) {
			case 0:
				$should_count = true;
				break;
			case 1:
				if ( (int) $user_ID == 0 ) {
					$should_count = true;
				}
				break;
			case 2:
				if ( (int) $user_ID > 0 ) {
					$should_count = true;
				}
				break;
		}
		if ( (int) $exclude_bots == 1 && $should_count ) {
			$useragent = $_SERVER['HTTP_USER_AGENT'];
			$notbot    = "Mozilla|Opera"; //Chrome|Safari|Firefox|Netscape - все равны Mozilla
			$bot       = "Bot/|robot|Slurp/|yahoo"; //Яндекс иногда как Mozilla представляется
			if ( ! preg_match( "/$notbot/i", $useragent ) || preg_match( "!$bot!i", $useragent ) ) {
				$should_count = false;
			}
		}

		if ( $should_count ) {
			if ( ! update_post_meta( $id, $meta_key, ( $post_views + 1 ) ) ) {
				add_post_meta( $id, $meta_key, 1, true );
			}
		}
	}

	return true;
}

//

function my_get_posts() {

	$id = $_POST['post_id'];
	$type = $_POST['post_type'];

	switch ($type) {
		case ('portfolio') :
			require_once ('single-portfolio.php');
			break;
		case ('diller') :
			require_once ('ajax/diller-card.php');
			break;
	}


//	$arg = array(
//		'post_type' =>'portfolio',
//		''
//	);
//
//	$post = query_posts($arg);

	wp_die();

}

add_action('wp_ajax_my_get_posts', 'my_get_posts');
add_action('wp_ajax_nopriv_my_get_posts', 'my_get_posts');


?>
