@extends('template.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <div class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Tabel Kategori -->
        <div class="lg:col-span-2 bg-white shadow rounded-xl p-5">
            <div class="flex justify-between mb-4 pb-3 border-b">
                <h2 class="text-xl font-bold flex items-center gap-2 text-red-600">
                    <i class="ri-folder-2-line"></i> Data Kategori
                </h2>

                @if (auth()->user()->role === 'super_admin')
                    <button onclick="resetForm()"
                        class="flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                        <i class="ri-add-line text-xl"></i> Tambah
                    </button>
                @endif
            </div>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-red-50 border-b text-sm text-gray-700">
                        <th class="px-3 py-2 text-left">Nama Kategori</th>
                        <th class="px-3 py-2 text-left">Deskripsi</th>
                        <th class="px-3 py-2 text-left">Dibuat Oleh</th>
                        <th class="px-3 py-2 w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr id="loadingRow">
                        <td colspan="4" class="text-center py-6 text-gray-500">
                            <i class="ri-loader-4-line animate-spin text-xl"></i>
                            Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white shadow rounded-xl p-5">
            <h2 class="text-xl font-bold mb-3 text-red-600 flex items-center gap-2">
                <i class="ri-edit-line"></i> Kelola Kategori
            </h2>

            <form id="categoryForm" class="space-y-3">
                <input type="hidden" id="categoryId">

                <div>
                    <label class="text-sm font-semibold">Nama Kategori</label>
                    <input id="category_name" type="text" class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="text-sm font-semibold">Deskripsi</label>
                    <textarea id="description" class="w-full border rounded-lg px-3 py-2"></textarea>
                </div>

                @if (auth()->user()->role === 'super_admin')
                    <button type="submit"
                        class="flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white w-full py-2 rounded-md">
                        <i class="ri-save-3-line text-xl"></i> Simpan
                    </button>
                @else
                    <div class="text-xs text-gray-400 text-center italic">
                        <i class="ri-lock-line"></i> Anda tidak memiliki akses untuk mengubah data
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-5 right-5 space-y-2 z-50"></div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const isSuperAdmin = "{{ auth()->user()->role }}" === "super_admin";
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

        function toast(msg, err = false) {
            const box = document.createElement('div');
            box.className = (err ? "bg-red-700" : "bg-black") +
                " text-white px-4 py-2 rounded-lg shadow flex items-center gap-2";
            box.innerHTML = `<i class="ri-information-line"></i>${msg}`;
            document.getElementById('toast-container').appendChild(box);
            setTimeout(() => box.remove(), 2500);
        }

        function escape(t) {
            return t?.replace(/</g, "&lt;") ?? "";
        }

        function fetchCategories() {
            axios.get("{{ route('categories.list') }}").then(res => {
                const categories = res.data.data ?? [];
                const tb = document.getElementById('tableBody');
                tb.innerHTML = '';

                if (categories.length === 0) {
                    tb.innerHTML =
                        '<tr><td colspan="4" class="text-center py-6 text-gray-500">Belum ada kategori.</td></tr>';
                    return;
                }

                categories.forEach(c => {
                    tb.insertAdjacentHTML('beforeend', `
                        <tr class="border-b hover:bg-red-50/20">
                            <td class="px-3 py-2 font-medium">${escape(c.name)}</td>
                            <td class="px-3 py-2 text-gray-600">${escape(c.description ?? '-')}</td>
                            <td class="px-3 py-2 text-gray-500 italic">${c.creator?.username ?? '-'}</td>
                            <td class="px-3 py-2 text-center flex gap-2 justify-center">
                                ${
                                    isSuperAdmin ?
                                    `<button onclick="edit(${c.id})" title="Edit" class="text-blue-600 hover:scale-110 transition">
                                                <i class="ri-edit-line text-lg"></i>
                                            </button>
                                            <button onclick="del(${c.id})" title="Hapus" class="text-red-600 hover:scale-110 transition">
                                                <i class="ri-delete-bin-6-line text-lg"></i>
                                            </button>` :
                                    `<i class="ri-lock-line text-gray-400 text-lg"></i>`
                                }
                            </td>
                        </tr>
                    `);
                });
            }).catch(() => toast('Gagal memuat kategori', true));
        }

        function resetForm() {
            categoryId.value = "";
            category_name.value = "";
            description.value = '';
        }

        function edit(id) {
            if (!isSuperAdmin) return;
            axios.get(`/categories/${id}/edit`).then(res => {
                const c = res.data.data ?? res.data;
                categoryId.value = c.id;
                category_name.value = c.name ?? "";
                description.value = c.description ?? "";
            });
        }

        function del(id) {
            if (!isSuperAdmin) return;
            if (!confirm("Yakin ingin menghapus kategori ini?")) return;
            axios.delete(`/categories/${id}/delete`)
                .then(() => {
                    fetchCategories();
                    toast("Data berhasil dihapus");
                })
                .catch(() => toast("Gagal menghapus data", true));
        }

        document.getElementById('categoryForm').addEventListener('submit', e => {
            e.preventDefault();
            if (!isSuperAdmin) return;

            const id = categoryId.value;
            const url = id ? `/categories/${id}/update` : `/categories/store`;

            axios.post(url, {
                name: category_name.value,
                description: description.value,
            }).then(() => {
                resetForm();
                fetchCategories();
                toast("Data berhasil disimpan");
            }).catch(() => toast("Gagal menyimpan data", true));
        });

        document.addEventListener("DOMContentLoaded", fetchCategories);
    </script>
@endsection
