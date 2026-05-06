<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>My Tree User</h3>
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
                    <h2>My Tree Level 1</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_1 as $v1) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v1->FIRST_NAME . ' ' . $v1->LAST_NAME; ?></td>
                              <td><?php echo $v1->EMAIL; ?></td>
                              <td><?php echo $v1->ADDRESS; ?></td>
                              <td><?php echo $v1->MOBILE; ?></td>
                              <td><?php echo $v1->MY_ID; ?></td>
                              <td><?php echo $v1->REFFER_ID; ?></td>
                              <td><?php echo $v1->NATIONAL_ID; ?></td>
                              <td><?php echo $v1->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v1->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v1->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v1->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v1->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v1->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v1->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v1->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 1 -->

                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 2</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_2 as $v2) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v2->FIRST_NAME . ' ' . $v2->LAST_NAME; ?></td>
                              <td><?php echo $v2->EMAIL; ?></td>
                              <td><?php echo $v2->ADDRESS; ?></td>
                              <td><?php echo $v2->MOBILE; ?></td>
                              <td><?php echo $v2->MY_ID; ?></td>
                              <td><?php echo $v2->REFFER_ID; ?></td>
                              <td><?php echo $v2->NATIONAL_ID; ?></td>
                              <td><?php echo $v2->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v2->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v2->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v2->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v2->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v2->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v2->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v2->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 2 -->


                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 3</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_3 as $v3) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v3->FIRST_NAME . ' ' . $v3->LAST_NAME; ?></td>
                              <td><?php echo $v3->EMAIL; ?></td>
                              <td><?php echo $v3->ADDRESS; ?></td>
                              <td><?php echo $v3->MOBILE; ?></td>
                              <td><?php echo $v3->MY_ID; ?></td>
                              <td><?php echo $v3->REFFER_ID; ?></td>
                              <td><?php echo $v3->NATIONAL_ID; ?></td>
                              <td><?php echo $v3->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v3->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v3->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v3->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v3->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v3->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v3->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v3->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 3 -->

                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 4</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_4 as $v4) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v4->FIRST_NAME . ' ' . $v4->LAST_NAME; ?></td>
                              <td><?php echo $v4->EMAIL; ?></td>
                              <td><?php echo $v4->ADDRESS; ?></td>
                              <td><?php echo $v4->MOBILE; ?></td>
                              <td><?php echo $v4->MY_ID; ?></td>
                              <td><?php echo $v4->REFFER_ID; ?></td>
                              <td><?php echo $v4->NATIONAL_ID; ?></td>
                              <td><?php echo $v4->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v4->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v4->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v4->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v4->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v4->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v4->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v4->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 4 -->


                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 5</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_5 as $v5) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v5->FIRST_NAME . ' ' . $v5->LAST_NAME; ?></td>
                              <td><?php echo $v5->EMAIL; ?></td>
                              <td><?php echo $v5->ADDRESS; ?></td>
                              <td><?php echo $v5->MOBILE; ?></td>
                              <td><?php echo $v5->MY_ID; ?></td>
                              <td><?php echo $v5->REFFER_ID; ?></td>
                              <td><?php echo $v5->NATIONAL_ID; ?></td>
                              <td><?php echo $v5->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v5->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v5->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v5->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v5->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v5->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v5->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v5->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 5 -->

                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 6</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_6 as $v6) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v6->FIRST_NAME . ' ' . $v6->LAST_NAME; ?></td>
                              <td><?php echo $v6->EMAIL; ?></td>
                              <td><?php echo $v6->ADDRESS; ?></td>
                              <td><?php echo $v6->MOBILE; ?></td>
                              <td><?php echo $v6->MY_ID; ?></td>
                              <td><?php echo $v6->REFFER_ID; ?></td>
                              <td><?php echo $v6->NATIONAL_ID; ?></td>
                              <td><?php echo $v6->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v6->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v6->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v6->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v6->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v6->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v6->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v6->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 6 -->

                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 7</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_7 as $v7) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v7->FIRST_NAME . ' ' . $v7->LAST_NAME; ?></td>
                              <td><?php echo $v7->EMAIL; ?></td>
                              <td><?php echo $v7->ADDRESS; ?></td>
                              <td><?php echo $v7->MOBILE; ?></td>
                              <td><?php echo $v7->MY_ID; ?></td>
                              <td><?php echo $v7->REFFER_ID; ?></td>
                              <td><?php echo $v7->NATIONAL_ID; ?></td>
                              <td><?php echo $v7->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v7->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v7->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v7->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v7->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v7->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v7->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v7->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 7 -->

                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 8</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_8 as $v8) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v8->FIRST_NAME . ' ' . $v8->LAST_NAME; ?></td>
                              <td><?php echo $v8->EMAIL; ?></td>
                              <td><?php echo $v8->ADDRESS; ?></td>
                              <td><?php echo $v8->MOBILE; ?></td>
                              <td><?php echo $v8->MY_ID; ?></td>
                              <td><?php echo $v8->REFFER_ID; ?></td>
                              <td><?php echo $v8->NATIONAL_ID; ?></td>
                              <td><?php echo $v8->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v8->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v8->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v8->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v8->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v8->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v8->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v8->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 8 -->

                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 9</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_9 as $v9) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v9->FIRST_NAME . ' ' . $v9->LAST_NAME; ?></td>
                              <td><?php echo $v9->EMAIL; ?></td>
                              <td><?php echo $v9->ADDRESS; ?></td>
                              <td><?php echo $v9->MOBILE; ?></td>
                              <td><?php echo $v9->MY_ID; ?></td>
                              <td><?php echo $v9->REFFER_ID; ?></td>
                              <td><?php echo $v9->NATIONAL_ID; ?></td>
                              <td><?php echo $v9->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v9->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v9->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v9->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v9->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v9->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v9->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v9->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 9 -->

                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 10</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_10 as $v10) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v10->FIRST_NAME . ' ' . $v10->LAST_NAME; ?></td>
                              <td><?php echo $v10->EMAIL; ?></td>
                              <td><?php echo $v10->ADDRESS; ?></td>
                              <td><?php echo $v10->MOBILE; ?></td>
                              <td><?php echo $v10->MY_ID; ?></td>
                              <td><?php echo $v10->REFFER_ID; ?></td>
                              <td><?php echo $v10->NATIONAL_ID; ?></td>
                              <td><?php echo $v10->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v10->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v10->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v10->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v10->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v10->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v10->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v10->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 10 -->

                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 11</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_11 as $v11) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v11->FIRST_NAME . ' ' . $v11->LAST_NAME; ?></td>
                              <td><?php echo $v11->EMAIL; ?></td>
                              <td><?php echo $v11->ADDRESS; ?></td>
                              <td><?php echo $v11->MOBILE; ?></td>
                              <td><?php echo $v11->MY_ID; ?></td>
                              <td><?php echo $v11->REFFER_ID; ?></td>
                              <td><?php echo $v11->NATIONAL_ID; ?></td>
                              <td><?php echo $v11->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v11->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v11->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v11->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v11->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v11->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v11->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v11->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 11 -->

                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 12</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_12 as $v12) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v12->FIRST_NAME . ' ' . $v12->LAST_NAME; ?></td>
                              <td><?php echo $v12->EMAIL; ?></td>
                              <td><?php echo $v12->ADDRESS; ?></td>
                              <td><?php echo $v12->MOBILE; ?></td>
                              <td><?php echo $v12->MY_ID; ?></td>
                              <td><?php echo $v12->REFFER_ID; ?></td>
                              <td><?php echo $v12->NATIONAL_ID; ?></td>
                              <td><?php echo $v12->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v12->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v12->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v12->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v12->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v12->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v12->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v12->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 12 -->

                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 13</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_13 as $v13) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v13->FIRST_NAME . ' ' . $v13->LAST_NAME; ?></td>
                              <td><?php echo $v13->EMAIL; ?></td>
                              <td><?php echo $v13->ADDRESS; ?></td>
                              <td><?php echo $v13->MOBILE; ?></td>
                              <td><?php echo $v13->MY_ID; ?></td>
                              <td><?php echo $v13->REFFER_ID; ?></td>
                              <td><?php echo $v13->NATIONAL_ID; ?></td>
                              <td><?php echo $v13->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v13->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v13->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v13->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v13->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v13->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v13->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v13->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 13 -->

                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Tree Level 14</h2>
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
                            <th>Account Status</th>
                            <th>Created Date</th>
                            <th>Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($level_14 as $v14) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v14->FIRST_NAME . ' ' . $v14->LAST_NAME; ?></td>
                              <td><?php echo $v14->EMAIL; ?></td>
                              <td><?php echo $v14->ADDRESS; ?></td>
                              <td><?php echo $v14->MOBILE; ?></td>
                              <td><?php echo $v14->MY_ID; ?></td>
                              <td><?php echo $v14->REFFER_ID; ?></td>
                              <td><?php echo $v14->NATIONAL_ID; ?></td>
                              <td><?php echo $v14->COUNTRY; ?></td>
                              <td>
                                <?php
                                  if($v14->ACC_STATUS == 0) {
                                    echo '<span class="label label-primary">Non Premium</span>';
                                  }
                                  else if($v14->ACC_STATUS == 1) {
                                    echo '<span class="label label-success">Premium</span>';
                                  }
                                  else if($v14->ACC_STATUS == 2) {
                                    echo '<span class="label label-danger">Expired</span>';
                                  }
                                  else if($v14->ACC_STATUS == 3) {
                                    echo '<span class="label label-danger">Ban</span>';
                                  }
                                ?>
                              </td>
                              <td><?php echo $v14->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v14->STATUS == 0) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v14->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
                <!-- end level 14 -->





              </div>

            </div>
          </div>
        </div>
        <!-- /page content -->

<?php include(APPPATH."views/admin_new/footer_table.php"); ?>