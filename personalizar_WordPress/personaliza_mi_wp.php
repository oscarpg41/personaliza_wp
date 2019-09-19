<?php
/*****************************************************************************************************
Plugin Name: Personaliza tu WordPress
Description: En este plugin pondremos las funciones para personalizar nuestra instalación de WordPress
Plugin URI: 
Version: 1.0
License: GPL
Author: Oscar Pérez Gómez
Author URI: https://oscarperez.es/
*****************************************************************************************************/

	/* ELIMINA METADATOS INNECESARIOS */
	/* No muestres la versión de tu WordPress */
	remove_action('wp_head', 'wp_generator'); //elimina la version del WordPress
	remove_action('wp_head', 'wlwmanifest_link'); 
	remove_action('wp_head', 'rsd_link');  // rel="EditURI"

	/////////////////////////////////////////////////////////////////////
	// MODIFICACIONES PARA LA PAGINA DE LOGIN DEL PANEL DE ADMINISTRACION
	/////////////////////////////////////////////////////////////////////
	//cambiar el mensaje de error en el login del panel de administración
	function sin_errores(){
		return 'Acceso denegado';
	}
	add_filter( 'login_errors', 'sin_errores' );


	//cambiar el enlace del logo de la página de login de WordPress	
	function la_url_del_logo_de_wpadmin() {
	  return home_url();
	}//end my_login_logo_url()
	add_filter( 'login_headerurl', 'la_url_del_logo_de_wpadmin' );

	function titulo_de_la_url_del_logo_de_wpadmin() {
	  return 'Ayuntamiento Real Sitio de San Ildefonso';
	}//end my_login_logo_url_title()
	add_filter( 'login_headertitle', 'titulo_de_la_url_del_logo_de_wpadmin' );

	

	// Personalizar el look del login del panel de control
	// Añadimos nuestro fichero css a la página del login
	function el_login_de_wpadmin() { 
		wp_register_style('mi_login', WPMU_PLUGIN_URL .'/personalizar/assets/mi_login.css');
		wp_enqueue_style('mi_login');
	}
	add_action( 'login_enqueue_scripts', 'el_login_de_wpadmin' );

	//Quitar cajas del escritorio
	function quita_cajas_escritorio() {
		//if( !current_user_can('manage_options') ) { //para los usuarios no adminsitradores
			remove_action( 'welcome_panel', 'wp_welcome_panel' ); //Quitar el panel de bienvenida de WorPress en el panel de administracion

			remove_meta_box('dashboard_right_now', 'dashboard', 'normal');   // De un vistazo
			remove_meta_box('dashboard_primary', 'dashboard', 'side');   // Noticas del blog de WordPress
			remove_meta_box('dashboard_quick_press', 'dashboard', 'side');  // Borrador rápido
		//}
	} 
	add_action('wp_dashboard_setup', 'quita_cajas_escritorio' );


	//Cambiamos el icono de WordPress que se muestra en la esquina superior izquierda del panel de administración
	//Añadimos nuestro fichero css al panel de administración
	function modificar_CSS() {
		wp_register_style('mi_css', WPMU_PLUGIN_URL .'/personalizar/assets/mi_css.css');
		wp_enqueue_style('mi_css');
	}
	add_action('admin_print_styles', 'modificar_CSS');


	//DESACTIVAR GUTEMBERG
	add_filter('use_block_editor_for_post_type', '__return_false');


	//////////////////////////////////////////////////////////////////////////////////////////
	// Widget para el Dashboard
	// http://www.emenia.es/personaliza-wordpress-admin/

	function custom_dashboard_widget() { 
	?>
		<p><a href="<?php echo get_site_url();?>/wp-admin/post.php?post=15111&action=edit">Agenda Institucional</a></p>
	<?php 
	} 
	
	function my_dashboard_setup_function() {
		add_meta_box( 'my_dashboard_widget', 'Agendas Municipales', 'custom_dashboard_widget', 'dashboard', 'normal', 'high' );
	}
	add_action( 'wp_dashboard_setup', 'my_dashboard_setup_function' );


	/**
	* Register widget area.
	*
	* @since San Ildefonso 1.0
	*
	* @link https://codex.wordpress.org/Function_Reference/register_sidebar
	*/
	function sanildefonso_widgets_init() {
		register_sidebar( array(
			'name'          => __( 'Área de widgets', 'sanildefonso' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Añade los widgets para que aparezcan en la barra lateral.', 'sanildefonso' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
	add_action( 'widgets_init', 'sanildefonso_widgets_init' );


/*
	//Borrar adjuntos al borrar la entrada
	add_action( 'before_delete_post', function( $id ) {
	  $attachments = get_attached_media( '', $id );
	  foreach ($attachments as $attachment) {
	    wp_delete_attachment( $attachment->ID, 'true' );
	  }
	} );

	//Borrar adjuntos del CPT cuyo slug es 'project'
	add_action( 'before_delete_post', 'delete_all_attached_media' );
	function delete_all_attached_media( $post_id ) {
	  if( get_post_type($post_id) == "product" ) {
	    $attachments = get_attached_media( '', $post_id );
	    foreach ($attachments as $attachment) {
	      wp_delete_attachment( $attachment->ID, 'true' );
	    }
	  }
	}
*/



	/* CREAR CUSTOM POST TYPE PARA LAS OFERTAS COMERCIALES */
/*
add_action( 'init', 'productos_cpt_create' );


function productos_cpt_create() {
	$labels = array(
		'name' => __( 'productos'), 
		'singular_name' => __( 'producto' ),
		'add_new' => _x( 'Añadir nuevo', 'producto' ),
		'add_new_item' => __( 'Añadir nuevo producto'),
		'edit_item' => __( 'Editar producto' ),
		'new_item' => __( 'Nuevo producto' ),
		'view_item' => __( 'Ver producto' ),
		'search_items' => __( 'Buscar productos' ),
		'not_found' =>  __( 'No se ha encontrado ningún producto' ),
		'not_found_in_trash' => __( 'No se han encontrado productos en la papelera' ),
		'parent_item_colon' => ''
	);

	// Creamos un array para $args
	$args = array(
		'label' => __('productos'),
		'labels' => $labels,
		'public' => true,
		'can_export' => true,
		'show_ui' => true,
		'_builtin' => false,
		'capability_type' => 'post',        
		'hierarchical' => false,
		'rewrite' => array( "slug" => "productos" , 'with_front' => true),
		'supports'=> array('title','editor','thumbnail','excerpt') ,
		'show_in_nav_menus' => true,
		'taxonomies' => array( 'productos_category'),
		'menu_icon' => 'dashicons-admin-appearance',
		'map_meta_cap' => true
	);
	//register_post_type( 'productos', $args ); // Registramos y a funcionar 
}
*/
?>