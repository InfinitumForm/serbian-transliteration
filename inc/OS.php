<?php if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
/**
 * Find details of the operating system
 *
 * @since      7.0.0
 * @package    CF_Geoplugin
 * @author     Ivijan-Stefan Stipic
 * @url        https://github.com/CreativForm/PHP-Solutions/blob/master/class.os.php
 */
if (!class_exists('Serbian_Transliteration_OS', false)):
    class Serbian_Transliteration_OS
    {
        /*
         * Get user agent informations
         *
         * @return string
        */
        public static function user_agent()
        {
            global $HTTP_USER_AGENT, $HTTP_SERVER_VARS;

            if (!empty($HTTP_USER_AGENT)) return $HTTP_USER_AGENT;

            if (!empty($HTTP_SERVER_VARS) && isset($HTTP_SERVER_VARS['HTTP_USER_AGENT']) && !empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) return $HTTP_SERVER_VARS['HTTP_USER_AGENT'];

            if (isset($_SERVER['HTTP_USER_AGENT'])) return $_SERVER['HTTP_USER_AGENT'];

            return 'undefined';
        }

        /*
         * Check if OS is Windows
         *
         * @return boolean true or false
        */
        public static function is_win()
        {
            // Sandard search
            if (in_array(strtoupper(substr(PHP_OS, 0, 3)) , array(
                'WIN'
            ) , true) !== false) return true;

            // If PHP_SHLIB_SUFFIX is equal to "dll",then PHP is running on a Windows.
            if (defined('PHP_SHLIB_SUFFIX') && strtolower(PHP_SHLIB_SUFFIX) === 'dll') return true;

            // Laravel approach
            if (defined('DIRECTORY_SEPARATOR') && '\\' === DIRECTORY_SEPARATOR) return true;

            return false;
        }

        /*
         * Check if PHP is 64 bit vesion
         *
         * @return boolean true or false
        */
        public static function is_php64()
        {
            // Check is PHP 64bit (PHP 64bit only running on 64bit OS version)
            if (version_compare(PHP_VERSION, '5.0.5', '>='))
            {
                if (defined('PHP_INT_SIZE') && PHP_INT_SIZE === 8) return true;
            }

            // Let's play with bits
            if (strlen(decbin(~0)) == 64) return true;

            // Let's do something more worse but can work if all above fail
            // The largest integer supported in 64 bit systems is 9223372036854775807. (https://en.wikipedia.org/wiki/9,223,372,036,854,775,807)
            $int = '9223372036854775807';
            if (intval($int) == $int) return true;

            // That's the end
            return false;
        }

        /*
         * Check if any OS is 64 bit vesion
         *
         * @return boolean true or false
        */
        public static function is_os64()
        {
            // Let's ask system directly
            if (function_exists('shell_exec'))
            {
                if (self::is_win())
                {
                    // Is Windows OS
                    $shell = shell_exec('wmic os get osarchitecture');
                    if (!empty($shell))
                    {
                        if (strpos($shell, '64') !== false) return true;
                    }
                }
                else
                {
                    // Let's check some UNIX approach if is possible
                    $shell = shell_exec('uname -m');
                    if (!empty($shell))
                    {
                        if (strpos($shell, '64') !== false) return true;
                    }
                }
            }

            // Check if PHP is 64 bit vesion (PHP 64bit only running on 64bit OS version)
            $is_php64 = self::is_php64();
            if ($is_php64) return true;

            // bit-shifting can help also
            if ((bool)((1 << 32) - 1)) return true;

            return false;
        }

        /**
         * Get operating system architecture number
         *
         * @return int 32 or 64 (bit)
         */
        public static function architecture()
        {
            return self::is_os64() ? 64 : 32;
        }

        /**
         * Get operating system name
         *
         * @param $user_agent null
         * @return string
         */
        public static function getOS($user_agent = null)
        {
			$os_array = [];
            if (empty($user_agent))
            {
                if (function_exists('php_uname')) {
					$user_agent = php_uname('a');
                } else {
					$user_agent = NULL;
				}
				
                // Get Windows versions
                foreach (apply_filters('rstr_windows_version', array(
                    '95',
                    '98',
                    '2000',
                    'XP Professional',
                    'XP',
                    '7\.1',
                    '7',
                    '8\.1 Pro',
                    '8\.1 Home',
                    '8\.1 Enterprise',
                    '8\.1 OEM',
                    '8\.1',
                    '8 Home',
                    '8 Enterprise',
                    '8 OEM',
                    '8',
                    '10\.1',
                    '10 Home',
                    '10 Pro Education',
                    '10 Pro',
                    '10 Education',
                    '10 Enterprise LTSB',
                    '10 Enterprise',
                    '10 IoT Core',
                    '10 IoT Enterprise',
                    '10 IoT',
                    '10 S',
                    '10 OEM',
                    '10',
					
					'11\.1',
                    '11 Home',
                    '11 Pro Education',
                    '11 Pro',
                    '11 Education',
                    '11 Enterprise LTSB',
                    '11 Enterprise',
                    '11 IoT Core',
                    '11 IoT Enterprise',
                    '11 IoT',
                    '11 S',
                    '11 OEM',
                    '11',
					
                    'server',
                    'vista',
                    'me',
                    'nt'
                )) as $ver)
                {
                    $os_array['windows ' . $ver] = 'Windows ' . $ver;
                }
                $os_array['microsoft windows'] = 'Microsoft Windows';
                $os_array['windows'] = 'Windows';
                // Get Linux/Unix/Mac
                foreach (apply_filters('rstr_unix_version', array(
                    'raspberry' => 'Linux - Raspbian',
                    'jessie' => 'Linux - Debian Jessie',
                    'squeeze' => 'Linux - Debian Squeeze',
                    'wheezy' => 'Linux - Debian Wheezy',
                    'stretch' => 'Linux - Debian Stretch',
                    'kubuntu' => 'Linux - Kubuntu',
                    'mandriva' => 'Linux - Mandriva',
                    'lubuntu' => 'Linux - Lubuntu',
                    'ubuntu' => 'Linux - Ubuntu',
                    'debian' => 'Linux - Debian',
                    'gentoo' => 'Linux - Gentoo',
                    'manjaro' => 'Linux - Manjaro',
                    'opensuse' => 'Linux - openSUSE',
                    'openwrt' => 'Linux - openWRT',
                    'fedora' => 'Linux - Fedora',
                    'linux' => 'Linux',
                    'sierra' => 'Mac OS - Sierra',
                    'mavericks' => 'Mac OS - Mavericks',
                    'yosemite' => 'Mac OS - Yosemite',
                    'mac os x' => 'Mac OS X',
                    'os x' => 'Mac OS X',
                    'mac os' => 'Mac OS',
                    'mac' => 'Mac OS',
                    'android' => 'Android'
                )) as $ver => $name)
                {
                    $os_array[$ver] = $name;
                }
				
				$regex_check = apply_filters('rstr_unix_version_regex', array(
					'Mac OS X 10\.1[^0-9]' => 'Mac OS X Puma'
				));
            }
            else
            {
                // https://stackoverflow.com/questions/18070154/get-operating-system-info-with-php
                $os_array = apply_filters('rstr_os_version', array(
                    'win10' => 'Windows 10',
                    'windows 10' => 'Windows 10',
                    'windows 10 enterprise' => 'Windows 10',
                    'windows 10 home' => 'Windows 10',
                    'windows 10 pro' => 'Windows 10',
                    'windows nt 10' => 'Windows 10',
					'win11' => 'Windows 11',
                    'windows 11' => 'Windows 11',
                    'windows 11 enterprise' => 'Windows 11',
                    'windows 11 home' => 'Windows 11',
                    'windows 11 pro' => 'Windows 11',
                    'windows nt 11' => 'Windows 11',
                    'windows nt 6\.3' => 'Windows 8.1',
                    'windows nt 6\.2' => 'Windows 8',
                    'windows nt 6\.0' => 'Windows Vista',
                    'windows nt 5\.2' => 'Windows Server 2003/XP x64',
                    'windows nt 5\.1' => 'Windows XP',
                    'windows me' => 'Windows ME',
                    'windows ce' => 'Windows CE',
                    'win16' => 'Windows 3.11',
                    'mac\_powerpc' => 'Mac OS 9',
                    'linux' => 'Linux',
                    'ubuntu' => 'Linux - Ubuntu',
                    'iphone' => 'iPhone',
                    'ipod' => 'iPod',
                    'ipad' => 'iPad',
                    'android' => 'Android',
                    'blackberry' => 'BlackBerry',
                    'webos' => 'Mobile',
                    'Win 9x 4\.90' => 'Windows ME',
                    'win32' => 'Windows',
                    'dos x86' => 'DOS',
                    'Mac OS X' => 'Mac OS X',
                    'Mac\_PowerPC' => 'Macintosh PowerPC',
                    'unix' => 'Unix',
                    'os\/2' => 'OS/2',
                    'freebsd' => 'FreeBSD',
                    'openbsd' => 'OpenBSD',
                    'netbsd' => 'NetBSD',
                    'irix' => 'IRIX',
                    'plan9' => 'Plan9',
                    'osf' => 'OSF',
                    'aix' => 'AIX',
                    'GNU Hurd' => 'GNU Hurd',
                    'amiga\-aweb' => 'AmigaOS',
                    'amiga' => 'Amiga',
                    'AvantGo' => 'PalmOS',
                    'Dreamcast' => 'Dreamcast OS',
                    'GetRight' => 'Windows',
                    'go\!zilla' => 'Windows',
                    'gozilla' => 'Windows',
                    'gulliver' => 'Windows',
                    'ia archiver' => 'Windows',
                    'NetPositive' => 'Windows',
                    'mass downloader' => 'Windows',
                    'microsoft' => 'Windows',
                    'offline explorer' => 'Windows',
                    'teleport' => 'Windows',
                    'web downloader' => 'Windows',
                    'webcapture' => 'Windows',
                    'webcollage' => 'Windows',
                    'webcopier' => 'Windows',
                    'webstripper' => 'Windows',
                    'webzip' => 'Windows',
                    'wget' => 'Windows',
                    'Java' => 'Unknown',
                    'flashget' => 'Windows',
                    'MS FrontPage' => 'Windows',
                    'libwww\-perl' => 'Unix',
                    'UP\.Browser' => 'Windows CE',
                    'NetAnts' => 'Windows',
                    'ios' => 'iOS'
                ));
				
				$regex_check = apply_filters('rstr_os_version_regex', array(
					'(fedora)' => 'Linux - Fedora',
					'(kubuntu)' => 'Linux - Kubuntu',
					'(ubuntu)' => 'Linux - Ubuntu',
					'(debian)' => 'Linux - Debian',
					'(CentOS)' => 'Linux - CentOS',
					'(Dropline)' => 'Linux - Slackware (Dropline GNOME)',
					'(ASPLinux)' => 'Linux - ASPLinux',
					'(Red Hat)' => 'Linux - Red Hat',
					'(linux)' => 'Linux',
					'windows nt 6\.1|windows nt 7\.0' => 'Windows 7',
					'windows nt 5\.0|windows nt5.1|windows 2000' => 'Windows 2000',
					'windows nt 4\.0|winnt4\.0' => 'Windows NT',
					'windows 98|win98' => 'Windows 98',
					'windows 95|win95' => 'Windows 95',
					'mac os x 10\.1[^0-9]' => 'Mac OS X Puma',
					'macintosh|mac os x' => 'Mac OS X',
					'(windows)([0-9]{1,2}\.[0-9]{1,2})' => 'Windows',
					'Mac OS X 10\.1[^0-9]' => 'Mac OS X Puma',
					'(media center pc)\.([0-9]{1,2}\.[0-9]{1,2})' => 'Windows Media Center',
					'(win)([0-9]{1,2}\.[0-9x]{1,2})' => 'Windows',
					'(win)([0-9]{2})' => 'Windows',
					'(windows)([0-9x]{2})' => 'Windows',
					'(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'Windows NT',
					'(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})' => 'Windows NT',
					'(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})' => 'Java',
					'(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}' => 'Solaris',
					'(mac|Macintosh)' => 'Mac OS',
					'(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'SunOS',
					'(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'BeOS',
					'(risc os)([0-9]{1,2}\.[0-9]{1,2})' => 'RISC OS',
					'(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - Mandriva',
					'(SUSE)\.([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - SUSE',
					'(amigaos)([0-9]{1,2}\.[0-9]{1,2})' => 'AmigaOS',
					'([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})' => 'Linux',
					'(webtv)/([0-9]{1,2}\.[0-9]{1,2})' => 'WebTV',
					'(msproxy)/([0-9]{1,2}\.[0-9]{1,2})' => 'Windows',
					'(msie)([0-9]{1,2}\.[0-9]{1,2})' => 'Windows'
				));
            }
			
            foreach ($os_array as $regex => $value)
            {
                if (@preg_match('~\b(' . self::preg_quote( $regex ) . ')\b~i', $user_agent)) {
                    return $value;
                }
            }
			
			foreach ($regex_check as $regex => $value)
            {
                if ( @preg_match('~\b(' . self::preg_quote( $regex ) . ')\b~i', $user_agent) ) {
                    return $value;
                }
            }
            return __('undefined', 'cf-geoplugin');
        }
		
		/**
         * PRIVATE: Quote regular expression characters
         *
         * @param $regex
         * @return string
         */
		private static function preg_quote ($regex) {
		//	$regex = preg_quote($regex);
			return $regex;	
		}
		
    }
endif;

