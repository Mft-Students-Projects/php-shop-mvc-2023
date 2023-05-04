<?php
require "src/views/includes/client/header.php";
?>

    <!-- Start main-content -->
    <main class="main-content dt-sl mb-3">
        <div class="container main-container">
            <div class="row mx-0 mt-5">
                <div class="col-xl-9 col-lg-8 col-md-12 col-sm-12 mb-2">
                    <nav class="tab-cart-page">
                        <div class="nav nav-tabs border-bottom" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link d-inline-flex w-auto active" id="nav-home-tab"
                               data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home"
                               aria-selected="true">نتیجه پرداخت<span class="count-cart">1</span></a>
                        </div>
                    </nav>
                </div>

                <div class="col-12 mt-5">
                    <?php
                            if (isset($result["Status"]) && $result["Status"] == 100)
                            {
                                // Success
                                ?>
                                <div class="alert alert-success">
                                    <?php
                                        echo "تراکنش با موفقیت انجام شد";
                                        echo "<br />مبلغ : ". $result["Amount"];
                                        echo "<br />کد پیگیری : ". $result["RefID"];
                                        echo "<br />Authority : ". $result["Authority"];
                                    ?>
                                </div>
                                <?php

                            } else {
                                // error
                                ?>
                                <div class="alert alert-danger">
                                    <?php
                                    echo "پرداخت ناموفق";
                                    echo "<br />کد خطا : ". $result["Status"];
                                    echo "<br />تفسیر و علت خطا : ". $result["Message"];
                                    ?>
                                </div>
                                <?php

                            }
                    ?>

                </div>
            </div>
        </div>
    </main>
    <!-- End main-content -->

<?php
require "src/views/includes/client/footer.php";
?>