<?php include(APPPATH."views/web/header.php"); ?>

    <section id="title" class="emerald">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <h1>Contact Us</h1>
                    <p>Pellentesque habitant morbi tristique senectus et netus et malesuada</p>
                </div>
                <div class="col-sm-6">
                    <ul class="breadcrumb pull-right">
                        <li><a href="index.html">Home</a></li>
                        <li class="active">Contact Us</li>
                    </ul>
                </div>
            </div>
        </div>
    </section><!--/#title-->    

    <section id="contact-page" class="container">
        <div class="row">
            <div class="col-sm-8">
                <h4>Contact Form</h4>
                <div class="status alert alert-success" style="display: none"></div>
                <form id="main-contact-form" class="contact-form" name="contact-form" method="post" action="sendemail.php" role="form">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <input type="text" class="form-control" required="required" placeholder="First Name">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" required="required" placeholder="Last Name">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" required="required" placeholder="Email address">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <textarea name="message" id="message" required="required" class="form-control" rows="8" placeholder="Message"></textarea>
                        </div>
                    </div>
                </form>
            </div><!--/.col-sm-8-->
            <div class="col-sm-4">
                <h4>Our Location</h4>
                <iframe width="100%" height="215" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.citymaps.ie/create-google-map/map.php?width=100%&amp;height=600&amp;hl=en&amp;q=795%20FOLSOM%20AVE%2C%20SUITE%20600%20%2C%20SAN%20FRANCISCO%2C%20CALIFORNIA+(NEOBUXXX%2C%20INC.)&amp;ie=UTF8&amp;t=&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe>
            </div><!--/.col-sm-4-->
        </div>
    </section><!--/#contact-page-->

<?php include(APPPATH."views/web/footer.php"); ?>