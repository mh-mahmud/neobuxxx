<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Ban User</h3>
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
                    <h2>Ban User</h2>
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
                  <div class="x_content" width="100%">
                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Mobile</th>
                            <th>Business ID</th>
                            <th>Reffered ID</th>
                            <th>National ID</th>
                            <th>Country</th>
                            <th>Level One</th>
                            <th>Level Two</th>
                            <th>Level Three</th>
                            <th>Level Four</th>
                            <th>Level Five</th>
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($get_record as $v) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v->FIRST_NAME . ' ' . $v->LAST_NAME; ?></td>
                              <td><?php echo $v->EMAIL; ?></td>
                              <td><?php echo $v->ADDRESS; ?></td>
                              <td><?php echo $v->MOBILE; ?></td>
                              <td><?php echo $v->MY_ID; ?></td>
                              <td><?php echo $v->REFFER_ID; ?></td>
                              <td><?php echo $v->NATIONAL_ID; ?></td>
                              <td><?php echo $v->COUNTRY; ?></td>
                              <td><?php echo $this->webspice->admin_name($v->LEVEL_1); ?></td>
                              <td><?php echo $this->webspice->admin_name($v->LEVEL_2); ?></td>
                              <td><?php echo $this->webspice->admin_name($v->LEVEL_3); ?></td>
                              <td><?php echo $this->webspice->admin_name($v->LEVEL_4); ?></td>
                              <td><?php echo $this->webspice->admin_name($v->LEVEL_5); ?></td>
                              <td>
                                <?php
                                  if($v->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                              <td>
                                <?php if( $this->webspice->permission_verify('ban_user',true) && $v->STATUS==0 ): ?>
                                    <a href="<?php echo $url_prefix; ?>ban_user/active/<?php echo $this->webspice->encrypt_decrypt($v->USER_REG_ID,'encrypt'); ?>" class="btn btn-success btn-sm">Active</a>
                                <?php endif; ?>
                                <?php if( $this->webspice->permission_verify('ban_user',true)): ?>
                                    <a href="<?php echo $url_prefix; ?>ban_user/delete/<?php echo $this->webspice->encrypt_decrypt($v->USER_REG_ID,'encrypt'); ?>" class="btn btn-sm btn-danger">Delete</a>
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