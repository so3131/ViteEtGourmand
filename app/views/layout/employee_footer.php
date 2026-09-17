<div id="toast-container" aria-live="polite" aria-atomic="true"></div>

<script src="<?= BASE_URL ?>/public/assets/javascript/notifications.js"></script>
<!-- 1. jQuery-->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- 2. Bootstrap  -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- 3. DataTables  -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- 4. autres scripts spécifiques -->
<?php if (isset($specific_scripts)): ?>
    <?php foreach ($specific_scripts as $js): ?>
        <?php
        // On évite de recharger ce qu'on a déjà mis en dur ci-dessus
        if (!str_contains($js, 'jquery') && !str_contains($js, 'bootstrap') && !str_contains($js, 'datatables')): ?>
            <script src="<?= $js ?>" defer></script>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
</body>

</html>