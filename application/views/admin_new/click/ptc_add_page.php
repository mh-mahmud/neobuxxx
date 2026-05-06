<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php $url_prefix = $this->webspice->settings()->site_url_prefix; ?>
<head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
    <title>NeoBuxxx</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $url_prefix; ?>global/add-click.css">
    <script type="text/javascript">
        var id="99138";
        var url_variables="sid=T0M0NE56YzRPRGM0TVRF&sid2=T0M0N&type=ptc&siduid=2808924&";
        var timer=<?php echo $add_duration+1; ?>;
        var type="ptc";
        var key="";
        var pretime="1492641424";

    </script>
    <script src="<?php echo $url_prefix; ?>global/admin_new/vendors/jquery/dist/jquery.min.js"></script>
    <script>
    var send_url = "<?php echo $url_prefix; ?>ptc_earn/confirm_click/<?php echo $this->webspice->enc($add_id, 'encrypt'); ?>";
    var add_id = "<?php echo $this->webspice->enc($add_id, 'decrypt'); ?>";
    function reportAd() {
        window.open("gpt.php?v=report&id="+id+"&type="+type+"&"+url_variables+"","","width=500,height=400,status=1")
    }

    function next(num) {
        if(timer == 0) {
            parent.location.href=send_url;
        }
        else { alert("You must wait for the counter to reach 0"); }
    }

    function adTimer() {
        timer--;
        if(timer == 0) {
            var show="Click "+key;
            $("#buttons").fadeIn();
        }
        else {
            var show="Wait: "+timer;
            setTimeout(adTimer, 1000);
        }
        $("#timer").html(show);
    }

    $(document).ready(function() {
        if(id != -1) adTimer();
        else $("#timer").html("Cheat Check");
    });

    </script>
</head>
<body>

<!-- START 468x60 Ad Peeps Ad Code -->
<div id="banner">
    <script type="text/javascript" src="http://www.dmrotate.com/bannerad/adpeeps.php?bf=showad&amp;uid=100000&amp;bmode=off&amp;gpos=center&amp;bzone=timersites&amp;bsize=468x60&amp;btype=3&amp;bpos=default&amp;btotal=1&amp;btarget=_blank&amp;bborder=0"></script>
    <noscript>
    <a rel="nofollow" href="http://www.dmrotate.com/bannerad/adpeeps.php?bf=go&amp;uid=100000&amp;bmode=off&amp;bzone=default&amp;bsize=468x60&amp;btype=1&amp;bpos=default" target="_blank">
    <img src="http://www.dmrotate.com/bannerad/adpeeps.php?bf=showad&amp;uid=100000&amp;bmode=off&amp;bzone=default&amp;bsize=468x60&amp;btype=1&amp;bpos=default" width="468" height="60" alt="Click Here!" title="Click Here!" border="0" /></a>
    </noscript>
</div>
<!-- END Ad Peeps Ad Code -->

<div id="logo"></div>

<div id="timer">Loading</div>

<div id="buttons">
    <ul>
        <li><a href="#" onclick="next(0)" id="button0"><img height="70" src="<?php echo $url_prefix; ?>global/submit.gif" alt="Button" /></a></li>
    </ul>
</div>
<div class="space"></div>
<div class="show-add">
    <!-- <iframe width="100%" height="800px" frameborder="0" src="http://www.bbc.com/" /> -->
    <iframe width="100%" height="800px" frameborder="0" src="<?php echo $url; ?>" />
</div>

</body>
</html>
