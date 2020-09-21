<?php
(!defined('THEMEPATH'))?exit:'';

class create_file_manager{
	public static function _layout($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}
		
		if(is_callable(array(new self(),$args['func']))){
			$func = $args['func'];
			self::{$func}($args['data']);
		}
	}
	
	private static function _upload_file($args=array()){
		?>
			<div class="col-md-12">
				<p>
					<span class="label label-danger">NOTE: </span>
					max upload file 2MB
				</p>
				<form class="dropzone dz-clickable" id="<?php print($args['id']) ;?>">
					<input type="hidden" name="ajax" value="<?php print($args['func']) ;?>">
					<input type="hidden" name="object" value="<?php print($args['object']) ;?>">
					<input type="hidden" name="data" value="upload_file">
					<div class="dz-default dz-message">
						<span>Drop files here to upload</span>
					</div>
				</form>
			</div>
		<?php

		self::_script_upload($args);
	}

	private static function _list_file($args=array()){
		if(isset($args['search'])){
			create_table::_search($args['search'],$args['data']);
		}

		?>
			<div class="imgList" style="height: 350px;overflow-y: scroll;">
				<?php
					self::_style_list();
					self::_layout_list_file($args['list']);
					self::_script_list($args['script']);
				?>
			</div>
		<?php

		if(isset($args['page'])){
			create_table::_pagination($args['page']);
		}
	}

	private static function _layout_list_file($args=array()){
		$count = count($args);
		$ceil = ceil($count/6);

		for($i=0;$i<$ceil;$i++){
			?>
				<div class="row">
					<?php
						$j = 0;
						foreach ($args as $key => $val) {
							$j += 1;
							if($j>6){
								continue;
							}

							$url_img = self::_filter_image_file($val['type'],$val['url']);
							$index = isset($val['id'])?$val['id']:$val['name'];
							$click = isset($val['func'])?$val['func']:'select_file_list(this,false)';

							?>
								<div class="col-md-2 box_file_list">
									<a href="javascript:" onclick="<?php print($click) ;?>" data-image="<?php print($index) ;?>" data-name="<?php print($val['name']) ;?>" data-src="<?php print($url_img) ;?>" data-type="<?php print($val['type']) ;?>">
										<div class="content_file_list">
											<div class="img-list">
												<img src="<?php print($url_img) ;?>">
											</div>
											<div class="name-list">
												<span><?php print($val['name']) ;?></span>
											</div>
										</div>
									</a>
									<a class="remove_file_list" href="javascript:" onclick="remove_file_list(this)" data-image="<?php print($index) ;?>">
										<i style="font-size: 18px;color: #e0262c;" class="fa fa-trash"></i>
									</a>
								</div>
							<?php
							unset($args[$key]);
						}
					?>
				</div>
			<?php
		}
	}

	private static function _filter_image_file($type='',$url=''){
		$asset = 'asset/img/';
		$list = array(
			'text'			=> 'icon/icon-text.jpg',
			'word'			=> 'icon/icon-word.jpg',
			'excel'			=> 'icon/icon-excel.jpg',
			'power-point'	=> 'icon/icon-power-point.jpg',
			'winrar'		=> 'icon/icon-rar.jpg',
			'winzip'		=> 'icon/icon-zip.jpg',
			'image'			=> file_exists($asset.'upload/'.$url)?'no-image.png':'upload/'.$url,
			'profile'		=> file_exists($asset.'user/'.$url)?'user/no-profile.jpg':'user/'.$url,
			'file'			=> 'icon/icon-file.png'
		);

		if(!isset($list[$type])){
			$type = 'file';
		}

		return $asset.$list[$type];
	}

	private static function _script_upload($args=array()){
		?>
			<script type="text/javascript">
				var myDropzone = new Dropzone("#<?php print($args['id']) ;?>",{ 
						url: "include/ajax.php",
						paramName: "file", // The name that will be used to transfer the file
						maxFilesize: 2, // MB
						acceptedFiles:'<?php print($args['accept']) ?>'
					});

				<?php if(isset($args['load']) && !empty($args['load'])): ?>

				myDropzone.on("success",function(file,response){
					sobad_callback("#<?php print($args['load']) ;?>",response,'html',true);
				});

				<?php endif ;?>

				myDropzone.on("addedfile", function(file) {
					// Create the remove button
					var removeButton = Dropzone.createElement("<button class='btn btn-sm btn-block'>Remove file</button>");

					// Capture the Dropzone instance as closure.
					var _this = this;

					// Listen to the click event
					removeButton.addEventListener("click", function(e) {
						// Make sure the button click doesn't submit the form:
						e.preventDefault();
						e.stopPropagation();

						// Remove the file preview.
						_this.removeFile(file);
						// If you want to the delete the file on the server as well,
						// you can do the AJAX request here.
					});

					// Add the button to the file preview element.
					file.previewElement.appendChild(removeButton);
				});
			</script>
		<?php
	}

	private static function _style_list(){
		?>
			<style>
				.imgList .row {
				    margin-left: 0px;
				    margin-right: 0px;
				    padding: 15px 0px;
				    border-bottom: 1px solid #cfcfcf;
				    height: 185px;
				}

				.imgList .box_file_list {
				    padding: 3px;
				    height: 100%;
				}

				.imgList .box_file_list.selected {
				    background-color: #8dc7f994;
				    border-radius: 5px !important;
				    border: 1px solid #64b3f7;
				}

				.box_file_list a {
				    width: 100%;
				    height: 100%;
				    display: block;
				    position: relative;
				}

				.box_file_list .content_file_list {
				    padding: 3px;
				    text-align: center;
				    position: absolute;
				    bottom: 0px;
				}

				.box_file_list .content_file_list:hover {
				    box-shadow: 0 0 2px 1px rgba(0, 140, 186, 0.5);
				}

				.img-list {
				    box-shadow: 0 0 4px 2px rgba(150, 150, 150, 0.5);
				}

				.img-list img {
    				width: 100%;
    				height: 100%;
				}

				.name-list {
				    margin-top: 10px;
				}

				.box_file_list:hover > a.remove_file_list{
					opacity: 1;
				}

				a.remove_file_list {
				    position: absolute;
				    top: 0px;
				    height: auto;
				    right: 0px;
				    width: auto;
				    opacity: 0;
				}

				a.remove_file_list:hover{
					border:1px solid #dfdfdf;
					padding: 3px;
				}

				a.remove_file_list:hover > i{
					font-size: 24px;
				}
			</style>
		<?php
	}

	private static function _script_list($args=array()){
		?>
			<script type="text/javascript">
				var _select_file_list = [];

				function select_file_list(val,status){
					var idx_file = $(val).attr('data-image');
					var name_file = $(val).attr('data-name');
					var url_file = $(val).attr('data-src');;
					var type_file = $(val).attr('data-type');

					if(status){
						// Multiple Select

					}else{
						// Single Select

						$(".imgList .row .box_file_list.selected").removeClass("selected");
						$(val).parent().addClass("selected");
						_select_file_list[0] = {"id":idx_file,"name":name_file,"url":url_file,"type":type_file};
					}
				}

				function remove_file_list(val){
					var idx = $(val).attr('data-image');

					// loading	
					var html = $(val).html();
					$(val).html('<i class="fa fa-spinner fa-spin"></i>');
					$(val).attr('disabled','');

					data = "ajax=<?php print($args['func_remove']) ;?>&object="+object+"&data="+idx;
					sobad_ajax('#<?php print($args['id']) ;?>',data,'html',true,val,html);
				}
			</script>
		<?php
	}
}