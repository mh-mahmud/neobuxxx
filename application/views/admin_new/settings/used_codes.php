<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Sold Pin Codes</h3>
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
              <?php include(APPPATH."views/admin_new/flash_message.php"); ?>
                <div class="x_panel">
                  <div class="x_title">
                    <h2><a class="btn btn-success btn-xs" href="<?php echo $url_prefix; ?>create_new_code"><i class="fa fa-plus"></i> Create Pin Code</a></h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content" width="100%">
                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                            
                            <th>Pin</th>
                            <th>Package Name</th>
                            <th>Created by</th>
                            <th>Used By</th>
                            <th>Pin Value</th>
                            <th>Created Date</th>
                            <th>Used Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($get_record as $v) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v->PIN_CODE; ?></td>
                              <td><?php echo $v->PACKAGE_NAME; ?></td>
                              <td><?php echo $this->webspice->admin_name($v->GENERATOR_ID); ?></td>
                              <td>
                                <?php
                                  /* here data comes from user_registration - USER_REG_ID */
                                  $used_by = $this->db->query("SELECT FIRST_NAME, LAST_NAME FROM user_registration WHERE USER_REG_ID='{$v->OWNER_ID}'")->row();
                                  if(count($used_by)) {
                                    echo $used_by->FIRST_NAME . " " . $used_by->LAST_NAME;
                                  }
                                  else {
                                    echo "";
                                  }
                                ?>
                              </td>
                              <td><?php echo $v->PIN_VALUE; ?></td>
                              <td><?php echo $v->CREATED_DATE; ?></td>
                              <td><?php echo $v->USED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v->STATUS == 0) {
                                    echo '<span class="label label-success">Available</span>';
                                  }
                                  else if($v->STATUS == 1) {
                                    echo '<span class="label label-primary">Sold</span>';
                                  }
                                ?>
                              </td>
                              <td>
                                <?php if( $this->webspice->permission_verify('available_codes',true)): ?>
                                    <a href="<?php echo $url_prefix; ?>available_codes/delete/<?php echo $this->webspice->encrypt_decrypt($v->PIN_ID,'encrypt'); ?>" class="btn btn-danger btn-xs">Delete</a>
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