<?php
(!defined('THEMEPATH'))?exit:'';

class metronic_quick_sidebar{
	public function _create(){
		?>
			<div class="page-quick-sidebar-wrapper">
				<div class="page-quick-sidebar">
					<div class="nav-justified">
						<?php
							self::_tabmenu();
						?>
						<div class="tab-content">
							<?php
								self::_tab_user();
								self::_tab_alert();
								self::_tab_setting();
							?>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
	
	private function _tabmenu(){
		$args = array(
			1	=> 'Users',
			2	=> 'Alerts',
			3	=> 'Setting'
		);
		?>
			<ul class="nav nav-tabs nav-justified">
				<?php
					foreach($args as $key => $val){
						$status = $key==1?'active':'';
						echo '
							<li class="'.$status.'">
								<a href="#quick_sidebar_tab_'.$key.'" data-toggle="tab" aria-expanded="true">
								'.$val.' <span class="badge badge-danger"></span>
								</a>
							</li>
						';
					}
				?>
			</ul>
		<?php
	}
	
	private function _tab_user(){
		?>
			<div class="tab-pane page-quick-sidebar-chat active" id="quick_sidebar_tab_1">
				<div class="page-quick-sidebar-list" style="position: relative; overflow: hidden; width: auto; height: 306px;">
					<div class="page-quick-sidebar-chat-users" data-rail-color="#ddd" data-wrapper-class="page-quick-sidebar-list" data-height="306" data-initialized="1" style="overflow: hidden; width: auto; height: 306px;">
						<h3 class="list-heading">Staff</h3>
						<ul class="media-list list-items">
							<!-- Next For Loop -->
							<li class="media">
								<div class="media-status">
									<span class="badge badge-success">8</span>
								</div>
								<img class="media-object" src="../../assets/admin/layout/img/avatar2.jpg" alt="...">
								<div class="media-body">
									<h4 class="media-heading">Ella Wong</h4>
									<div class="media-heading-sub">
										 Project Manager
									</div>
								</div>
							</li>
						</ul>
					</div>
					<?php
						self::get_scrollbar();
					?>
				</div>
			</div>
		<?php
	}
	
	private function _user_message(){
		?>
			<div class="page-quick-sidebar-item">
				<div class="page-quick-sidebar-chat-user">
					<div class="page-quick-sidebar-nav">
						<a href="javascript:;" class="page-quick-sidebar-back-to-list">
							<i class="icon-arrow-left"></i>Back
						</a>
					</div>
					<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 202px;">
						<div class="page-quick-sidebar-chat-user-messages" data-height="202" data-initialized="1" style="overflow: hidden; width: auto; height: 202px;">
							<?php
								self::get_message();
							?>
						</div>
					</div>
					<div class="page-quick-sidebar-chat-user-form">
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Type a message here...">
							<div class="input-group-btn">
								<button type="button" class="btn blue">
									<i class="icon-paper-clip"></i>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
	
	private function get_message($type=false){
		if($type==false){
			$type = 'post out';
		}else{
			$type = 'post in';
		}
		?>
			<div class="<?php print($type) ;?>">
				<img class="avatar" alt="" src="../../assets/admin/layout/img/avatar2.jpg">
				<div class="message">
					<span class="arrow"></span>
					<a href="javascript:;" class="name">Bob Nilson</a>
					<span class="datetime">20:15</span>
					<span class="body"> When could you send me the report ? </span>
				</div>
			</div>
		<?php
	}
	
	private function _tab_alert(){
		$args = array(
			0	=> array(
				'status'	=> 'fa fa-check',
				'msg'		=> 'You have 4 pending tasks. <span class="label label-sm label-warning "> Take action <i class="fa fa-share"></i></span>',
				'date'		=> 'Just now'
			)
		);
		?>
			<div class="tab-pane page-quick-sidebar-alerts" id="quick_sidebar_tab_2">
				<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 306px;">
					<div class="page-quick-sidebar-alerts-list" data-height="306" data-initialized="1" style="overflow: hidden; width: auto; height: 306px;">
						<h3 class="list-heading">General</h3>
						<ul class="feeds list-items">
							<?php
								foreach($args as $key => $val){
									echo '
										<li>
											<div class="col1">
												<div class="cont">
													<div class="cont-col1">
														<div class="label label-sm label-info">
															<i class="'.$val['status'].'"></i>
														</div>
													</div>
													<div class="cont-col2">
														<div class="desc">
															 '.$val['msg'].'
														</div>
													</div>
												</div>
											</div>
											<div class="col2">
												<div class="date">
													 '.$val['date'].'
												</div>
											</div>
										</li>
									';
								}
							?>
						</ul>
					</div>
					<?php
						self::get_scrollbar();
					?>
				</div>
			</div>
		<?php
	}
	
	private function _tab_setting(){
		?>
			<div class="tab-pane page-quick-sidebar-settings" id="quick_sidebar_tab_3">
				<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 306px;">
					<div class="page-quick-sidebar-settings-list" data-height="306" data-initialized="1" style="overflow: hidden; width: auto; height: 306px;">
						<h3 class="list-heading">General Settings</h3>
						<?php self::form_setting() ;?>
						<div class="inner-content">
							<button class="btn btn-success">
								<i class="icon-settings"></i> Save Changes
							</button>
						</div>
					</div>
					<?php
						self::get_scrollbar();
					?>
				</div>
			</div>
		<?php
	}
	
	private function get_scrollbar(){
		?>
			<div class="slimScrollBar" style="background: rgb(187, 187, 187); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 123.205px;"></div>
			<div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(221, 221, 221); opacity: 0.2; z-index: 90; right: 1px;"></div>
		<?php
	}
	
	private function form_setting(){
		?>
			<ul class="list-items borderless">
				<li>
					Enable Notifications <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-on bootstrap-switch-small bootstrap-switch-animate">
						<div class="bootstrap-switch-container">
							<span class="bootstrap-switch-handle-on bootstrap-switch-success">ON</span>
							<label class="bootstrap-switch-label">&nbsp;</label>
							<span class="bootstrap-switch-handle-off bootstrap-switch-default">OFF</span>
							<input type="checkbox" class="make-switch" checked="" data-size="small" data-on-color="success" data-on-text="ON" data-off-color="default" data-off-text="OFF">
						</div>
					</div>
				</li>
				<li>
					Allow Tracking <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-off bootstrap-switch-small bootstrap-switch-animate">
						<div class="bootstrap-switch-container">
							<span class="bootstrap-switch-handle-on bootstrap-switch-info">ON</span>
							<label class="bootstrap-switch-label">&nbsp;</label>
							<span class="bootstrap-switch-handle-off bootstrap-switch-default">OFF</span>
							<input type="checkbox" class="make-switch" data-size="small" data-on-color="info" data-on-text="ON" data-off-color="default" data-off-text="OFF">
						</div>
					</div>
				</li>
				<li>
					Log Errors <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-on bootstrap-switch-small bootstrap-switch-animate">
						<div class="bootstrap-switch-container">
							<span class="bootstrap-switch-handle-on bootstrap-switch-danger">ON</span>
							<label class="bootstrap-switch-label">&nbsp;</label>
							<span class="bootstrap-switch-handle-off bootstrap-switch-default">OFF</span>
							<input type="checkbox" class="make-switch" checked="" data-size="small" data-on-color="danger" data-on-text="ON" data-off-color="default" data-off-text="OFF">
						</div>
					</div>
				</li>
				<li>
					Auto Sumbit Issues <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-off bootstrap-switch-small bootstrap-switch-animate">
						<div class="bootstrap-switch-container">
							<span class="bootstrap-switch-handle-on bootstrap-switch-warning">ON</span>
							<label class="bootstrap-switch-label">&nbsp;</label>
							<span class="bootstrap-switch-handle-off bootstrap-switch-default">OFF</span>
							<input type="checkbox" class="make-switch" data-size="small" data-on-color="warning" data-on-text="ON" data-off-color="default" data-off-text="OFF">
						</div>
					</div>
				</li>
				<li>
					Enable SMS Alerts <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-on bootstrap-switch-small bootstrap-switch-animate">
						<div class="bootstrap-switch-container">
							<span class="bootstrap-switch-handle-on bootstrap-switch-success">ON</span>
							<label class="bootstrap-switch-label">&nbsp;</label>
							<span class="bootstrap-switch-handle-off bootstrap-switch-default">OFF</span>
							<input type="checkbox" class="make-switch" checked="" data-size="small" data-on-color="success" data-on-text="ON" data-off-color="default" data-off-text="OFF">
						</div>
					</div>
				</li>
			</ul>
		<?php
	}
}