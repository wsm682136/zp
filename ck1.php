<?php

$lines = file('err.log', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
$count = 0;

foreach ($lines as $line) {
	$count += 1;
	echo str_pad($count, 2, 0, STR_PAD_LEFT) . ". " . $line . "<br>";
}
