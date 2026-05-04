  <footer>
    <div class="footer-logo">MY ARCADE ZONE</div>
    <div class="footer-neon">◄ EL SALÓN RECREATIVO EN ESPAÑOL ►</div>
    <p>
      Hecho con amor y muchos <span>duros de 25 pesetas</span><br>
      Todos los juegos pertenecen a sus respectivos propietarios<br>
      <span>Este sitio no tiene fines comerciales · Solo nostalgia arcade</span>
    </p>
    <div class="footer-insert">● INSERT COIN ●</div>
  </footer>

</div><!-- #site -->

<script>
/* ── BÚSQUEDA ── */
function doSearch() {
  const q = document.getElementById('search-input')?.value.trim();
  if (!q) return;
  window.location.href = '/buscar.php?q=' + encodeURIComponent(q);
}
document.getElementById('search-input')?.addEventListener('keydown', e => {
  if (e.key === 'Enter') doSearch();
});

<?php if (!empty($show_boot)): ?>
/* ── BOOT COUNTDOWN ── */
let secs = 3;
const cdEl  = document.getElementById('cd');
const boot  = document.getElementById('boot');
const flash = document.getElementById('flash');
const site  = document.getElementById('site');

setTimeout(() => {
  const iv = setInterval(() => {
    secs--;
    if (cdEl) cdEl.textContent = secs;
    if (secs <= 0) { clearInterval(iv); enterSite(); }
  }, 1000);
}, 4400);

function enterSite() {
  flash.classList.add('go');
  setTimeout(() => boot.classList.add('out'), 120);
  setTimeout(() => site.classList.add('on'), 550);
}
<?php endif; ?>

<?php if (!empty($extra_js)) echo $extra_js; ?>
</script>
</body>
</html>
