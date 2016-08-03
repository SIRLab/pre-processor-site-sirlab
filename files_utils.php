<?php
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
		 * Returns the extension of a given file.
		 */
		 
		public static function getExtension($filename) {
			return preg_replace('/.+\./', '', $filename);
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
		
		/* 
		 * List all the files and folders from a given directory
		 * Base from: http://stackoverflow.com/a/32078936
		 */
		
		public static function getAllFiles($dir, $basedir = '') {
			$results = array();
			$subresults = array();
			if ($basedir == '') {
				$basedir = realpath($dir).DIRECTORY_SEPARATOR;
			}

			$files = scandir($dir);
			foreach ($files as $key => $value){
				if ($value != '.' && $value != '..') {
					$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
					$subresults[] = str_replace($basedir,'',$path);
					if (is_dir($path)) {
						$subdirresults = self::getAllFiles($path, $basedir);
						$results = array_merge($results,$subdirresults);
					}
				}
			}
			if (count($subresults) > 0) {
				$results = array_merge($subresults, $results);
			}
			return $results;
		}
	}
?>