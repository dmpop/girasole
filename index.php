<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('max_execution_time', '3600');
$library = "photos"; // Relative or absolute path to the photo library root
$tims = "tims"; // Directory for saving tims
$excluded = "tims"; // Directory to exclude from search (useful for excluding thumbnails)
$ext = array('jpg', 'jpeg'); // File types to search
$footer = "This is <a href='https://github.com/dmpop/memories'>Memories</a>. Read the <a href='https://gumroad.com/l/linux-photography'>Linux Photography</a> book."; // Footer
?>

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
		<img style="height: 5em; margin-bottom: 1.5em;" src="favicon.svg" alt="logo" />
		<?php
		$current_date = date('d-m');

		function rsearch($dir, $excluded, $pattern_array)
		{
			$return = array();
			$iti = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
			foreach (new RecursiveIteratorIterator($iti) as $file => $details) {
				if (!is_file($iti->getBasename()) && ($iti->getBasename() != $excluded)) {
					$file_ext = pathinfo($file, PATHINFO_EXTENSION);
					if (in_array(strtolower($file_ext), $pattern_array)) {
						$return[] = $file;
					}
				}
			}
			return $return;
		}

		function showTims($tims)
		{
			$files = glob("$tims/*");
			foreach ($files as $tim) {
				$exif = @exif_read_data($tim);
				echo "<h2>" . date("Y / l / G:s", strtotime($exif['DateTimeOriginal'])) . "</h2>";
				echo '<p><img loading="lazy" src="' . $tim . '" alt="" /></p>';
				echo '<p style="margin-bottom: 2em;">' . $exif['COMMENT']['0'] . '</p>';
			}
		}

		if (!extension_loaded('exif')) {
			die("<div>⚠️ The PHP exif extension is missing</div>");
		}
		if (!file_exists($library)) {
			die("<div>⚠️ The <u>$library</u> directory does not exist</div>");
		}
		if (!file_exists($tims)) {
			mkdir($tims, 0755, true);
		}

		if (date("d-m", filemtime($tims)) === $current_date && count(glob("$tims/*")) !== 0) {
			showTims($tims);
		} else {

			array_map('unlink', glob("$tims/*.*"));
			$files = rsearch($library, $tims, $ext);
			foreach ($files as $file) {
				$exif = @exif_read_data($file);
				$dm = date("d-m", strtotime($exif['DateTimeOriginal']));
				if ($current_date == $dm) {
					copy($file, $tims . DIRECTORY_SEPARATOR . basename($file));
				}
			}
			showTims($tims);
		}
		if (count(glob("$tims/*")) === 0) {
			die("<div>🪣 No photos from the past today</div>");
		}
		?>
		<div class="footer"><?php echo $footer; ?></div>
	</div>
</body>

</html>