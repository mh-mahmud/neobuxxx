<?php include('header.php'); ?>
<?php include('menu.php'); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <!-- top tiles -->
          <div class="row tile_count">
            
          </div>
          <!-- /top tiles -->
          
          <!-- flash message -->
          <?php include(APPPATH."views/admin_new/flash_message.php"); ?>

          <!-- start service logo -->
          <div class="row">
            <div class="col-md-12">
              <div class="new_section" style="border-bottom: 2px solid #ddd">
                <marquee style="font-weight: bold;color: #5cb85c">        
                  NeoBuxxx is easy eanring plartfrom, world's 1 microjobs site, world class reputation, free workspace for all &amp; fast earning projects.
                </marquee>
              </div>
            </div>
          </div>


          <?php if(!$this->webspice->admin_verify()) : ?>

            <div class="row">
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-dollar"></i>
                  </div>
                  <div class="count">$<?php echo $this->webspice->user_adds_balance($this->webspice->get_user_id()); ?></div>

                  <h3 style="margin-top: 10px">Adds Wallet</h3>
                  <p>Earn Money From Clicked Adds</p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa fa-dollar"></i>
                  </div>
                  <div class="count">$<?php echo ($this->webspice->user_joining_balance($this->webspice->get_user_id()) > 0) ? $this->webspice->user_joining_balance($this->webspice->get_user_id()) : 0; ?></div>

                  <h3 style="margin-top: 10px">Joining Wallet</h3>
                  <p>Wallet Use For Joining </p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-dollar"></i>
                  </div>
                  <div class="count">$<?php echo ($this->webspice->user_shopping_balance($this->webspice->get_user_id()) > 0) ? $this->webspice->user_shopping_balance($this->webspice->get_user_id()) : 0; ?></div>

                  <h3 style="margin-top: 10px">Shopping Wallet</h3>
                  <p>Wallet For Shopping &amp; purchases</p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-dollar"></i>
                  </div>
                  <div class="count">$<?php echo ($this->webspice->user_refer_balance($this->webspice->get_user_id()) > 0) ? $this->webspice->user_refer_balance($this->webspice->get_user_id()) : 0; ?></div>

                  <h3 style="margin-top: 10px">Refer Wallet</h3>
                  <p>Earn Money From Joining People</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 col-xs-12">
              <a href="<?php echo $url_prefix; ?>my_referral_user" class="btn btn-info btn-block btn-lg"> <i class="fa fa-users fa-5x"></i><br> My Referral User</a>
              </div>

              <div class="col-md-4 col-xs-12">
              <a href="<?php echo $url_prefix; ?>manage_share" class="btn btn-info btn-block btn-lg"> <i class="fa fa-suitcase fa-5x"></i><br> My Shopping</a>
              </div>

              <div class="col-md-4 col-xs-12">
              <a href="<?php echo $url_prefix; ?>my_profile" class="btn btn-info btn-block btn-lg"> <i class="fa fa-user fa-5x"></i><br> My Profile</a>
              </div>
            </div>

            <div style="margin-top:30px;"></div>
            <div class="row">
              <div class="col-md-4 col-xs-12">
              <a href="<?php echo $url_prefix; ?>reffer_income" class="btn btn-success btn-block btn-lg"> <i class="fa fa-dollar fa-5x"></i><br> Referral Income</a>
              </div>

              <div class="col-md-4 col-xs-12">
              <a href="<?php echo $url_prefix; ?>share_income" class="btn btn-success btn-block btn-lg"> <i class="fa fa-suitcase fa-5x"></i><br> Shopping Package</a>
              </div>

              <div class="col-md-4 col-xs-12">
              <a href="<?php echo $url_prefix; ?>generation_income" class="btn btn-success btn-block btn-lg"> <i class="fa fa-credit-card fa-5x"></i><br> Generation Income</a>
              </div>
            </div>

            <div style="margin-top:30px;"></div>
            <div class="row">
              <div class="col-md-4 col-xs-12">
              <a href="<?php echo $url_prefix; ?>send_money" class="btn btn-primary btn-block btn-lg"> <i class="fa fa-plus fa-5x"></i><i class="fa fa-dollar fa-5x"></i><br> Send Money</a>
              </div>

              <div class="col-md-4 col-xs-12">
              <a href="<?php echo $url_prefix; ?>balance_withdraw" class="btn btn-primary btn-block btn-lg"> <i class="fa fa-repeat fa-5x"></i><i class="fa fa-dollar fa-5x"></i><br> Withdraw Fund</a>
              </div>

              <div class="col-md-4 col-xs-12">
              <a href="<?php echo $url_prefix; ?>transfer_balance_history" class="btn btn-primary btn-block btn-lg"> <i class="fa fa-share-alt fa-5x"></i><i class="fa fa-dollar fa-5x"></i><br> Transaction History</a>
              </div>
            </div>
          <?php endif; ?>

          <?php if($this->webspice->admin_verify()) : ?>

            <!-- <div class="row top_tiles" style="margin: 10px 0;">
              <div class="col-md-3 tile">
                <span>Total Sessions</span>
                <h2>231,809</h2>
                <span class="sparkline_one" style="height: 160px;">
                              <canvas width="200" height="60" style="display: inline-block; vertical-align: top; width: 94px; height: 30px;"></canvas>
                          </span>
              </div>
              <div class="col-md-3 tile">
                <span>Total Revenue</span>
                <h2>$ 1,231,809</h2>
                <span class="sparkline_one" style="height: 160px;">
                              <canvas width="200" height="60" style="display: inline-block; vertical-align: top; width: 94px; height: 30px;"></canvas>
                          </span>
              </div>
              <div class="col-md-3 tile">
                <span>Total Sessions</span>
                <h2>231,809</h2>
                <span class="sparkline_two" style="height: 160px;">
                              <canvas width="200" height="60" style="display: inline-block; vertical-align: top; width: 94px; height: 30px;"></canvas>
                          </span>
              </div>
              <div class="col-md-3 tile">
                <span>Total Sessions</span>
                <h2>231,809</h2>
                <span class="sparkline_one" style="height: 160px;">
                              <canvas width="200" height="60" style="display: inline-block; vertical-align: top; width: 94px; height: 30px;"></canvas>
                          </span>
              </div>
            </div> -->
            <?php
              $share_purchase = $this->db->query("SELECT SUM(TOTAL_SHARE) AS TOTAL_SHARE FROM share_balance")->row();
              if(count($share_purchase)) {
                $share_purchase = $share_purchase->TOTAL_SHARE;
              }
              else {
                $share_purchase = 0;
              }

              $balance_transfer = $this->db->query("SELECT SUM(AMOUNT) BLC_AMOUNT FROM user_balance WHERE PROVIDER_ID='".$this->webspice->get_user_id()."'")->row();
              if(count($balance_transfer)) {
                $balance_transfer = $balance_transfer->BLC_AMOUNT;
              }
              else {
                $balance_transfer = 0;
              }

              $withdraw_amount = $this->db->query("SELECT SUM(AMOUNT) AS WTD_AMOUNT FROM payment")->row();
              if(count($withdraw_amount)) {
                $withdraw_amount = $withdraw_amount->WTD_AMOUNT;
              }
              else {
                $withdraw_amount = 0;
              }

              $tot_complain = $this->db->query("SELECT COUNT(*) AS TOT_COMPLAIN FROM complain")->row();
              if(count($tot_complain)) {
                $tot_complain = $tot_complain->TOT_COMPLAIN;
              }
              else {
                $tot_complain = 0;
              }

              $refer_amt = $this->db->query("SELECT SUM(AMOUNT) AS REFER_AMT FROM user_balance WHERE TRANS_STATUS=2")->row();
              if(count($refer_amt)) {
                $refer_amt = $refer_amt->REFER_AMT;
              }
              else {
                $refer_amt = 0;
              }

              $tot_reg = $this->db->query("SELECT COUNT(*) AS TOT_REG FROM user_registration")->row();
              if(count($tot_reg)) {
                $tot_reg = $tot_reg->TOT_REG;
              }
              else {
                $tot_reg = 0;
              }

              $tot_sms = $this->db->query("SELECT COUNT(*) AS TOT_SMS FROM messaging")->row();
              if(count($tot_sms)) {
                $tot_sms = $tot_sms->TOT_SMS;
              }
              else {
                $tot_sms = 0;
              }

              $premium_user = $this->db->query("SELECT COUNT(*) AS TOT_PREM FROM user_registration WHERE ACC_STATUS=1")->row();
              if(count($premium_user)) {
                $premium_user = $premium_user->TOT_PREM;
              }
              else {
                $premium_user = 0;
              }
            ?>

            <div class="row">
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-suitcase"></i>
                  </div>
                  <div class="count"><?php echo ($share_purchase) ? $share_purchase : 0; ?></div>

                  <h3 style="margin-top: 10px">Package Purchase</h3>
                  <p>Total Shopping Purchase Purched</p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa fa-credit-card"></i>
                  </div>
                  <div class="count"><?php echo "$".$balance_transfer; ?></div>

                  <h3 style="margin-top: 10px">Balance Transfer</h3>
                  <p>Total Balance Transfer From Admin</p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-google-wallet"></i>
                  </div>
                  <div class="count"><?php echo "$".$withdraw_amount; ?></div>

                  <h3 style="margin-top: 10px">Total Withdraw</h3>
                  <p>Premium User Withdraw Request</p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-check-square-o"></i>
                  </div>
                  <div class="count"><?php echo $premium_user; ?></div>

                  <h3 style="margin-top: 10px">Premium User</h3>
                  <p>Total Premium User</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-thumbs-o-down"></i>
                  </div>
                  <div class="count"><?php echo $tot_complain; ?></div>

                  <h3 style="margin-top: 10px">Total Complain</h3>
                  <p>Total Complain Assign To Admin</p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa fa-dollar"></i>
                  </div>
                  <div class="count"><?php echo "$".$refer_amt; ?></div>

                  <h3 style="margin-top: 10px">Referral Income</h3>
                  <p>Total Referral Income Paid</p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-users"></i>
                  </div>
                  <div class="count"><?php echo $tot_reg; ?></div>

                  <h3 style="margin-top: 10px">Total User</h3>
                  <p>Total Registered User</p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-envelope"></i>
                  </div>
                  <div class="count"><?php echo $tot_sms; ?></div>

                  <h3 style="margin-top: 10px">Messaging</h3>
                  <p>Total Internal Messaging Transfer</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-3 col-xs-12">
              <a href="<?php echo $url_prefix; ?>premium_user" class="btn btn-info btn-block btn-lg"> <i class="fa fa-users fa-5x"></i><br> Premium Users</a>
              </div>

              <div class="col-md-3 col-xs-12">
              <a href="<?php echo $url_prefix; ?>manage_share" class="btn btn-info btn-block btn-lg"> <i class="fa fa-suitcase fa-5x"></i><br> View Share</a>
              </div>

              <div class="col-md-3 col-xs-12">
              <a href="<?php echo $url_prefix; ?>new_registration" class="btn btn-info btn-block btn-lg"> <i class="fa fa-user fa-5x"></i><br> New User</a>
              </div>

              <div class="col-md-3 col-xs-12">
              <a href="<?php echo $url_prefix; ?>reffer_income" class="btn btn-info btn-block btn-lg"> <i class="fa fa-dollar fa-5x"></i><br> User Referral Income</a>
              </div>
            </div>

            <div style="margin-top:30px;"></div>
            <div class="row">

              <div class="col-md-3 col-xs-12">
              <a href="<?php echo $url_prefix; ?>send_money" class="btn btn-info btn-block btn-lg"> <i class="fa fa-credit-card fa-5x"></i><br> Send Money</a>
              </div>

              <div class="col-md-3 col-xs-12">
              <a href="<?php echo $url_prefix; ?>return_balance_history" class="btn btn-info btn-block btn-lg"> <i class="fa fa-close fa-5x"></i><br> Return Balance</a>
              </div>

              <div class="col-md-3 col-xs-12">
              <a href="<?php echo $url_prefix; ?>transfer_balance_history" class="btn btn-info btn-block btn-lg"> <i class="fa fa-plus fa-5x"></i><i class="fa fa-dollar fa-5x"></i><br> Transfer History</a>
              </div>

              <div class="col-md-3 col-xs-12">
              <a href="<?php echo $url_prefix; ?>manage_withdraw" class="btn btn-info btn-block btn-lg"> <i class="fa fa-repeat fa-5x"></i><i class="fa fa-dollar fa-5x"></i><br> Withdraw Request</a>
              </div>
            </div>

            <div style="margin-top:30px;"></div>
            

          <?php endif; ?>

          <!-- end service logo -->

          <!-- <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="dashboard_graph">

                <div class="row x_title">
                  <div class="col-md-6">
                    <h3>Network Activities <small>Graph title sub-title</small></h3>
                  </div>
                  <div class="col-md-6">
                    <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                      <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                      <span>December 30, 2014 - January 28, 2015</span> <b class="caret"></b>
                    </div>
                  </div>
                </div>

                <div class="col-md-9 col-sm-9 col-xs-12">
                  <div id="placeholder33" style="height: 260px; display: none" class="demo-placeholder"></div>
                  <div style="width: 100%;">
                    <div id="canvas_dahs" class="demo-placeholder" style="width: 100%; height:270px;"></div>
                  </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12 bg-white">
                  <div class="x_title">
                    <h2>Top Campaign Performance</h2>
                    <div class="clearfix"></div>
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-6">
                    <div>
                      <p>Facebook Campaign</p>
                      <div class="">
                        <div class="progress progress_sm" style="width: 76%;">
                          <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="100"></div>
                        </div>
                      </div>
                    </div>
                    <div>
                      <p>Twitter Campaign</p>
                      <div class="">
                        <div class="progress progress_sm" style="width: 76%;">
                          <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="60"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 col-sm-12 col-xs-6">
                    <div>
                      <p>Conventional Media</p>
                      <div class="">
                        <div class="progress progress_sm" style="width: 76%;">
                          <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="40"></div>
                        </div>
                      </div>
                    </div>
                    <div>
                      <p>Bill boards</p>
                      <div class="">
                        <div class="progress progress_sm" style="width: 76%;">
                          <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="50"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="clearfix"></div>
              </div>
            </div>

          </div> -->
          <br />


          <!-- start first pannel -->
          <div class="row">
            
          </div>
          <!-- end first panel -->


          <!-- start second pannel -->
          <div class="row">
            
          </div>
          <!-- end second panel -->


          <div class="row">
            
          </div>
        </div>
        <!-- /page content -->
    

<?php include('footer.php'); ?>