  <?php 
    include_once('tools/header.php');
    $page_title  = "Login";
  ?>
    <div>
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Template</h1>
              <h2>Login</h2>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><i class="fa fa-home fa-lg"> </i> <a href="tienda.php"> Tienda</a></li>
                <li class="breadcrumb-item active"><?php echo $page_title ?></li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-4 offset-md-4">
              <div class="card">
                <div class="card-body">
                  <?php //include_once('loginModal.php'); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

<?php include_once('tools/footer.php'); ?>