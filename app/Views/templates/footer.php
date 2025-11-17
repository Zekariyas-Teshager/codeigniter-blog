    </main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>CodeIgniter Blog</h5>
                    <p>A simple blog system built with CodeIgniter 4</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>&copy; <?= date('Y') ?> CodeIgniter Blog. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (isset($custom_scripts)): ?>
        <?= $this->renderSection('custom_scripts') ?>
    <?php endif; ?>
</body>
</html>