<?php

	Class extension_SCSS_Compiler extends Extension{

		public function about(){
			return array(
				'name' => 'SCSS Compiler',
				'version' => '1.1',
				'release-date' => '2015-03-18',
				'author' => array(
					array(
						'name' => 'deepilla',
						'website' => 'http://deepilla.com',
						'email' => 'hello@deepilla.com'
					)
				)
			);
		}

		public function getSubscribedDelegates(){
			return array();
		}

		public function install(){
			General::realiseDirectory(CACHE . '/scss_compiler/', Symphony::Configuration()->get('write_mode', 'directory'));

			$htaccess = @file_get_contents(DOCROOT . '/.htaccess');

			if($htaccess === false) return false;

			## Cannot use $1 in a preg_replace replacement string, so using a token instead
			$token = md5(time());

			$rule = "
	### SCSS RULES
	RewriteRule ^scss\/(.+\.scss)\$ extensions/scss_compiler/lib/scss.php?param={$token} [L,NC]\n\n";

			## Remove existing the rules
			$htaccess = self::__removeScssRules($htaccess);

			if(preg_match('/### SCSS RULES/', $htaccess)){
				$htaccess = preg_replace('/### SCSS RULES/', $rule, $htaccess);
			}
			else{
				$htaccess = preg_replace('/RewriteRule .\* - \[S=14\]\s*/i', "RewriteRule .* - [S=14]\n{$rule}\t", $htaccess);
			}

			## Replace the token with the real value
			$htaccess = str_replace($token, '$1', $htaccess);

			return @file_put_contents(DOCROOT . '/.htaccess', $htaccess);
		}

		public function uninstall(){
			$htaccess = @file_get_contents(DOCROOT . '/.htaccess');

			if($htaccess === false) return false;

			$htaccess = self::__removeScssRules($htaccess);
			$htaccess = preg_replace('/### SCSS RULES/', NULL, $htaccess);

			return @file_put_contents(DOCROOT . '/.htaccess', $htaccess);
		}

		public function enable(){
			return $this->install();
		}

		public function disable(){
			$htaccess = @file_get_contents(DOCROOT . '/.htaccess');

			if($htaccess === false) return false;

			$htaccess = self::__removeScssRules($htaccess);
			$htaccess = preg_replace('/### SCSS RULES/', NULL, $htaccess);

			return @file_put_contents(DOCROOT . '/.htaccess', $htaccess);
		}

	/*-------------------------------------------------------------------------
		Utilities:
	-------------------------------------------------------------------------*/

		private static function __removeScssRules($string){
			return preg_replace('/RewriteRule \^scss[^\r\n]+[\r\n]?/i', NULL, $string);
		}

	}
