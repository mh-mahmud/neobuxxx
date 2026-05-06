<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Package List</h3>
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
                    <h2><a class="btn btn-success btn-xs" href="<?php echo $url_prefix; ?>create_package"><i class="fa fa-plus"></i> Create Package</a></h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content" width="100%">
                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                            
                            <th>Name</th>
                            <th>Description</th>
                            <th>PTC</th>
                            <th>Amount</th>
                            <th>Validity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($get_record as $v) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v->PACKAGE_NAME; ?></td>
                              <td><?php echo $v->PACKAGE_DESC; ?></td>
                              <td><?php echo $v->PTC_LINK; ?></td>
                              <td><?php echo $v->PACKAGE_AMOUNT; ?></td>
                              <td><?php echo $v->PACKAGE_VALIDITY; ?> months</td>
                              <td>
                                <?php
                                  if($v->STATUS == -7) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                              <td>
                                <?php if( $this->webspice->permission_verify('manage_package',true) && $v->STATUS!=9 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_package/edit/<?php echo $this->webspice->encrypt_decrypt($v->PACKAGE_ID,'encrypt'); ?>" class="btn btn-success btn-xs">Edit</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_package',true) && $v->STATUS==7 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_package/inactive/<?php echo $this->webspice->encrypt_decrypt($v->PACKAGE_ID,'encrypt'); ?>" class="btn btn-warning btn-xs">Inactive</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_package',true) && $v->STATUS==-7 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_package/active/<?php echo $this->webspice->encrypt_decrypt($v->PACKAGE_ID,'encrypt'); ?>" class="btn btn-warning btn-xs">Active</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_package',true)): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_package/delete/<?php echo $this->webspice->encrypt_decrypt($v->PACKAGE_ID,'encrypt'); ?>" class="btn btn-danger btn-xs">Delete</a>
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