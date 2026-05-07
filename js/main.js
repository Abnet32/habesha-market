/* ============================================================
   Habesha Market - Main JavaScript
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    // ===== NAVBAR SCROLL EFFECT =====
    const navbar = document.querySelector('.navbar-custom');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // ===== MOBILE HAMBURGER =====
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', function () {
            navLinks.classList.toggle('open');
            hamburger.classList.toggle('active');
        });
        document.addEventListener('click', function (e) {
            if (!hamburger.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('open');
            }
        });
    }

    // ===== PARTICLES ANIMATION =====
    const canvas = document.getElementById('particles-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const particles = [];
        const colors = ['rgba(7,137,48,0.6)', 'rgba(252,221,9,0.4)', 'rgba(218,18,26,0.3)', 'rgba(255,255,255,0.2)'];

        for (let i = 0; i < 60; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                r: Math.random() * 3 + 1,
                dx: (Math.random() - 0.5) * 0.5,
                dy: (Math.random() - 0.5) * 0.5,
                color: colors[Math.floor(Math.random() * colors.length)],
                alpha: Math.random() * 0.5 + 0.2
            });
        }

        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = p.color;
                ctx.globalAlpha = p.alpha;
                ctx.fill();
                p.x += p.dx;
                p.y += p.dy;
                if (p.x < 0 || p.x > canvas.width) p.dx = -p.dx;
                if (p.y < 0 || p.y > canvas.height) p.dy = -p.dy;
            });
            ctx.globalAlpha = 1;
            requestAnimationFrame(animateParticles);
        }

        animateParticles();

        window.addEventListener('resize', function () {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
    }

    // ===== SCROLL REVEAL ANIMATION =====
    const animatedEls = document.querySelectorAll('.animate-in');
    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

    animatedEls.forEach(el => observer.observe(el));

    // ===== COUNTER ANIMATION =====
    const counterEls = document.querySelectorAll('.counter-number[data-target]');
    const counterObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counterEls.forEach(el => counterObserver.observe(el));

    function animateCounter(el) {
        const target = parseInt(el.dataset.target);
        const suffix = el.dataset.suffix || '';
        const duration = 2000;
        const steps = 60;
        const increment = target / steps;
        let current = 0;
        const timer = setInterval(function () {
            current += increment;
            if (current >= target) {
                el.textContent = target + suffix;
                clearInterval(timer);
            } else {
                el.textContent = Math.floor(current) + suffix;
            }
        }, duration / steps);
    }

    // ===== LIVE SEARCH =====
    const searchInput = document.getElementById('live-search');
    const resultsBox = document.getElementById('search-results-live');
    if (searchInput && resultsBox) {
        let searchTimer;
        searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            clearTimeout(searchTimer);
            if (query.length < 2) {
                resultsBox.style.display = 'none';
                return;
            }
            searchTimer = setTimeout(function () {
                fetch('ajax/search.php?q=' + encodeURIComponent(query))
                    .then(r => r.json())
                    .then(data => {
                        if (data.length === 0) {
                            resultsBox.innerHTML = '<div class="live-result-item"><span>No results found</span></div>';
                        } else {
                            resultsBox.innerHTML = data.map(item => `
                                <a class="live-result-item" href="product_detail.php?id=${item.id}">
                                    <img class="live-result-img" src="${item.image}" alt="${item.name}" onerror="this.src='img/placeholder.jpg'">
                                    <div>
                                        <strong>${item.name}</strong>
                                        <div style="font-size:0.78rem;color:var(--primary-light)">${item.category} &mdash; ETB ${parseFloat(item.price).toLocaleString()}</div>
                                    </div>
                                </a>
                            `).join('');
                        }
                        resultsBox.style.display = 'block';
                    })
                    .catch(() => {
                        resultsBox.style.display = 'none';
                    });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.style.display = 'none';
            }
        });
    }

    // ===== PASSWORD TOGGLE =====
    document.querySelectorAll('.toggle-icon').forEach(function (icon) {
        icon.addEventListener('click', function () {
            const input = this.closest('.password-toggle').querySelector('input');
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    });

    // ===== SIGNUP FORM VALIDATION =====
    const signupForm = document.getElementById('signup-form');
    if (signupForm) {
        const emailInput = document.getElementById('email');
        let emailTimer;

        if (emailInput) {
            emailInput.addEventListener('input', function () {
                clearTimeout(emailTimer);
                const val = this.value.trim();
                const msg = document.getElementById('email-msg');
                if (!isValidEmail(val)) {
                    setFieldState(this, msg, 'error', 'Please enter a valid email address.');
                    return;
                }
                emailTimer = setTimeout(function () {
                    fetch('ajax/validate_email.php?email=' + encodeURIComponent(val))
                        .then(r => r.json())
                        .then(data => {
                            if (data.taken) {
                                setFieldState(emailInput, msg, 'error', 'This email is already registered.');
                            } else {
                                setFieldState(emailInput, msg, 'success', 'Email is available.');
                            }
                        });
                }, 600);
            });
        }

        signupForm.addEventListener('submit', function (e) {
            let valid = true;
            const fields = [
                { id: 'full_name', msgId: 'name-msg', validate: v => v.trim().length >= 3, msg: 'Name must be at least 3 characters.' },
                { id: 'email', msgId: 'email-msg', validate: isValidEmail, msg: 'Please enter a valid email.' },
                { id: 'phone', msgId: 'phone-msg', validate: v => /^[+\d\s-]{9,15}$/.test(v), msg: 'Enter a valid phone number.' },
                { id: 'password', msgId: 'pass-msg', validate: v => v.length >= 8, msg: 'Password must be at least 8 characters.' },
                { id: 'confirm_password', msgId: 'cpass-msg', validate: v => v === document.getElementById('password').value, msg: 'Passwords do not match.' }
            ];
            fields.forEach(f => {
                const el = document.getElementById(f.id);
                const msg = document.getElementById(f.msgId);
                if (el && !f.validate(el.value)) {
                    setFieldState(el, msg, 'error', f.msg);
                    valid = false;
                }
            });
            if (!valid) e.preventDefault();
        });
    }

    // ===== LOGIN FORM VALIDATION =====
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            let valid = true;
            const email = document.getElementById('login-email');
            const pass = document.getElementById('login-password');
            const emailMsg = document.getElementById('login-email-msg');
            const passMsg = document.getElementById('login-pass-msg');

            if (!isValidEmail(email.value)) {
                setFieldState(email, emailMsg, 'error', 'Please enter a valid email.');
                valid = false;
            } else {
                setFieldState(email, emailMsg, 'success', '');
            }

            if (pass.value.length < 6) {
                setFieldState(pass, passMsg, 'error', 'Password is too short.');
                valid = false;
            } else {
                setFieldState(pass, passMsg, 'success', '');
            }

            if (!valid) e.preventDefault();
        });
    }

    // ===== CART QUANTITY CONTROLS =====
    document.querySelectorAll('.qty-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const action = this.dataset.action;
            const itemId = this.dataset.id;
            const qtyEl = this.closest('.qty-control').querySelector('.qty-value');
            let qty = parseInt(qtyEl.textContent);

            if (action === 'minus' && qty <= 1) return;
            qty = action === 'plus' ? qty + 1 : qty - 1;

            fetch('ajax/cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update&item_id=${itemId}&qty=${qty}`
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        qtyEl.textContent = qty;
                        if (data.subtotal !== undefined) {
                            const subtotalEl = document.getElementById('subtotal-' + itemId);
                            if (subtotalEl) subtotalEl.textContent = 'ETB ' + parseFloat(data.subtotal).toLocaleString();
                        }
                        if (data.total !== undefined) {
                            const totalEl = document.getElementById('cart-total');
                            if (totalEl) totalEl.textContent = 'ETB ' + parseFloat(data.total).toLocaleString();
                        }
                        updateCartBadge(data.count);
                        showToast('Cart updated', 'success');
                    }
                })
                .catch(() => showToast('Something went wrong', 'error'));
        });
    });

    // ===== ADD TO CART BUTTONS (AJAX) =====
    document.querySelectorAll('.add-to-cart-ajax').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.dataset.id;
            const self = this;
            self.innerHTML = '<span class="spinner"></span>';

            fetch('ajax/cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=add&product_id=${productId}`
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        self.innerHTML = '<i class="fas fa-check"></i>';
                        self.classList.add('in-cart');
                        updateCartBadge(data.count);
                        showToast('Added to cart!', 'success');
                    } else if (data.login) {
                        window.location.href = 'login.php';
                    } else {
                        self.innerHTML = '<i class="fas fa-cart-plus"></i>';
                        showToast(data.message || 'Could not add to cart', 'error');
                    }
                })
                .catch(() => {
                    self.innerHTML = '<i class="fas fa-cart-plus"></i>';
                    showToast('Something went wrong', 'error');
                });
        });
    });

    // ===== HELPERS =====
    function isValidEmail(v) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
    }

    function setFieldState(input, msgEl, state, msg) {
        input.classList.remove('error', 'success');
        input.classList.add(state);
        if (msgEl) {
            msgEl.textContent = msg;
            msgEl.className = 'field-msg ' + state;
        }
    }

    function updateCartBadge(count) {
        const badge = document.querySelector('.cart-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-flex' : 'none';
        }
    }

    window.showToast = function (msg, type = 'info') {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        const icons = { success: '✅', error: '❌', info: 'ℹ️' };
        const toast = document.createElement('div');
        toast.className = `toast-msg ${type}`;
        toast.innerHTML = `<span>${icons[type] || 'ℹ️'}</span> ${msg}`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    };

    // ===== PRODUCT FILTER (category dropdown) =====
    const filterSelect = document.getElementById('category-filter');
    if (filterSelect) {
        filterSelect.addEventListener('change', function () {
            const url = new URL(window.location);
            url.searchParams.set('category', this.value);
            url.searchParams.delete('q');
            window.location = url.toString();
        });
    }

    // ===== SORT SELECT =====
    const sortSelect = document.getElementById('sort-filter');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            const url = new URL(window.location);
            url.searchParams.set('sort', this.value);
            window.location = url.toString();
        });
    }
});
