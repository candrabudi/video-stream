<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - YouTube Sederhana</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9f9f9;
        }

        .youtube-red {
            background-color: #ff0000;
        }

        .youtube-red-text {
            color: #ff0000;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-sm bg-white p-6 sm:p-10 rounded-xl shadow-2xl border border-gray-100">

        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-4">
                <img src="{{ asset('homepage/logo/logo.png') }}" alt="YouTube Logo"
                    class="w-14 h-14">

            </div>

            <p class="text-gray-500 text-sm">Gunakan akun Anda untuk melanjutkan.</p>
        </div>


        <form id="login-form">
            <div class="mb-5">
                <input type="text" id="username" placeholder="Nama Pengguna atau Email" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
            </div>

            <div class="mb-6 relative">
                <input type="password" id="password" placeholder="Kata Sandi" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg pr-10 focus:ring-2 focus:ring-red-500">
                <button type="button" id="togglePassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                    <i id="icon-closed" class="ri-eye-off-line text-xl"></i>
                    <i id="icon-open" class="ri-eye-line text-xl hidden"></i>
                </button>
            </div>

            <div id="message-box" class="hidden p-3 mb-4 text-center rounded-lg text-sm transition-all duration-300">
            </div>

            <button type="submit"
                class="w-full youtube-red text-white font-semibold py-3 rounded-lg hover:bg-red-700 transition">
                Masuk
            </button>
        </form>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const messageBox = document.getElementById('message-box');
            const submitButton = e.target.querySelector('button');

            submitButton.disabled = true;
            submitButton.textContent = "Memproses...";

            axios.post('/login', {
                    username: document.getElementById('username').value,
                    password: document.getElementById('password').value
                }, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => {
                    messageBox.textContent = res.data.message;
                    messageBox.className = "bg-green-100 text-green-700 p-3 mb-4 rounded-lg text-sm";
                    messageBox.classList.remove('hidden');
                    setTimeout(() => location.href = res.data.redirect, 600);
                })
                .catch(err => {
                    const data = err.response?.data || {
                        message: "Login gagal"
                    };
                    messageBox.textContent = data.message;
                    messageBox.className = "bg-red-100 text-red-700 p-3 mb-4 rounded-lg text-sm";
                    messageBox.classList.remove('hidden');
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = "Masuk";
                });
        });

        const passwordInput = document.getElementById('password');
        const iconOpen = document.getElementById('icon-open');
        const iconClosed = document.getElementById('icon-closed');

        document.getElementById('togglePassword').addEventListener('click', () => {
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
            iconOpen.classList.toggle('hidden');
            iconClosed.classList.toggle('hidden');
        });
    </script>

</body>

</html>
