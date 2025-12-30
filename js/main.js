// Main JS - Navigation and Auth
function checkUserSession() {
    fetch('api/auth.php')
        .then(res => res.json())
        .then(data => {
            const userMenu = document.getElementById('user-menu');
            const authMenu = document.getElementById('auth-menu');
            
            if (data.success) {
                if (userMenu) userMenu.style.display = 'block';
                if (authMenu) authMenu.style.display = 'none';
            } else {
                if (userMenu) userMenu.style.display = 'none';
                if (authMenu) authMenu.style.display = 'block';
            }
        });
}

function logout() {
    fetch('api/logout.php')
        .then(() => {
            window.location.href = 'index.php';
        });
}

function toggleUserMenu() {
    const dropdown = event.target.parentElement.querySelector('.dropdown');
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }
}

// Check session on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', checkUserSession);
} else {
    checkUserSession();
}
