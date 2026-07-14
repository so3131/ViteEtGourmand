<?php
 
/* 
* Genere un champ de formulaire standardisé pour l'application */  
function render_form_input(string $name, string $label, string $type = 'text', string $placeholder = '', string $value = '')
{
?>
    <div class="mb-3">
        <label for="input_<?= $name ?>" class="form-label fw-bold"><?= $label ?></label>
        <input type="<?= $type ?>"
            class="form-control"
            id="input_<?= $name ?>"
            name="<?= $name ?>"
            value="<?= htmlspecialchars($value) ?>"
            placeholder="<?= htmlspecialchars($placeholder) ?>"
            required>
    </div>
<?php
}
    
/*
 * Génère un champ de formulaire standardisé pour l'application avec gestion d'erreur optionnelle*/
function render_standard_field(string $name, string $label, string $type = 'text', string $placeholder = '', string $value = '', string $errorId = '', array $errors = [])
{
    // On n'utilise PAS global $errors ici. 
    // On utilise directement le tableau $errors reçu en argument !
    
    $isInvalid = (isset($errors[$name])) ? 'is-invalid' : '';
    $errorMessage = $errors[$name] ?? '';
?>
    <div class="mb-3" id="div_<?= $name ?>">
        <label for="<?= $name ?>" class="form-label fw-bold"><?= $label ?></label>
        <input type="<?= $type ?>"
            class="form-control py-2 <?= $isInvalid ?>" 
            id="<?= $name ?>"
            name="<?= $name ?>"
            value="<?= htmlspecialchars($value) ?>"
            placeholder="<?= htmlspecialchars($placeholder) ?>">
        
        <?php if (!empty($errorMessage)): ?>
            <div id="<?= $errorId ?>" class="text-danger small mt-1"><?= $errorMessage ?></div>
        <?php endif; ?>
    </div>
<?php
}

/*
 * Génère un textarea standardisé pour l'application avec gestion d'erreur optionnelle */
function render_textarea_field(string $name, string $placeholder = '', int $rows = 6, string $errorId = '')
{
?>
    <div class="mb-3">
        <textarea class="form-control"
            id="<?= $name ?>"
            name="<?= $name ?>"
            rows="<?= $rows ?>"
            placeholder="<?= htmlspecialchars($placeholder) ?>"
            required></textarea>
        <?php if ($errorId): ?>
            <div id="<?= $errorId ?>" class="erreurMessage text-danger small mt-1"></div>
        <?php endif; ?>
    </div>
<?php
}

/*
 *  Génère un menu déroulant <select> standardisé pour l'application*/
function render_select_field(string $name, string $label, array $options, string $selectedValue = '')
{
?>
    <div class="mb-3" id="div_<?= $name ?>">
        <label for="<?= $name ?>" class="form-label fw-bold"><?= $label ?></label>
        <select class="form-select py-2" id="<?= $name ?>" name="<?= $name ?>" required>
            <option value="" disabled <?= ($selectedValue === '') ? 'selected' : '' ?>>-- Choisissez une option --</option>
            <?php foreach ($options as $value => $text): ?>
                <?php $isSelected = ($value == $selectedValue) ? 'selected' : ''; ?>
                <option value="<?= htmlspecialchars($value) ?>" <?= $isSelected ?>><?= htmlspecialchars($text) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
<?php
}
