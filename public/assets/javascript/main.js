// S'assure que les messages d'erreur ou de succès ne restent pas dans l'URL après leur affichage, pour éviter les répétitions gênantes lors du rafraîchissement de la page ou de la navigation.
document.addEventListener("DOMContentLoaded", function () {
  const url = new URL(window.location.href);

  // Si l'URL contient un paramètre 'error' ou 'success'
  if (url.searchParams.has("error") || url.searchParams.has("success")) {
    setTimeout(() => {
      // On NETTOIE UNIQUEMENT les messages, sans toucher au reste de l'URL
      url.searchParams.delete("error");
      url.searchParams.delete("success");

      // On met à jour la barre d'adresse proprement
      window.history.replaceState({}, document.title, url.toString());
    }, 1000); // 1 seconde de délai
  }
});


