<?php
function __autoload($class)
{
		$class = 'lib/'.str_replace('\\', '/', $class).'.php';
		require $class;
}

