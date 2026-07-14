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
 <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderModalLabel">Détails de la commande</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <!--  données injectées ici -->
      </div>
    </div>
  </div>
</div>
</main>