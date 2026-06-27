// モバイルカートアイコンをCSSだけでなくJSで出し分け
(() => {
  const mc = document.getElementById("mobileCart");
  if (mc && window.innerWidth <= 767) mc.style.display = "flex";
})();

// ハンバーガー
const btn = document.getElementById("hamburgerBtn");
const drawer = document.getElementById("mobileDrawer");
const overlay = document.getElementById("drawerOverlay");
const close = document.getElementById("drawerClose");

const openDrawer = () => {
  drawer.classList.add("open");
  btn.classList.add("open");
  document.body.style.overflow = "hidden";
};

const closeDrawer = () => {
  drawer.classList.remove("open");
  btn.classList.remove("open");
  document.body.style.overflow = "";
};

if (btn) btn.addEventListener("click", openDrawer);
if (overlay) overlay.addEventListener("click", closeDrawer);
if (close) close.addEventListener("click", closeDrawer);
