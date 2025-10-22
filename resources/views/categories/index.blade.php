@extends('template.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="max-w-12xl mx-auto p-4 md:p-8">
        <div class="flex flex-wrap justify-between items-center my-6 pb-4 border-b">
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Kategori</h1>
            <button id="btnAddCategory"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition duration-150 transform hover:scale-105"
                onclick="openModalForCreate()">
                Tambah Kategori
            </button>
        </div>

        <div id="categoryGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-8">
            <div id="loadingRow" class="col-span-full text-center p-10">
                <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-red-600 mx-auto"></div>
                <p class="mt-4 text-gray-500 font-medium">Memuat data kategori...</p>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="categoryModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div id="modalContent"
            class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform transition-all scale-95 opacity-0">
            <form id="categoryForm" novalidate>
                <div class="p-6">
                    <div class="flex justify-between items-center border-b pb-3 mb-4">
                        <h5 id="categoryModalTitle" class="text-xl font-bold">Tambah Kategori</h5>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <input type="hidden" id="categoryId" value="">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700" for="name">Nama Kategori</label>
                            <input id="name" type="text" required class="w-full border rounded-lg px-3 py-2"
                                placeholder="Nama kategori">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700" for="description">Deskripsi</label>
                            <textarea id="description" class="w-full border rounded-lg px-3 py-2 min-h-[80px]" placeholder="Deskripsi (opsional)"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700" for="meta_title">Meta Title</label>
                            <input id="meta_title" type="text" class="w-full border rounded-lg px-3 py-2"
                                placeholder="Meta title (opsional)">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700" for="meta_description">Meta
                                Description</label>
                            <textarea id="meta_description" class="w-full border rounded-lg px-3 py-2 min-h-[60px]"
                                placeholder="Meta description (opsional)"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700" for="keywords">Keywords</label>
                            <input id="keywords" type="text" class="w-full border rounded-lg px-3 py-2"
                                placeholder="kata kunci (dipisah koma)">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-4 bg-gray-50 rounded-b-xl border-t">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg"
                        onclick="closeModal()">Batal</button>
                    <button id="saveBtn" type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2"></div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        (function() {
            // awal: set CSRF header (aman karena meta ada di head)
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')
                ?.content || '';

            // util toast
            function showToast(message, isError = false) {
                const container = document.getElementById('toast-container');
                if (!container) return;
                const toast = document.createElement('div');
                toast.className = (isError ? 'bg-red-700 ' : 'bg-gray-800 ') + 'text-white px-4 py-2 rounded-lg shadow';
                toast.textContent = message;
                container.appendChild(toast);
                requestAnimationFrame(() => {
                    toast.style.opacity = 1;
                    toast.style.transform = 'translateY(0)';
                });
                setTimeout(() => {
                    toast.style.transition = 'all .25s';
                    toast.style.opacity = 0;
                    toast.style.transform = 'translateY(10px)';
                    toast.addEventListener('transitionend', () => toast.remove());
                }, 3000);
            }

            // modal helpers
            function openModal() {
                const modal = document.getElementById('categoryModal');
                const content = document.getElementById('modalContent');
                if (!modal || !content) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                // animate in
                requestAnimationFrame(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                });
            }

            function closeModal() {
                const modal = document.getElementById('categoryModal');
                const content = document.getElementById('modalContent');
                if (!modal || !content) return;
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 250);
            }

            // open modal for create (reset form)
            window.openModalForCreate = function() {
                const form = document.getElementById('categoryForm');
                if (form) form.reset();
                document.getElementById('categoryId').value = '';
                document.getElementById('categoryModalTitle').innerText = 'Tambah Kategori';
                openModal();
            };

            // open modal for edit (populate then open)
            window.openModalForEdit = function(data) {
                // populate fields first
                document.getElementById('categoryId').value = data.id ?? '';
                document.getElementById('name').value = data.name ?? '';
                document.getElementById('description').value = data.description ?? '';
                document.getElementById('meta_title').value = data.meta_title ?? '';
                document.getElementById('meta_description').value = data.meta_description ?? '';
                document.getElementById('keywords').value = data.keywords ?? '';
                document.getElementById('categoryModalTitle').innerText = 'Edit Kategori';
                openModal();
            };

            // fetch
            window.fetchCategories = function() {
                const loadingRow = document.getElementById('loadingRow');
                if (loadingRow) loadingRow.style.display = 'block';
                axios.get("{{ route('categories.list') }}")
                    .then(res => {
                        if (loadingRow) loadingRow.style.display = 'none';
                        renderCategories(Array.isArray(res.data) ? res.data : (res.data.data ?? []));
                    })
                    .catch(err => {
                        if (loadingRow) loadingRow.style.display = 'none';
                        console.error('fetchCategories error:', err);
                        showToast('Gagal memuat data kategori', true);
                    });
            };

            function renderCategories(categories) {
                const grid = document.getElementById('categoryGrid');
                if (!grid) return;
                grid.innerHTML = '';
                if (!categories || categories.length === 0) {
                    grid.innerHTML = '<p class="text-center col-span-full text-gray-500 p-10">Belum ada kategori.</p>';
                    return;
                }
                categories.forEach(c => {
                    const html = `
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition duration-200 p-6 flex flex-col items-center text-center">
                    <h5 class="font-bold text-xl text-gray-900 truncate max-w-full mb-2">${escapeHtml(c.name)}</h5>
                    <p class="text-gray-500 text-sm mb-4 line-clamp-2 min-h-[40px]">${escapeHtml(c.description || '-')}</p>
                    <div class="flex gap-3 mt-auto">
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm px-4 py-1.5 rounded-full" onclick="handleEdit(${c.id})">Edit</button>
                        <button class="bg-red-100 hover:bg-red-200 text-red-600 text-sm px-4 py-1.5 rounded-full" onclick="handleDelete(${c.id})">Hapus</button>
                    </div>
                </div>
            `;
                    grid.insertAdjacentHTML('beforeend', html);
                });
            }

            // escape helper to avoid XSS when inserting strings
            function escapeHtml(text) {
                if (text === null || text === undefined) return '';
                return String(text)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // click edit handler: fetch the single category then open modal (populate first)
            window.handleEdit = function(id) {
                axios.get(`/categories/${id}/edit`)
                    .then(res => {
                        const c = res.data;
                        // if controller returns wrapper like {data: {...}}, normalize:
                        const data = (c && c.id) ? c : (c.data ?? c);
                        if (!data || !data.id) {
                            console.error('edit: invalid data', c);
                            showToast('Data kategori tidak valid', true);
                            return;
                        }
                        openModalForEdit(data);
                    })
                    .catch(err => {
                        console.error('edit fetch error:', err);
                        showToast('Gagal mengambil data kategori', true);
                    });
            };

            // submit create/update
            document.getElementById('categoryForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                const saveBtn = document.getElementById('saveBtn');
                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.innerText = 'Menyimpan...';
                }
                const id = document.getElementById('categoryId').value || '';
                const payload = {
                    name: document.getElementById('name').value.trim(),
                    description: document.getElementById('description').value.trim(),
                    meta_title: document.getElementById('meta_title').value.trim(),
                    meta_description: document.getElementById('meta_description').value.trim(),
                    keywords: document.getElementById('keywords').value.trim(),
                };

                const url = id ? `/categories/${id}/update` : `/categories/store`;

                // use axios.post with JSON (controller expects Request - will validate)
                axios.post(url, payload)
                    .then(res => {
                        const data = res.data;
                        console.log('save response:', data);
                        if (data && data.success) {
                            fetchCategories();
                            closeModal();
                            showToast(data.message || 'Berhasil disimpan');
                        } else {
                            const msg = (data && data.message) ? data.message : 'Gagal menyimpan';
                            showToast(msg, true);
                        }
                    })
                    .catch(err => {
                        console.error('save error:', err);
                        // try to show validation message if present
                        const msg = err.response?.data?.message || err.response?.data?.errors ||
                            'Gagal menyimpan';
                        showToast(Array.isArray(msg) ? JSON.stringify(msg) : (typeof msg === 'object' ? JSON
                            .stringify(msg) : msg), true);
                    })
                    .finally(() => {
                        if (saveBtn) {
                            saveBtn.disabled = false;
                            saveBtn.innerText = 'Simpan Kategori';
                        }
                    });
            });

            // delete
            window.handleDelete = function(id) {
                if (!confirm('Apakah yakin ingin menghapus kategori ini?')) return;
                axios.delete(`/categories/${id}/delete`)
                    .then(res => {
                        const data = res.data;
                        if (data && data.success) {
                            fetchCategories();
                            showToast(data.message || 'Kategori dihapus');
                        } else {
                            showToast('Gagal menghapus', true);
                        }
                    })
                    .catch(err => {
                        console.error('delete error:', err);
                        showToast('Gagal menghapus', true);
                    });
            };

            // initial load after DOM ready
            document.addEventListener('DOMContentLoaded', function() {
                // Safety: ensure required DOM nodes exist
                if (!document.getElementById('categoryGrid')) {
                    console.error('categoryGrid element not found');
                    return;
                }
                fetchCategories();
            });

            // expose some functions to global scope for inline onclick
            window.fetchCategories = fetchCategories;
            window.openModal = openModal;
            window.closeModal = closeModal;
            window.openModalForEdit = openModalForEdit;
            window.openModalForCreate = window.openModalForCreate;
        })();
    </script>
@endsection
