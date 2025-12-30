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
                
                // Update user name in menu
                const userLink = document.querySelector('#user-menu > a');
                if (userLink && data.user && data.user.name) {
                    userLink.textContent = '👤 ' + data.user.name;
                }
            } else {
                if (userMenu) userMenu.style.display = 'none';
                if (authMenu) authMenu.style.display = 'block';
            }
        })
        .catch(err => {
            console.error('Session check error:', err);
            // Show login menu by default on error
            const userMenu = document.getElementById('user-menu');
            const authMenu = document.getElementById('auth-menu');
            if (userMenu) userMenu.style.display = 'none';
            if (authMenu) authMenu.style.display = 'block';
        });
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        fetch('api/logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.php';
                }
            })
            .catch(err => {
                console.error('Logout error:', err);
                window.location.href = 'index.php';
            });
    }
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
