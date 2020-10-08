<?php
require 'Admin/include.php';
require 'Dashboard/include.php';

class dash_absensi{
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
			'script'	=> array('dash_absensi','dash_script'),
			'func'		=> '_layout',
			'object'	=> 'dash_head1',
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

	public static function dash_punishment(){
		return dash_head1::_statistic();
	}
}