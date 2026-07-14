<!-- <main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">Donnez votre avis sur la commande #<?= htmlspecialchars("") ?></h2>
            
            <form action="index.php?page=submit-review" method="POST">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars("") ?>">

                <div class="mb-3">
                    <label for="note" class="form-label fw-bold">Votre note (sur 5)</label>
                    <select class="form-select" id="note" name="note" required aria-required="true">
                        <option value="">-- Choisissez une note --</option>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Très bien</option>
                        <option value="3">3 - Moyen</option>
                        <option value="2">2 - Décevant</option>
                        <option value="1">1 - Très décevant</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="commentaire" class="form-label fw-bold">Votre commentaire</label>
                    <textarea class="form-control" id="commentaire" name="commentaire" rows="4" required aria-required="true" placeholder="Partagez votre expérience avec Vite & Gourmand..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Publier mon avis</button>
            </form>
        </div>
    </div>
</main> -->