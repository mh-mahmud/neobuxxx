<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Manage PTC Add</h3>
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
                    <h2><a class="btn btn-success btn-xs" href="<?php echo $url_prefix; ?>create_ptc_add"><i class="fa fa-plus"></i> Create PTC Add</a></h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content" width="100%">
                    <table id="" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <?php
                      $package_data = $this->db->query("SELECT PACKAGE_NAME FROM package_setup WHERE STATUS=7")->result();
                    ?>
                      <thead>
                        <tr>
                            <th>Add Name</th>
                            <?php foreach($package_data as $pv) : ?>
                              <th><?php echo $pv->PACKAGE_NAME; ?> price</th>
                            <?php endforeach; ?>
                            <th>URL</th>
                            <th>Add Duration</th>
                            <th>Add Type</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php
                          foreach($get_record as $v) :
                            $price_data = $this->db->query("SELECT PRICE FROM add_setup WHERE ADD_UNIQ_ID='".$v->ADD_UNIQ_ID."'")->result();
                        ?>
                          <tr class="odd gradeX">
                              <td><?php echo $v->ADD_NAME; ?></td>
                              <?php foreach($price_data as $pd) : ?>
                                <td><?php echo $pd->PRICE; ?></td>
                              <?php endforeach; ?>
                              <td><?php echo $v->URL_1; ?></td>
                              <td><?php echo $v->ADD_DURATION; ?> sec</td>
                              <td><?php echo $v->ADD_TYPE; ?></td>
                              <td><?php echo date("D jS F Y", strtotime($v->CREATED_DATE)); ?></td>
                              <!-- <td>
                                <?php
                                  /*if($v->STATUS == -7) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }*/
                                ?>
                              </td> -->
                              <td>
                                <?php if( $this->webspice->permission_verify('manage_ptc_add',true) && $v->STATUS!=9 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_ptc_add/edit/<?php echo $this->webspice->encrypt_decrypt($v->ADD_UNIQ_ID,'encrypt'); ?>" class="btn btn-success btn-xs">Edit</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_ptc_add',true) && $v->STATUS==7 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_ptc_add/inactive/<?php echo $this->webspice->encrypt_decrypt($v->ADD_UNIQ_ID,'encrypt'); ?>" class="btn btn-warning btn-xs">Inactive</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_ptc_add',true) && $v->STATUS==-7 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_ptc_add/active/<?php echo $this->webspice->encrypt_decrypt($v->ADD_UNIQ_ID,'encrypt'); ?>" class="btn btn-warning btn-xs">Active</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_ptc_add',true)): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_ptc_add/delete/<?php echo $this->webspice->encrypt_decrypt($v->ADD_UNIQ_ID,'encrypt'); ?>" class="btn btn-danger btn-xs">Delete</a>
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