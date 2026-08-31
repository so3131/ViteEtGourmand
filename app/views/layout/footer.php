<?php use App\Models\Timetable; ?>
<footer class="container-fluid mt-5 p-4 bg-light text-black">
    <div class="container">
        <div class="row row-30">
            <div class="col-md-3">
                <a class="navbar-brand d-inline-block py-1" href="?page=home">
                    <img src="assets/img/others/Logo.svg" alt="Logo de la societé Vite & Gourmand" width="140" height="37">
                </a>
                <p class="mt-3 small">
                    Vite & Gourmand : Traiteur gastronomique et livraison.<br>
                    La qualité d'un restaurant, le confort de votre table.
                </p>
                <p class="rights small">© <span class="copyright-year">2026</span> Vite&Gourmand.</p>
            </div>

            <div class="col-md-3">
                <h5>Horaires</h5>
                <ul class="list-unstyled small">
                    <?php
                    if (isset($db)) {
                        require_once ROOT_PATH . '/app/models/Timetable.php';
                        $timetables = Timetable::ShowTimetable($db);
                        foreach ($timetables as $timetable): ?>
                            <li><?= htmlspecialchars($timetable->jour) ?> : 
    <?php 
    if (empty($timetable->heure_fermeture)): ?>
        <span class="text-danger">Fermé</span>
    <?php else: ?>
        <?= htmlspecialchars($timetable->heure_ouverture) ?> - <?= htmlspecialchars($timetable->heure_fermeture) ?>
    <?php endif; ?>
</li>
                        <?php endforeach;
                    }
                    ?>
                </ul>
            </div>

            <div class="col-md-3">
                <h5>Contacts</h5>
                <dl class="contact-list small">
                    <dt>Mail :</dt>
                    <dd><a href="mailto:vite&gourmand@gmail.com" class="text-black d-inline-block py-1">Vite&Gourmand@gmail.com</a></dd>
                    <dt>Tél :</dt>
                    <dd>
                        <a href="tel:0531053105" class="text-black d-inline-block py-1">05 05 05 05 05</a><br>
                        <a href="tel:0665066506" class="text-black d-inline-block py-1">06 06 06 06 06</a>
                    </dd>
                </dl>
            </div>

            <div class="col-md-3">
                <h5>Informations</h5>
                <ul class="nav-list list-unstyled small">
                    <li><a href="?page=contact" class="text-black d-block py-1">Nous contacter</a></li>
                    <li><a href="?page=mention" class="text-black d-block py-1">Mentions Légales</a></li>
                    <li><a href="?page=mention#cgv" class="text-black d-block py-1">Conditions Générales de Vente</a></li>
                </ul>
            </div>
        </div>
    </div>

   <div class="container mt-4 pt-4 border-top">
    <div class="row text-center">
        <div class="col">
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="text-dark text-decoration-none small d-inline-flex align-items-center gap-1 py-2 px-2">
                <i class="bi bi-facebook fs-5 text-primary"></i> Facebook
            </a>
        </div>
        <div class="col">
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="text-dark text-decoration-none small d-inline-flex align-items-center gap-1 py-2 px-2">
                <i class="bi bi-instagram fs-5 text-danger"></i> Instagram
            </a>
        </div>
        <div class="col">
            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="text-dark text-decoration-none small d-inline-flex align-items-center gap-1 py-2 px-2">
                <i class="bi bi-twitter-x fs-5 text-dark"></i> Twitter / X
            </a>
        </div>
    </div>
</div>
    <?php if (isset($specific_scripts)): ?>
        <?php foreach ($specific_scripts as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
   <script src="<?= BASE_URL ?>/public/assets/javascript/js-bootstrap/jquery-3.7.1.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>

<script src="<?= BASE_URL ?>/public/assets/javascript/js-bootstrap/bootstrap.bundle.min.js"></script>

<script src="<?= BASE_URL ?>/public/assets/javascript/recherche.js" defer></script>

</footer>