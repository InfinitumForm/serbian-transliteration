<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Transliteration tools
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @autor             Ivijan-Stefan Stipic
 */
if(!class_exists('Serbian_Transliteration_Menu')) :
class Serbian_Transliteration_Menu extends Serbian_Transliteration {
	
	function __construct(){
		$this->add_action( 'admin_head-nav-menus.php', 'admin_nav_menu' );
		$this->add_filter( 'wp_setup_nav_menu_item', 'menu_setup' );
		$this->add_filter( 'wp_nav_menu_objects', 'menu_objects' );
	}

	/* Registers Login/Logout/Register Links Metabox */
	function admin_nav_menu() {
		add_meta_box( 'transliteration_menu', __( 'Transliteration', RSTR_NAME ), array( $this, 'admin_nav_menu_callback' ), 'nav-menus', 'side', 'default' );
	}
	
	/* Displays Login/Logout/Register Links Metabox */
    function admin_nav_menu_callback(){

		global $nav_menu_selected_id;
		
		$elems = array(
			'#transliteration-lat#'	      => __( 'Latin', RSTR_NAME ),
			'#transliteration-cyr#'	    => __( 'Cyrillic', RSTR_NAME ),
			'#transliteration-latcyr#' => __( 'Latin', RSTR_NAME ) . ' | ' . __( 'Cyrillic', RSTR_NAME )
		);
		$logitems = array(
			'db_id' => 0,
			'object' => 'bawlog',
			'object_id',
			'menu_item_parent' => 0,
			'type' => 'custom',
			'title',
			'url',
			'target' => '',
			'attr_title' => '',
			'classes' => array(),
			'xfn' => '',
		);
		
		$elems_obj = array();
		foreach ( $elems as $value => $title ) {
			$elems_obj[ $title ] 		= (object) $logitems;
			$elems_obj[ $title ]->object_id	= esc_attr( $value );
			$elems_obj[ $title ]->title	= esc_attr( $title );
			$elems_obj[ $title ]->url	= esc_attr( $value );
		}
		
		$walker = new Walker_Nav_Menu_Checklist( array() );
		?>
		<div id="transliteration-links" class="transliterationlinksdiv">
		
            <div id="tabs-panel-transliteration-links-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
              <ul id="transliteration-linkschecklist" class="list:transliteration-links categorychecklist form-no-clear">
                <?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $elems_obj ), 0, (object) array( 'walker' => $walker ) ); ?>
              </ul>
            </div>
            
            <p class="button-controls">
              <span class="list-controls hide-if-no-js">
                <a href="javascript:void(0);" class="help" onclick="jQuery( '#transliteration-menu-help' ).toggle();"><?php _e( 'Help', RSTR_NAME ); ?></a>
                <span class="hide-if-js" id="transliteration-menu-help"><br /><a name="transliteration-menu-help"></a>
                  <?php
                  echo '•' . esc_html__( 'To insert language script selector just add a relative link after the link\'s keyword, example :', RSTR_NAME ) . ' <br /><code>#transliteration-latcyr#</code>.';
                  echo '<br /><br />•' . esc_html__( 'You can also use', RSTR_NAME ) . ' <code>#transliteration-lat#</code> ' . esc_html__( 'for change to Latin or use', RSTR_NAME ) . ' <code>#transliteration-cyr#</code>' . esc_html__( 'for change to Cyrillic', RSTR_NAME ) . '.';
                    ?>
                  </span>
                </span>
            
                <span class="add-to-menu">
                  <input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', RSTR_NAME ); ?>" name="add-transliteration-links-menu-item" id="submit-transliteration-links" />
                  <span class="spinner"></span>
                </span>
              </p>
		
		</div>
		<?php

      }
	
	/**
	* Show Login || Logout Menu item for front end.
	*
	* @since 1.0.0
	* @param object $menu_item The menu item object.
	*/
	function transliteration_setup_title( $title, $options ) {
	
		$titles = explode( '|', $title );
		
		if(is_array($titles)) {
			$titles = array_map('trim', $titles);
		}
		
		if ( $options->active == 'cyr' ) {
			return esc_html( isset( $titles[0] ) ? $titles[0] : $title );
		} else {
			return esc_html( isset( $titles[1] ) ? $titles[1] : $title );
		}
	}
	
	/**
	* Filters a navigation menu item object. Decorates a menu item object with the shared navigation menu item properties on front end.
	*
	* @since 1.2.0
	* @param object $menu_item The menu item object.
	*/
	function menu_setup( $item ) {
	
		global $pagenow;
		
		if ( $pagenow != 'nav-menus.php' && ! defined( 'DOING_AJAX' ) && isset( $item->url ) && strstr( $item->url, '#transliteration' ) != '' ) {
			$url = get_current_url();
			
			$get_script = (isset($_COOKIE['rstr_script']) && in_array($_COOKIE['rstr_script'], apply_filters('rstr/allowed_script', array('cyr', 'lat')), true) !== false ? $_COOKIE['rstr_script'] : 'none');
			
			$options = (object)array(
				'active'	=> $get_script,
				'cyr'		=> add_query_arg(array(
								'rstr' => 'cyr',
							//	'cache' => uniqid()
							), $url),
				'lat'		=> add_query_arg(array(
								'rstr' => 'lat',
							//	'cache' => uniqid()
							), $url)
			);
			
			$item_url = substr( $item->url, 0, strpos( $item->url, '#', 1 ) ) . '#';
			$item_redirect = str_replace( $item_url, '', $item->url );
			
			if ( $item_redirect == '%current-page%' ) {
				$item_redirect = $_SERVER['REQUEST_URI'];
			}
			
			switch ( $item_url ) {
				case '#transliteration-latcyr#' :
					$item_redirect = explode( '|', $item_redirect );
					
					if ( count( $item_redirect ) != 2 ) {
						$item_redirect = array_map('trim', $item_redirect);
						$item_redirect[1] = $item_redirect[0];
					}
					
					if ( $options->active == 'cyr' ) {
						$item->url = $options->lat;
					} else {
						$item->url = $options->cyr;
					}
					
					$item->title = $this->transliteration_setup_title( $item->title, $options ) ;
				break;
				
				case '#transliteration-lat#' :
					if ( $options->active == 'cyr' ) {
						$item->url = $options->lat;
						return $item;
					}
				break;
				
				case '#transliteration-cyr#' :
					if ( $options->active == 'lat' ) {
						$item->url = $options->cyr;
						return $item;
					}
				break;
			
			}
			
			$item->url = esc_url( $item->url );
		}
		return $item;
	}
	
	function menu_objects( $sorted_menu_items ) {
		foreach ( $sorted_menu_items as $menu => $item ) {
			if ( strstr( $item->url, '#transliteration' ) != '' ) {
				unset( $sorted_menu_items[ $menu ] );
			}
		}
		return $sorted_menu_items;
	}	
}
endif;