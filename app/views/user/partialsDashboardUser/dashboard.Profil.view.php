<div class="user row full-width align-items-center mb-5 p-3 rounded shadow-sm" style="--bs-bg-opacity: 0.8;">
  
  <div class="imgPseudo col-12 col-md-auto text-center">
    <img src="assets/img/others/younes-detail.jpeg" alt=" photo de profil" width="120" height="120"
      class="img-profil rounded-circle img-fluid border border-3 border-white shadow-sm">
  </div>

  <div class="Pseudo col-12 col-md mt-3 mt-md-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-center gap-3">
      
      <div class="text-center text-md-start">
        <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
          <h2 class="mb-0 fw-bold"><?= htmlspecialchars($_SESSION['nom']. ' ' . $_SESSION['prenom'] ?? 'testuser2') ?></h2>
          <a class="btn btn-outline-secondary btn-sm rounded-circle p-1" href="?page=update-profil" title="Modifier mes infos" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;">
            <i class="bi bi-pencil-fill" style="font-size: 0.7rem;"></i>
          </a>
          
        </div>
        <p class=" small mb-0"><?= htmlspecialchars($_SESSION['email'] ?? 'user@test.com') ?></p>
      </div>

      <div class="d-flex flex-column align-items-center align-items-md-end gap-2">
        
    
      </div>

    </div>
  </div>

</div>