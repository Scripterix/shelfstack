window.addEventListener("DOMContentLoaded", () => {
  const htmlContainer = document.getElementById("offers");
  const jsonContainer = document.getElementById("JSONoffers");

  if (!htmlContainer || !jsonContainer) return;

  const endpoint = htmlContainer.dataset.endpoint;

  async function fetchHtmlOffer() {
    try {
      const response = await fetch(endpoint, { headers: { Accept: "text/html" } });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      htmlContainer.innerHTML = await response.text();
    } catch {
      htmlContainer.textContent = "Oferty są chwilowo niedostępne.";
    }
  }

  async function fetchJsonOffer() {
    try {
      const response = await fetch(`${endpoint}?useJSON=1`, { headers: { Accept: "application/json" } });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const offer = await response.json();

      const box = document.createElement("div");
      box.className = "border rounded p-2";

      const title = document.createElement("strong");
      title.textContent = offer.bookTitle;
      const details = document.createElement("p");
      details.className = "mb-0";
      details.textContent = `${offer.catDesc} — ${Number(offer.bookPrice).toFixed(2)} zł`;

      box.append(title, details);
      jsonContainer.replaceChildren(box);
    } catch {
      jsonContainer.textContent = "Oferta JSON jest chwilowo niedostępna.";
    }
  }

  fetchHtmlOffer();
  fetchJsonOffer();
  window.setInterval(fetchHtmlOffer, 5000);
});
