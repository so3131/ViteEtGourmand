<?php

// Affichage des messages flash
if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
<input type="hidden"
       name="csrf_token"
       value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div id="success-alert" class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> Vos informations ont été mises à jour avec succès !
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php include ROOT_PATH . '/app/views/user/partialsDashboardUser/dashboard.Profil.view.php'; ?>

<main class="container-fluid py-4 px-lg-5">
 


  <div class="row g-4 align-items-start">
    <aside class="col-12 col-lg-3">
<?php include ROOT_PATH . '/app/views/user/partialsDashboardUser/navbar.order.view.php'; ?>
       
    </aside>

    <section class="col-12 col-lg-9">
        <div class="tab-content" id="ordersTabContent">
            <?php include ROOT_PATH . '/app/views/user/partialsDashboardUser/dashboard.command.tracking.view.php'; ?>
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