@extends('template.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="max-w-12xl mx-auto p-6">

        <div class="flex justify-between items-center mb-5">
            <h1 class="text-3xl font-bold text-red-600">Manajemen User</h1>
            <button onclick="resetForm()"
                class="bg-red-600 text-white px-6 py-3 rounded-lg shadow hover:bg-red-700 transition duration-200">
                Tambah User
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse bg-white shadow rounded-xl transition-all duration-300">
                <thead class="bg-red-50">
                    <tr class="text-gray-700 text-lg">
                        <th class="px-4 py-3 text-left">Username</th>
                        <th class="px-4 py-3 text-left">Nama Lengkap</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 w-40 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="transition-all duration-300">
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-500 animate-pulse">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Form -->
        <div id="userFormModal"
            class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50 transition-opacity">
            <div class="bg-white p-6 rounded-xl w-full max-w-2xl shadow-xl transform transition-all">
                <h2 class="text-2xl font-bold mb-5 text-red-600">Kelola User</h2>
                <form id="userForm" class="space-y-4">
                    <input type="hidden" id="userId">

                    <div>
                        <label class="font-semibold">Username</label>
                        <input type="text" id="username" class="w-full border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="font-semibold">Nama Lengkap</label>
                        <input type="text" id="full_name" class="w-full border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="font-semibold">Email</label>
                        <input type="email" id="email" class="w-full border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="font-semibold">Password</label>
                        <input type="password" id="password" class="w-full border rounded-lg px-4 py-2">
                        <small class="text-gray-400">Kosongkan jika tidak ingin diubah</small>
                    </div>

                    <div>
                        <label class="font-semibold">Role</label>
                        <select id="role" class="w-full border rounded-lg px-4 py-2">
                            <option value="user">User</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()"
                            class="px-5 py-2 rounded-lg border hover:bg-gray-100 transition">Batal</button>
                        <button type="submit"
                            class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

        function fetchUsers() {
            const tb = document.getElementById('tableBody');
            tb.innerHTML =
                `<tr><td colspan="5" class="text-center py-10 text-gray-500 animate-pulse">Memuat data...</td></tr>`;

            axios.get("{{ route('users.list') }}")
                .then(res => {
                    const users = res.data.data ?? [];
                    tb.innerHTML = '';
                    if (!users.length) {
                        tb.innerHTML =
                            '<tr><td colspan="5" class="text-center py-10 text-gray-500">Belum ada user.</td></tr>';
                        return;
                    }
                    users.forEach(u => {
                        tb.insertAdjacentHTML('beforeend', `
                    <tr class="border-b hover:bg-red-50 transition-all duration-200">
                        <td class="px-4 py-3">${u.username}</td>
                        <td class="px-4 py-3">${u.full_name}</td>
                        <td class="px-4 py-3">${u.email ?? '-'}</td>
                        <td class="px-4 py-3">${u.role}</td>
                        <td class="px-4 py-3 text-center flex gap-2 justify-center">
                            <button onclick="editUser(${u.id})" class="text-blue-600 hover:underline">Edit</button>
                            <button onclick="deleteUser(${u.id})" class="text-red-600 hover:underline">Hapus</button>
                        </td>
                    </tr>
                `);
                    });
                });
        }

        function resetForm() {
            document.getElementById('userId').value = '';
            document.getElementById('username').value = '';
            document.getElementById('full_name').value = '';
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
            document.getElementById('role').value = 'user';
            openModal();
        }

        function openModal() {
            const modal = document.getElementById('userFormModal');
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('opacity-100'), 10);
        }

        function closeModal() {
            const modal = document.getElementById('userFormModal');
            modal.classList.add('hidden');
        }

        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('userId').value;
            const url = id ? `/users/${id}/update` : `/users/store`;

            axios.post(url, {
                username: document.getElementById('username').value,
                full_name: document.getElementById('full_name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                role: document.getElementById('role').value
            }).then(() => {
                closeModal();
                fetchUsers();
            }).catch(err => alert('Gagal menyimpan user'));
        });

        function editUser(id) {
            axios.get(`/users/${id}/edit`).then(res => {
                const u = res.data.data;
                document.getElementById('userId').value = u.id;
                document.getElementById('username').value = u.username;
                document.getElementById('full_name').value = u.full_name;
                document.getElementById('email').value = u.email ?? '';
                document.getElementById('role').value = u.role;
                document.getElementById('password').value = '';
                openModal();
            });
        }

        function deleteUser(id) {
            if (!confirm('Yakin ingin menghapus user ini?')) return;
            axios.delete(`/users/${id}/delete`).then(() => fetchUsers());
        }

        document.addEventListener('DOMContentLoaded', fetchUsers);
    </script>
@endsection
