function showToast(message, type = "info", duration = 4500) {
  const container = document.getElementById("toast-container");

  if (!container) {
    return;
  }

  const toast = document.createElement("div");
  toast.className = `app-toast ${type}`;
  toast.setAttribute("role", type === "error" ? "alert" : "status");

  const text = document.createElement("span");
  text.textContent = message;

  const closeButton = document.createElement("button");
  closeButton.type = "button";
  closeButton.setAttribute("aria-label", "Fermer");
  closeButton.innerHTML = "&times;";

  closeButton.addEventListener("click", () => {
    toast.remove();
  });

  toast.append(text, closeButton);
  container.appendChild(toast);

  window.setTimeout(() => {
    toast.remove();
  }, duration);
}
function showConfirm(message) {
  return new Promise((resolve) => {
    const modal = document.getElementById("confirm-modal");
    const messageElement = document.getElementById("confirm-message");
    const cancelButton = document.getElementById("confirm-cancel");
    const okButton = document.getElementById("confirm-ok");

    messageElement.textContent = message;
    modal.hidden = false;

    const close = (result) => {
      modal.hidden = true;
      cancelButton.onclick = null;
      okButton.onclick = null;
      resolve(result);
    };

    cancelButton.onclick = () => close(false);
    okButton.onclick = () => close(true);
  });
}
