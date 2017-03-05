<?php

namespace Collection;

if (!function_exists("\Collection\AppendValues")) {

	function AppendValues($data, $n, $v) {
		yield from $data;
		for ($i = 0; $i < $n; $i++) {
			yield  $v;
		}
	}

}