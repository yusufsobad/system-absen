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

	echo get_style($args['style']);

	if(is_callable($args['html'])){
		$args['html']($args['data']);
	}
	
	$content = ob_get_clean();

//return $content;
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
