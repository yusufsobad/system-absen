<?php
function template_default($args=array(),$data=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	?>
	<page backtop="10mm" backbottom="20mm" backleft="15mm" backright="15mm" pagegroup="new">
		<page_footer>
			<?php print(footer_type1()) ;?>
		</page_footer>
		<table style="width:180mm;font-family:calibri">
		<?php 
		foreach($args as $key => $vals){
			echo '<'.$key.'>';
			foreach($vals as $ky => $func){ ;?>
					<tr>
						<td>
							<?php 
								if(is_callable($func)){
									$func($data[$key][$ky]);
								}
							?>
						</td>
					</tr>
				<?php }
			echo '</'.$key.'>';
		}
		?>
		</table>
	</page>
	<?php
}

function get_style($style=array()){
	$check = array_filter($style);
	if(empty($check)){
		return '';
	}
	?>
		<style type="text/css">
		<!--
			<?php
				foreach($style as $key => $val){
					if(is_callable($val)){
						echo $val();
					}
				}
			?>
		-->
		</style>
	<?php
}

function style_type1(){
	?>
		page{
			background:#e6e6e6;
		}
		table.page_header {
			width: 100%; 
			border: none; 
			background-color: #fff; 
			border-bottom: solid 1mm #aaaadd; 
			padding: 2mm 
		}
		table.page_footer {
			width: 100%; 
			height: 10mm;
			border: none; 
			border-top: 2px solid #aaaadd; 
			padding: 2mm;
		}
		
		div.header_kmi {
			width: 100%;
			height: 25.4mm;
		}
		div.header_kmi div.logo_header {
			width: 69mm;
			float: left;
			height: 25.4mm;
		}
		div.logo_header img {
			width: 41mm;
			position: relative;
			top: 20px;
			left: 0%;
		}
		div.header_kmi div.gradient_header {
			width: 109mm;
			height: 100%;
			color: #fff;
			position:absolute;
			top: 10mm;
			left: 69mm;
		}
		div.gradient_header img{
			width:100%;
		}
		div.gradient_header label {
			font-size: 19pt;
			position: absolute;
			left: 120px;
			top: 30px;
		}
		div.gradient_header label span {
			text-transform: uppercase;
			font-weight: bold;
			font-family:calibriBold;
			position: relative;
			left: -20px;
			top:5px;
		}
		
		.txt_content{
			width:100%;
			font-size:10pt;
		}
		.sub_head{
			font-size:16pt;
			font-weight:bold;
			font-family:calibriBold;
		}
		.sub_head3{
			font-size:10pt;
			font-weight:bold;
			font-family:calibriBold;
		}
		
		table.layout1 thead tr td{
			padding:5px 10px 5px 5px;
		}
		
		table.layout1 tbody tr td{
			padding:5px 10px 5px 5px;
			vertical-align:top;
		}
		
		.tbl-title{
			font-size:12pt;
		}
		.bg-blue-kmi{
			background-color:#00A2E9;
			font-family:calibriBold;
			color:#fff;
		}
		.bg-dark-kmi{
			background-color:#3C3F47;
			font-family:calibriBold;
			color:#fff;
		}
		.foot_template img{
			margin-right:15px;
		}
		.foot_template div{
			margin-bottom:5px;
			margin-left:50px;
			font-size:10px;
		}
	<?php
}

function style_type2(){
	?>
		table {
 			border-spacing: 0;
 			border-collapse: collapse;
		}
		
		.table-bordered thead tr th{
			font-family: calibriBold;
		}

		.table-bordered thead tr th,
		.table-bordered tbody tr td {
			font-size: 14px;
			border:1px solid #000;
		}
	<?php
}

function heading_type1($title){
	?>
		<div class="header_kmi">
			<div class="logo_header">
				<img src="../asset/img/Logo KMI.png">
			</div>
			<div class="gradient_header">
				<img style="margin-top: 30px" src="../asset/img/template/header type 2.png">
				<?php print($title) ;?>
			</div>
		</div>
	<?php
}

function address_type1($data=array()){
	$check = array_filter($data);
	if(empty($check)){
		return '';
	}
	
	?>
		<table style="width:100%;">
			<tbody>
				<tr>
					<td colspan="2">
						<span style="font-size:10pt;">
							To :
						</span>
					</td>
				</tr>
				<tr>
					<td style="width:65%;vertical-align: top;">
						<span class="sub_head3">
							<?php print($data['company']) ;?><br>
						</span>
						<span class="txt_content">
							<?php print($data['comp_addr']) ;?><br>
						</span>
						<span class="txt_content">
							<?php print($data['comp_phone']) ;?>
						</span>
					</td>
					<td style="width:35%;vertical-align: top;">
						<span class="sub_head3">No. </span>
						<span class="txt_content" style="margin-left:25px">
							<?php print($data['post_code']) ;?>
						</span>
						<br>
						<span class="sub_head3">Date </span>
						<span class="txt_content" style="margin-left:16px">
							<?php print(format_date_id($data['post_date'])) ;?>
						</span>
					</td>
				</tr>
				<tr>
					<td style="width:65%;vertical-align: top;">
						<span class="sub_head3">
							<?php print($data['contact']) ;?>
						</span><br>
						<span class="txt_content">
							<?php print($data['cont_phone']) ;?>
						</span>
					</td>
					<td style="width:35%;vertical-align: top;">
						
					</td>
				</tr>
			</tbody>
		</table>
	<?php
}

function address_type2($data=array()){
	$check = array_filter($data);
	if(empty($check)){
		return '';
	}

	$note = json_decode($data['description'],true);
	$note = $note['invoice'];

	$user = new malika_user();
	$sales = $user->get_user($note['data']['sales'],array('username'));
	$sales = $sales[0]['username'];
	
	?>
		<table style="width:100%;">
			<tbody>
				<tr>
					<td colspan="2">
						<span style="font-size:10pt;">
							To :
						</span>
					</td>
				</tr>
				<tr>
					<td style="width:55%;vertical-align: top;">
						<span class="sub_head3">
							<?php print($data['contact']) ;?><br>
						</span>
						<span class="sub_head3">
							<?php print($data['company']) ;?><br>
						</span>
						<span class="sub_head3">
							<?php print($data['comp_addr']) ;?><br>
						</span>
					</td>
					<td style="width:45%;vertical-align: top;">
						<span class="sub_head3">Invoice Date</span>
						<span class="txt_content" style="margin-left:16px">
							: <?php print(format_date_id($data['post_date'])) ;?>
						</span>
						<br>
						<span class="sub_head3">Invoice No.</span>
						<span class="txt_content" style="margin-left:23px">
							: <?php print($data['post_code']) ;?>
						</span>
						<br>
						<span class="sub_head3">Project No</span>
						<span class="txt_content" style="margin-left:27px">
							: <?php print($note['data']['project_no']) ;?>
						</span>
						<br>
						<span class="sub_head3">Sales</span>
						<span class="txt_content" style="margin-left:57px">
							: <?php print($sales) ;?>
						</span>
					</td>
				</tr>
				<tr>
					<td style="width:55%;vertical-align: top;">
						
					</td>
					<td style="width:45%;vertical-align: top;">
						<i style="margin-top: 5px;"></i>
						<br>
						<span class="txt_content"> Referensi </span>
						<br>
						<span class="sub_head3">DO Number</span>
						<span class="txt_content" style="margin-left:20px">
							: <?php print($note['referensi']['DO']) ;?>
						</span>
						<br>
						<span class="sub_head3">PO Number</span>
						<span class="txt_content" style="margin-left:21px">
							: <?php print($note['referensi']['PO']) ;?>
						</span>
						<br>
						<i style="margin-top: 5px;">&nbsp;</i>
					</td>
				</tr>
			</tbody>
		</table>
	<?php
}

function payment_info($data=array()){

	$note = json_decode($data['description'],true);
	$info = array(
		0	=> array(
			'title'	=> 'Estimasi Pengerjaan',
			'text'	=> $note['est'],
		),
		1	=> array(
			'title'	=> 'System Pembayaran',
			'text'	=> $note['pay'],
		),
		2	=> array(
			'title'	=> 'Keterangan',
			'text'	=> $note['note'],
		),
	);
	?>
		<table style="width:100%;margin-top:50px;">
			<tbody>
				<?php 
				foreach($info as $key => $val){
					echo '
						<tr>
							<td class="txt_content" style="width:25%;vertical-align:top;">'.$val['title'].'</td>
							<td class="txt_content" style="width:5%;vertical-align:top;">:</td>
							<td class="txt_content" style="width:70%">'.$val['text'].'</td>
						</tr>
					';
				}
				?>
				<tr>
					<td colspan="3" class="txt_content" style="padding:10px 0px;">
						Mudah-mudahan penawaran harga yang kami buat cukup menarik
					</td>
				</tr>
				<tr>
					<td colspan="3" class="txt_content">
						Kami sangat berharap kabar gembira dari Bapak/Ibu untuk mempercayakan project ini kepada kami.
					</td>
				</tr>
				<tr>
					<td colspan="3" class="txt_content">
						Atas perhatian dan kerjasamanya kami ucapkan terimakasih.
					</td>
				</tr>
				<tr>
					<td colspan="3" class="txt_content" style="padding-top:35px;">
						Best Regard,
					</td>
				</tr>
				<tr>
					<td colspan="3" class="txt_content" style="padding-top:10px;">
						<img style="width:150px;" src="../asset/img/<?php print($data['sales']) ;?>.jpg">
					</td>
				</tr>
				<tr>
					<td colspan="3" class="txt_content" style="padding-top:10px;font-family:calibriBold;">
						<?php print(ucwords($data['sales'])) ;?><br>
						<?php print($data['sales_phone']) ;?>
					</td>
				</tr>
			</tbody>
		</table>
	<?php
}

function invoice_info($data=array()){
	$info = $data['info'];
	$bank = $data['bank'];

	?>
		<table style="width:100%;margin-top:5px;">
			<tbody>
				<?php 
				foreach($info as $key => $val){
					echo '
						<tr class="txt_content">
							<td class="txt_content" style="width:20%;font-family:calibriBold;">'.$val['title'].'</td>
							<td class="txt_content" style="width:3%">:</td>
							<td class="txt_content" style="width:77%">'.$val['text'].'</td>
						</tr>
					';
				}

				echo '
					<tr>
						<td colspan="3" >
							&nbsp;
						</td>
					</tr>';

				echo '
					<tr>
						<td colspan="3">
							<table style="width:100%;">
								<tbody>
				';

							foreach($bank as $key => $val){
								echo '
									<tr>
										<td style="width:10%;">Bank</td>
										<td style="width:3%">:</td>
										<td style="width:87%">'.$val['bank'].'</td>
									</tr>
									<tr>
										<td style="width:10%;">A/C No.</td>
										<td style="width:3%">:</td>
										<td style="width:87%">'.$val['no_rek'].'</td>
									</tr>
									<tr>
										<td style="width:10%;">Name</td>
										<td style="width:3%">:</td>
										<td style="width:87%">'.$val['name'].'</td>
									</tr>
									<tr>
										<td colspan="3" >
											&nbsp;
										</td>
									</tr>
								';
							}
				
				echo '
								</tbody>
							</table>
						</td>
					</tr>
				';
				?>
				<tr>
					<td colspan="3" class="txt_content">
						If you havee any question about this invoice,<br>please contact us.<br>Thank you for your business
					</td>
				</tr>
				<tr>
					<td colspan="3" class="txt_content" style="padding-top:35px;padding-right:40px;text-align: right;">
						Best Regard,
					</td>
				</tr>
				<tr>
					<td colspan="3" class="txt_content" style="padding-top:40px;text-align: right;">
						_____________________
					</td>
				</tr>
			</tbody>
		</table>
	<?php
}

function footer_type1($info=''){
	?>
		<table class="page_footer">
			<tr>
				<td>
					<div class="foot_template">
						<div>
							<img align="left" style="width:13px;" src="../asset/img/template/footer location.png">
							Jl. Slamet Raya Tawangsari RT.04 RW.34 <br>
							Mojosongo, Jebres, Surakarta, Jawa Tengah - Indonesia
						</div>
						<div>
							<img style="width:11px;" src="../asset/img/template/footer whatsapp.png"> 0812 6600 6161 
						</div>
						<div>
							<img style="width:11px;" src="../asset/img/template/footer email.png"> admin@kreasimudaindonesia.com 
						</div>
						<div>
							<img style="width:11px;" src="../asset/img/template/footer web.png"> www.kreasimudaindonesia.com
						</div>
					</div>
				</td>
			</tr>
        </table>
	<?php
}