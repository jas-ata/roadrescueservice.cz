<!-- Mobile Menu Button -->
<button class="menu-toggle" id="menuToggle">
    <span class="menu-icon">
        <span class="menu-line"></span>
        <span class="menu-line"></span>
        <span class="menu-line"></span>
    </span>
</button>

<!-- Sidebar Navigation -->
<div class="sidebar-menu" id="sidebarMenu">
    <button class="menu-close" id="menuClose">
        <i class="material-icons">close</i>
    </button>
    <ul>
        <li><a href="index.html">HOME</a></li>
        <li><a href="autoservis.html">AUTOSERVIS</a></li>
        <li><a href="mobilnis.html">MOBILNÍ AUTOSERVIS</a></li>
        <li><a href="pujcovna.html">AUTOPŮJČOVNA</a></li>
        <li><a href="cenik.html">CENÍK</a></li>
        <li><a href="tym.html">TÝM</a></li>
        <li><a href="kontakt.html">KONTAKT</a></li>
    </ul>
</div>

<!-- Overlay for mobile menu -->
<div class="menu-overlay" id="menuOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const menuClose = document.getElementById('menuClose');
    const sidebarMenu = document.getElementById('sidebarMenu');
    const menuOverlay = document.getElementById('menuOverlay');
    const body = document.body;
    
    // Open menu
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebarMenu.classList.add('active');
            menuOverlay.classList.add('active');
            body.classList.add('menu-open');
        });
    }
    
    // Close menu
    function closeMenu() {
        sidebarMenu.classList.remove('active');
        menuOverlay.classList.remove('active');
        body.classList.remove('menu-open');
    }
    
    if (menuClose) {
        menuClose.addEventListener('click', closeMenu);
    }
    
    if (menuOverlay) {
        menuOverlay.addEventListener('click', closeMenu);
    }
    
    // Close menu when clicking on a link (for mobile)
    const menuLinks = sidebarMenu.querySelectorAll('a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                closeMenu();
            }
        });
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                closeMenu();
            }
        }, 250);
    });
    
    // Highlight current page in navigation
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = sidebarMenu.querySelectorAll('a');
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.parentElement.classList.add('active');
        }
    });
});
</script>