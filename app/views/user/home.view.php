<!-- 1. BANDEAU DE PRÉSENTATION -->
<section class="full-width-section text-white">
    <div class="container">
        <h2 class="text-center mb-5 titre-1">Vite & Gourmand : 25 ans de passion au service de vos événements</h2>

        <div class="row align-items-center">
            <div class="col-md-6 lead">
                <p class="slogan-1">
                    Tout a commencé à Bordeaux, il y a un quart de siècle, autour d'une idée simple, rapide et gourmande :</p>
                <p class="slogan-2">Partager une cuisine authentique et créative.
                </p>

                <p class="slogan-3">
                    Julie et José, fondateurs de <strong>Vite & Gourmand</strong>, ont bâti leur réputation sur des menus en constante évolution, capables de sublimer vos moments les plus précieux.
                </p>
                <p>
                    Avec notre application web, vous pouvez découvrir nos créations culinaires et nous solliciter plus facilement.
                </p>
            </div>

            <div class="col-md-6 text-center mt-4 mt-md-0">
                <img src="assets/img/others/plat-presentation.webp"
                    class="hero-img plat-presentation-img"
                    alt="Un plat de la cuisine artisanale de Vite & Gourmand">
            </div>
        </div>
    </div>
</section>

<!-- 2. CONTENEUR PRINCIPAL -->
<main>
    <!-- Section Équipe -->
    <section class="mb-5 pt-4 equipe-section">
        <h2 class="text-center mb-4 text-dark">~ Notre Équipe ~ </h2>

        <div class="row g-4 justify-content-center">
            <!-- Membre 1 -->
            <div class="col-md-3 col-sm-6">
                <div class="team card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <img src="assets/img/others/chef-ratatouille.webp" class="w-100" alt="Chef Ratatouille">
                    <div class="card-body text-center p-3 d-flex flex-column justify-content-between">
                        <div>
                            <p class="fw-bold fs-5 mb-1 text-dark">Chef Ratatouille</p>
                            <p class="text-dark small mb-2 fw-semibold">Chef de cuisine</p>
                        </div>
                        <p class="team-role card-text small text-dark bg-light p-2 rounded-2 mb-0 mt-2">Maître des saveurs et garant de notre tradition culinaire.</p>
                    </div>
                </div>
            </div>

            <!-- Membre 2 -->
            <div class="col-md-3 col-sm-6">
                <div class="team card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <img src="assets/img/others/maeva-boumi.webp" class="w-100" alt="Photo de Maeva Boumi, Responsable Logistique">
                    <div class="card-body text-center p-3 d-flex flex-column justify-content-between">
                        <div>
                            <p class="fw-bold fs-5 mb-1 text-dark">Maeva Boumi</p>
                            <p class="text-dark small mb-2 fw-semibold">Responsable logistique</p>
                        </div>
                        <p class="team-role card-text small text-dark bg-light p-2 rounded-2 mb-0 mt-2">S'assure que chaque événement est livré sans accroc.</p>
                    </div>
                </div>
            </div>

            <!-- Membre 3 -->
            <div class="col-md-3 col-sm-6">
                <div class="team card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <img src="assets/img/others/kenza-beco.webp" class="w-100" alt="Photo de Kenza Beco, Service client">
                    <div class="card-body text-center p-3 d-flex flex-column justify-content-between">
                        <div>
                            <p class="fw-bold fs-5 mb-1 text-dark">Kenza Beco</p>
                            <p class="text-dark small mb-2 fw-semibold">Service client</p>
                        </div>
                        <p class="team-role card-text small text-dark p-2 rounded-2 mb-0 mt-2">À l'écoute pour personnaliser vos demandes.</p>
                    </div>
                </div>
            </div>

            <!-- Membre 4 -->
            <div class="col-md-3 col-sm-6">
                <div class="team card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <img src="assets/img/others/younes-detail.webp" class="w-100" alt="Photo de Younes Detail, Relation traiteur & événements">
                    <div class="card-body text-center p-3 d-flex flex-column justify-content-between">
                        <div>
                            <p class="fw-bold fs-5 mb-1 text-dark">Younes Detail</p>
                            <p class="text-dark small mb-2 fw-semibold">Relation traiteur</p>
                        </div>
                        <p class="team-role card-text small text-dark bg-light p-2 rounded-2 mb-0 mt-2">Votre interlocuteur privilégié pour les réceptions.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Avis Clients -->
    <section class="full-width-section text-white reviews-section mb-5">
        <div class="container">
            <h2 class="text-center mb-4">Ce que nos clients disent de nous :</h2>

            <div class="row g-4 justify-content-center">
                <?php if (!empty($approvedReviews)): ?>
                    <?php foreach ($approvedReviews as $review): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100 shadow-sm bg-white text-dark border-0">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <p class="fw-bold fs-5 text-dark mb-1"><?= htmlspecialchars($review['nom_auteur'] ?? 'Client Anonyme') ?></p>

                                        <!-- Affichage du nom du menu associé -->

                                        <?php if (!empty($review['titre'])): ?>
                                            <small class="text-muted d-block mb-2">
                                                <i class="fa-solid fa-utensils"></i>Menu commandé : <?= htmlspecialchars($review['titre']) ?>
                                            </small>
                                        <?php endif; ?>

                                        <div class="text-warning mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?= $i <= ($review['note'] ?? 5) ? '★' : '☆' ?>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="card-text text-dark"><?= nl2br(htmlspecialchars($review['description'] ?? '')) ?></p>
                                    </div>

                                    <small class="text-dark mt-3">Publié le <?= date('d/m/Y', strtotime($review['created_at'])) ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-light">Aucun avis pour le moment. Soyez le premier à donner le vôtre !</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>