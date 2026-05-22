    </main>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p>&copy; 2024 Vite & Gourmand. Tous droits réservés.</p>
            <p>
                <a href="?page=mention" class="text-white-50 text-decoration-none">Mentions légales</a> | 
                <a href="?page=contact" class="text-white-50 text-decoration-none">Contact</a>
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
    
    <!-- Scripts spécifiques par page -->
    <?php if (isset($specific_scripts)): ?>
        <?php foreach ($specific_scripts as $script): ?>
            <script src="<?= htmlspecialchars($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
