<?php if ( !defined('WPINC') ) die();
/*
 * Main global classes with active hooks
 * @since     1.0.0
 * @verson    1.0.0
 */
if(!class_exists('Serbian_Transliteration', false) && class_exists('Serbian_Transliteration_Transliterating', false)) :
class Serbian_Transliteration extends Serbian_Transliteration_Transliterating{

	/*
	 * Translate from cyr to lat
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function cyr_to_lat($content){

		if(parent::can_trasliterate($content) || Serbian_Transliteration_Utilities::is_editor()){
			return $content;
		}
		
		$formatSpecifiers = [];
		$content = preg_replace_callback('/(\b\d+(?:\.\d+)?&#37;)/', function($matches) use (&$formatSpecifiers) {
			$placeholder = '@=[0' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		$content = Serbian_Transliteration_Utilities::decode($content);
		$content = $this->transliteration($content, 'cyr_to_lat');
		$content = self::fix_attributes($content);
		
		if($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
		}

		return $content;
	}

	/*
	 * Translate from cyr to lat
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function cyr_to_lat_sanitize($content) {
		if (parent::can_trasliterate($content)) {
			return $content;
		}

		$content = $this->cyr_to_lat($content);
		$content = Serbian_Transliteration_Utilities::normalize_latin_string($content);

		if (function_exists('iconv')) {
			$locale = parent::get_locales($this->get_locale());
			if ($locale && preg_match('/([a-zA-Z]{2})(_[a-zA-Z]{2})?/', $locale)) {
				setlocale(LC_CTYPE, $locale);
			}

			$converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $content);
			if ($converted) {
				$content = str_replace(array("\"", "'", "`", "^", "~"), '', $converted);
			}
		}

		return $content;
	}


	/*
	 * Translate from lat to cyr
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function lat_to_cyr($content, $fix_html = true, $fix_diacritics = false) {
		if(parent::can_trasliterate($content) || Serbian_Transliteration_Utilities::is_editor()){
			return $content;
		}
		
		$formatSpecifiers = [];
		$content = preg_replace_callback('/(\b\d+(?:\.\d+)?&#37;)/', function($matches) use (&$formatSpecifiers, $content) {
			$placeholder = '@=[0' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		$content = Serbian_Transliteration_Utilities::decode($content);

		if($fix_diacritics) {
			$content = self::fix_diacritics($content);
		}

		$content = $this->transliteration($content, 'lat_to_cyr');

		if($fix_html && strip_tags($content) != $content){
		//	$content = self::fix_cyr_html($content);
		//	$content = self::fix_attributes($content);
		}
		
		if($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
		}

		return $content;
	}

	public static function fix_diacritics($content) {
		if (parent::can_trasliterate($content) || Serbian_Transliteration_Utilities::is_editor() || 
			!in_array(self::__instance()->get_locale(), ['sr_RS', 'bs_BA', 'cnr'])) {
			return $content;
		}

		$search = parent::get_diacritical();
		if (!$search) {
			return $content;
		}

		$new_string = strtr($content, [
			'dj' => 'đ', 'Dj' => 'Đ', 'DJ' => 'Đ',
			'sh' => 'š', 'Sh' => 'Š', 'SH' => 'Š',
			'ch' => 'č', 'Ch' => 'Č', 'CH' => 'Č',
			'cs' => 'ć', 'Cs' => 'Ć', 'CS' => 'Ć',
			'dz' => 'dž', 'Dz' => 'Dž', 'DZ' => 'DŽ'
		]);

		$skip_words = array_map('strtolower', parent::get_skip_words());
		$search = array_map('strtolower', $search);

		$arr = Serbian_Transliteration_Utilities::explode(' ', $new_string);
		$arr_origin = Serbian_Transliteration_Utilities::explode(' ', $content);

		$result = '';
		foreach ($arr as $i => $word) {
			$word_origin = $arr_origin[$i];
			$word_search = strtolower(preg_replace('/[.,?!-*_#$]+/i', '', $word));
			$word_search_origin = strtolower(preg_replace('/[.,?!-*_#$]+/i', '', $word_origin));

			if (in_array($word_search_origin, $skip_words)) {
				$result .= $word_origin . ' ';
				continue;
			}

			if (in_array($word_search, $search)) {
				$result .= self::apply_case($word, $search, $word_search);
			} else {
				$result .= $word;
			}

			$result .= ($i < count($arr) - 1) ? ' ' : '';
		}

		return $result ?: $content;
	}

	/*
	 * PRIVATE: Apply Case
	 */
	private static function apply_case($word, $search, $word_search) {
		if (ctype_upper($word) || preg_match('~^[A-ZŠĐČĆŽ]+$~u', $word)) {
			return strtoupper($search[array_search($word_search, $search)]);
		} elseif (preg_match('~^\p{Lu}~u', $word)) {
			$ucfirst = $search[array_search($word_search, $search)];
			return strtoupper(substr($ucfirst, 0, 1)) . substr($ucfirst, 1);
		}
		return $word;
	}

	/*
	 * Automatic transliteration
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public function transliterate_text($content, $type = NULL, $fix_html = true) {
		if (parent::can_trasliterate($content) || Serbian_Transliteration_Utilities::is_editor()) {
			return $content;
		}


		$type = (empty($type) || is_bool($type)) ? Serbian_Transliteration_Utilities::get_current_script() : $type;

		if (get_rstr_option('site-script') === 'cyr' && $type === 'lat_to_cyr') {
			return $content;
		}
		
		$formatSpecifiers = [];
		$content = preg_replace_callback('/(\b\d+(?:\.\d+)?&#37;)/', function($matches) use (&$formatSpecifiers) {
			$placeholder = '@=[0' . count($formatSpecifiers) . ']=@';
			$formatSpecifiers[$placeholder] = $matches[0];
			return $placeholder;
		}, $content);

		$content = Serbian_Transliteration_Utilities::decode($content);
		$content = $this->transliteration($content, $type);

		if ($type === 'lat_to_cyr' && $fix_html && strip_tags($content) !== $content) {
			$content = self::fix_cyr_html($content);
			$content = self::fix_attributes($content);
		}
		
		if($formatSpecifiers) {
			$content = strtr($content, $formatSpecifiers);
		}

		return $content;
	}

	/*
	 * Transliterate associative array or strings
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	*/
	public function transliterate_objects($array, $type = NULL, $fix_html = true, $isObject = false) {
		if (empty($array) || Serbian_Transliteration_Utilities::is_editor()) {
			return $array;
		}

		$type = (empty($type) || is_bool($type)) ? Serbian_Transliteration_Utilities::get_current_script() : $type;

		if (is_object($array)) {
			$array = (array) $array;
			$isObject = true;
		}

		if (is_array($array)) {
			$data = array_map(function ($item) use ($type, $fix_html, $isObject) {
				return $this->transliterate_objects($item, $type, $fix_html, $isObject);
			}, $array);

			return $isObject ? (object) $data : $data;
		}

		if (is_scalar($array) && !$this->_is_special_type($array)) {
			return $this->transliterate_text($array, $type, $fix_html);
		}

		return $array;
	}

	/*
	 * PRIVATE: Is special type
	 */
	private function _is_special_type($content) {
		if (empty($content)) {
			return true;
		}

		if (is_numeric($content) || is_bool($content)) {
			return true;
		}

		if (parent::is_url_or_email($content)) {
			return true;
		}

		return false;
	}

	/*
	 * All available HTML tags
	 * @return        array
	 * @author        Ivijan-Stefan Stipic
	 */
	public static function html_tags() {
		$html_tags = get_option(RSTR_NAME . '-html-tags');

		if( empty($html_tags) )
		{
			$tags = apply_filters('rstr/html/tags',  '!DOCTYPE,a,abbr,acronym,address,applet,area,article,aside,audio,b,base,basefont,bdi,bdo,big,blockquote,body,br,button,canvas,caption,center,cite,code,col,colgroup,data,details,dd,del,details,dfn,dialog,dir,div,dl,dt,em,embed,fieldset,figcaption,figure,font,footer,form,frame,frameset,h1,h2,h3,h4,h5,h6,head,header,hr,html,i,iframe,img,input,ins,kbd,label,legend,li,link,main,map,mark,meta,master,nav,noframes,noscript,object,ol,optgroup,option,output,p,param,picture,pre,progress,q,rp,rt,ruby,s,samp,script,section,select,small,source,span,strike,strong,style,sub,summary,sup,svg,table,tbody,td,template,textarea,tfoot,th,thead,time,title,tr,track,tt,u,ul,var,video,wbr');

			$tags_latin = Serbian_Transliteration_Utilities::explode(',', ($tags??''));
			$tags_latin = apply_filters('rstr_html_tags_lat', $tags_latin);

			$tags_cyr = self::__instance()->lat_to_cyr($tags, false);
			$tags_cyr = Serbian_Transliteration_Utilities::explode(',', ($tags_cyr??''));
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
	public static function fix_cyr_html($content) {
		// Dekodirajte HTML entitete
		$content = htmlspecialchars_decode($content);

		// Popravite HTML entitete
		$content = self::fix_html_entities($content);

		// Dohvatite zamenu za HTML tagove
		$tag_replace = self::get_tag_replacements();

		// Izvršite zamenu tagova
		$content = strtr($content, $tag_replace);

		// Popravite HTML atribute
		$content = self::fix_html_attributes($content);
		
		// Fix open tags
		$content = preg_replace_callback ('/(<[\x{0400}-\x{04FF}qwyx0-9a-zA-Zα-ωΑ-Ω\/\=\"\'\_\-\s\.\;\,\!\?\*\:\#\$\%\&\(\)\[\]\+\@\€]+>)/iu', 'self::preserve_regex_content', $content);

		// Fix closed tags
		$content = preg_replace_callback ('/(<\/[\x{0400}-\x{04FF}qwyx0-9a-zA-Zα-ωΑ-Ω]+>)/iu', 'self::preserve_regex_content', $content);

		// Specifična logika za script i style tagove
		$content = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/is', 'self::preserve_script_style_content', $content);
		$content = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>/is', 'self::preserve_script_style_content', $content);
		
		// Fix email
		$content = preg_replace_callback ('/(([\x{0400}-\x{04FF}qwyx0-9α-ωΑ-Ω\_\-\.]+)@([\x{0400}-\x{04FF}0-9α-ωΑ-Ω\_\-\.]+)\.([\x{0400}-\x{04FF}0-9α-ωΑ-Ω]{3,10}))/iu', 'self::preserve_regex_content', $content);

		// Fix URL
		$content = preg_replace_callback ('/(([\x{0400}-\x{04FF}α-ωΑ-Ω]{4,5}):\/{2}([\x{0400}-\x{04FF}qwyx0-9α-ωΑ-Ω\_\-\.]+)\.([\x{0400}-\x{04FF}qwyx0-9α-ωΑ-Ω]{3,10})(.*?)($|\n|\s|\r|\"\'\.\;\,\:\)\]\>))/iu', 'self::preserve_regex_content', $content);
		$content = preg_replace_callback ('/"('.self::__instance()->lat_to_cyr('https', false).'?:\/\/.*?)"/iu', 'self::preserve_regex_content', $content);

		// Fix mailto link
		$content = preg_replace_callback ('/"('.self::__instance()->lat_to_cyr('mailto', false).':\/\/.*?)"/iu', 'self::preserve_regex_content', $content);

		// Fix attributes with doublequote
		$content = preg_replace_callback ('/(title|alt|data-(title|alt))\s?=\s?"(.*?)"/iu', function($m){
			return sprintf('%1$s="%2$s"', $m[1], esc_attr(self::__instance()->lat_to_cyr($m[3], false)));
		}, $content);

		// Fix attributes with single quote
		$content = preg_replace_callback ('/(title|alt|data-(title|alt))\s?=\s?\'(.*?)\'/iu', function($m){
			return sprintf('%1$s=\'%2$s\'', $m[1], esc_attr(self::__instance()->lat_to_cyr($m[3], false)));
		}, $content);
		

		// Fix shortcode
		$content = preg_replace_callback ('/\[\/([\x{0400}-\x{04FF}qwyx0-9α-ωΑ-Ω\/\=\“\"\'\_\-\s\.\;\,\!\?\*\:\#\$\%\&\(\)\[\]\+\@\€]+)\]/iu', function($m){
			return '[/'.self::__instance()->cyr_to_lat($m[1]).']';
		}, $content);
		
		$content = preg_replace_callback ('/\[([\x{0400}-\x{04FF}qwxy0-9α-ωΑ-Ω\/\=\“\"\'\_\-\s\.\;\,\!\?\*\:\#\$\%\&\(\)\[\]\+\@\€]+)\]/iu', function($m){
			return '['.self::__instance()->cyr_to_lat($m[1]).']';
		}, $content);

		return $content;
	}

	private static function fix_html_entities($content) {
		return preg_replace_callback('/\&([\x{0400}-\x{04FF}qwyx0-9α-ωΑ-Ω]+)\;/iu', function($m) {
			return '&' . self::__instance()->cyr_to_lat($m[1]) . ';';
		}, $content);
	}

	private static function get_tag_replacements() {
		$tag_replace = Serbian_Transliteration_Cache::get('fix_cyr_html');
		if(empty($tag_replace)) {
			$tags = self::html_tags();

			foreach($tags->lat as $i=>$tag_lat){
				$tag_cyr = $tags->cyr[$i];
				$tag_replace['<' . $tag_cyr] = '<' . $tag_lat;
				$tag_replace['</' . $tag_cyr . '>'] = '</' . $tag_lat . '>';
			}
			$tags = NULL;
			
			$tag_replace = apply_filters('rstr/html/tags/replace', array_merge($tag_replace, array(
				self::__instance()->lat_to_cyr('href', false) => 'href',
				self::__instance()->lat_to_cyr('src', false) => 'src',
				'&'.self::__instance()->lat_to_cyr('scaron', false).';' => 'ш',
				'&'.self::__instance()->lat_to_cyr('Scaron', false).';' => 'Ш'
			)), $tag_replace);
			
			Serbian_Transliteration_Cache::set('fix_cyr_html', $tag_replace);
		}
		return $tag_replace;
	}

	private static function fix_html_attributes($content) {
		$content = preg_replace_callback ('/\s([\x{0400}-\x{04FF}qwyx0-9α-ωΑ-Ω\-]+)(=["\'])/siu', function($m){
			return ' ' . self::__instance()->cyr_to_lat($m[1]) . $m[2];
		}, $content);
		$content = preg_replace_callback ('/\s(class|id|rel|selected|type|style|loading|srcset|sizes|lang|name|href)\s?=\s?"(.*?)"/siu', function($m){
			return sprintf(' %1$s="%2$s"', $m[1], self::__instance()->cyr_to_lat($m[2]));
		}, $content);
		$content = preg_replace_callback ('/\s(class|id|rel|selected|type|style|loading|srcset|sizes|lang|name|href)\s?=\s?\'(.*?)\'/siu', function($m){
			return sprintf(' %1$s=\'%2$s\'', $m[1], self::__instance()->cyr_to_lat($m[2]));
		}, $content);

		// Fix attributes with doublequote
		$content = preg_replace_callback ('/('.self::__instance()->lat_to_cyr('title|alt|src|data', false).'-([\x{0400}-\x{04FF}qwyx0-9a-zA-Zα-ωΑ-Ω\/\=\"\'\_\-\s\.\;\,\!\?\*\:\#\$\%\&\(\)\[\]\+\@\€]+))\s?=\s?"(.*?)"/siu', function($m){
			return sprintf('%1$s="%2$s"', self::__instance()->cyr_to_lat($m[1]), esc_attr(self::__instance()->lat_to_cyr($m[3], false)));
		}, $content);
		// Fix attributes with singlequote
		$content = preg_replace_callback ('/('.self::__instance()->lat_to_cyr('title|alt|src|data', false).'-([\x{0400}-\x{04FF}qwyx0-9a-zA-Zα-ωΑ-Ω\/\=\"\'\_\-\s\.\;\,\!\?\*\:\#\$\%\&\(\)\[\]\+\@\€]+))\s?=\s?\'(.*?)\'/siu', function($m){
			return sprintf('%1$s="%2$s"', self::__instance()->cyr_to_lat($m[1]), esc_attr(self::__instance()->lat_to_cyr($m[3], false)));
		}, $content);

		// Fix data attributes
		$content = preg_replace_callback ('/(data-[a-z0-9\_\-]+)\s?=\s?"(.*?)"/siu', function($m){
			if($m[1] == 'data-nectar-animated-gradient-settings'){
		//		echo '<pre>', var_dump($m[2], self::__instance()->cyr_to_lat($m[2])), '</pre>';
			}
			return sprintf('%1$s="%2$s"', $m[1], self::__instance()->cyr_to_lat($m[2]));
		}, $content);

		return $content;
	}

	private static function preserve_script_style_content($matches) {
		return self::__instance()->cyr_to_lat($matches[2]);
	}
	
	private static function preserve_regex_content($matches) {
		return self::__instance()->cyr_to_lat($matches[1]);
	}

	/*
	 * Prefiler for the upload
	*/
	public function upload_prefilter ($file) {
		$file['name']= $this->sanitize_file_name($file['name']);
		return $file;
	}

	/*
	 * Sanitize file name
	*/
	public function sanitize_file_name($filename){
		$delimiter = get_rstr_option('media-delimiter', 'no');

		if($delimiter != 'no') {
			$name = $this->cyr_to_lat_sanitize($filename);
			$name = preg_split("/[\-_~\s]+/", $name);
			$name = array_filter($name);

			if(!empty($name)) {
				return join($delimiter, $name);
			} else {
				return $filename;
			}
		}
		
		return $filename;
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
	 * Fix attributes
	 * @return        string
	 * @author        Ivijan-Stefan Stipic
	*/
	public static function fix_attributes($content){

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
				return Serbian_Transliteration_Utilities::decode($m[2]);
		}, $content);

		// Fix scripts
		$content = preg_replace_callback('/(?=<script(.*?)>)(.*?)(?<=<\/script>)/s', function($m) {
				return Serbian_Transliteration_Utilities::decode($m[2]);
		}, $content);

		$content = preg_replace_callback('/\\{1,5}&([a-zA-Z]+);/s', function($m) {
				return html_entity_decode('&' . $m[1] . ';');
		}, $content);

		$content = stripslashes($content);

		// Fix data attributes
		$content = preg_replace_callback ('/(data-[a-z0-9\_\-]+)\s?=\s?"(.*?)"/iu', function($m){
			return sprintf('%1$s="%2$s"', $m[1], htmlspecialchars_decode($m[2]));
		}, $content);

		return $content;
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
		$class = self::class;
		$instance = Serbian_Transliteration_Cache::get($class);
		if ( !$instance ) {
			$instance = Serbian_Transliteration_Cache::set($class, new self());
		}
		return $instance;
	}
	
	/*
	* Instance - Helper
	* @since     1.0.9
	* @verson    1.0.0
	*/
	public static function get() {
		return self::__instance();
	}
}
endif;
