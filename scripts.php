<?php
(!defined('DEFPATH'))?exit:'';

if(!class_exists('vendor_script')){

	class vendor_script{
		public function _get_($func='',$idx=array()){
			if(is_callable(array($this,$func))){
				$script = self::$func($idx);
			}else{
				$script = array();
			}
			
			return $script;
		}
		
		private function lokasi(){
			return 'vendor/';
		}
	// BEGIN GLOBAL MANDATORY STYLES ---->

		private function _css_global($idx=array()){
			$loc = $this->lokasi();
			$css = array(
				'font-awesome'		=> $loc.'font-awesome/css/font-awesome.min.css',
				'jquery-ui'			=> $loc.'jquery-ui/jquery-ui.min.css',
				'simple-line-icon'	=> $loc.'simple-line-icons/simple-line-icons.min.css',
				'bootstrap-core'	=> $loc.'bootstrap/css/bootstrap.min.css',
				'uniform'			=> $loc.'uniform/css/uniform.default.css',
				'bootstrap-switch'	=> $loc.'bootstrap-switch/css/bootstrap-switch.min.css'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$css[$key];
				}
			}
			
			return $css;
		}
		
	// BEGIN PAGE LEVEL PLUGIN STYLES ---->	
		
		private function _css_page_level($idx=array()){
			$loc = $this->lokasi();
			$css = array(
				'select2'				=> $loc.'select2/select2.css',
				'bootstrap-select'		=> $loc.'bootstrap-select/bootstrap-select.min.css',
				'bootstrap-toastr'		=> $loc.'bootstrap-toastr/toastr.min.css',
				'bootstrap-datepicker'	=> $loc.'bootstrap-datepicker/css/datepicker3.css',
				'bootstrap-daterange'	=> $loc.'bootstrap-daterangepicker/daterangepicker-bs3.css',
				'bootstrap-clockpicker'	=> $loc.'bootstrap-clockpicker/bootstrap-clockpicker.min.css',
				'fullcalender'			=> $loc.'fullcalendar/fullcalendar.min.css',
				'bootstrap-editable'	=> $loc.'bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css',
				'bootstrap-wysihtml5'	=> $loc.'bootstrap-wysihtml5/bootstrap-wysihtml5.css',
				'bootstrap-summernote'	=> $loc.'bootstrap-summernote/summernote.css'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$css[$key];
				}
			}
			
			return $css;
		}
		
		private function _css_datatable($idx=array()){
			$loc = $this->lokasi();
			$css = array(
				'table-scroller'		=> $loc.'datatables/extensions/Scroller/css/dataTables.scroller.min.css',
				'table-reorder'			=> $loc.'datatables/extensions/ColReorder/css/dataTables.colReorder.min.css',
				'datatable'				=> $loc.'datatables/plugins/bootstrap/dataTables.bootstrap.css'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$css[$key];
				}
			}
			
			return $css;
		}

		private function _css_chart($idx=array()){
			$loc = $this->lokasi();
			$css = array(
				'table-scroller'		=> $loc.'chartjs/Chart.min.css'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$css[$key];
				}
			}
			
			return $css;
		}

		private function _css_tags_input($idx=array()){
			$loc = $this->lokasi();
			$css = array(
				'bootstrap'			=> $loc.'bootstrap-tagsinput/src/bootstrap-tagsinput.css',
				'jquery-tags'		=> $loc.'jquery-tags-input/jquery.tagsinput.css',
				'typeahead'			=> $loc.'typeahead/typeahead.css',
				'tokenize2'			=> $loc.'Tokenize2/tokenize2.css'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$css[$key];
				}
			}
			
			return $css;
		}

		private function _css_dropzone($idx=array()){
			$loc = $this->lokasi();
			$css = array(
				'dropzone'			=> $loc.'dropzone/css/dropzone.css'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$css[$key];
				}
			}
			
			return $css;
		}

		private function _css_contextmenu($idx=array()){
			$loc = $this->lokasi();
			$css = array(
				'contextmenu'		=> $loc.'jquery-contextmenu/dist/jquery.contextMenu.min.css'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$css[$key];
				}
			}
			
			return $css;
		}

		private function _css_font($idx=array()){
			$loc = $this->lokasi();
			$css = array(
				'Gotham'		=> 'asset/font/Gotham/gotham.css'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$css[$key];
				}
			}
			
			return $css;
		}
		
	// BEGIN CORE PLUGINS ---->	

		private function _js_core($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'jquery-core'			=> $loc.'jquery.min.js',
				'jquery-migrate'		=> $loc.'jquery-migrate.min.js',
				'jquery-ui'				=> $loc.'jquery-ui/jquery-ui.min.js',
				'bootstrap-core'		=> $loc.'bootstrap/js/bootstrap.min.js',
				'bootstrap-hover'		=> $loc.'bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js',
				'jquery-slimscroll'		=> $loc.'jquery-slimscroll/jquery.slimscroll.min.js',
				'jquery-blockui'		=> $loc.'jquery.blockui.min.js',
				'jquery-cokie'			=> $loc.'jquery.cokie.min.js',
				'uniform'				=> $loc.'uniform/jquery.uniform.min.js',
				'bootstrap-switch'		=> $loc.'bootstrap-switch/js/bootstrap-switch.min.js'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
			
			return $js;
		}
		
	// BEGIN PAGE LEVEL PLUGINS ---->	

		private function _js_page_level($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'flot-jquery'			=> $loc.'flot/jquery.flot.min.js',
				'flot-resize'			=> $loc.'flot/jquery.flot.resize.min.js',
				'flot-category'			=> $loc.'flot/jquery.flot.categories.min.js',
				'jquery-pulsate'		=> $loc.'jquery.pulsate.min.js',
				'bootstrap-select'		=> $loc.'bootstrap-select/bootstrap-select.min.js',
				'bootstrap-toastr'		=> $loc.'bootstrap-toastr/toastr.min.js',
				'bootstrap-moment'		=> $loc.'bootstrap-daterangepicker/moment.min.js',
				'bootstrap-datepicker'	=> $loc.'bootstrap-datepicker/js/bootstrap-datepicker.js',
				'bootstrap-daterange'	=> $loc.'bootstrap-daterangepicker/daterangepicker.js',
				'bootstrap-clockpicker'	=> $loc.'bootstrap-clockpicker/bootstrap-clockpicker.min.js',
				'boot-wysihtml5'		=> $loc.'bootstrap-wysihtml5/wysihtml5-0.3.0.js',
				'bootstrap-wysihtml5'	=> $loc.'bootstrap-wysihtml5/bootstrap-wysihtml5.js',
				'bootstrap-summernote'	=> $loc.'bootstrap-summernote/summernote.min.js',
				'fullcalender'			=> $loc.'fullcalendar/fullcalendar.min.js',
				'jquery-piechart'		=> $loc.'jquery-easypiechart/jquery.easypiechart.min.js',
				'jquery-sparkline'		=> $loc.'jquery.sparkline.min.js',
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
			
			return $js;
		}
		
		private function _js_page_modal($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'bootstrap-modalmgr'	=> $loc.'bootstrap-modal/js/bootstrap-modalmanager.js',
				'bootstrap-modal'		=> $loc.'bootstrap-modal/js/bootstrap-modal.js',
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
			
			return $js;
		}
		
		private function _js_page_login($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'flot-jquery'			=> $loc.'jquery-validation/js/jquery.validate.min.js',
				'flot-resize'			=> $loc.'backstretch/jquery.backstretch.min.js',
				'flot-category'			=> $loc.'select2/select2.min.js'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
			
			return $js;
		}
		
		private function _js_form_editable($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'mocjax'				=> $loc.'jquery.mockjax.js',
				'bootstrap-editable'	=> $loc.'bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
			
			return $js;
		}

		private function _js_amChart($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'amChart'				=> $loc.'amcharts/amcharts/amcharts.js',
				'serial'				=> $loc.'amcharts/amcharts/serial.js',
				'pie'					=> $loc.'amcharts/amcharts/pie.js',
				'radar'					=> $loc.'amcharts/amcharts/radar.js',
				'theme-light'			=> $loc.'amcharts/amcharts/themes/light.js',
				'theme-pattern'			=> $loc.'amcharts/amcharts/themes/patterns.js',
				'theme-chalk'			=> $loc.'amcharts/amcharts/themes/chalk.js',
				'ammap'					=> $loc.'amcharts/ammap/ammap.js',
				'map-worldLow'			=> $loc.'amcharts/ammap/maps/js/worldLow.js',
				'amstock'				=> $loc.'amcharts/amstockcharts/amstock.js'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
		
			return $js;
		}

		private function _js_chart($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'core'					=> $loc.'chartjs/Chart.min.js',
				'bundle'				=> $loc.'chartjs/Chart.bundle.min.js'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
		
			return $js;
		}

		private function _js_mask_money($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'mask-money'			=> $loc.'mask-money/jquery.maskMoney.js'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
		
			return $js;
		}

		private function _js_tags_input($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'bootstrap'				=> $loc.'bootstrap-tagsinput/src/bootstrap-tagsinput.js',
				'jquery-tags'			=> $loc.'jquery-tags-input/jquery.tagsinput.min.js',
				'handlebar'				=> $loc.'typeahead/handlebars.min.js',
				'typeahead'				=> $loc.'typeahead/typeahead.bundle.min.js',			
				'tokenize2'				=> $loc.'Tokenize2/tokenize2.js'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
		
			return $js;
		}

		private function _js_dropzone($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'dropzone'				=> $loc.'dropzone/dropzone.js'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
		
			return $js;
		}

		private function _js_contextmenu($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'contextmenu'			=> $loc.'jquery-contextmenu/dist/jquery.contextMenu.min.js',
				'ui-position'			=> $loc.'jquery-contextmenu/dist/jquery.ui.position.min.js'
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
		
			return $js;
		}

		private function _js_multislider($idx=array()){
			$loc = $this->lokasi();
			$js = array(
				'contextmenu'			=> $loc.'multislider.min.js',
			);
			
			$check = array_filter($idx);
			if(!empty($check)){
				foreach($idx as $key){
					$js[$key];
				}
			}
		
			return $js;
		}
	}

}