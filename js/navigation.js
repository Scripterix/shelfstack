window.addEventListener("DOMContentLoaded", () => {
  const navigation = document.querySelector(".site-nav");
  const toggle = navigation?.querySelector(".nav-toggle");
  const menu = navigation?.querySelector(".nav-links");

  if (!navigation || !toggle || !menu) return;

  navigation.classList.add("nav-ready");

  function setMenu(open) {
    menu.classList.toggle("is-open", open);
    toggle.setAttribute("aria-expanded", String(open));
  }

  toggle.addEventListener("click", () => {
    setMenu(toggle.getAttribute("aria-expanded") !== "true");
  });

  menu.addEventListener("click", (event) => {
    if (event.target.closest("a")) setMenu(false);
  });

  document.addEventListener("click", (event) => {
    if (!navigation.contains(event.target)) setMenu(false);
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      setMenu(false);
      toggle.focus();
    }
  });
});
