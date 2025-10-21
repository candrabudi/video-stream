@extends('bo.template.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between my-4 page-header-breadcrumb flex-wrap gap-2">
        <div>
            <p class="fw-medium fs-20 mb-0">Manajemen Kategori</p>
        </div>
        <div>
            <button class="btn btn-primary" id="createBtn">Tambah Kategori</button>
        </div>
    </div>

    {{-- Table --}}
    <div class="row">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap" id="categoryTable">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Meta Title</th>
                                <th>Meta Description</th>
                                <th>Keywords</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr id="emptyRow" style="display:none;">
                                <td colspan="6" class="text-center">Belum ada data</td>
                            </tr>
                            <tr id="loadingRow" style="display:none;">
                                <td colspan="6" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Memuat...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create/Edit --}}
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <input type="hidden" id="categoryId">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea class="form-control" id="meta_description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keywords</label>
                            <input type="text" class="form-control" id="keywords">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Apakah yakin ingin menghapus kategori ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div class="position-fixed top-0 end-0 p-3" style="z-index:1080">
        <div id="statusToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Tutup"></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content');

        let deleteCategoryId = null;
        const toastEl = document.getElementById('statusToast');
        const toast = new bootstrap.Toast(toastEl, {
            delay: 3000
        });
        const categoryModalEl = document.getElementById('categoryModal');
        const categoryModal = new bootstrap.Modal(categoryModalEl, {
            backdrop: 'static',
            keyboard: false
        });
        const deleteModalEl = document.getElementById('deleteModal');
        const deleteModal = new bootstrap.Modal(deleteModalEl, {
            backdrop: 'static',
            keyboard: false
        });

        function showToast(msg, bg = 'bg-primary') {
            document.getElementById('toastMessage').innerText = msg;
            toastEl.className = `toast align-items-center text-white ${bg} border-0`;
            toast.show();
        }

        function toggleLoading(show = true) {
            document.getElementById('loadingRow').style.display = show ? '' : 'none';
            document.getElementById('emptyRow').style.display = 'none';
        }

        function fetchCategories() {
            toggleLoading(true);
            axios.get('/categories/list')
                .then(res => {
                    toggleLoading(false);
                    const data = res.data;
                    const tableBody = document.getElementById('tableBody');
                    tableBody.querySelectorAll('tr:not(#loadingRow)').forEach(tr => tr.remove());

                    if (!data || data.length === 0) {
                        document.getElementById('emptyRow').style.display = '';
                        return;
                    }

                    data.forEach(c => {
                        const row = `
<tr>
    <td>${c.name}</td>
    <td>${c.description || '-'}</td>
    <td>${c.meta_title || '-'}</td>
    <td>${c.meta_description || '-'}</td>
    <td>${c.keywords || '-'}</td>
    <td>
        <button class="btn btn-sm btn-warning edit-btn" data-id="${c.id}">Edit</button>
        <button class="btn btn-sm btn-danger delete-btn" data-id="${c.id}">Hapus</button>
    </td>
</tr>`;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });

                    document.querySelectorAll('.edit-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const id = this.dataset.id;
                            axios.get(`/categories/${id}/edit`)
                                .then(res => {
                                    const c = res.data;
                                    document.getElementById('categoryId').value = c.id;
                                    document.getElementById('name').value = c.name;
                                    document.getElementById('description').value = c.description ||
                                        '';
                                    document.getElementById('meta_title').value = c.meta_title ||
                                    '';
                                    document.getElementById('meta_description').value = c
                                        .meta_description || '';
                                    document.getElementById('keywords').value = c.keywords || '';
                                    document.getElementById('modalTitle').innerText =
                                        'Edit Kategori';
                                    categoryModal.show();
                                });
                        });
                    });

                    document.querySelectorAll('.delete-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            deleteCategoryId = this.dataset.id;
                            deleteModal.show();
                        });
                    });
                })
                .catch(err => {
                    console.error(err);
                    toggleLoading(false);
                    document.getElementById('emptyRow').style.display = '';
                });
        }

        document.getElementById('createBtn').addEventListener('click', function() {
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryId').value = '';
            document.getElementById('modalTitle').innerText = 'Tambah Kategori';
            categoryModal.show();
        });

        document.getElementById('saveBtn').addEventListener('click', function() {
            const id = document.getElementById('categoryId').value;
            const payload = {
                name: document.getElementById('name').value,
                description: document.getElementById('description').value,
                meta_title: document.getElementById('meta_title').value,
                meta_description: document.getElementById('meta_description').value,
                keywords: document.getElementById('keywords').value
            };
            this.disabled = true;
            this.innerText = 'Menyimpan...';

            const request = id ? axios.post(`/categories/${id}/update`, payload) : axios.post('/categories/store',
                payload);

            request.then(res => {
                    showToast(res.data.message, 'bg-success');
                    fetchCategories();
                    categoryModal.hide();
                })
                .catch(err => {
                    console.error(err);
                    showToast('Gagal menyimpan kategori', 'bg-danger');
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerText = 'Simpan';
                });
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!deleteCategoryId) return;
            this.disabled = true;
            this.innerText = 'Menghapus...';

            axios.delete(`/categories/${deleteCategoryId}/delete`)
                .then(res => {
                    showToast(res.data.message, 'bg-success');
                    fetchCategories();
                    deleteModal.hide();
                })
                .catch(err => {
                    console.error(err);
                    showToast('Gagal menghapus kategori', 'bg-danger');
                    deleteModal.hide();
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerText = 'Hapus';
                });
        });

        document.addEventListener('DOMContentLoaded', fetchCategories);
    </script>
@endpush
