<?php
require_once dirname(__DIR__, 2) . '/config/constants.php';
?>

<main class="container my-5">
  <div class="row g-5">

    <div class="col-lg-7">
      <div class="box p-4 shadow rounded bg-white">
        <h5 class="text-muted mb-3">Contactez-nous</h5>

        <?php
        $success = $success ?? false;
        $errors = $errors ?? [];
        ?>
        <?php if ($success): ?>
          <div class="alert alert-success">Votre message a été envoyé !</div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
        <?php endif; ?>

        <form id="formulaire" method="POST" action="index.php?page=contact" novalidate class="mt-4">

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
            <legend class="float-none w-auto px-2">
              <h2>Informations</h2>
            </legend>

            <div class="row">
              <div class="col-md-6">
                <?php render_standard_field('nom', 'Nom :', 'text', 'Nom', $_POST['nom'] ?? '', 'erreurName'); ?>
              </div>
              <div class="col-md-6">
                <?php render_standard_field('prenom', 'Prénom :', 'text', 'Prénom', $_POST['prenom'] ?? '', 'erreurFirstname'); ?>
              </div>
            </div>

            <div class="mt-2">
              <?php render_standard_field('email', 'Email :', 'email', 'email@mail.com', $_POST['email'] ?? '', 'erreurMail'); ?>
            </div>

            <div class="mt-2">
              <?php render_select_field('sujet', 'Motif de votre message :', $motifsTickets, $_POST['sujet'] ?? ''); ?>
            </div>

          </fieldset>

          <fieldset id="message" class="border p-3 rounded mb-4">
            <legend class="float-none w-auto px-2">
              <h2>Message</h2>
            </legend>

            <?php render_textarea_field('message', 'Votre message...', 6, 'erreurChampMessage'); ?>

            <button type="submit" class="btn btn-primary w-100 py-2 mt-2">Envoyer</button>
          </fieldset>

        </form>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="box p-4 shadow rounded bg-white">
        <h3 class="mb-4 text-center">Notre Équipe</h3>
        <div class="row row-cols-1 g-4">

          <div class="col text-center d-flex flex-column align-items-center">
            <img src="../public/assets/images/Rectangle 27.png" alt="Photo" class="img-fluid rounded mb-2 shadow-sm">
            <div class="small fw-bold">Chef José</div>
            <div class="text-muted x-small">Chef de cuisine</div>
          </div>

          <div class="col text-center d-flex flex-column align-items-center">
            <img src="../public/assets/images/Rectangle 28.png" alt="Photo" class="img-fluid rounded mb-2 shadow-sm">
            <div class="small fw-bold">Maeva Boumi</div>
            <div class="text-muted x-small">Responsable logistique</div>
          </div>

          <div class="col text-center d-flex flex-column align-items-center">
            <img src="../public/assets/images/Rectangle 29.png" alt="Photo" class="img-fluid rounded mb-2 shadow-sm">
            <div class="small fw-bold">Kenza Beco</div>
            <div class="text-muted x-small">Service client</div>
          </div>

          <div class="col text-center d-flex flex-column align-items-center">
            <img src="../public/assets/images/Rectangle 30.png" alt="Photo" class="img-fluid rounded mb-2 shadow-sm">
            <div class="small fw-bold">Younes Detail</div>
            <div class="text-muted x-small">Relation traiteur & événements</div>
          </div>

        </div>
      </div>
    </div>

  </div>
</main>