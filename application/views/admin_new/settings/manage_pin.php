<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Manage PIN</h3>
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
                    <h2>Manage PIN</h2>
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
                            <th>Reseller Name</th>
                            <th>Pin Expire Date</th>
                            <th>Pin</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($get_record as $v) : ?>
                                                <tr class="odd gradeX">
                                                    
                                                  <td>
                                                    <?php
                                                      echo $this->webspice->service_name($v->SERVICE_ID);
                                                    ?>
                                                  </td>
                                                  <td>
                                                    <?php
                                                      echo $this->webspice->reseller_name($v->RESELLER_ID);
                                                    ?>
                                                  </td>
                                                  <td><?php echo date("D jS F Y", strtotime($v->PIN_EXPIRE_DATE)); ?></td>
                                                  <td>**********</td>
                                                  <td><?php echo $v->CREATED_DATE; ?></td>
                                                  <td>
                                                    <?php if( $this->webspice->permission_verify('manage_reseller',true) && $v->STATUS!=9 ): ?>
                                                        <a href="<?php echo $url_prefix; ?>manage_pin/edit/<?php echo $this->webspice->encrypt_decrypt($v->PIN_ID,'encrypt'); ?>" class="btn btn-primary">Edit</a>
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