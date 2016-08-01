<?php
	// Set here the primary language
	const PRIMARY_LANG = 'pt-br';
	
	echo "===== Get all languages to be processed... =====<br><br>";
	
	$langFiles = FilesUtils::getFiles('langs');
	
	echo "Languages files loaded: <b>" . implode('</b>, <b>', $langFiles) . '</b><br><br>';
	
	echo "===== Generating static files... =====<br><br>";
	
	// Get all files to be processed
	
	$filesPath = 'files';
	$files = FilesUtils::getFiles($filesPath);
	
	// Creates the output directory (or cleans it)
	if (file_exists('output')) {
		FilesUtils::deleteDir('output');
	}
	mkdir('output');
	
	// Creates the languages directory
	foreach ($langFiles as $langFile) {
		$langFilename = FilesUtils::removeExtension($langFile);
		$langDir = 'output/' . $langFilename;
		if ($langFilename != PRIMARY_LANG) {
			if (file_exists($langDir)) {
				FilesUtils::deleteDir($langDir);
			}
			mkdir($langDir);
		}
	}
	
	// For each file...
	foreach($files as $file) {
		$filename = FilesUtils::removeExtension($file);
		echo "* Processing file: " . $file . "<br>";
		
		// Generates it with each lang
		foreach ($langFiles as $langFile) {
			$langFilename = FilesUtils::removeExtension($langFile);
			$langDir = 'output/' . $langFilename;
			$langFinalPath = ($langFilename == PRIMARY_LANG ? 'output/' : $langDir . '/') . $filename . '.html';
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
		
		public static function getFiles($path) {
			return array_diff(scandir($path), array('.', '..'));
		}
		
		/*
		 * Removes the extension from a filename. "file.php" => "file"
		 */
		
		public static function removeExtension($filename) {
			return preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
		}
	
		/* 
		 * Delete a directory with recursion (including the files and another directories)
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