  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="<?php echo $url_prefix; ?>admin" class="site_title"><i class="fa fa-bar-chart-o"></i> <span style="font-size: 16px">NuoBux</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile">
              <div class="profile_pic">
                <img src="<?php echo $url_prefix; ?>global/admin_new/images/img.jpg" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2><?php echo $this->webspice->admin_name($this->webspice->enc($_SESSION['user']['USER_ID'], 'decrypt')); ?></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>&nbsp;</h3>
                <ul class="nav side-menu">
                  <li><a href="<?php echo $url_prefix; ?>admin"><i class="fa fa-home"></i> Dashboard </a></li>

                  <?php $get_permission_group = $this->db->query("SELECT GROUP_NAME FROM permission WHERE STATUS=1 GROUP BY GROUP_NAME ORDER BY GROUP_NAME")->result(); ?>
                  <?php
                    $icons = array(
                      // 'fa-cube',
                      // 'fa-database',
                      // 'fa-comment',
                      // 'fa-dedent',
                      // 'fa-tree',
                      'fa-credit-card',
                      'fa fa-thumbs-o-down',
                      'fa-dollar',
                      'fa-money',
                      'fa-envelope',
                      'fa-tag',
                      'fa-male',
                      'fa-wrench',
                      'fa-suitcase',
                      'fa-bullseye',
                      'fa-user',
                      'fa-google-wallet'
                    );

                    $i=0;
                    foreach($get_permission_group as $gk=>$gv) {
                      $get_permission = $this->db->query("SELECT * FROM permission WHERE STATUS=1 AND GROUP_NAME='".$gv->GROUP_NAME."' ORDER BY MENU_NAME")->result();
                      # find out that; at least one permission has or not according to the group name
                      $is_permitted = false;
                      foreach($get_permission as $pk=>$pv){
                          if( $this->webspice->permission_verify($pv->PERMISSION_NAME, true) ){
                              $is_permitted = true; 
                              break;
                          }
                      }

                      # create main menu
                      if( $is_permitted ){

                  ?>

                  <li><a><i class="fa <?php echo $icons[$i]; ?>"></i> <?php echo ucwords(str_replace("_"," ",$gv->GROUP_NAME)) ?> <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">

                      <?php
                      # generate sub menu
                      $menu_name = null;
                      foreach($get_permission as $pk1=>$pv1){
                          if( $this->webspice->permission_verify($pv1->PERMISSION_NAME, true) && $pv1->MENU_NAME != $menu_name ){
                              $menu_name = $pv1->MENU_NAME;
                      ?>

                      <li><a href="<?php echo $url_prefix.$pv1->ROUTE_NAME; ?>"><?php echo ucwords(str_replace('_',' ',$pv1->MENU_NAME)); ?></a></li>
                      <?php } } ?>
                    </ul>
                  </li>
                  <?php } ?>

                  <?php $i++; } ?>
                </ul>
              </div>
              

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" href="<?php echo $url_prefix; ?>logout" data-placement="top" title="Logout">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo $url_prefix; ?>global/admin_new/images/img.jpg" alt=""><?php echo $this->webspice->admin_name($this->webspice->enc($_SESSION['user']['USER_ID'], 'decrypt')); ?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="<?php echo $url_prefix; ?>my_profile"> Profile</a></li>
                    <?php if($this->webspice->admin_verify()) { ?>
                      <li>
                        <a href="<?php echo $url_prefix; ?>initial_settings">
                          <span class="badge bg-red pull-right"></span>
                          <span>Software Settings</span>
                        </a>
                      </li>
                    <?php } else { ?>
                      <li>
                        <a href="<?php echo $url_prefix; ?>pin_setup">
                          <span class="badge bg-red pull-right"></span>
                          <span>Pin Setting</span>
                        </a>
                      </li>
                    <?php } ?>
                    <li><a href="<?php echo $url_prefix; ?>change_user_password">Change Password</a></li>
                    <li><a href="<?php echo $url_prefix; ?>logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                <?php if(!$this->webspice->admin_verify()) : ?>
                  <li style="margin-top: 0px;background: #f9f9f9;padding: 15px">
                    <div>
                      <p style="margin-top:5px;color:#C9302C;font-weight:bold;font-size:16\5px;">Balance: $<?php echo $this->webspice->user_balance($this->webspice->get_user_id()); ?> USD</p>
                    </div>
                  </li>
                <?php endif; ?>

                <!-- <li role="presentation" class="dropdown">
                  <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-envelope-o"></i>
                    <span class="badge bg-green">6</span>
                  </a>
                  <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <div class="text-center">
                        <a>
                          <strong>See All Alerts</strong>
                          <i class="fa fa-angle-right"></i>
                        </a>
                      </div>
                    </li>
                  </ul>
                </li> -->
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->