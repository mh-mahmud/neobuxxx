<!DOCTYPE html>
<html lang="en">
  <?php $url_prefix = $this->webspice->settings()->site_url_prefix; ?>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $this->webspice->settings()->site_title; ?></title>

    <!-- Bootstrap -->
    <link href="<?php echo $url_prefix; ?>global/admin_new/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo $url_prefix; ?>global/admin_new/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?php echo $url_prefix; ?>global/admin_new/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="<?php echo $url_prefix; ?>global/admin_new/vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo $url_prefix; ?>global/admin_new/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form action="" method="post">
              <h1><a style="text-decoration: none;" href="<?php echo $url_prefix; ?>">NeoBuxxx</a></h1>
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <div>
                <input type="text" name="user_email" value="<?php echo set_value('user_email'); ?>" class="form-control" placeholder="USER NAME" required="" />
              </div>
              <div>
                <input type="password" name="user_password" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                <input type="submit" name="submit" class="btn btn-default submit" value="Submit" />
                <a class="reset_pass" href="<?php echo $url_prefix; ?>contact">Lost your password?</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">New to site?
                  <!-- <a href="#signup" class="to_register"> Create Account </a> -->
                  <a href="<?php echo $url_prefix; ?>registration" class="to_register"> Create Account </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-bar-chart-o"></i> NeoBuxxx</h1>
                  <p>&copy; 2017 All Rights Reserved. NeoBuxxx.</p>
                </div>
              </div>
            </form>
          </section>
        </div>

        <div id="register" class="animate form registration_form">
          <section class="login_content">
            <form>
              <h1><a style="text-decoration: none;" href="<?php echo $url_prefix; ?>">创建一个帐户</a></h1>
              <div>
                <input type="text" class="form-control" placeholder="First Name" required="" />
              </div>
              <div>
                <input type="text" class="form-control" placeholder="Last Name" required="" />
              </div>
              <div>
                <input type="email" class="form-control" placeholder="Email" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Re-type Password" required="" />
              </div>
              <div>
                <textarea rows="5" name="address" class="form-control"></textarea>
              </div><br />
              <div>
                <input type="text" class="form-control" placeholder="Mobile / Cell" required="" />
              </div>
              <div>
                <input type="text" class="form-control" placeholder="National ID" required="" />
              </div>
              <div>
                <input type="text" class="form-control" placeholder="Country" required="" />
              </div>
              <div>
                <a class="btn btn-default submit" href="index.html">Submit</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Already a member ?
                  <a href="#signin" class="to_register"> Log in </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-bar-chart-o"></i> 大兴交易</h1>
                  <p>&copy; 2017 All Rights Reserved. Daxing Deal.</p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>
