<?php error_reporting(E_ALL ^ E_NOTICE); ?>

<!DOCTYPE html>
<html>

<!--
    Author: Dmitri Popov, dmpop@linux.com
    License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt
    Source code: https://github.com/dmpop/memories
-->

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width">
	<link rel="shortcut icon" href="favicon.png" />
	<link rel="stylesheet" href="lit.css" type="text/css">
	<title>Memories</title>
</head>

<body>
	<div class="c">
		<img style="height: 3em; margin-right: 0.5em;" src="favicon.svg" alt="logo" />
		<h1 style="margin-left: 0.19em; letter-spacing: 0.3em; margin-top: 0em; color: #5f8dd3;">Memories</h1>
		<hr style="margin-bottom: 1.5em;">

		<?php
		$photos = "photos";
		$tims = "tims";
		$current_date = date('d-m');

		function createTim($original, $tim, $timWidth)
		{
			$img = @imagecreatefromjpeg($original);
			if (!$img) return false;
			$width = imagesx($img);
			$height = imagesy($img);
			$new_width	= $timWidth;
			$new_height = floor($height * ($timWidth / $width));
			$tmp_img = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			$ok = @imagejpeg($tmp_img, $tim);
			imagedestroy($img);
			imagedestroy($tmp_img);
			return $ok;
		}
		if (!file_exists($tims)) {
			mkdir($tims, 0755, true);
		}
		array_map('unlink', glob("$tims/*.*"));

		$files = glob($photos . DIRECTORY_SEPARATOR . '*.{jpg,jpeg,JPG,JPEG}', GLOB_BRACE);
		foreach ($files as $file) {
			$f = $photos . DIRECTORY_SEPARATOR . basename($file);
			$exif = @exif_read_data($f);
			$dm = date("d-m", strtotime($exif['DateTimeOriginal']));
			if ($current_date == $dm) {
				$t = $tims . DIRECTORY_SEPARATOR . basename($file);
				createTim($f, $t, 800);
				echo "<h2>" . $exif['DateTimeOriginal'] . "</h2>";
				echo '<p><img src="' . $t . '" alt="" /></p>';
				echo '<p>' . $exif['COMMENT']['0'] . '</p>';
			}
		}
		?>

		<hr style="margin-top: 1em;">
		<p>This is <a href="https://github.com/dmpop/memories">Memories</a>
	</div>
</body>

</html>