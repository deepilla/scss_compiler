<?php

	@ini_set('display_errors', 'off');

	define('DOCROOT', rtrim(realpath(dirname(__FILE__) . '/../../../'), '/'));
	define('DOMAIN', rtrim(rtrim($_SERVER['HTTP_HOST'], '/') . str_replace('/extensions/scss_compiler/lib', NULL, dirname($_SERVER['PHP_SELF'])), '/'));

	// Include some parts of the engine
	// From Symphony 2.6.0 we need to include the autoloader file first
	// Wrap the require in a test so that it will continue to work for prior versions
	if (file_exists(DOCROOT . '/vendor/autoload.php')) {
		require_once(DOCROOT . '/vendor/autoload.php');
	}
	require_once(DOCROOT . '/symphony/lib/boot/bundle.php');
	require_once(CONFIG);

	require_once('scssphp/scss.inc.php');

	function processParams($string){
		$param = (object)array(
			'file' => 0
		);

		if(preg_match_all('/^(.+)$/i', $string, $matches, PREG_SET_ORDER)){
			$param->file = $matches[0][1];
		}

		return $param;
	}

	$param = processParams($_GET['param']);
	$fileinfo = pathinfo($param->file);

	header('Content-type: text/css');

	$parser = new scssc();
	// Assume that imported files are in the same directory as the parent file
	$parser->addImportPath(WORKSPACE . '/' . $fileinfo['dirname']);
	try {
		$css = $parser->compile(file_get_contents(WORKSPACE . '/' . $param->file));
	}
	catch(Exception $e) {
		// If compilation fails, output the error message as a CSS comment
		$css = '/* ' . $e->getMessage() . ' */';
	}

	file_put_contents(CACHE . '/scss_compiler/' . $fileinfo['filename'] . '.css', $css);

	echo $css;

	exit;
