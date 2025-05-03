<?php
session_start();
if(!isset($_SESSION['email']))
{echo'  <meta http-equiv="refresh" content="0; URL=login.php">'; exit;}
include "header.php";
?>



<!-- ========== section start ========== -->
<section class="section">
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title">
                        <h2>Smarton Dashboard</h2>
                    </div>
                </div>
                <!-- end col -->
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#0">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    eCommerce
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- ========== title-wrapper end ========== -->
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="icon-card mb-30">
                    <div class="icon purple">
                        <i class="lni lni-cart-full"></i>
                    </div>
                    <div class="content">
                        <h6 class="mb-10">New Products</h6>
                        <h3 class="text-bold mb-10">34567</h3>
                        <p class="text-sm text-success">
                            <i class="lni lni-arrow-up"></i> +2.00%
                            <span class="text-gray">(30 days)</span>
                        </p>
                    </div>
                </div>
                <!-- End Icon Cart -->
            </div>

            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="icon-card mb-30">
                    <div class="icon orange">
                        <i class="lni lni-user"></i>
                    </div>
                    <div class="content">
                        <h6 class="mb-10">New User</h6>
                        <h3 class="text-bold mb-10">34567</h3>
                        <p class="text-sm text-danger">
                            <i class="lni lni-arrow-down"></i> -25.00%
                            <span class="text-gray"> Earning</span>
                        </p>
                    </div>
                </div>
                <!-- End Icon Cart -->
            </div>
            <!-- End Col -->
        </div>
        <!-- End Row -->

    </div>
    <!-- end container -->
</section>
<!-- ========== section end ========== -->
<?php

include "footer.php";
?>
</body>
</html>

