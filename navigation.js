document.addEventListener('DOMContentLoaded', function() {
    // Load navigation
    fetch('navigation.html')
        .then(response => response.text())
        .then(data => {
            // Insert navigation at the beginning of body
            document.body.insertAdjacentHTML('afterbegin', data);
            
            // Initialize menu functionality after navigation is loaded
            initializeMenu();
        })
        .catch(error => console.error('Error loading navigation:', error));
});

function initializeMenu() {
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
}