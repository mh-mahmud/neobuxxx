<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Wallet Conversion History</h3>
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
                    <h2>Wallet Conversion History</h2>
                    <div class="clearfix"></div>


                  </div>
                  <div class="x_content" width="100%">
                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Transfer Wallet</th>
                            <th>Transfer Data</th>
                            <th>Amount</th>
                            <th>Transfer Note</th>
                            <th>Transfer Date</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($get_record as $v) : ?>
                          <tr class="odd gradeX">
                              <td><?php echo $this->webspice->admin_name($v->USER_ID); ?></td>
                              <td><?php echo str_replace("_", " ", $v->CONVERSION_WALLET); ?></td>
                              <td><?php echo $v->REASON; ?></td>
                              <td><?php echo "$".$v->AMOUNT; ?></td>
                              <td><?php echo $v->USER_NOTE; ?></td>
                              <td><?php echo $v->TRANS_DATE; ?></td>
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