<!DOCTYPE html>
<?php $url_prefix = $this->webspice->settings()->site_url_prefix; ?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo $this->webspice->settings()->site_title; ?></title>
    <link href="<?php echo $url_prefix; ?>global/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $url_prefix; ?>global/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo $url_prefix; ?>global/css/prettyPhoto.css" rel="stylesheet">
    <link href="<?php echo $url_prefix; ?>global/css/animate.css" rel="stylesheet">
    <link href="<?php echo $url_prefix; ?>global/css/main.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->       
    <link rel="shortcut icon" href="<?php echo $url_prefix; ?>global/images/ico/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $url_prefix; ?>global/images/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $url_prefix; ?>global/images/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $url_prefix; ?>global/images/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo $url_prefix; ?>global/images/ico/apple-touch-icon-57-precomposed.png">
</head><!--/head-->
<body>
    <header class="navbar navbar-inverse navbar-fixed-top wet-asphalt" role="banner">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html"><img src="<?php echo $url_prefix; ?>global/images/logo.png" alt="logo"></a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="<?php echo $url_prefix; ?>">Home</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Features <i class="icon-angle-down">
                        </i></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo $url_prefix; ?>works">HOW IT WORKS</a></li>
                            
                        </ul>
                    <li><a href="<?php echo $url_prefix; ?>about">About</a></li>
                    <li><a href="<?php echo $url_prefix; ?>policy">Policy</a></li>
                    <li><a href="<?php echo $url_prefix; ?>faq">FAQ</a></li>
                    <li><a href="<?php echo $url_prefix; ?>contact">Contact</a></li>
                    <!--
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Pages <i class="icon-angle-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="career.html">Career</a></li>
                            <li><a href="blog-item.html">Blog Single</a></li>
                            <li><a href="pricing.html">Pricing</a></li>
                            <li><a href="404.html">404</a></li>
                            <li><a href="registration.html">Registration</a></li>
                            <li class="divider"></li>
                            <li><a href="privacy.html">Privacy Policy</a></li>
                            <li><a href="terms.html">Terms of Use</a></li>
                        </ul>
                        -->
                    </li>
                    <li><a href="<?php echo $url_prefix; ?>registration">Sign Up</a></li> 
                    <li><a href="<?php echo $url_prefix; ?>login">Login</a></li>
                </ul>
            </div>
        </div>
    </header><!--/header-->