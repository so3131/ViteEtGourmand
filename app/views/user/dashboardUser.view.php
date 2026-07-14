<main class="container-fluid py-4 px-lg-5">
  <?php include ROOT_PATH . '/app/views/user/partialsDashboardUser/dashboard.Profil.view.php'; ?>

  <div class="row g-4 align-items-start">
    <aside class="col-12 col-lg-3">
<?php include ROOT_PATH . '/app/views/user/partialsDashboardUser/navbar.order.view.php'; ?>
       
    </aside>

    <section class="col-12 col-lg-9">
        <div class="tab-content" id="ordersTabContent">
            <?php include ROOT_PATH . '/app/views/user/partialsDashboardUser/dashboard.command.tracking.view.php'; ?>
        </div>
        
        <div class="row g-4 mb-4 mt-2">
            <div class="col-12">
              <?php include ROOT_PATH . '/app/views/user/partialsDashboardUser/dashboard.add.review.view.php'; ?>
            </div>
        </div>
    </section>
  </div>
</main>