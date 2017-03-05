<?php

namespace Collection;

if (!function_exists("\Collection\AppendValues")) {

	function AppendValues($data, $n, $v) {
		$result = $data;
		for ($i = 0; $i < $n; $i++) {
			$result[] = $v;
		}
		return $result;
	}

}