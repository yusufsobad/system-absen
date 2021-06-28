<?php
class dash_head2{
	public static function _layout(){
		metronic_layout::sobad_chart(self::_data());
		self::_birthday();
		self::_contract();
		self::_massDayOff();
	}

	public static function _data(){
		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'dash-punishment',
				'func'		=> 'dash_punishment',
				'status'	=> '',
				'col'		=> 8,
				'label'		=> 'Punishment '. conv_month_id(date('m')) . ' '. date('Y'),
				'type'		=> ''
			),
		);
		
		return $chart;
	}

	public static function _birthday(){
		$date = strtotime(date('Y-m-d'));
		$besuk = date('-m-d',strtotime('+1 days',$date));
		$today = date('-m-d');

		$whr = "AND (`abs-user-meta`.meta_key = '_birth_date' AND (`abs-user-meta`.meta_value LIKE '%$today%' OR `abs-user-meta`.meta_value LIKE '%$besuk%'))";
		$user = sobad_user::get_all(array('name','picture','_birth_date'),$whr);
		?>
			<div class="col-md-4 col-sm-4">
					<div class="portlet light ">
						<div class="portlet-title">
							<div class="caption">
								<i class="icon-share font-blue-steel hide"></i>
								<span class="caption-subject font-blue-steel bold uppercase">Ulang Tahun</span>
							</div>
							<div class="actions">
							</div>
						</div>
						<div class="portlet-body">
							<div class="slimScrollDiv">
								<div class="scroller">
									<div class="row">
										<?php
											foreach ($user as $key => $val) {
												$birthday = $val['_birth_date'];
												$birthday = explode('-', $birthday);
												unset($birthday[0]);

												$birthday = implode('-', $birthday);
												$birthday = '-'.$birthday;
												if($birthday==$today){
													$status = '<span class="label label-sm label-success label-mini">Hari ini</span>';
												}else{
													$status = '<span class="label label-sm label-info">Next</span>';
												}

												$umur = date($val['_birth_date']);
												$umur = strtotime($umur);
												$umur = $date - $umur;
												$umur = floor($umur / (60 * 60 * 24 * 365))." th";
												
												$img = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict'];
												?>
													<div class="col-md-12 user-info">
														<img style="width:50px;" alt="" src="asset/img/user/<?php print($img) ;?>" class="img-responsive">
														<div class="details">
															<div>
																<a href="javascript:;"><?php print($val['name']) ;?></a>
																<?php print($status) ;?>
															</div>
															<div>
																 <?php echo format_date_id(date('Y').$birthday).' ('.$umur.')' ;?>
															</div>
														</div>
													</div>
												<?php
											}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		<?php
	}

	public static function _contract(){
		$today = date('Y-m-d');

		$whr = "AND `abs-user`.status IN (1,2,3)";
		$user = sobad_user::get_all(array('name','picture','status','_entry_date'),$whr);

		?>
			<div class="col-md-4 col-sm-4">
					<div class="portlet light ">
						<div class="portlet-title">
							<div class="caption">
								<i class="icon-share font-blue-steel hide"></i>
								<span class="caption-subject font-blue-steel bold uppercase">Habis Kontrak</span>
							</div>
							<div class="actions">
							</div>
						</div>
						<div class="portlet-body">
							<div class="slimScrollDiv">
								<div class="scroller">
									<div class="row">
										<?php
											foreach ($user as $key => $val) {
												$life = employee_absen::_check_lifetime($val['status'],$val['_entry_date']);
												if($life['masa']<-10){
													continue;
												}

												$masa = empty($life['masa'])?'':$life['masa'].' Hari';
												$end_date = $life['end_date'];

												if($end_date>=$today){
													$status = '<span class="label label-sm label-success label-mini">Kontrak</span>';
												}else{
													$status = '<span class="label label-sm label-info">Next</span>';
												}
												
												$img = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict'];
												?>
													<div class="col-md-12 user-info">
														<img style="width:50px;" alt="" src="asset/img/user/<?php print($img) ;?>" class="img-responsive">
														<div class="details">
															<div>
																<a href="javascript:;"><?php print($val['name']) ;?></a>
																<?php print($status) ;?>
															</div>
															<div>
																 <?php echo format_date_id($life['end_date']).' ('.$masa.')' ;?>
															</div>
														</div>
													</div>
												<?php
											}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		<?php
	}

	public static function _massDayOff(){
		$now = date('Y-m-d');
		$_y = date('Y');
		$s = date('Y').'-01-05';
		$f = (date('Y')+1).'-01-04';

		$whr = "AND status='3' AND holiday BETWEEN '$s' AND '$f'";
		$holiday = sobad_holiday::get_all(array('holiday','title'),$whr);

		$_option = sobad_module::get_all(array('meta_note'),"AND meta_key='opt_dayoff' AND meta_reff='$_y'");
		$_option = $_option[0]['meta_note'];

		$sisa = $_option - count($holiday);
		?>
			<div class="col-md-4 col-sm-4">
					<div class="portlet light ">
						<div class="portlet-title">
							<div class="caption">
								<i class="icon-share font-blue-steel hide"></i>
								<span class="caption-subject font-blue-steel bold uppercase">Cuti Bersama (Sisa : <?php print($sisa) ;?> Hari)</span>
							</div>
							<div class="actions">
							</div>
						</div>
						<div class="portlet-body">
							<div class="slimScrollDiv">
								<div class="scroller">
									<ul class="feeds">
										<?php
											foreach ($holiday as $key => $val) {
												if($val['holiday']<=$now){
													$status = '
															<div class="label label-sm label-danger">
																<i class="fa fa-bell-o"></i>
															</div>
													';
												}else{
													$status = '
															<div class="label label-sm label-success">
																<i class="fa fa-bell-o"></i>
															</div>
													';
												}

												?>
													<li>
														<a href="javascript:;">
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1" style="margin-top: 12px;">
																		<?php print($status); ?>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			<span style="font-size: 16px;color:#666;">
																				<?php print(format_date_id($val['holiday'])) ;?>
																			</span>
																			<div>
																				<?php print($val['title']) ;?>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</a>
													</li>
												<?php
											}
										?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
		<?php
	}

	public static function _statistic(){
		$now = date('Y-m-d');
		$label = array();

		$data = array();
		$data[0]['label'] = 'Punishment';
		$data[0]['type'] = '';

		$data[0]['bgColor'] = array();
		$data[0]['brdColor'] = 'rgba(256,256,256,1)';

		$data[0]['data'] = array();

		$user = sobad_user::get_all(array('ID','name'),"AND status!='0'");
		foreach ($user as $key => $val) {
			$log = report_absen::_checkLate($val['ID'],$now);

			$color = 0;
			if($log['qty']>0){
				if($log['qty']>2){
					$color = 1; // orange
				}

				if($log['status']>1){
					$color = 2; // merah
				}

				$label[] = $val['name'];
				$data[0]['data'][] = $log['qty'];
				$data[0]['bgColor'][] = dash_absensi::get_color($color,0.8);
			}
		}

		$args = array(
			'type'		=> 'horizontalBar',
			'label'		=> $label,
			'data'		=> $data,
			'option'	=> '_option_bar'
		);
		
		return $args;
	}
}