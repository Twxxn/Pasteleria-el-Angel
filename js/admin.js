document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("menuToggle");
  const menu = document.getElementById("adminMenu");

  toggleBtn.addEventListener("click", function () {
    menu.style.display = menu.style.display === "block" ? "none" : "block";
  });
});
