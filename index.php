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

		function rsearch($dir, $tims, $pattern_array)
		{
			$return = array();
			$iti = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
			foreach (new RecursiveIteratorIterator($iti) as $file => $details) {
				if (!is_file($iti->getBasename()) && ($iti->getBasename() != $tims)) {
					$file_ext = pathinfo($file, PATHINFO_EXTENSION);
					if (in_array(strtolower($file_ext), $pattern_array)) {
						$return[] = $file;
					}
				}
			}
			return $return;
		}

		if (isset($_COOKIE['memories'])) {
			$files = glob($tims . DIRECTORY_SEPARATOR . '*.{jpg,jpeg,JPG,JPEG}', GLOB_BRACE);
			foreach ($files as $tim) {
				$txt = $tim . ".txt";
				if (file_exists($txt)) {
					$h2 = file($txt)[0];
					echo "<h2>" . $h2 . "</h2>";
				}
				echo '<p><img src="' . $tim . '" alt="" /></p>';
				if (file_exists($txt)) {
					$comment = file($txt)[1];
					echo '<p style="margin-bottom: 2em;">' . $comment . '</p>';
				}
			}
		} else {

			if (!file_exists($tims)) {
				mkdir($tims, 0755, true);
			}

			array_map('unlink', glob("$tims/*.*"));

			$files = rsearch($photos, $tims, array('jpg', 'jpeg'));
			foreach ($files as $file) {
				$exif = @exif_read_data($file);
				$dm = date("d-m", strtotime($exif['DateTimeOriginal']));
				if ($current_date == $dm) {
					$tim = $tims . DIRECTORY_SEPARATOR . basename($file);
					createTim($file, $tim, 800);
					file_put_contents($tims . DIRECTORY_SEPARATOR . basename($tim) . ".txt", date("l, M d Y, G:s", strtotime($exif['DateTimeOriginal'])) . "\n" . $exif['COMMENT']['0'], FILE_APPEND | LOCK_EX);
					echo "<h2>" . date("l, M d Y, G:s", strtotime($exif['DateTimeOriginal'])) . "</h2>";
					echo '<p><img src="' . $tim . '" alt="" /></p>';
					if (!empty($exif['COMMENT']['0'])) {
						echo '<p style="margin-bottom: 2em;">' . $exif['COMMENT']['0'] . '</p>';
					}
				}
			}
			setcookie('memories', 1, strtotime('today 23:59'), '/');
			if (count(glob("$tims/*")) === 0) {
				echo '<p>No photos from the past today :-( </p>';
			}
		}
		?>

		<hr style="margin-top: 1em;">
		<p>This is <a href="https://github.com/dmpop/memories">Memories</a>
	</div>
</body>

</html>