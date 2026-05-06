<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo $url_prefix; ?>global/add-show.css">
<?php include(APPPATH."views/admin_new/menu.php"); ?>
<style>
.dataTables_filter {
    width: 100% !important;
}
.dataTables_filter input[type="search"] {
    width: 60% !important;
}
</style>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>PTC Add Click</h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <div class="input-group">
                    <!--<input type="text" class="form-control" placeholder="Search for...">-->
                    <!--<span class="input-group-btn">-->
                    <!--  <button class="btn btn-default" type="button">Go!</button>-->
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
                <!-- end flash message -->

                <div class="x_panel">
                  
                </div>
              </div>
            </div>

            <div class="row">

              <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Add New Package</h2>
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
                    <br />
                    <form id="demo-form2"  method="post" action=""  enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">

                      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                      

                        
                        <?php
                          foreach($get_record as $val) :
                            $url_data = $val->ADD_ID . "|" . $val->PACKAGE_ID . "|" . $val->ADD_NAME . "|" . $val->URL_1 . "|" . $val->PRICE . "|" . $val->ADD_DURATION;
                            $url_data = $this->webspice->enc($url_data, 'encrypt');
                        ?>
                          <!-- <p class="bg-success click-add" style="padding: 10px"><a class="click-add" target="_blank" href="<?php //echo $url_prefix; ?>ptc_earn/add_click/<?php //echo $url_data; ?>"><?php //echo $val->ADD_NAME . ": " . $val->URL_1; ?></a></p> -->

                          <!-- new add design -->
                          <!-- <div id='ptc-count'>96 Links Available To Click</div> -->
                          <div class='ptcbox-w1'>
                            <div class='ptcbox-link'><a href='#'></a></div>
                            <div class='ptcbox-value-w1'>
                              <div class='ptcbox-value'><a class="click-add" target="_blank" href="<?php echo $url_prefix; ?>ptc_earn/add_click/<?php echo $url_data; ?>">$<?php echo number_format($val->PRICE, 2); ?></a></div>
                              <div class='ptcbox-seconds'><?php echo $val->ADD_DURATION; ?> seconds</div>
                            </div>
                            <div class='ptcbox-title'><?php echo $val->ADD_NAME; ?></div>
                            <div class='ptcbox-clicks'>38433 Member Clicks &nbsp;&nbsp;&nbsp; 214036 Outside Clicks</div>
                          </div>

                        <?php endforeach; ?>

                        <div class="ln_solid"></div>

                    </form>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Today's Earn</h2>
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
                  <?php
                    // $get_record = $this->db->query("SELECT * FROM ptc_click WHERE ")->result();
                  ?>
                  <div class="x_content" width="100%">
                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                            <th>Name</th>
                            <th>URL</th>
                            <th>Duration</th>
                            <th>Add Value</th>
                            <th>Click Validation</th>
                            <th>Clicked Time</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($click_data as $cv) : $add_data = $this->db->query("SELECT * FROM add_setup WHERE ADD_ID='{$cv->ADD_ID}'")->row(); ?>
                          <tr>
                            <td><?php echo $add_data->ADD_NAME; ?></td>
                            <td><?php echo $add_data->URL_1; ?></td>
                            <td><?php echo $add_data->ADD_DURATION; ?>s</td>
                            <td><?php echo ($cv->CLICK_VALIDATION == 1) ? $cv->ADD_VALUE : 0; ?></td>
                            <td><?php echo ($cv->CLICK_VALIDATION == 1) ? '<span class="label label-success">Success</span>' : '<span class="label label-danger">Failed</span>'; ?></td>
                            <td><?php echo date("H:i:s", strtotime($cv->CLICK_DATE_TIME)); ?></td>
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

<script>
  var url = "<?php $url_prefix; ?>ptc_earn";
  $("a.click-add").on('click', function() {
    window.location = url;
  });
</script>