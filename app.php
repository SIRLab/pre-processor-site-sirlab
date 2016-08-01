<?php
	

	// Set here the primary language
	$PRIMARY_LANG = 'pt-br';
	
	$l = array(); // Initialize the language array
	
	echo "===== Get all languages to be processed... =====<br><br>";
	
	$langFiles = FilesUtils::get_files('langs');
	
	echo "Languages files loaded: <b>" . implode('</b>, <b>', $langFiles) . '</b><br><br>';
	
	echo "===== Generating static files... =====<br><br>";
	
	// Get all files to be processed
	
	$filesPath = 'files';
	$files = FilesUtils::get_files($filesPath);
	
	// Creates the output directory (or cleans it)
	if (file_exists('output')) {
		FilesUtils::deleteDir('output');
	}
	mkdir('output');
	
	// Creates the languages directory
	foreach ($langFiles as $langFile) {
		$langFilename = FilesUtils::get_filename($langFile);
		$langDir = 'output/' . $langFilename;
		if ($langFilename != $PRIMARY_LANG) {
			if (file_exists($langDir)) {
				FilesUtils::deleteDir($langDir);
			}
			mkdir($langDir);
		}
	}
	
	// For every file...
	foreach($files as $file) {
		$filename = FilesUtils::get_filename($file);
		echo "* Processing file: " . $file . "<br>";
		
		// Generates it with every lang
		foreach ($langFiles as $langFile) {
			$langFilename = FilesUtils::get_filename($langFile);
			$langDir = 'output/' . $langFilename;
			if ($langFilename == $PRIMARY_LANG) {
				$langFinalPath = 'output/' . $filename . '.html';
				$ld = '';
			} else {
				$langFinalPath = $langDir . '/' . $filename . '.html';
				$ld = $langFilename . '/';
			}
			echo "&nbsp;&nbsp;&nbsp;| Lang: " . $langFilename . "<br>";
			include('langs/' . $langFile);
			ob_start();
			include($filesPath . '/' . $file);
			file_put_contents($langFinalPath, ob_get_contents());
			ob_end_clean();	
		}
		
		echo "&nbsp;&nbsp;&nbsp;| Done.<br><br>";
	}
	
	// ==================================================
	// * FilesUtils
	
	class FilesUtils {
		
		/*
		 * Returns an array that contains only the files of a directory
		 */
		
		public static function get_files($path) {
			return array_diff(scandir($path), array('.', '..'));
		}
		
		/*
		 * Removes the extension from a filename. "file.php" => "file"
		 */
		
		public static function get_filename($filename) {
			return preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
		}
	
		/* 
		 * Delete a directory with recursion (including the files and another dirs)
		 * Source: http://stackoverflow.com/a/3349792
		 */
		
		public static function deleteDir($dirPath) {
			if (!is_dir($dirPath)) {
				throw new InvalidArgumentException("$dirPath must be a directory");
			}
			if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
				$dirPath .= '/';
			}
			$files = glob($dirPath . '*', GLOB_MARK);
			foreach ($files as $file) {
				if (is_dir($file)) {
					self::deleteDir($file);
				} else {
					unlink($file);
				}
			}
			rmdir($dirPath);
		}
	}