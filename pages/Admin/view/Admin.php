<?php
require 'Admin/include.php';
require 'Dashboard/include.php';

class dash_admin{
	private static function head_title(){
		$args = array(
			'title'	=> 'Dashboard <small>reports & statistics</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> 'dash_absensi',
					'label'	=> 'dashboard'
				)
			),
			'date'	=> false
		);
		
		return $args;
	}

	public static function _sidemenu(){
		$label = array();
		$data = array();

		$data[] = array(
			'style'		=> array(),
			'script'	=> array(''),
			'func'		=> '_layout',
			'object'	=> 'admin_head1',
			'data'		=> ''
		);
		
		$title = self::head_title();
		
		ob_start();
		metronic_layout::_head_content($title);
		metronic_layout::_content('_panel',$data);
		return ob_get_clean();
	}

	public static function get_color($int=0,$opc=1,$single=true){
		$color = array(
			'rgba(75, 192, 192,'.$opc.')', //green
			'rgba(255, 159, 64,'.$opc.')', //orange
			'rgba(255, 99, 132,'.$opc.')', //red
			'rgba(54, 162, 235,'.$opc.')', //blue
			'rgba(255, 205, 86,'.$opc.')', //yellow
			'rgba(153, 102, 255,'.$opc.')', //purple
			'rgba(201, 203, 207,'.$opc.')' //grey
		);

		if($single){
			$int = $int % 7;
			$warna = $color[$int];
		}else{
			$warna = array();
			foreach ($int as $ky => $val) {
				$val = $val % 7;
				$warna[$ky] = $color[$val];
			}
		}

		return $warna;
	}

	public static function dash_script(){
		?>
			<script type="text/javascript">
				var dash_year = <?php echo date('Y') ;?>;

				$(".chart_malika").ready(function(){
					if($('div').hasClass('chart_malika')){
						for(var i=0;i<$(".chart_malika").length;i++){
							var ajx = $('.chart_malika:eq('+i+')').attr('data-sobad');
							var id = $('.chart_malika:eq('+i+')').attr('data-load');
							var tp = $('.chart_malika:eq('+i+')').attr('data-type');
				
							data = "ajax="+ajx+"&object=dashboard&data="+dash_year+"&type="+tp;
							sobad_ajax(id,data,load_chart_dash);
						}
					}
				});

			// Function Option
				function _option_bar(){
					var option = {
						responsive	: true,
						scales		: {
							yAxes		: [{
								ticks		: {
									callback 	: function(value, index, values) {return value;}
								}
							}],
							xAxes		: [{
								ticks		: {
									beginAtZero: true,
					                userCallback: function(label, index, labels) {
					                    // when the floored value is the same as the value we have a whole number
					                    if (Math.floor(label) === label) {
					                        return label;
					                    }
					                },
								}
							}]
						},
						tooltips	: {
							enabled		: true,
							mode		: 'single',
							callbacks	: {
								label 		: function(value, data) {return value.xLabel;}
							}
						}
					}

					return option;
				}

				function _option_doughnut(){
					var option = {
						responsive	: true,
						animation	: {
							animateScale  : true,
							animateRotate : true
						},
						legend : {
							display : false
						},
						tooltips	: {
							enabled		: true,
							mode		: 'single',
							callbacks	: {
								label 		: function(value, data) {
									var idx = value.index;
									return number_format(data.datasets[0].data[idx]);
								},
								footer		: function(value, data) {
									var idx = value[0].index;
									return data.labels[idx];
								}
							}
						}
					}

					return option;
				}
			</script>
		<?php
	}

	// ----------------------------------------------------
	// Ajax request ---------------------------------------
	// ----------------------------------------------------

	public static function dash_punishment(){
		return dash_head2::_statistic();
	}

	public static function dash_comparison(){
		$func = '_'.$_POST['type'];
		if(is_callable(array(new dash_head3(),$func))){
			return dash_head3::{$func}();
		}

		return '';
	}

	// ----------------------------------------------------
	// View Detail ----------------------------------------
	// ----------------------------------------------------

	public static function _view_block($id=0){
		$id = str_replace('absen_', '', $id);
		intval($id);

		$date = date('Y-m-d');
		$day = date('w');
		$limit = '';

		$args = array('ID','picture','no_induk','name','divisi','status','inserted');
		if($id!=0){
			$args = array('ID','picture','no_induk','name','divisi','status','inserted','type','shift','_inserted','time_in');
			$limit = "AND `abs-user-log`._inserted='$date' AND `abs-user-log`.type='$id'";
		}

		$args = sobad_user::get_all($args,"AND status!='0' $limit");

		$_logs = array();
		if($id==0){
			$logs = sobad_user::get_logs(array('user')," _inserted='$date'");
			foreach ($logs as $ky => $vl) {
				$_logs[] = $vl['user'];
			}
		}

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($args as $key => $val) {
			$userid = $val['ID'];
			if(in_array($val['ID'], $_logs)){
				continue;
			}
			$no += 1;

			$image = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict'];
			$status = employee_absen::_conv_status($val['status']);

			$permit = '';
			if($id==4){
				$whr_permit = "AND user='$userid' AND type!='9' AND start_date<='$date' AND range_date>='$date' OR user='$userid' AND start_date<='$date' AND range_date='0000-00-00' AND num_day='0.0'";
				$_permit = sobad_permit::get_all(array('type'),$whr_permit);
				$check = array_filter($_permit);
				if(!empty($check)){
					$val['type'] = $_permit[0]['type'];
				}

				$permit = permit_absen::_conv_type($val['type']);					
			}

			$timein = '';
			if($id==1){
				$timein = $val['time_in'];

				$work = sobad_work::get_id($val['shift'],array('time_in'),"AND days='$day' AND status='1'");
				$check = array_filter($work);
				if(!empty($check)){
					if($val['time_in']>=$work[0]['time_in']){
						$timein = '<span style="color:red">'.$val['time_in'].'</span>';
					}
				}
			}

			$data['table'][$no-1]['tr'] = array('');
			$data['table'][$no-1]['td'] = array(
				'No'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Profile'	=> array(
					'left',
					'5%',
					'<img src="asset/img/user/'.$image.'" style="width:100%">',
					true
				),
				'NIK'		=> array(
					'left',
					'5%',
					$val['status']==7?internship_absen::_conv_no_induk($val['no_induk'],$val['inserted']):$val['no_induk'],
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$val['name'],
					true
				),
				'Divisi'	=> array(
					'left',
					'20%',
					$val['meta_value_divi'],
					true
				),
				'Status'	=> array(
					'left',
					'13%',
					$status,
					true
				),
				'Masuk'		=> array(
					'left',
					'10%',
					$timein,
					true
				),
				'Note'		=> array(
					'left',
					'15%',
					$permit,
					true
				),
			);

			if($id!=1){
				unset($data['table'][$no-1]['td']['Masuk']);
			}

			if($id!=4){
				unset($data['table'][$no-1]['td']['Note']);
			}
		}

		$args = array(
			'title'		=> 'Detail data',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}
}