<?php

namespace Collection;

if (version_compare(PHP_VERSION, "7", "<")) {
	require __DIR__ . "/collection.php";
} else {
	require __DIR__ . "/collection.php7.php";
}
