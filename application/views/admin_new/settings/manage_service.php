<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Service Settings</h3>
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
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Service Settings</h2>
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
                            
                            <th>Service Name</th>
                            <th>Service Type</th>
                            <th>Service Code</th>
                            <th>Prefix</th>
                            <th>Minimum Amount</th>
                            <th>Maximum Amount</th>
                            <th>Bulk Limit</th>
                            <th>Logo</th>
                            <th>Created Date</th>
                            <th>Created By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($get_record as $v) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v->SERVICE_NAME; ?></td>
                              <td><?php echo ucwords(str_replace("_", " ", $v->SERVICE_TYPE)); ?></td>
                              <td><?php echo $v->SERVICE_CODE; ?></td>
                              <td><?php echo $v->PREFIX; ?></td>
                              <td><?php echo $v->MIN_AMOUNT; ?></td>
                              <td><?php echo $v->MAX_AMOUNT; ?></td>
                              <td><?php echo $v->BULK_LIMIT; ?></td>
                              <td>
                                <?php if( file_exists($this->webspice->get_path('service_full').$v->LOGO) ): ?>
                                    <img src="<?php echo $this->webspice->get_path('service').$v->LOGO; ?>"  alt="" class="img-responsive" width="70px;"/>
                                <?php endif;  ?>
                              </td>
                              <td><?php echo $v->CREATED_DATE; ?></td>
                              <td><?php echo $this->webspice->admin_name($v->CREATED_BY); ?></td>
                              <td>
                                <?php
                                  if($v->STATUS == -7) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v->STATUS == 2) {
                                    echo '<span class="label label-warning">Assigned</span>';
                                  }
                                  else if($v->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                              <td>
                                <div class="btn-group" role="group">
                                  <button type="button" class="btn btn-default dropdown-toggle customized-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu customized-menu">
                                    <li>
                                      <?php if( $this->webspice->permission_verify('manage_service',true) && $v->STATUS!=9 ): ?>
                                          <a href="<?php echo $url_prefix; ?>manage_service/edit/<?php echo $this->webspice->encrypt_decrypt($v->SERVICE_ID,'encrypt'); ?>" class="btn btn-success">Edit</a>
                                      <?php endif; ?>
                                    </li>
                                    <li>
                                      <?php if( $this->webspice->permission_verify('manage_service',true) && $v->STATUS==7 ): ?>
                                          <a href="<?php echo $url_prefix; ?>manage_service/inactive/<?php echo $this->webspice->encrypt_decrypt($v->SERVICE_ID,'encrypt'); ?>" class="btn btn-warning">Inactive</a>
                                      <?php endif; ?>
                                    </li>
                                    <li>
                                      <?php if( $this->webspice->permission_verify('manage_service',true) && $v->STATUS==-7 ): ?>
                                          <a href="<?php echo $url_prefix; ?>manage_service/active/<?php echo $this->webspice->encrypt_decrypt($v->SERVICE_ID,'encrypt'); ?>" class="btn btn-warning">Active</a>
                                      <?php endif; ?>
                                    </li>
                                    <li>
                                      <?php if( $this->webspice->permission_verify('manage_service',true)): ?>
                                          <a href="<?php echo $url_prefix; ?>manage_service/delete/<?php echo $this->webspice->encrypt_decrypt($v->SERVICE_ID,'encrypt'); ?>" class="btn btn-danger">Delete</a>
                                      <?php endif; ?>
                                    </li>
                                  </ul>
                                </div>
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