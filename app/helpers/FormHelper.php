<?php
 
/* 
*function pour générer un champ de formulaire standardisé */
  
function render_form_input(string $name, string $label, string $type = 'text', string $placeholder = '', string $value = '', string $autocomplete = 'off')
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
            autocomplete="<?= htmlspecialchars($autocomplete) ?>"
            required>
    </div>
<?php
}

//*function pour générer un champ de mot de passe avec un bouton pour afficher/masquer le mot de passe */

function render_password_input(string $name, string $label, string $value = '')
{
?>
    <div class="mb-3">
        <label for="input_<?= $name ?>" class="form-label fw-bold"><?= $label ?></label>
        <div class="input-group">
            <input type="password"
                class="form-control"
                id="input_<?= $name ?>"
                name="<?= $name ?>"
                value="<?= htmlspecialchars($value) ?>"
                placeholder="Votre mot de passe"
                autocomplete="current-password"
                required>
            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn">
                👁️
            </button>
        </div>
    </div>
    <?php
}

/*
 *function pour générer un champ de formulaire standardisé avec gestion d'erreur optionnelle*/
function render_standard_field(string $name, string $label, string $type = 'text', string $placeholder = '', string $value = '', string $errorId = '', array $errors = [])
{
    
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

//*function pour générer un champ de zone de texte standardisé avec gestion d'erreur optionnelle

function render_textarea_field(string $name, string $placeholder = '', int $rows = 6, string $errorId = '', string $value = '', array $errors = [])
{
    $isInvalid = (isset($errors[$name])) ? 'is-invalid' : '';
    $errorMessage = $errors[$name] ?? '';
?>
    <div class="mb-3">
        <textarea class="form-control <?= $isInvalid ?>"
            id="<?= $name ?>"
            name="<?= $name ?>"
            rows="<?= $rows ?>"
            placeholder="<?= htmlspecialchars($placeholder) ?>"
            required><?= htmlspecialchars($value) ?></textarea>
            
        <?php if (!empty($errorMessage)): ?>
            <div id="<?= $errorId ?>" class="text-danger small mt-1"><?= $errorMessage ?></div>
        <?php elseif ($errorId): ?>
            <!-- Fallback si tu gères l'erreur en JS -->
            <div id="<?= $errorId ?>" class="erreurMessage text-danger small mt-1"></div>
        <?php endif; ?>
    </div>
<?php
}

/*
 *function pour générer un menu déroulant <select> standardisé */
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
