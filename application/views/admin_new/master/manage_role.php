<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Manage Role</h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
              <!-- flash message -->
              <?php include(APPPATH."views/admin_new/flash_message.php"); ?>
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Manage Role</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#">Settings 1</a>
                          </li>
                          <li><a href="#">Settings 2</a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatable" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Role Name</th>
                          <th>Permission Name</th>
                          <th>Created By</th>
                          <th>Created Date</th>
                          <!-- <th>Updated By</th> -->
                          <!-- <th>Updated Date</th> -->
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($get_record as $k=>$v): ?>
                          <tr>
                            
                            <td><?php echo $v->ROLE_NAME; ?></td>
                            <td><?php echo ucwords(str_replace(',',', ', str_replace('_',' ',$v->PERMISSION_NAME))); ?></td>
                            <td><?php echo $this->webspice->admin_name($v->CREATED_BY); ?></td>
                            <td><?php echo $this->webspice->formatted_date($v->CREATED_DATE); ?></td>
                            <!-- <td><?php //echo $this->webspice->admin_name($v->UPDATED_BY,'USER_NAME'); ?></td> -->
                            <!-- <td><?php //echo $this->webspice->formatted_date($v->UPDATED_DATE); ?></td> -->
                            <td><?php echo $this->webspice->static_status($v->STATUS); ?></td>
                            <td class="field_button">
                              <?php if( $this->webspice->permission_verify('manage_role',true) && $v->STATUS!=9 ): ?>
                              <a href="<?php echo $url_prefix; ?>manage_role/edit/<?php echo $this->webspice->encrypt_decrypt($v->ROLE_ID,'encrypt'); ?>" class="btn btn-success">Edit</a>
                              <?php endif; ?>
                              
                              <?php if( $this->webspice->permission_verify('manage_role',true) && $v->STATUS==7 ): ?>
                              <a href="<?php echo $url_prefix; ?>manage_role/inactive/<?php echo $this->webspice->encrypt_decrypt($v->ROLE_ID,'encrypt'); ?>" class="btn btn-danger">Inactive</a>
                              <?php endif; ?>
                              
                              <?php if( $this->webspice->permission_verify('manage_role',true) && $v->STATUS==-7 ): ?>
                              <a href="<?php echo $url_prefix; ?>manage_role/active/<?php echo $this->webspice->encrypt_decrypt($v->ROLE_ID,'encrypt'); ?>" class="btn btn-warning">Active</a>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
        <!-- /page content -->

<?php include(APPPATH."views/admin_new/footer_table.php"); ?>