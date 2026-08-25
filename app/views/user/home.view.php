<main class="container my-5">

    <section class="mb-5">
        <h1 class="text-center mb-4">Vite & Gourmand : 25 ans de passion au service de vos événements</h1>
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="lead">
                    Tout a commencé à Bordeaux, il y a un quart de siècle, autour d'une idée simple : partager une cuisine authentique et créative.
                </p>
                <p>
                    Julie et José, fondateurs de <strong>Vite & Gourmand</strong>, ont bâti leur réputation sur des menus en constante évolution, capables de sublimer vos moments les plus précieux, qu'il s'agisse d'un repas de famille intimiste ou de vos célébrations annuelles comme Noël et Pâques.
                </p>
                <p>
                    Aujourd'hui, nous franchissons une nouvelle étape : notre application web vous permet de découvrir nos créations culinaires et de nous solliciter plus facilement.
                </p>
            </div>
            <div class="col-md-6 text-center">
                <img src="assets/images/cuisine-passion.jpg" class="img-fluid rounded shadow" alt="Un plat de la cuisine artisanale de Vite & Gourmand">
            </div>
        </div>
    </section>

    <section class="mb-5 bg-light p-4 rounded">
        <h2 class="text-center mb-4">Notre Équipe</h2>
        <div class="row text-center">
            <div class="col-md-3">
                <img src="assets/images/jose.jpg" class="rounded-circle mb-3 team-img" alt="Portrait de Chef Ratatouille, Chef de cuisine">
                <h5>Chef Ratatouille</h5>
                <p class="text-muted">Chef de cuisine</p>
            </div>
            <div class="col-md-3">
                <img src="assets/images/maeva.jpg" class="rounded-circle mb-3 team-img" alt="Portrait de Maeva Boumi , Responsable logistique">
                <h5>Maeva Boumi</h5>
                <p class="text-muted">Responsable logistique</p>
            </div>
            <div class="col-md-3">
                <img src="assets/images/kenza.jpg" class="rounded-circle mb-3 team-img" alt="Portrait de Kenza Beco du Service client">
                <h5>Kenza Beco</h5>
                <p class="text-muted">Service client</p>
            </div>
            <div class="col-md-3">
                <img src="assets/images/younes.jpg" class="rounded-circle mb-3 team-img" alt="Portrait de Younes Detail des Relation traiteur et événements">
                <h5>Younes Detail</h5>
                <p class="text-muted">Relation traiteur & événements</p>
            </div>
        </div>
    </section>

    <section class="container py-5">
    <h2 class="text-center mb-4">Ce que nos clients disent de nous</h2>
    
    <div class="row">
        <?php if (!empty($approvedReviews)): ?>
            <?php foreach ($approvedReviews as $review): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($review['client_name'] ?? 'Client Anonyme') ?></h5>
                            <div class="text-warning mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?= $i <= ($review['rating'] ?? 5) ? '★' : '☆' ?>
                                <?php endfor; ?>
                            </div>
                            <p class="card-text"><?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?></p>
                            <small class="text-muted">Publié le <?= date('d/m/Y', $review['created_at']->toDateTime()->getTimestamp()) ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">Aucun avis pour le moment. Soyez le premier à donner le vôtre !</p>
        <?php endif; ?>
    </div>
</section>

</main>