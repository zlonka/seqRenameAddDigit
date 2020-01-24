<?php
/*
php seqRenameAddDigit.php <dir> [start]
see usage below
*/
set_time_limit(0);
ini_set("allow_url_fopen",1);
ini_set("memory_limit", "1924M");

if (!isset($argv[1])) die("usage: ".$argv[0]." <dir> [start]
Renames files in dir by adding one more digit in their number part. Ex: IMG_1599.jpg => IMG_01599.jpg
If [start] is defined, add a leading 0 if file number part > start, or a 1 if not. If we have :
	a01.jpg  a02.jpg  a98.jpg  a99.jpg
with start=50 it will become:
	a098.jpg a099.jpg a101.jpg a102.jpg
");

function getExt($file){
	if (strpos($file, ".")===false) return "";
	$t=explode(".", $file);
	return array_pop($t);
}
function listDirRfunc($traitement, $dir, $recurOn, $tExt=array("jpg", "jpeg", "JPG", "JPEG", "gif", "GIF", "png", "PNG")){
	echo "listDirRfunc $dir...\n";
	$tFiles=array();
	$tDirs=array();
	if (!$dh = opendir($dir)) return;
	while (($file = readdir($dh)) !== false) {
		if ($file=="." || $file=="..") continue;
		if (is_dir($dir."/".$file)){
			if ($recurOn) $tDirs[]=$dir."/".$file;
		}
		else{
			$ext=getExt($file);
			if ($ext!="" && in_array($ext, $tExt)) $tFiles[]=$dir."/".$file;
		}
	}
	closedir($dh);
	print_r($tFiles);
	if (count($tFiles)!=0) $traitement(".", $tFiles);
	if ($recurOn && count($tDirs)!=0){
		sort($tDirs);
		foreach($tDirs as $file) listDirRfunc($traitement, $file, $recurOn, $tExt);
	}
}

$dir="popoZ/2016-01-26";	if (isset($argv[1])) $dir=$argv[1];
$dir=str_replace("\\","/",$dir);$dir=str_replace("C:/Users/m/", "../../../", $dir);

$limite=0; if (isset($argv[2])) $limite=$argv[2];

if ($limite==0) echo "Rename IMG_xxxx.jpg en img_0xxxx.jpg pour xxxx entre 1 et 9999\n";
else{
	echo "Rename IMG_xxxx.jpg en img_0xxxx.jpg pour xxxx entre $limite et 9999\n";
	echo "Rename IMG_xxxx.jpg en img_1xxxx.jpg pour xxxx entre 1 et ".($limite-1)."\n";
}
chdir($dir) or die("oops cant go to dir $dir");

$totSameFound=0;
$totRemovedSpace=0;
echo "parsing directory $dir...";

$iFile=1;

// if (!file_exists($dirDest)){ mkdir($dirDest, 0777) or die("cant create dir $dirDest!\n"); }

listDirRfunc("f", ".", false, array("jpg", "jpeg", "JPG", "JPEG"));

echo "done. ".($iFile-1)." files renamed.\n";

function f($dir, $files){
	global $iFile, $dirDest, $limite;
	$n=count($files);
	if ($n<2) return;
	sort($files);
	$firstDigit=-1;
	$lastDigit=-1;
	foreach($files as $f){
		if (filesize($dir."/".$f)<1000){ echo "File $dir"."/"."$f too small : ignored.\n"; continue; }
		if ($firstDigit==-1){
			for($i=0;$i<strlen($f);$i++){
				if ($f{$i}>='0' && $f{$i}<='9'){
					$firstDigit=$i;
					while($i<strlen($f) && $f{$i}>='0' && $f{$i}<='9'){ $lastDigit=$i; $i++; }
					break;
				}
			}
		}
		if ($firstDigit==-1) die("Ooops, file $f has no digit in its name.\n");
		$deb=substr($f, 0, $firstDigit);
		$fin=substr($f, $lastDigit+1);
		$icur=substr($f, $firstDigit, $lastDigit-$firstDigit+1);
		// die("deb=$deb fin=$fin icur=$icur");
		// $icur=1*$icur;
		if ($limite==0){
			$icur="0$icur";
		}
		else{
			if (1*$icur>=$limite) $icur="0$icur";
			else $icur="1".$icur;
		}
		// echo "f=$f icur=$icur"; die("");
		// echo "rename($f, 'img_".sprintf("%06d",1*$icur).".jpg');\n";
		// rename($f, 'img_'.sprintf("%06d",1*$icur).'.jpg');
		echo "rename($f, '$deb".$icur."$fin');\n";
		rename($f, $deb.$icur.$fin);
		$iFile++;
	}
}
?>
