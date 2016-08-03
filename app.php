<?php
	// Load FilesUtils class
	require('files_utils.php');
	
	// Set here the primary language
	const PRIMARY_LANG = 'pt-br';
	
	echo "===== Get all languages to be processed... =====<br><br>";
	
	$langFiles = FilesUtils::getFiles('langs');
	
	echo "Languages files loaded: <b>" . implode('</b>, <b>', $langFiles) . '</b><br><br>';
	
	echo "===== Generating static files... =====<br><br>";
	
	// Get all files to be processed
	
	$filesPath = 'files';
	$files = FilesUtils::getAllFiles($filesPath);
	
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
		$extension = FilesUtils::getExtension($file);
		$targetExtension = $extension == "php" ? "html" : $extension;
		
		echo "* Processing " . (is_file($filesPath . '/' . $file) ? "file" : "directory") .  ": " . $file . "<br>";
		
		// Generates it with each lang
		foreach ($langFiles as $langFile) {
			$langFilename = FilesUtils::removeExtension($langFile);
			$langDir = 'output/' . $langFilename;
			
			$langFinalPath = ($langFilename == PRIMARY_LANG ? 'output/' : $langDir . '/') . $filename . '.' . $targetExtension;
			
			if (is_dir($filesPath . '/' . $file)) {
				// If it is a directory, just creates it
				$langFinalPath = ($langFilename == PRIMARY_LANG ? 'output/' : $langDir . '/') . $filename;
				mkdir($langFinalPath);
				echo "&nbsp;&nbsp;&nbsp;| Lang: " . $langFilename . ". Folder created<br>";
			} else {
				// Otherwise, do something with the file
				$langFinalPath = ($langFilename == PRIMARY_LANG ? 'output/' : $langDir . '/') . $filename . '.' . $targetExtension;
				
				if ($extension == "php") {
					// If the file is php, then process it
					echo "&nbsp;&nbsp;&nbsp;| Lang: " . $langFilename . "<br>";
					include('langs/' . $langFile);
					ob_start();
					include($filesPath . '/' . $file);
					file_put_contents($langFinalPath, ob_get_contents());
					ob_end_clean();	
				} else {
					// Otherwise, just copy it to the language folder
					echo "&nbsp;&nbsp;&nbsp;| Lang: " . $langFilename . ". File copied<br>";
					copy($filesPath . '/' . $file, $langFinalPath);
				}
			}
		}
		
		echo "&nbsp;&nbsp;&nbsp;| Done.<br><br>";
	}