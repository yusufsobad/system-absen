<?php

// ---------------------------------------------
// Create To PDF -------------------------------
// ---------------------------------------------
function sobad_convToPdf($args = array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	date_default_timezone_set('UTC');

	ob_start();

	if(isset($_SESSION[_prefix.'development']) && $_SESSION[_prefix.'development']==1){
		echo '<style type="text/css">';
		foreach($args['style'] as $key => $val){
			if(is_callable($val)){
				echo $val();
			}
		}
		echo '</style>';
	}else{
		echo get_style($args['style']);
	}

	if(is_callable($args['html'])){
		$args['html']($args['data']);
	}

	if(isset($args['object'])){
		if(class_exists($args['object'])){
			$object = $args['object'];
			
			if(is_callable(array(new $object(),$args['html']))){
				$func = $args['html'];
				$object::{$func}($args['data']);
			}
		}
	}
	
	$content = ob_get_clean();

	if(isset($_SESSION[_prefix.'development']) && $_SESSION[_prefix.'development']==1){
		return $content;
	}

	$pos = $args['setting']['posisi'];
	$lay = $args['setting']['layout'];
	$nama = $args['name save'];
	
	try{
		$html2pdf = new HTML2PDF($pos, $lay, 'en', true, 'UTF-8',array(0,0,0,0));
		$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
		$html2pdf->Output($nama.".pdf");
	}
	catch(HTML2PDF_exception $e) {
		echo $e;
		exit;
	}

}
