<?php
defined('KARVIA_APP') or exit('Akses langsung tidak diizinkan.');
?>
      </main>
      <footer class="border-t border-[#dfe6de] bg-white py-4 px-6 text-center text-xs text-slate-500">
        KARVIA &copy; <?= date('Y') ?> — Media Bimbingan Karier Siswa SMA/MA
      </footer>
    </div>
  </div>
  <script>
    (function () {
      var toggle = document.getElementById('karvia-menu-toggle');
      var close = document.getElementById('karvia-menu-close');
      var sidebar = document.getElementById('karvia-sidebar');
      var backdrop = document.getElementById('karvia-backdrop');
      if (!toggle || !sidebar || !backdrop) return;

      function openMenu() {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      }

      function closeMenu() {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }

      toggle.addEventListener('click', openMenu);
      if (close) close.addEventListener('click', closeMenu);
      backdrop.addEventListener('click', closeMenu);

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
          closeMenu();
        }
      });
    })();
  </script>
</body>
</html>
