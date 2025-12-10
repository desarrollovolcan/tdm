<?php
         require_once __DIR__ . '/config/dz.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
        <title><?php echo !empty(['pagelevel'][]['title']) ? ['pagelevel'][]['title'].' | ' : '' ; echo ['site_level']['site_title'] ?></title>
        <?php include 'elements/meta.php';?>
        <!-- FAVICONS ICON -->
        <link rel="shortcut icon" type="image/png" href="<?php echo ['site_level']['favicon']?>">
        <?php include 'elements/page-css.php'; ?>

</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
   <?php include 'elements/preloader.php'; ?>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
      <?php include 'elements/nav-header.php'; ?>
        <!--**********************************
            Nav header end
        ***********************************-->

                <!--**********************************
            Chat box start
        ***********************************-->
                <?php include 'elements/chatbox.php'; ?>
                <!--**********************************
            Chat box End
        ***********************************-->

                <!--**********************************
            Header start
        ***********************************-->
                        <?php include 'elements/header.php'; ?>


        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
                <?php include 'elements/sidebar.php'; ?>
        <!--**********************************
            Sidebar end
        ***********************************-->

                <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
                        <div class="container-fluid">

                                <div class="row page-titles">
                                        <ol class="breadcrumb">
                                                <li class="breadcrumb-item"><a href="panel-principal.php">Panel</a></li>
                                                <li class="breadcrumb-item active"><a href="javascript:void(0)">Campeonatos</a></li>
                                        </ol>
                </div>
                                <!-- Row -->
                                <div class="row">
                                        <div class="col-xl-12">
                                                <div class="card">
                                                        <div class="card-header d-sm-flex d-block border-0 pb-0">
                                                                <div>
                                                                        <h4 class="fs-20 mb-1">Programación de partidos</h4>
                                                                        <span class="fs-14 text-muted">Asigna mesas, horarios y árbitros para cada enfrentamiento</span>
                                                                </div>
                                                        </div>
                                                        <div class="card-body">
                                                                <p class="mb-0">Asigna mesas, horarios y árbitros para cada enfrentamiento</p>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
                <!-- Button trigger modal -->



                <!--**********************************
            Footer start
        ***********************************-->
                <?php include 'elements/footer.php'; ?>




        </div>


        <!--**********************************
        Scripts
    ***********************************-->

        <!-- Required vendors -->
<?php include 'elements/page-js.php'; ?>


</body>

</html>
