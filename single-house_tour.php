<?php
	if( ! defined( 'ABSPATH' ) )	{ die(); }

	$redirectLink = get_field('hour_tour_url');
	
	echo "<script type='text/javascript'>
        window.location.href = '" . $redirectLink . "';
    </script>";
	exit;

	
