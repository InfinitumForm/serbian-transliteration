<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/*
 * Main global classes with active hooks
 * @since     1.0.0
 * @verson    1.0.0
 */
if(!class_exists('Serbian_Transliteration') && class_exists('Serbian_Transliteration_Transliterating')) :
class Serbian_Transliteration extends Serbian_Transliteration_Transliterating{
	/*
	 * Plugin mode
	 * @return        array/string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function plugin_mode($mode=NULL){
		$modes = array(
			'standard'	=> __('Standard mode (content, themes, plugins, translations, menu)', RSTR_NAME),
			'advanced'	=> __('Advanced mode (content, widgets, themes, plugins, translations, menu‚ permalinks, media)', RSTR_NAME),
			'forced'	=> __('Forced transliteration (everything)', RSTR_NAME)
		);
		
		if(RSTR_WOOCOMMERCE) {
			$modes = array_merge($modes, array(
				'woocommerce'	=> __('Only WooCommerce (It bypasses all other transliterations and focuses only on WooCommerce)', RSTR_NAME)
			));
		}
		
		$modes = apply_filters('rstr_plugin_mode', $modes);
		
		if($mode){
			if(isset($modes[$mode])) {
				return $modes[$mode];
			}
			
			return false;
		}
		
		return $modes;
	}
	
	/*
	 * Transliteration mode
	 * @return        array/string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function transliteration_mode($mode=NULL){
		$modes = array(
			'none'			=> __('Transliteration disabled', RSTR_NAME),
			'cyr_to_lat'	=> __('Cyrillic to Latin', RSTR_NAME),
			'lat_to_cyr'	=> __('Latin to Cyrillic', RSTR_NAME)
		);
		
		$modes = apply_filters('rstr_transliteration_mode', $modes);
		
		if($mode && isset($modes[$mode])){
			return $modes[$mode];
		}
		
		return $modes;
	}
	
	/*
	 * Decode content
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function decode($content, $flag=ENT_NOQUOTES){
		if (filter_var($content, FILTER_VALIDATE_URL)) {
			$content = rawurldecode($content);
		} else {
			$content = htmlspecialchars_decode($content, $flag);
			$content = html_entity_decode($content, $flag);
			$content = strtr($content, array_flip(get_html_translation_table(HTML_ENTITIES, $flag)));
		}
		return $content;
	}
	
	/*
	 * Translate from cyr to lat
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function cyr_to_lat($content){
		
		if(empty($content) || is_array($content) || is_object($content) || is_numeric($content) || is_bool($content) || Serbian_Transliteration_Utilities::is_editor()){
			return $content;
		}
		
		$content = $this->decode($content);
		$content = $this->transliteration($content, 'cyr_to_lat');
		$content = $this->fix_attributes($content);

		return $content;
	}
	
	/*
	 * Translate from cyr to lat
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function cyr_to_lat_sanitize($content){
		if(empty($content) || is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)){
			return $content;
		}
		
		$content = $this->cyr_to_lat($content);
		
		$content = strtr($content, apply_filters('rstr_cyr_to_lat_sanitize', array(
			'Ć' => 'C',
			'ć' => 'c',
			'Č' => 'C',
			'č' => 'c',
			'Š' => 'S',
			'š' => 's',
			'Ž' => 'Z',
			'ž' => 'z',
			'Đ' => 'Dj',
			'dj' => 'dj',
			'DŽ' => 'DZ',
			'Dž' => 'Dz',
			'dž' => 'dz'
		)));

		if(function_exists('iconv'))
		{
			if($locale = $this->get_locales( $this->get_locale() )) {
				setlocale(LC_CTYPE, $locale);
			}
			
			if($converted = iconv("UTF-8","ASCII//TRANSLIT", $content)) {
				$content = str_replace(array("\"","'","`","^","~"), '', $converted);
			}
		}
		
		return $content;
	}
	
	/*
	 * Translate from lat to cyr
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function lat_to_cyr($content, $fix_html = true, $fix_diacritics = false){
		if(empty($content) || is_array($content) || is_object($content) || is_numeric($content) || is_bool($content) || Serbian_Transliteration_Utilities::is_editor()){
			return $content;
		}
		
		$content = $this->decode($content);
		
		if($fix_diacritics) {
			$content = $this->fix_diacritics($content);
		}
		
		$content = $this->transliteration($content, 'lat_to_cyr');
		
		if($fix_html){
			$content = $this->fix_cyr_html($content);
			$content = $this->fix_attributes($content);
		}
		
		return $content;
	}
	
	public function fix_diacritics($content){
		if(empty($content) || is_array($content) || is_object($content) || is_numeric($content) || is_bool($content) || Serbian_Transliteration_Utilities::is_editor()){
			return $content;
		}
		
		if($this->get_locale() != 'sr_RS') return $content;
		
		if($search = $this->get_diacritical())
		{
			$new_string = str_replace(
				array('dj', 'Dj', 'DJ', 'sh', 'Sh', 'SH', 'ch', 'Ch', 'CH', 'cs', 'Cs', 'CS', 'dz', 'Dz', 'DZ'),
				array('đ', 'Đ', 'Đ', 'š', 'Š', 'Š', 'č', 'Č', 'Č', 'ć', 'Ć', 'Ć', 'dž', 'Dž', 'DŽ'),
				$content
			);
			
			$skip_words = $this->get_skip_words();
			$skip_words = array_map((function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower'), $skip_words);
			
			$search = array_map((function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower'), $search);
			
			$arr = explode(' ', $new_string);
			$arr = array_filter($arr);
			
			$arr_origin = explode(' ', $content);
			$arr_origin = array_filter($arr_origin);
			
			if(!empty($arr))
			{
				$words = array();
				foreach($arr as $i=>$word)
				{					
					$word_search = (function_exists('mb_strtolower') ? mb_strtolower($word, 'UTF-8') : strtolower($word));
					$word_search = preg_replace('/[.,?!-*_#$]+/i','',$word_search);
					
					$word_search_origin = (function_exists('mb_strtolower') ? mb_strtolower($arr_origin[$i]) : strtolower($arr_origin[$i]));
					$word_search_origin = preg_replace('/[.,?!-*_#$]+/i','',$word_search_origin);
					
					if(in_array($word_search_origin, $skip_words)){
						$words[]=$arr_origin[$i];
						continue;
					}
					
					if(in_array($word_search, $search)) {
						if(ctype_upper($word) || preg_match('~^[A-ZŠĐČĆŽ]+$~u', $word)){
							$words[]=mb_strtoupper($search[array_search($word_search, $search)], 'UTF-8');
						} else if( preg_match('~^\p{Lu}~u', $word) ) {
							$ucfirst = $search[array_search($word_search, $search)];
							$firstChar = mb_substr($ucfirst, 0, 1, 'UTF-8');
							$then = mb_substr($ucfirst, 1, NULL, 'UTF-8');
							$words[]=mb_strtoupper($firstChar, 'UTF-8') . $then;
						} else {
							$words[]=$word;
						}
					} else {
						$words[]=$word;
					}
				}
				
				$new_string = $skip_words = $search = $arr = $arr_origin = NULL;
				
				if(!empty($words)) return join(' ', $words);
			}
		}
		
		return $content;
	}
	
	/*
	 * Automatic transliteration
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function transliterate_text($content, $type = NULL, $fix_html = true){
		if(empty($content) || is_array($content) || is_object($content) || is_numeric($content) || is_bool($content) || Serbian_Transliteration_Utilities::is_editor()){
			return $content;
		}
		
		if(empty($type) || is_bool($type)){
			$type = Serbian_Transliteration_Utilities::get_current_script();
		}
		
		$content = $this->decode($content);
		$content = $this->transliteration($content, $type);
		
		if(($type == 'lat_to_cyr') && $fix_html){
			$content = $this->fix_cyr_html($content);
			$content = $this->fix_attributes($content);
		}
		
		return $content;
	}
	
	/*
	 * Transliterate associative array or strings
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public function transliterate_objects($array, $type = NULL, $fix_html = true)
	{
		// First setup all properly
		if(empty($array) || Serbian_Transliteration_Utilities::is_editor()) {
			return $array;
		}
		
		if(empty($type) || is_bool($type)) {
			$type = Serbian_Transliteration_Utilities::get_current_script();
		}
		
		$return = '';
		// Infinity loop... Until end.
		if( is_array($array) )
		{
			$data = array();
			
			foreach($array as $key => $val)
			{
				$data[$key] = $this->transliterate_objects($val, $fix_html);
			}
			
			$return = $data;
		}
		// transliterate string
		else
		{
			if(
				is_int($array) 
				|| is_float($array) 
				|| is_numeric($array)
				|| is_file($array) 
				|| is_bool($array) 
				|| is_object($array) 
				|| is_link($array)
				|| filter_var($array, FILTER_VALIDATE_URL)
				|| filter_var($array, FILTER_VALIDATE_EMAIL)
			) {
				$return = $array;
			} else {
				$return = $this->transliterate_text($array, $type, $fix_html);
			}
		}

		return $return;
	}
	
	/*
	 * Check is already cyrillic
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function already_cyrillic(){
        return in_array($this->get_locale(), apply_filters('rstr_already_cyrillic', array('sr_RS','mk_MK', 'bel', 'bg_BG', 'ru_RU', 'sah', 'uk', 'kk', 'el'))) !== false;
	}
	
	/*
	 * Check is latin letters
	 * @return        boolean
	 * @author        Ivijan-Stefan Stipic
	*/
	public function is_lat($c){
		return preg_match_all('/[\p{Latin}]+/ui', strip_tags($c, ''));
	}
	
	/*
	 * Check is cyrillic letters
	 * @return        boolean
	 * @author        Ivijan-Stefan Stipic
	*/
	public function is_cyr($c){
		return preg_match_all('/[\p{Cyrillic}]+/ui', strip_tags($c, ''));
	}
	
	
	/*
	 * All available HTML tags
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public function html_tags() {
		global $rstr_cache;
		
		if($html_tags = $rstr_cache->get('html-tags')){
			return apply_filters('rstr_html_tags_collected', $html_tags);
		}
		
		$html_tags = get_option(RSTR_NAME . '-html-tags');
		
		if( empty($html_tags) )
		{		
			$tags = apply_filters('rstr/html/tags',  '!DOCTYPE,a,abbr,acronym,address,applet,area,article,aside,audio,b,base,basefont,bdi,bdo,big,blockquote,body,br,button,canvas,caption,center,cite,code,col,colgroup,data,details,dd,del,details,dfn,dialog,dir,div,dl,dt,em,embed,fieldset,figcaption,figure,font,footer,form,frame,frameset,h1,h2,h3,h4,h5,h6,head,header,hr,html,i,iframe,img,input,ins,kbd,label,legend,li,link,main,map,mark,meta,master,nav,noframes,noscript,object,ol,optgroup,option,output,p,param,picture,pre,progress,q,rp,rt,ruby,s,samp,script,section,select,small,source,span,strike,strong,style,sub,summary,sup,svg,table,tbody,td,template,textarea,tfoot,th,thead,time,title,tr,track,tt,u,ul,var,video,wbr');
			$tags_latin = explode(',', $tags);
			$tags_latin = array_map('trim', $tags_latin);
			$tags_latin = array_filter($tags_latin);
			$tags_latin = apply_filters('rstr_html_tags_lat', $tags_latin);
			
			$tags_cyr = $this->lat_to_cyr($tags, false);
			$tags_cyr = explode(',', $tags_cyr);
			$tags_cyr = array_map('trim', $tags_cyr);
			$tags_cyr = array_filter($tags_cyr);
			$tags_cyr = apply_filters('rstr_html_tags_cyr', $tags_cyr);
			
			$html_tags = (object)array(
				'cyr' => $tags_cyr,
				'lat' => $tags_latin
			);
			
			if(!empty($html_tags)) {
				add_option(RSTR_NAME . '-html-tags', $html_tags, '', true);
			}
		}
		
		return apply_filters('rstr_html_tags_collected', $rstr_cache->set('html-tags', $html_tags));
	}
	
	/*
	 * Fix html codes
	 * @return        string/html
	 * @author        Ivijan-Stefan Stipic
	*/
	public function fix_cyr_html($content){
		$content = htmlspecialchars_decode($content);

		// Fix HTML entities
		$content = preg_replace_callback ('/\&([\x{0400}-\x{04FF}qwy0-9α-ωΑ-Ω]+)\;/iu', function($m){
			return '&' . $this->cyr_to_lat($m[1]) . ';';
		}, $content);


		// Fix HTML tags
		$tags = $this->html_tags();
		
		$tag_replace = array(
			'<'.$this->lat_to_cyr('img', false).' ' => '<img ',
			'<'.$this->lat_to_cyr('br', false).'>' => '<br>',
			'<'.$this->lat_to_cyr('br', false).' ' => '<br ',
			'<'.$this->lat_to_cyr('hr', false).'>' => '<hr>',
			'<'.$this->lat_to_cyr('hr', false).' ' => '<hr ',
			// Fix internal tags
			'{'.$this->lat_to_cyr('cyr_to_lat', false).'}' => '{cyr_to_lat}',
			'{/'.$this->lat_to_cyr('cyr_to_lat', false).'}' => '{/cyr_to_lat}',
			'{'.$this->lat_to_cyr('lat_to_cyr', false).'}' => '{lat_to_cyr}',
			'{/'.$this->lat_to_cyr('lat_to_cyr', false).'}' => '{/lat_to_cyr}',
			'{'.$this->lat_to_cyr('rstr_skip', false).'}' => '{rstr_skip}',
			'{/'.$this->lat_to_cyr('rstr_skip', false).'}' => '{/rstr_skip}'
		);
		
		foreach($tags->lat as $i=>$tag){
			$tag_cyr = $tags->cyr[$i];
			
			$tag_replace['<' . $tag_cyr] = '<' . $tag;
			$tag_replace['</' . $tag_cyr . '>'] = '</' . $tag . '>';
		}
		$tags = NULL;

		// Fix some characters
		$content = strtr($content, apply_filters('rstr/html/tags/replace', array_merge($tag_replace, array(
			$this->lat_to_cyr('href', false) => 'href',
			$this->lat_to_cyr('src', false) => 'src',
			'&'.$this->lat_to_cyr('scaron', false).';' => 'ш',
			'&'.$this->lat_to_cyr('Scaron', false).';' => 'Ш'
		)), $tag_replace));
		
		$tag_replace = NULL;
		
		$lastPos = 0;
		$positions = array();
		
/*
		// Fix tags on the old way
		while (($lastPos = mb_strpos($content, '<', $lastPos, 'UTF-8')) !== false) {
			$positions[] = $lastPos;
			$lastPos = $lastPos + mb_strlen('<', 'UTF-8');
		}

		foreach ($positions as $position) {
			if(mb_strpos($content, '>', 0, 'UTF-8') !== false) {
				$end   = mb_strpos($content, '>', $position, 'UTF-8') - $position;
				$tag  = mb_substr($content, $position, $end, 'UTF-8');
				$tag_lat = $this->cyr_to_lat($tag);
				$content = str_replace($tag, $tag_lat, $content);
			}
		}
		*/
		/* Fix HTML attributes */
		$content = preg_replace_callback ('/\s([\x{0400}-\x{04FF}qwy0-9α-ωΑ-Ω\-]+)(=["\'])/iu', function($m){
			return ' ' . $this->cyr_to_lat($m[1]) . $m[2];
		}, $content);
		$content = preg_replace_callback ('/\s(class|id|rel|selected|type|style|loading|srcset|sizes|lang|name)\s?=\s?"(.*?)"/iu', function($m){
			return sprintf(' %1$s="%2$s"', $m[1], $this->cyr_to_lat($m[2]));
		}, $content);
		$content = preg_replace_callback ('/\s(class|id|rel|selected|type|style|loading|srcset|sizes|lang|name)\s?=\s?\'(.*?)\'/iu', function($m){
			return sprintf(' %1$s=\'%2$s\'', $m[1], $this->cyr_to_lat($m[2]));
		}, $content);
		
		// Fix attributes with doublequote
		$content = preg_replace_callback ('/('.$this->lat_to_cyr('title|alt|src|data', false).'-([\x{0400}-\x{04FF}qwy0-9a-zA-Zα-ωΑ-Ω\/\=\"\'\_\-\s\.\;\,\!\?\*\:\#\$\%\&\(\)\[\]\+\@\€]+))\s?=\s?"(.*?)"/iu', function($m){
			return sprintf('%1$s="%2$s"', $this->cyr_to_lat($m[1]), esc_attr($this->lat_to_cyr($m[3], false)));
		}, $content);
		// Fix attributes with singlequote
		$content = preg_replace_callback ('/('.$this->lat_to_cyr('title|alt|src|data', false).'-([\x{0400}-\x{04FF}qwy0-9a-zA-Zα-ωΑ-Ω\/\=\"\'\_\-\s\.\;\,\!\?\*\:\#\$\%\&\(\)\[\]\+\@\€]+))\s?=\s?\'(.*?)\'/iu', function($m){
			return sprintf('%1$s="%2$s"', $this->cyr_to_lat($m[1]), esc_attr($this->lat_to_cyr($m[3], false)));
		}, $content);
		
		// Fix data attributes
		$content = preg_replace_callback ('/(data-[a-z0-9\_\-]+)\s?=\s?"(.*?)"/iu', function($m){
			return sprintf('%1$s="%2$s"', $m[1], htmlspecialchars_decode($this->cyr_to_lat($m[2])));
		}, $content);
		
		// Fix open tags
		$content = preg_replace_callback ('/(<[\x{0400}-\x{04FF}qwy0-9a-zA-Zα-ωΑ-Ω\/\=\"\'\_\-\s\.\;\,\!\?\*\:\#\$\%\&\(\)\[\]\+\@\€]+>)/iu', function($m){
			return $this->cyr_to_lat($m[1]);
		}, $content);
		
		// Fix closed tags
		$content = preg_replace_callback ('/(<\/[\x{0400}-\x{04FF}qwy0-9a-zA-Zα-ωΑ-Ω]+>)/iu', function($m){
			return $this->cyr_to_lat($m[1]);
		}, $content);
		
		// Fix JavaScript
		$content = preg_replace_callback('/(?=<script(.*?)>)(.*?)(?<=<\/script>)/s', function($m) {
			return $this->cyr_to_lat($m[2]);
		}, $content);
		
		// Fix CSS
		$content = preg_replace_callback('/(?=<style(.*?)>)(.*?)(?<=<\/style>)/s', function($m) {
			return $this->cyr_to_lat($m[2]);
		}, $content);
		
		// Fix email
		$content = preg_replace_callback ('/(([\x{0400}-\x{04FF}qwy0-9α-ωΑ-Ω\_\-\.]+)@([\x{0400}-\x{04FF}0-9α-ωΑ-Ω\_\-\.]+)\.([\x{0400}-\x{04FF}0-9α-ωΑ-Ω]{3,10}))/iu', function($m){
			return $this->cyr_to_lat($m[1]);
		}, $content);

		// Fix URL
		$content = preg_replace_callback ('/(([\x{0400}-\x{04FF}α-ωΑ-Ω]{4,5}):\/{2}([\x{0400}-\x{04FF}qwy0-9α-ωΑ-Ω\_\-\.]+)\.([\x{0400}-\x{04FF}qwy0-9α-ωΑ-Ω]{3,10})(.*?)($|\n|\s|\r|\"\'\.\;\,\:\)\]\>))/iu', function($m){
			return $this->cyr_to_lat($m[1]);
		}, $content);
		$content = preg_replace_callback ('/"('.$this->lat_to_cyr('https', false).'?:\/\/.*?)"/iu', function($m){
			return $this->cyr_to_lat($m[1]);
		}, $content);
	
		// Fix mailto link
		$content = preg_replace_callback ('/"('.$this->lat_to_cyr('mailto', false).':\/\/.*?)"/iu', function($m){
			return $this->cyr_to_lat($m[1]);
		}, $content);
		
		// Fix attributes with doublequote
		$content = preg_replace_callback ('/(title|alt|data-(title|alt))\s?=\s?"(.*?)"/iu', function($m){
			return sprintf('%1$s="%2$s"', $m[1], esc_attr($this->lat_to_cyr($m[3], false)));
		}, $content);
		
		// Fix attributes with single quote
		$content = preg_replace_callback ('/(title|alt|data-(title|alt))\s?=\s?\'(.*?)\'/iu', function($m){
			return sprintf('%1$s=\'%2$s\'', $m[1], esc_attr($this->lat_to_cyr($m[3], false)));
		}, $content);
		
		return $content;
	}
	
	/*
	 * Prefiler for the upload
	*/
	public function upload_prefilter ($file) {
		$file['name']= $this->cyr_to_lat_sanitize($file['name']);
		return $file;
	}
	
	/*
	 * Sanitize file name
	*/
	public function sanitize_file_name($filename){
		return $this->cyr_to_lat_sanitize($filename);
	}
	
	/*
	 * Force permalink to latin
	*/
	public function force_permalink_to_latin ($permalink) {
		$permalink = rawurldecode($permalink);
		$permalink= $this->cyr_to_lat_sanitize($permalink);
		return $permalink;
	}
	
	/*
	 * Force permalink to latin on the save
	*/
	public function force_permalink_to_latin_on_save ($data, $postarr) {
		$data['post_name'] = rawurldecode($data['post_name']);
		$data['post_name'] = $this->cyr_to_lat_sanitize( $data['post_name'] );
		return $data;
	}
	
	/* 
	* Register language script
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function attachment_taxonomies() {
		if(!taxonomy_exists('rstr-script'))
		{
			register_taxonomy( 'rstr-script', array( 'attachment' ), array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => _x( 'Script', 'Language script', RSTR_NAME ),
					'singular_name'     => _x( 'Script', 'Language script', RSTR_NAME ),
					'search_items'      => __( 'Search by Script', RSTR_NAME ),
					'all_items'         => __( 'All Scripts', RSTR_NAME ),
					'parent_item'       => __( 'Parent Script', RSTR_NAME ),
					'parent_item_colon' => __( 'Parent Script:', RSTR_NAME ),
					'edit_item'         => __( 'Edit Script', RSTR_NAME ),
					'update_item'       => __( 'Update Script', RSTR_NAME ),
					'add_new_item'      => __( 'Add New Script', RSTR_NAME ),
					'new_item_name'     => __( 'New Script Name', RSTR_NAME ),
					'menu_name'         => __( 'Script', RSTR_NAME ),
				),
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'publicly_queryable'=> false,
				'show_in_menu'		=> false,
				'show_in_nav_menus'	=> false,
				'show_in_rest'		=> false,
				'show_tagcloud'		=> false,
				'show_in_quick_edit'=> false
			) );
		}
	}
	
	/* 
	* Set current transliteration script
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public function set_current_script(){		
		if(isset($_REQUEST[$this->get_option('url-selector', 'rstr')]))
		{
			if(in_array($_REQUEST[$this->get_option('url-selector', 'rstr')], apply_filters('rstr/allowed_script', array('cyr', 'lat')), true) !== false)
			{
				$this->setcookie($_REQUEST[$this->get_option('url-selector', 'rstr')]);
				$parse_url = Serbian_Transliteration_Utilities::parse_url();
				$url = remove_query_arg($this->get_option('url-selector', 'rstr'), $parse_url['url']);
				
				if($this->get_option('cache-support', 'yes') == 'yes') {
					$url = add_query_arg('_rstr_nocache', uniqid($this->get_option('url-selector', 'rstr') . mt_rand(100,999)), $url);
				}

				if(wp_safe_redirect($url)) {
					if(function_exists('nocache_headers')) nocache_headers();
					exit;
				}
			}
		}
		return false;
	}
	
	/*
	 * Set cookie
	 * @since     1.0.10
	 * @verson    1.0.0
	*/
	public function setcookie ($val){
		if( !headers_sent() ) {
			global $rstr_cache;
			
			setcookie( 'rstr_script', $val, (time()+YEAR_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN );
			$rstr_cache->delete('get_current_script');
			
			if($this->get_option('cache-support', 'yes') == 'yes') {
				$this->cache_flush();
			}
		}
	}
	
	/*
	 * Flush Cache
	 * @verson    1.0.1
	*/
	public function cache_flush () {
		global $post, $user, $w3_plugin_totalcache;
		
		// Standard cache
		header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		// Set nocache headers
		if(function_exists('nocache_headers')) {
			nocache_headers();
		}
		
		// Flush WP cache
		if (function_exists('wp_cache_flush')) {
			wp_cache_flush();
		}
		
		// W3 Total Cache
		if (function_exists('w3tc_flush_all')) {
			w3tc_flush_all();
		} else if( $w3_plugin_totalcache ) {
			$w3_plugin_totalcache->flush_all();
		}
		
		// WP Fastest Cache
		if (function_exists('wpfc_clear_all_cache')) {
			wpfc_clear_all_cache(true);
		}
		
		// WP Rocket
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}
		
		// WP Super Cache
		if(function_exists( 'prune_super_cache' ) && function_exists( 'get_supercache_dir' )) {
			prune_super_cache( get_supercache_dir(), true );
		}
		
		// Cache Enabler.
		if (function_exists( 'clear_site_cache' )) {
			clear_site_cache();
		}
		
		// Clean stanrad WP cache
		if($post && function_exists('clean_post_cache')) {
			clean_post_cache( $post );
		}
		
		// Comet Cache
		if(class_exists('comet_cache') && method_exists('comet_cache', 'clear')) {
			comet_cache::clear();
		}
		
		// Clean user cache
		if($user && function_exists('clean_user_cache')) {
			clean_user_cache( $user );
		}
	}
	
	
	/*
	 * Get current URL
	 * @since     1.0.9
	 * @verson    1.0.0
	*/
	public function get_current_url()
	{
		global $wp;
		return add_query_arg( array(), home_url( $wp->request ) );
	}
	
	/*
	 * Fix attributes
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function fix_attributes($content){
		
		// Fix bad attribute space
		$content = preg_replace('/"([a-z-_]+\s?=)/i', ' $1', $content);
		
		// Fix entity
		$content = preg_replace_callback('/(data-[a-z-_]+\s?=\s?")(.*?)("(\s|\>|\/))/s', function($m) {
				return $m[1] . htmlentities($m[2], ENT_QUOTES | ENT_IGNORE, 'UTF-8') . $m[3];
		}, $content);
		
		$content = preg_replace_callback('/(data-[a-z-_]+\s?=\s?\')(.*?)(\'(\s|\>|\/))/s', function($m) {
				return $m[1] . htmlentities($m[2], ENT_QUOTES | ENT_IGNORE, 'UTF-8') . $m[3];
		}, $content);
		
		$content = preg_replace_callback('/(href\s?=\s?"#)(.*?)("(\s|\>|\/))/s', function($m) {
				return $m[1] . urlencode($m[2]) . $m[3];
		}, $content);
		
		$content = preg_replace_callback('/(href\s?=\s?\'#)(.*?)(\'(\s|\>|\/))/s', function($m) {
				return $m[1] . urlencode($m[2]) . $m[3];
		}, $content);
		
		// Fix CSS
		$content = preg_replace_callback('/(?=<style(.*?)>)(.*?)(?<=<\/style>)/s', function($m) {
				return $this->decode($m[2]);
		}, $content);
		
		// Fix scripts
		$content = preg_replace_callback('/(?=<script(.*?)>)(.*?)(?<=<\/script>)/s', function($m) {
				return $this->decode($m[2]);
		}, $content);
		
		$content = preg_replace_callback('/\\{1,5}&([a-zA-Z]+);/s', function($m) {
				return html_entity_decode('&' . $m[1] . ';');
		}, $content);
		
		$content = stripslashes($content);
		
		// Fix data attributes
		$content = preg_replace_callback ('/(data-[a-z0-9\_\-]+)\s?=\s?"(.*?)"/iu', function($m){
			return sprintf('%1$s="%2$s"', $m[1], htmlspecialchars_decode($m[2]));
		}, $content);
		
		// Fix emails
		$content = preg_replace_callback('/([a-z0-9\p{Cyrillic}_\-\.]+@[a-z0-9\p{Cyrillic}_\-\.]+\.[wqyx0-9\p{Cyrillic}_\-\.]+)/siu', function ($m) {
			return $this->cyr_to_lat($m[1]);
		}, $content);
		
		return $content;
	}
	
	public function mode($options=false){
		
		if(empty($options)) $options = get_rstr_option();
		if(is_null($options)) return false;
		
		$mode = ucfirst($options['mode']);
		$class_require = "Serbian_Transliteration_Mode_{$mode}";
		$path_require = "mode/{$mode}";
		
		$path = apply_filters('rstr/mode/path', RSTR_INC, $class_require, $options['mode']);
		
		if(!class_exists($class_require))
		{
			if(file_exists($path . "/{$path_require}.php"))
			{
				include_once $path . "/{$path_require}.php";
				if(class_exists($class_require)){
					return $class_require;
				} else {
					throw new Exception(sprintf('The class "$1%s" does not exist or is not correctly defined on the line %2%d', $mode_class, (__LINE__-2)));
				}
			} else {
				throw new Exception(sprintf('The file at location "$1%s" does not exist or has a permissions problem.', $path . "/{$path_require}.php"));
			}
		}
		else
		{
			return $class_require;
		}
		
		// Clear memory
		$class_require = $path_require = $path = $mode = NULL;
		
		return false;
	}
	
	/* 
	* Get plugin option
	* @verson    1.0.0
	*/
	public function get_option($name = false, $default = NULL) {
		return get_rstr_option($name, $default);
	}
	
	/* 
	* Get all plugin options
	* @verson    1.0.0
	*/
	public function get_options() {
		return get_rstr_option();
	}
	
	/*
	 * Hook for register_uninstall_hook()
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function register_uninstall_hook($function){
		return register_uninstall_hook( RSTR_FILE, $function );
	}
	
	/*
	 * Hook for register_deactivation_hook()
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function register_deactivation_hook($function){
		return register_deactivation_hook( RSTR_FILE, $function );
	}
	
	/*
	 * Hook for register_activation_hook()
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function register_activation_hook($function){
		return register_activation_hook( RSTR_FILE, $function );
	}
	/* 
	 * Hook for add_action()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1){
		if(!is_array($function_to_add)){
			$function_to_add = array(&$this, $function_to_add);
		}
		return add_action( (string)$tag, $function_to_add, (int)$priority, (int)$accepted_args );
	}
	
	/* 
	 * Hook for remove_action()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function remove_action($tag, $function_to_remove, $priority = 10){
		if(!is_array($function_to_remove)){
			$function_to_remove = array(&$this, $function_to_remove);
		}
		return remove_action( $tag, $function_to_remove, $priority );
	}
	
	/* 
	 * Hook for add_filter()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1){
		if(!is_array($function_to_add)){
			$function_to_add = array(&$this, $function_to_add);
		}
		return add_filter( (string)$tag, $function_to_add, (int)$priority, (int)$accepted_args );
	}
	
	/* 
	 * Hook for remove_filter()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function remove_filter($tag, $function_to_remove, $priority = 10){
		if(!is_array($function_to_remove)){
			$function_to_remove = array(&$this, $function_to_remove);
		}
		return remove_filter( (string)$tag, $function_to_remove, (int)$priority );
	}
	
	/* 
	 * Hook for add_shortcode()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_shortcode($tag, $function_to_add){
		if(!is_array($function_to_add)){
			$function_to_add = array(&$this, $function_to_add);
		}
		if(!shortcode_exists($tag)) {
			return add_shortcode( $tag, $function_to_add );
		}
		
		return false;
	}
	
	/* 
	 * Hook for add_options_page()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_options_page($page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null){
		if(!is_array($function)){
			$function = array(&$this, $function);
		}
		return add_options_page($page_title, $menu_title, $capability, $menu_slug, $function, $position);
	}
	
	/* 
	 * Hook for add_settings_section()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_settings_section($id, $title, $callback, $page){
		if(!is_array($callback)){
			$callback = array(&$this, $callback);
		}
		return add_settings_section($id, $title, $callback, $page);
	}
	
	/* 
	 * Hook for register_setting()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function register_setting($option_group, $option_name, $args = array()){
		if(!is_array($args) && is_callable($args)){
			$args = array(&$this, $args);
		}
		return register_setting($option_group, $option_name, $args);
	}
	
	/* 
	 * Hook for add_settings_field()
	 * @author        Ivijan-Stefan Stipic
	*/
	public function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = array()){
		if(!is_array($callback)){
			$callback = array(&$this, $callback);
		}
		return add_settings_field($id, $title, $callback, $page, $section, $args);
	}
	
	/* 
	* Instance
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function __instance()
	{
		global $rstr_cache;
		$class = self::class;
		$instance = $rstr_cache->get($class);
		if ( !$instance ) {
			$instance = $rstr_cache->set($class, new self());
		}
		return $instance;
	}
}
endif;