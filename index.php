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
		<h1>Memories</h1>
		<hr style="margin-bottom: 1.5em;">

		<?php
		$photos = "../photos";
		$thumbnails = "tims";
		$current_date = date('d-m');

		$files = glob($photos . DIRECTORY_SEPARATOR . '*.{jpg,jpeg,JPG,JPEG}', GLOB_BRACE);
		$fileCount = count($files);
		for ($i = ($fileCount - 1); $i >= 0; $i--) {
			$file = $files[$i];
			$tim = $photos . DIRECTORY_SEPARATOR . $thumbnails . DIRECTORY_SEPARATOR . basename($file);
			$filepath = pathinfo($file);
			$exif = exif_read_data($file);
			$dt = $exif['DateTimeOriginal'];
			$dm = date("d-m", strtotime($dt));
			if ($dm == $current_date) {
				echo "<h2>" . $exif['EXIF']['DateTimeOriginal'] . "</h2>";
				echo '<p><img src="' . $tim . '" alt="" width="800"/></p>';
				echo '<p>' . $exif['COMMENT']['0'] . '</p>';
			}
		}
		?>

		<hr style="margin-top: 1em;">
		<p>This is <a href="https://github.com/dmpop/memories">Memories</a>
	</div>
</body>

</html>