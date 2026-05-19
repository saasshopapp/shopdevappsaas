<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login My SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="config.js"></script>
    <link rel="stylesheet" href="design-system.css">
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-zinc-100 text-zinc-900">
    <main x-data="loginPage()" x-init="init()" x-cloak class="grid min-h-screen place-items-center px-4 py-10">
        <section class="w-full max-w-sm rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <p class="text-sm font-medium text-teal-700">My SaaS</p>
                <h1 class="mt-1 text-2xl font-semibold">Login</h1>
            </div>

            <div class="mb-5 grid grid-cols-2 gap-2 rounded-md bg-zinc-100 p-1" role="tablist" aria-label="Pilih role login">
                <button type="button" @click="setRole('user')"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-md text-sm font-semibold"
                    :class="loginForm.role === 'user' ? 'bg-white text-cyan-800 shadow-sm' : 'text-zinc-600 hover:text-zinc-900'">
                    <i data-lucide="user" class="h-4 w-4"></i>
                    User
                </button>
                <button type="button" @click="setRole('admin')"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-md text-sm font-semibold"
                    :class="loginForm.role === 'admin' ? 'bg-white text-teal-800 shadow-sm' : 'text-zinc-600 hover:text-zinc-900'">
                    <i data-lucide="shield-check" class="h-4 w-4"></i>
                    Admin
                </button>
            </div>

            <form @submit.prevent="login" class="space-y-4">
                <label class="block">
                    <span class="text-sm font-medium">Username atau email</span>
                    <input x-model="loginForm.login" type="text"
                        class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-100"
                        autocomplete="username" required>
                </label>

                <label class="block">
                    <span class="text-sm font-medium">Password</span>
                    <div class="mt-1 flex rounded-md border border-zinc-300 focus-within:border-teal-600 focus-within:ring-2 focus-within:ring-teal-100">
                        <input x-model="loginForm.password" :type="showPassword ? 'text' : 'password'"
                            class="min-w-0 flex-1 rounded-l-md border-0 px-3 py-2 text-sm outline-none"
                            autocomplete="current-password" required>
                        <button type="button" @click="showPassword = !showPassword; refreshIcons()"
                            class="grid w-11 place-items-center rounded-r-md text-zinc-500 hover:bg-zinc-50 hover:text-zinc-800"
                            :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                            <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="h-4 w-4"></i>
                        </button>
                    </div>
                </label>

                <button type="submit" :disabled="loading"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-60">
                    <i data-lucide="log-in" class="h-4 w-4"></i>
                    <span x-text="loading ? 'Memproses...' : 'Masuk'"></span>
                </button>
            </form>

            <div x-show="error" role="alert"
                class="mt-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                x-text="error"></div>
        </section>
    </main>

    <script>
        function loginPage() {
            return {
                apiBase: '',
                loading: false,
                error: '',
                showPassword: false,
                loginForm: { login: '', password: '', role: 'user' },

                init() {
                    this.apiBase = this.resolveApiBase();

                    const params = new URLSearchParams(window.location.search);
                    const role = params.get('role');
                    if (['admin', 'user'].includes(role)) {
                        this.loginForm.role = role;
                    }

                    this.refreshIcons();
                },
                refreshIcons() {
                    this.$nextTick(() => window.lucide?.createIcons());
                },
                resolveApiBase() {
                    if (['localhost', '127.0.0.1', '::1'].includes(window.location.hostname)) {
                        return '../backend/api/index.php?action=';
                    }

                    return window.SAAS_CONFIG?.API_BASE || '../backend/api/index.php?action=';
                },
                setRole(role) {
                    this.loginForm.role = role;
                    this.error = '';
                    this.refreshIcons();
                },
                async login() {
                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch(this.apiBase + 'login', {
                            method: 'POST',
                            credentials: 'include',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(this.loginForm),
                        });
                        const data = await response.json().catch(() => ({}));

                        if (!response.ok || data.ok === false) {
                            throw new Error(data.message || 'Login gagal.');
                        }

                        window.location.href = data.user?.role === 'admin' ? 'admin.html' : 'user.html';
                    } catch (error) {
                        this.error = error.message || 'Login gagal.';
                    } finally {
                        this.loading = false;
                        this.refreshIcons();
                    }
                },
            };
        }
    </script>
</body>

</html>
