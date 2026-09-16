<?php
$types = [
    'error'           => ['class' => 'text-bg-danger',  'icon' => 'bi-exclamation-circle'],
    'flash_error'     => ['class' => 'text-bg-danger',  'icon' => 'bi-exclamation-circle'],
    'success'         => ['class' => 'text-bg-success', 'icon' => 'bi-check-circle'],
    'flash_success'   => ['class' => 'text-bg-success', 'icon' => 'bi-check-circle'],
    'success_message' => ['class' => 'text-bg-success', 'icon' => 'bi-check-circle'],
    'info'            => ['class' => 'text-bg-info',    'icon' => 'bi-info-circle'],
    'warning'         => ['class' => 'text-bg-warning', 'icon' => 'bi-exclamation-triangle'],
];

$toasts = '';
foreach ($types as $type => $config):
    if (isset($_SESSION[$type])):
        ob_start(); ?>
        <div class="toast align-items-center <?= $config['class'] ?> border-0 show" role="alert" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi <?= $config['icon'] ?> me-2"></i><?= htmlspecialchars($_SESSION[$type]) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        <?php
        $toasts .= ob_get_clean();
        unset($_SESSION[$type]);
    endif;
endforeach;

if ($toasts !== ''):
?>
<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 2000;">
    <?= $toasts ?>
</div>
<?php endif; ?>


<script src="<?= BASE_URL ?>/public/assets/javascript/flash-messages.js"></script>

