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
	public function decode($content){
		if (filter_var($content, FILTER_VALIDATE_URL)) {
			$content = rawurldecode($content);
		} else {
			$content = htmlspecialchars_decode($content, ENT_NOQUOTES);
			$content = html_entity_decode($content, ENT_NOQUOTES);
			$content = strtr($content, array_flip(get_html_translation_table(HTML_ENTITIES, ENT_NOQUOTES)));
		}
		return $content;
	}
	
	/*
	 * Translate from cyr to lat
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function cyr_to_lat($content){
		
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;
		
		$content = $this->decode($content);
		
		if(method_exists('Serbian_Transliteration_Transliterating', $this->get_locale()))
		{
			$locale = $this->get_locale();
			$content = parent::$locale($content);
			// Filter special names from the list
			foreach($this->lat_exclude_list() as $item){
				$content = str_replace(parent::$locale($item), $item, $content);
			}
		}
		else
		{
			$content = str_replace($this->cyr(), $this->lat(), $content);
			// Filter special names from the list
			foreach($this->lat_exclude_list() as $item){
				$content = str_replace(str_replace($this->cyr(), $this->lat(), $item), $item, $content);
			}
		}
		
		$content = $this->fix_attributes($content);

		// Fix data attributes
		$content = preg_replace_callback ('/(data-[a-z0-9\_\-]+)\s?=\s?"(.*?)"/iu', function($m){
			return sprintf('%1$s="%2$s"', $m[1], htmlspecialchars_decode($m[2]));
		}, $content);

		return $content;
	}
	
	/*
	 * Translate from cyr to lat
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function cyr_to_lat_sanitize($content){
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;
		
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
		
		// Filter special names from the list
		foreach($this->lat_exclude_list() as $item){
			$content = str_replace(str_replace($this->cyr(), $this->lat(), $item), $item, $content);
		}
		
		return $content;
	}
	
	/*
	 * Translate from lat to cyr
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function lat_to_cyr($content, $fix_html = true, $fix_diacritics = false){
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;
		
		$content = $this->decode($content);
		
		if($fix_diacritics) {
			$content = $this->fix_diacritics($content);
		}
		
		if(method_exists('Serbian_Transliteration_Transliterating', $this->get_locale()))
		{
			$locale = $this->get_locale();
			$content = parent::$locale($content, 'lat_to_cyr');
			// Filter special names from the list
			foreach($this->cyr_exclude_list() as $item){
				$content = str_replace(parent::$locale($item, 'lat_to_cyr'), $item, $content);
			}
		}
		else
		{
			$content = str_replace($this->lat(), $this->cyr(), $content);
			// Filter special names from the list
			foreach($this->cyr_exclude_list() as $item){
				$content = str_replace(str_replace($this->lat(), $this->cyr(), $item), $item, $content);
			}
		}
		
		if($fix_html){
			$content = $this->fix_cyr_html($content);
			$content = $this->fix_attributes($content);
		}
		
		return $content;
	}
	
	public function fix_diacritics($content){
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;
		
		if($this->get_locale() != 'sr_RS') return $content;
		
		if($search = $this->get_diacritical())
		{
			$new_string = str_replace(
				array('dj', 'Dj', 'DJ', 'sh', 'Sh', 'SH', 'ch', 'Ch', 'CH', 'cs', 'Cs', 'CS', 'dz', 'Dz', 'DZ'),
				array('đ', 'Đ', 'Đ', 'š', 'Š', 'Š', 'č', 'Č', 'Č', 'ć', 'Ć', 'Ć', 'dž', 'Dž', 'DŽ'),
				$content
			);
			
			$skip_words = $this->get_skip_words();
			$skip_words = array_map('mb_strtolower', $skip_words);
			
			$search = array_map('mb_strtolower', $search);
			
			$arr = explode(' ', $new_string);
			$arr = array_filter($arr);
			
			$arr_origin = explode(' ', $content);
			$arr_origin = array_filter($arr_origin);
			
			if(!empty($arr))
			{
				$words = array();
				foreach($arr as $i=>$word)
				{					
					$word_search = mb_strtolower($word, 'UTF-8');
					$word_search = preg_replace('/[.,?!-*_#$]+/i','',$word_search);
					
					$word_search_origin = mb_strtolower($arr_origin[$i]);
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
	public function transliterate_text($content, $type, $fix_html = true){
		if(is_array($content) || is_object($content) || is_numeric($content) || is_bool($content)) return $content;
		
		$content = $this->decode($content);
		if(method_exists('Serbian_Transliteration_Transliterating', $this->get_locale()))
		{
			$locale = $this->get_locale();
			$content = parent::$locale($content, $type);
			// Filter special names from the list
			foreach($this->cyr_exclude_list() as $item){
				$content = str_replace(parent::$locale($item, $type), $item, $content);
			}
		}
		else
		{
			
			switch($type)
			{
				case 'lat_to_cyr':
					$content = str_replace($this->lat(), $this->cyr(), $content);
					// Filter special names from the list
					foreach($this->lat_exclude_list() as $item){
						$content = str_replace(str_replace($this->lat(), $this->cyr(), $item), $item, $content);
						$content = $this->fix_attributes($content);
					}
					break;
				case 'cyr_to_lat':
					$content = str_replace($this->cyr(), $this->lat(), $content);
					// Filter special names from the list
					foreach($this->cyr_exclude_list() as $item){
						$content = str_replace(str_replace($this->cyr(), $this->lat(), $item), $item, $content);
					}
					break;
			}
		}
		
		if($type == 'lat_to_cyr' && $fix_html){
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
	public function transliterate_objects($array, $type = false, $fix_html = true)
	{
		// First setup all properly
		if(empty($array)) return $array;
		if(empty($type) || is_bool($type)) $type = $this->get_current_script( $this->get_options() );
		
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
        return in_array($this->get_locale(), apply_filters('rstr_already_cyrillic', array('sr_RS','mk_MK', 'bel', 'bg_BG', 'ru_RU', 'sah', 'uk', 'kk'))) !== false;
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
		
		return apply_filters('rstr_html_tags_collected', $html_tags);
	}
	
	/*
	 * Fix html codes
	 * @return        string/html
	 * @author        Ivijan-Stefan Stipic
	*/
	public function fix_cyr_html($content){
		$content = htmlspecialchars_decode($content);

		// Fix HTML entities
		$content = preg_replace_callback ('/\&([\x{0400}-\x{04FF}qwy0-9]+)\;/iu', function($m){
			return '&' . $this->cyr_to_lat($m[1]) . ';';
		}, $content);


		// Fix HTML tags
		$tags = $this->html_tags();
		
		$tag_replace = array(
			'<имг ' => '<img ',
			'<бр>' => '<br>',
			'<бр ' => '<br ',
			'<хр>' => '<hr>',
			'<хр ' => '<hr ',
			// Fix internal tags
			'{цyр_то_лат}' => '{cyr_to_lat}',
			'{/цyр_то_лат}' => '{/cyr_to_lat}',
			'{лат_то_цyр}' => '{lat_to_cyr}',
			'{/лат_то_цyр}' => '{/lat_to_cyr}',
			'{рстр_скип}' => '{rstr_skip}',
			'{/рстр_скип}' => '{/rstr_skip}'
		);
		
		foreach($tags->lat as $i=>$tag){
			$tag_cyr = $tags->cyr[$i];
			
			$tag_replace['<' . $tag_cyr] = '<' . $tag;
			$tag_replace['</' . $tag_cyr . '>'] = '</' . $tag . '>';
		}
		$tags = NULL;

		// Fix some characters
		$content = strtr($content, apply_filters('rstr/html/tags/replace', array_merge($tag_replace, array(
			'хреф' => 'href',
			'срц' => 'src',
			'&сцарон;' => 'ш',
			'&Сцарон;' => 'Ш'
		)), $tag_replace));
		
		$tag_replace = NULL;
		
		$lastPos = 0;
		$positions = [];
		
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
		$content = preg_replace_callback ('/\s([\x{0400}-\x{04FF}qwy0-9\-]+)(=["\'])/iu', function($m){
			return ' ' . $this->cyr_to_lat($m[1]) . $m[2];
		}, $content);
		$content = preg_replace_callback ('/\s(class|id|rel|selected|type|style|loading|srcset|sizes|lang|name)\s?=\s?"(.*?)"/iu', function($m){
			return sprintf(' %1$s="%2$s"', $m[1], $this->cyr_to_lat($m[2]));
		}, $content);
		$content = preg_replace_callback ('/\s(class|id|rel|selected|type|style|loading|srcset|sizes|lang|name)\s?=\s?\'(.*?)\'/iu', function($m){
			return sprintf(' %1$s=\'%2$s\'', $m[1], $this->cyr_to_lat($m[2]));
		}, $content);
		
		// Fix attributes with doublequote
		$content = preg_replace_callback ('/(титле|алт|срц|дата-([\x{0400}-\x{04FF}qwy0-9a-zA-Z\/\=\"\'\_\-\s\.\;\,\!\?\*\:\#\$\%\&\(\)\[\]\+\@\€]+))\s?=\s?"(.*?)"/iu', function($m){
			return sprintf('%1$s="%2$s"', $this->cyr_to_lat($m[1]), esc_attr($this->lat_to_cyr($m[3], false)));
		}, $content);
		// Fix attributes with singlequote
		$content = preg_replace_callback ('/(титле|алт|срц|дата-([\x{0400}-\x{04FF}qwy0-9a-zA-Z\/\=\"\'\_\-\s\.\;\,\!\?\*\:\#\$\%\&\(\)\[\]\+\@\€]+))\s?=\s?\'(.*?)\'/iu', function($m){
			return sprintf('%1$s="%2$s"', $this->cyr_to_lat($m[1]), esc_attr($this->lat_to_cyr($m[3], false)));
		}, $content);
		
		// Fix data attributes
		$content = preg_replace_callback ('/(data-[a-z0-9\_\-]+)\s?=\s?"(.*?)"/iu', function($m){
			return sprintf('%1$s="%2$s"', $m[1], htmlspecialchars_decode($this->cyr_to_lat($m[2])));
		}, $content);
		
		// Fix open tags
		$content = preg_replace_callback ('/(<[\x{0400}-\x{04FF}qwy0-9a-zA-Z\/\=\"\'\_\-\s\.\;\,\!\?\*\:\#\$\%\&\(\)\[\]\+\@\€]+>)/iu', function($m){
			return $this->cyr_to_lat($m[1]);
		}, $content);
		
		// Fix closed tags
		$content = preg_replace_callback ('/(<\/[\x{0400}-\x{04FF}qwy0-9a-zA-Z]+>)/iu', function($m){
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
		$content = preg_replace_callback ('/(([\x{0400}-\x{04FF}qwy0-9\_\-\.]+)@([\x{0400}-\x{04FF}0-9\_\-\.]+)\.([\x{0400}-\x{04FF}0-9]{3,10}))/iu', function($m){
			return $this->cyr_to_lat($m[1]);
		}, $content);

		// Fix URL
		$content = preg_replace_callback ('/(([\x{0400}-\x{04FF}]{4,5}):\/{2}([\x{0400}-\x{04FF}qwy0-9\_\-\.]+)\.([\x{0400}-\x{04FF}qwy0-9]{3,10})(.*?)($|\n|\s|\r|\"\'\.\;\,\:\)\]\>))/iu', function($m){
			return $this->cyr_to_lat($m[1]);
		}, $content);
		$content = preg_replace_callback ('/"(хттпс:\/\/.*?)"/iu', function($m){
			return $this->cyr_to_lat($m[1]);
		}, $content);
	
		// Fix mailto link
		$content = preg_replace_callback ('/"(маилто:\/\/.*?)"/iu', function($m){
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
	
	public function upload_prefilter ($file) {
		$file['name']= $this->cyr_to_lat_sanitize($file['name']);
		return $file;
	}

	public function sanitize_file_name($filename){
		return $this->cyr_to_lat_sanitize($filename);
	}
	
	public function force_permalink_to_latin ($permalink) {
		$permalink = rawurldecode($permalink);
		$permalink= $this->cyr_to_lat_sanitize($permalink);
		return $permalink;
	}
	
	public function force_permalink_to_latin_on_save ($data, $postarr) {
		$data['post_name'] = rawurldecode($data['post_name']);
		$data['post_name'] = $this->cyr_to_lat_sanitize( $data['post_name'] );
		return $data;
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
	 * Generate unique token
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function generate_token($length=16){
		if(function_exists('openssl_random_pseudo_bytes') || function_exists('random_bytes'))
		{
			if (version_compare(PHP_VERSION, '7.0.0', '>='))
				return substr(str_rot13(bin2hex(random_bytes(ceil($length * 2)))), 0, $length);
			else
				return substr(str_rot13(bin2hex(openssl_random_pseudo_bytes(ceil($length * 2)))), 0, $length);
		}
		else
		{
			return substr(str_replace(array('.',' ','_'),mt_rand(1000,9999),uniqid('t'.microtime())), 0, $length);
		}
	}
	
	/*
	 * Return plugin informations
	 * @return        array/object
	 * @author        Ivijan-Stefan Stipic
	*/
	function plugin_info($fields = array()) {
        if ( is_admin() ) {
			if ( ! function_exists( 'plugins_api' ) ) {
				include_once( WP_ADMIN_DIR . '/includes/plugin-install.php' );
			}
			/** Prepare our query */
			//donate_link
			//versions
			$plugin_data = plugins_api( 'plugin_information', array(
				'slug' => RSTR_NAME,
				'fields' => array_merge(array(
					'active_installs' => false,           // rounded int
					'added' => false,                     // date
					'author' => false,                    // a href html
					'author_block_count' => false,        // int
					'author_block_rating' => false,       // int
					'author_profile' => false,            // url
					'banners' => false,                   // array( [low], [high] )
					'compatibility' => false,            // empty array?
					'contributors' => false,              // array( array( [profile], [avatar], [display_name] )
					'description' => false,              // string
					'donate_link' => false,               // url
					'download_link' => false,             // url
					'downloaded' => false,               // int
					// 'group' => false,                 // n/a 
					'homepage' => false,                  // url
					'icons' => false,                    // array( [1x] url, [2x] url )
					'last_updated' => false,              // datetime
					'name' => false,                      // string
					'num_ratings' => false,               // int
					'rating' => false,                    // int
					'ratings' => false,                   // array( [5..0] )
					'requires' => false,                  // version string
					'requires_php' => false,              // version string
					// 'reviews' => false,               // n/a, part of 'sections'
					'screenshots' => false,               // array( array( [src],  ) )
					'sections' => false,                  // array( [description], [installation], [changelog], [reviews], ...)
					'short_description' => false,        // string
					'slug' => false,                      // string
					'support_threads' => false,           // int
					'support_threads_resolved' => false,  // int
					'tags' => false,                      // array( )
					'tested' => false,                    // version string
					'version' => false,                   // version string
					'versions' => false,                  // array( [version] url )
				), $fields)
			));
		 
			return $plugin_data;
		}
    }
	
	/**
	* Get current page ID
	* @autor    Ivijan-Stefan Stipic
	* @since    1.0.7
	* @version  1.0.0
	**/
	public function get_current_page_ID(){
		global $post, $wp_query, $wpdb, $rstr_cache;
		
		if($current_page_id = $rstr_cache->get('current_page_id')) return $current_page_id;
		
		if(!is_null($wp_query) && isset($wp_query->post) && isset($wp_query->post->ID) && !empty($wp_query->post->ID))
			return $rstr_cache->set('current_page_id', $wp_query->post->ID);
		else if(function_exists('get_the_id') && !empty(get_the_id()))
			return $rstr_cache->set('current_page_id', get_the_id());
		else if(!is_null($post) && isset($post->ID) && !empty($post->ID))
			return $rstr_cache->set('current_page_id', $post->ID);
		else if( (isset($_GET['action']) && sanitize_text_field($_GET['action']) == 'edit') && $post = ((isset($_GET['post']) && is_numeric($_GET['post']))  ? absint($_GET['post']) : false))
			return $rstr_cache->set('current_page_id', $post);
		else if($p = ((isset($_GET['p']) && is_numeric($_GET['p']))  ? absint($_GET['p']) : false))
			return $rstr_cache->set('current_page_id', $p);
		else if($page_id = ((isset($_GET['page_id']) && is_numeric($_GET['page_id']))  ? absint($_GET['page_id']) : false))
			return $rstr_cache->set('current_page_id', $page_id);
		else if(!is_admin() && $wpdb)
		{
			$actual_link = rtrim($_SERVER['REQUEST_URI'], '/');
			$parts = explode('/', $actual_link);
			if(!empty($parts))
			{
				$slug = end($parts);
				if(!empty($slug))
				{
					if($post_id = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT ID FROM {$wpdb->posts} 
							WHERE 
								`post_status` = %s
							AND
								`post_name` = %s
							AND
								TRIM(`post_name`) <> ''
							LIMIT 1",
							'publish',
							sanitize_title($slug)
						)
					))
					{
						return $rstr_cache->set('current_page_id', absint($post_id));
					}
				}
			}
		}
		else if(!is_admin() && 'page' == get_option( 'show_on_front' ) && !empty(get_option( 'page_for_posts' )))
			return $rstr_cache->set('current_page_id', get_option( 'page_for_posts' ));

		return false;
	}
	
	/* 
	* Register language script
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function attachment_taxonomies() {
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
	
	/* 
	* Get current transliteration script
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public function get_current_script($options=array()){		
		if(isset($_COOKIE['rstr_script']) && !empty($_COOKIE['rstr_script']))
		{
			if($_COOKIE['rstr_script'] == 'lat') {
				if(isset($options['transliteration-mode']) && $options['site-script'] == 'lat') return 'lat';
		
				return 'cyr_to_lat';
			} else if($_COOKIE['rstr_script'] == 'cyr') {
				if(isset($options['transliteration-mode']) && $options['site-script'] == 'cyr') return 'cyr';
				
				return 'lat_to_cyr';
			}
		}
		
		return (isset($options['transliteration-mode']) && !empty($options['transliteration-mode']) ? $options['transliteration-mode'] : 'none');
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
				$parse_url = $this->parse_url();
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
			setcookie( 'rstr_script', $val, (time()+YEAR_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN );
			
			if($this->get_option('cache-support', 'yes') == 'yes') {
				$this->cache_flush();
			}
		}
	}
	
	/*
	 * Flush Cache
	 * @verson    1.0.0
	*/
	public function cache_flush () {
		global $post, $user;
		
		// Standard cache
		header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		if(function_exists('nocache_headers')) {
			nocache_headers();
		}
		
		// Flush WP cache
		if (function_exists('w3tc_flush_all')) {
			wp_cache_flush();
		}
		
		// W3 Total Cache
		if (function_exists('w3tc_flush_all')) {
			w3tc_flush_all();
		}
		
		// WP Fastest Cache
		if (function_exists('wpfc_clear_all_cache')) {
			wpfc_clear_all_cache(true);
		}
		
		// Clean stanrad WP cache
		if($post && function_exists('clean_post_cache')) {
			clean_post_cache( $post );
		}
		
		if($user && function_exists('clean_post_cache')) {
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
	
	/**
	 * Parse URL
	 * @since     1.2.2
	 * @verson    1.0.0
	 */
	public function parse_url(){
		global $rstr_cache;
		if(!$rstr_cache->get('url_parsed')) {
			$http = 'http'.( $this->is_ssl() ?'s':'');
			$domain = preg_replace('%:/{3,}%i','://',rtrim($http,'/').'://'.$_SERVER['HTTP_HOST']);
			$domain = rtrim($domain,'/');
			$url = preg_replace('%:/{3,}%i','://',$domain.'/'.(isset($_SERVER['REQUEST_URI']) && !empty( $_SERVER['REQUEST_URI'] ) ? ltrim($_SERVER['REQUEST_URI'], '/'): ''));
				
			$rstr_cache->set('url_parsed', array(
				'method'	=>	$http,
				'home_fold'	=>	str_replace($domain,'',home_url()),
				'url'		=>	$url,
				'domain'	=>	$domain,
			));
		}
		
		return $rstr_cache->get('url_parsed');
	}
	
	/*
	 * CHECK IS SSL
	 * @return	true/false
	 */
	public function is_ssl($url = false)
	{
		global $rstr_cache;
		
		if($url !== false && is_string($url)) {
			return (preg_match('/(https|ftps)/Ui', $url) !== false);
		} else if(!$rstr_cache->get('is_ssl')) {
			if(
				( is_admin() && defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN ===true )
				|| (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
				|| (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
				|| (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
				|| (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
				|| (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == 443)
				|| (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https')
			) {
				$rstr_cache->set('is_ssl', true);
			}
		}
		return $rstr_cache->get('is_ssl');
	}
	
	/* 
	* Check is block editor screen
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public function is_editor()
	{
		global $rstr_cache;
		
		if(!$rstr_cache->get('is_editor')) {
			if (version_compare(get_bloginfo( 'version' ), '5.0', '>=')) {
				if(!function_exists('get_current_screen')){
					include_once ABSPATH  . '/wp-admin/includes/screen.php';
				}
				$get_current_screen = get_current_screen();
				if(is_callable(array($get_current_screen, 'is_block_editor')) && method_exists($get_current_screen, 'is_block_editor')) {
					$rstr_cache->set('is_editor', $get_current_screen->is_block_editor());
				}
			} else {
				$rstr_cache->set('is_editor', ( isset($_GET['action']) && isset($_GET['post']) && $_GET['action'] == 'edit' && is_numeric($_GET['post']) ) );
			}
		}
		
		return $rstr_cache->get('is_editor');
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
		
		// Fix broken things
		$tags = $this->html_tags();
		foreach($tags->lat as $i=>$tag){	
			$content = str_replace(array(
				'&lt;' . $tag,
				'&lt;/' . $tag . '&gt;'
			), array(
				'<' . $tag,
				'</' . $tag . '>'
			), $content);	
		}
		
		// Fix CSS
		$content = preg_replace_callback('/(?=<style(.*?)>)(.*?)(?<=<\/style>)/s', function($m) {
				return $this->decode($m[2]);
		}, $content);
		
		// Fix scripts
		$content = preg_replace_callback('/(?=<script(.*?)>)(.*?)(?<=<\/script>)/s', function($m) {
				return $this->decode($m[2]);
		}, $content);
		
		return $content;
	}
	
	public function mode($options=false){
		
		if(empty($options)) $options = $this->get_options();
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
	* Admin action links
	* @verson    1.0.0
	*/
	public function admin_action_links($actions = array()) {
		$active = (isset($_GET['action']) ? $_GET['action'] : '');
		$tab = (isset($_GET['tab']) ? $_GET['tab'] : '');
	?>
<ul class="action-links">
<?php foreach($actions as $action=>$name): ?>
	<li class="action-tab<?php echo ($action==$active ? ' active' : ''); ?>"><a href="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=' . $tab . '&action=' . $action); ?>" class="action-link<?php echo ($action==$active ? ' active' : ''); ?>"><?php echo $name; ?></a></li>
<?php endforeach; ?>
</ul>
<select class="action-links-select" onchange="location = this.value;">
<?php foreach($actions as $action=>$name): ?>
	<option value="<?php echo admin_url('/options-general.php?page=' . RSTR_NAME . '&tab=' . $tab . '&action=' . $action); ?>"<?php echo ($action==$active ? ' selected' : ''); ?>><?php echo $name; ?></option>
<?php endforeach; ?>
</select>
	<?php
	}
	
	/* 
	* Instance
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function __instance()
	{
		global $rstr_cache;
		$class = get_called_class();
		if(!$class){
			$class = static::self;
		}
		$instance = $rstr_cache->get($class);
		if ( !$instance ) {
			$instance = $rstr_cache->set($class, new self());
		}
		return $instance;
	}
}
endif;