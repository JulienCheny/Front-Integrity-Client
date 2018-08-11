<?php

require_once './class.Diff.php';

$logfile = 'check.log';
$str = file_get_contents('checklist.json');
$checkList = json_decode($str, true);


foreach($checkList as $website => $fileArray) {
	if(is_array($fileArray)) {
		////////  Mode page par page /////////

	    foreach($fileArray as $remoteFilename => $localFilename) {
	        checkFiles('https://' . $website . $remoteFilename,'websites/' . $website . $localFilename);
		}
	}
	else {
		if($fileArray == 'all') {
			////////  Mode front entier /////////
			$localFilenameArray = getDirContentFiles('websites/' . $website);
			foreach($localFilenameArray as $key => $value) {
				$relativePath = str_replace('/srv/IntegrityChecker/websites/', '', $value);
				checkFiles('https://' . $relativePath,'websites/' . $relativePath);
				var_dump(hash_file('haval160,4', $value));
			}
		}
	}
}

function log($str) {
    file_put_contents($logfile, $str, FILE_APPEND);
}

function getDirContentFiles($dir, &$results = array()){
    $files = scandir($dir);
    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContentFiles($path, $results);
        }
    }

    return $results;
}

function checkFiles($remoteName, $localName) {
	$remoteFile = file_get_contents($remoteName);
	$localFile = file_get_contents($localName);
				
	if($remoteFile == false) {
	    echo 'file ' . $remoteName . ' error, remote file dont exist. ';
	    log('file ' . $remoteName . ' error, remote file dont exist. ');
	}
	if(strcmp($remoteFile, $localFile) != 0) {
	    echo 'file ' . $remoteName . ' and local file ' . $localName . ' error, are not the same : ' . $diff = Diff::compareFiles($localName, $remoteName);
	    log('file ' . $remoteName . ' and local file ' . $localName . ' error, are not the same : ' . $diff = Diff::compareFiles($localName, $remoteName));
	}
}
