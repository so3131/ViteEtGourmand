<?php
require_once dirname(__DIR__, 2) . '/config/constants.php';
?>

<main class="container my-5">
  <div class="row g-5">

    <!-- Colonne Formulaire de contact -->
    <div class="col-lg-7">
      <div class="box p-4 shadow rounded bg-white">
        <h5 class="text-muted mb-3">Contactez-nous</h5>

        <?php
        $success = $success ?? false;
        $errors = $errors ?? [];
        ?>
        <?php if ($success): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            Votre message a été envoyé avec succès !
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errors['general']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form id="formulaire" method="POST" action="index.php?page=contact" novalidate class="mt-4">
<input type="hidden"
       name="csrf_token"
       value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
          <?php
          $motifsTickets = [
            'commande'    => 'Suivi de commande en cours',
            'devis'       => 'Demande de devis pour un événement',
            'allergene'   => 'Question sur les allergènes ou composition',
            'materiel'    => 'Restitution de matériel',
            'autre'       => 'Autre demande'
          ];
          ?>

          <fieldset id="infos" class="border p-3 rounded mb-4">
            <legend class="float-none w-auto px-2 fs-5 fw-semibold text-dark">Informations</legend>

            <div class="row">
              <div class="col-md-6 mb-2">
                <?php render_standard_field('nom', 'Nom :', 'text', 'Nom', $_POST['nom'] ?? '', 'erreurName', $errors); ?>
              </div>
              <div class="col-md-6 mb-2">
                <?php render_standard_field('prenom', 'Prénom :', 'text', 'Prénom', $_POST['prenom'] ?? '', 'erreurFirstname', $errors); ?>
              </div>
            </div>

            <div class="mt-2 mb-2">
              <?php render_standard_field('email', 'Email :', 'email', 'email@mail.com', $_POST['email'] ?? '', 'erreurMail', $errors); ?>
            </div>

            <div class="mt-2">
              <?php render_select_field('sujet', 'Motif de votre message :', $motifsTickets, $_POST['sujet'] ?? ''); ?>
            </div>

          </fieldset>

          <fieldset id="message-section" class="border p-3 rounded mb-4">
            <legend class="float-none w-auto px-2 fs-5 fw-semibold text-dark">Message</legend>

            <?php 
            render_textarea_field(
                'message', 
                'Votre message...', 
                6, 
                'erreurChampMessage', 
                $_POST['message'] ?? '', 
                $errors ?? []
            ); 
            ?>

            <button type="submit" class="btn btn-primary w-100 py-2 mt-3 fw-semibold">Envoyer le message</button>
          </fieldset>

        </form>
      </div>
    </div>

    <!-- Colonne Équipe -->
    <div class="col-lg-5">
      <div class="box p-4 shadow rounded bg-white">
        <h3 class="mb-4 text-center h5 fw-bold">Notre Équipe</h3>
        <div class="row row-cols-1 g-4">

          <div class="col text-center d-flex flex-column align-items-center">
            <img src="assets/img/others/chef-ratatouille.jpeg" alt="Photo du Chef José" class="img-fluid rounded-circle mb-2 shadow-sm" style="width: 90px; height: 90px; object-fit: cover;">
            <div class="small fw-bold">Chef José</div>
            <div class="text-muted small">Chef de cuisine</div>
          </div>

          <div class="col text-center d-flex flex-column align-items-center">
            <img src="assets/img/others/maeva_boumi.jpeg" alt="Photo de Maeva Boumi, Responsable Logistique" class="img-fluid rounded-circle mb-2 shadow-sm" style="width: 90px; height: 90px; object-fit: cover;">
            <div class="small fw-bold">Maeva Boumi</div>
            <div class="text-muted small">Responsable logistique</div>
          </div>

          <div class="col text-center d-flex flex-column align-items-center">
            <img src="assets/img/others/kenza-beco.jpeg" alt="Photo de Kenza Beco, Service client" class="img-fluid rounded-circle mb-2 shadow-sm" style="width: 90px; height: 90px; object-fit: cover;">
            <div class="small fw-bold">Kenza Beco</div>
            <div class="text-muted small">Service client</div>
          </div>

          <div class="col text-center d-flex flex-column align-items-center">
            <img src="assets/img/others/younes-detail.jpeg" alt="Photo de Younes Detail, Relation traiteur & événements" class="img-fluid rounded-circle mb-2 shadow-sm" style="width: 90px; height: 90px; object-fit: cover;">
            <div class="small fw-bold">Younes Detail</div>
            <div class="text-muted small">Relation traiteur & événements</div>
          </div>

        </div>
      </div>
    </div>

  </div>
</main>