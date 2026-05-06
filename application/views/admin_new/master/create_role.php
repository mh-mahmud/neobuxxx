<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Create Role</h3>
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
                    <h2>Create Role</h2>
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
                  <input type="hidden" name="ROLE_ID" value="<?php if( isset($edit['ROLE_ID']) && $edit['ROLE_ID'] ){echo $this->webspice->encrypt_decrypt($edit['ROLE_ID'], 'encrypt');} ?>" />

                  <table width="100%">
                    <tr>
                      <td>
                        <div class="form_label">Role Name*</div>
                        <div>
                          <input type="text"  class="input_full input_style" id="role_name" name="role_name" value="<?php echo set_value('role_name',$edit['ROLE_NAME']); ?>"  required />
                          <span class="fred"><?php echo form_error('role_name'); ?></span>
                        </div>
                      </td>
                    </tr>

                  <div class="x_content">
                    <!--permission-->
                    <?php if($get_permission): ?>
                    <tr>
                      <td>
                        <table class="table table-bordered">
                            <?php
                            $total=array();
                            $group_name = null;
                            $group_count = 0;
                            $is_checked = null;
                            $edit['PERMISSION_NAME'] ? $edited_permission = explode(',', $edit['PERMISSION_NAME']) : $edited_permission = array();
                            foreach($get_permission_data as $k=>$v){
                              $is_checked = null;

                              if($this->webspice->permission_verify($v->PERMISSION_NAME, true)) {

                                # for edit - verify that; the permission is selected before or notes_body
                                foreach($edited_permission as $k11=>$v11) {
                                  if( $v11==$v->PERMISSION_NAME ){ $is_checked = ' checked="checked"'; }
                                }

                                # get new group name and count by group name
                                if( $v->GROUP_NAME != $group_name ){
                                  $group_name = $v->GROUP_NAME;
                                  $group_count = 0;
                                  foreach($get_permission_data as $k1=>$v1){
                                    if($v1->GROUP_NAME == $v->GROUP_NAME){$group_count++;}
                                  }
                                  
                                  echo '<tr>';
                                    echo '<td rowspan="'.$group_count.'" class="fbold" style="vertical-align:middle;">'.ucwords(str_replace('_',' ',$group_name)).'</td>';
                                    echo '<td><div><input type="checkbox" name="permission[]" value="'.$v->PERMISSION_NAME.'"'.$is_checked.'/>&nbsp;'.ucwords(str_replace('_',' ',$v->MENU_NAME)).'</div></td>';
                                  echo '</tr>';
                                  
                                }
                                elseif( $v->GROUP_NAME == $group_name ){
                                  # create checkbox
                                  echo '<tr><td><div><input type="checkbox" name="permission[]" value="'.$v->PERMISSION_NAME.'"'.$is_checked.' />&nbsp;'.ucwords(str_replace('_',' ',$v->MENU_NAME)).'</div></td></tr>';
                                }
                              }
                              
                            }     
                            ?>
                        
                        </table>
                      </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                      <td>
                        <div><input type="submit" class="btn btn-success" value="Submit Data" />
                          <a class="btn btn-danger" href="<?php echo $url_prefix; ?>manage_role">Cancel</a>
                          </div>
                      </td>
                    </tr>
                  </div>

                </table>

                </form>
            <!-- END FORM-->
                </div>
              </div>

            </div>
          </div>
        </div>
        <!-- /page content -->

<?php include(APPPATH."views/admin_new/footer_table.php"); ?>