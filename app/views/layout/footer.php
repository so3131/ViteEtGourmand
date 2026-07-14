<?php use App\Models\Timetable; ?>
<footer class="container-fluid mt-5 p-4 bg-light text-black">
    <div class="container">
        <div class="row row-30">
            <div class="col-md-3">
                <a class="navbar-brand" href="?page=home">
                    <img src="assets/images/logotest.png" alt="Logo Vite & Gourmand" width="140" height="37">
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
    // On vérifie si la valeur est vide (string vide ou NULL)
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
                    <dd><a href="mailto:vite&gourmand@gmail.com" class="text-black">Vite&Gourmand@gmail.com</a></dd>
                    <dt>Tél :</dt>
                    <dd>
                        <a href="tel:0531053105" class="text-black">05 05 05 05 05</a><br>
                        <a href="tel:0665066506" class="text-black">06 06 06 06 06</a>
                    </dd>
                </dl>
            </div>

            <div class="col-md-3">
                <h5>Informations</h5>
                <ul class="nav-list list-unstyled small">
                    <li><a href="?page=contact" class="text-black">Nous contacter</a></li>
                    <li><a href="?page=mention" class="text-black">Mentions Légales</a></li>
                    <li><a href="?page=cgv" class="text-black">Conditions Générales de Vente</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container mt-4 pt-4 border-top">
        <div class="row text-center">
            <div class="col"><a href="#" class="text-black small">Facebook</a></div>
            <div class="col"><a href="#" class="text-black small">Instagram</a></div>
            <div class="col"><a href="#" class="text-black small">Twitter</a></div>
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
