<?php
	function clean($input){
		return htmlspecialchars(stripslashes(trim($input)));
	}
?>