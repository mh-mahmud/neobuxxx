<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>View Rate Module Settings</h3>
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
                    <h2>View Rate Module Settings</h2>
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

                <?php if(isset($errors) && count($errors)) : ?>
                  <div class="alert alert-error alert-block">
                    <a class="close" data-dismiss="alert" href="#">&times;</a>
                    <h4 class="alert-heading">Error!</h4>
                    <?php
                      foreach($errors as $error) {
                        echo $error . "<br />";
                      }
                    ?>
                  </div>
                <?php endif; ?>

                <!-- BEGIN FORM-->
                <form method="post" action=""  enctype="multipart/form-data" id="" class="form-horizontal">

                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                  <div class="x_content">
                    <table id="" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Image</th>
                            <th>Service Code</th>
                            <th>Prefix</th>
                            <th>Comission (%)</th>
                            <th>Charge (%)</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($get_record as $list) : ?>
                          <tr class="odd gradeX">
                            <td><?php echo $list->SERVICE_NAME; ?></td>
                            <td>
                              <?php if( file_exists($this->webspice->get_path('service_full').$list->LOGO) ): ?>
                                  <img src="<?php echo $this->webspice->get_path('service').$list->LOGO; ?>"  alt="" class="img-responsive" width="70px;"/>
                              <?php endif;  ?>
                            </td>
                            <td><?php echo $list->SERVICE_CODE; ?></td>
                            <td><?php echo $list->PREFIX; ?></td>
                            <td>
                              <input type="text" class="span12 m-wrap" name="comission[<?php echo $list->SERVICE_ID; ?>]" value="<?php echo $list->COMISSION; ?>">
                            </td>
                            <td>
                              <input type="text" class="span12 m-wrap" name="charge[<?php echo $list->SERVICE_ID; ?>]" value="<?php echo $list->CHARGE; ?>">
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>

                <div class="form-actions">
                    <input type="submit" name="submit" class="btn btn-primary" value="Update Data"  />
                     <a class="btn btn-danger" href="<?php echo $url_prefix; ?>manage_reseller">Cancel</a>
                </div>

                </form>
            <!-- END FORM-->
                </div>
              </div>

            </div>
          </div>
        </div>
        <!-- /page content -->

<?php include(APPPATH."views/admin_new/footer_table.php"); ?>