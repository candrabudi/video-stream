@extends('bo.template.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between my-4 page-header-breadcrumb flex-wrap gap-2">
        <div>
            <p class="fw-medium fs-20 mb-0">Manajemen Video</p>
        </div>
        <div>
            <a href="{{ route('videos.create') }}" class="btn btn-primary">Tambah Video</a>
        </div>
    </div>

    <div class="row">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap" id="videoTable">
                        <thead class="table-light">
                            <tr>
                                <th>Judul</th>
                                <th>Channel</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Diupload</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr id="emptyRow" style="display:none;">
                                <td colspan="7" class="text-center">Belum ada data</td>
                            </tr>
                            <tr id="loadingRow" style="display:none;">
                                <td colspan="7" class="text-center">
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

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Apakah yakin ingin menghapus video ini?
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

        let deleteVideoId = null;
        const toastEl = document.getElementById('statusToast');
        const toast = new bootstrap.Toast(toastEl, {
            delay: 3000
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

        function fetchVideos() {
            toggleLoading(true);
            axios.get('/videos/list')
                .then(res => {
                    toggleLoading(false);
                    const data = res.data;
                    const tableBody = document.getElementById('tableBody');
                    tableBody.querySelectorAll('tr:not(#loadingRow)').forEach(tr => tr.remove());

                    if (!data || data.length === 0) {
                        document.getElementById('emptyRow').style.display = '';
                        return;
                    }

                    data.forEach(v => {
                        const row = `
                            <tr>
                                <td>${v.title}</td>
                                <td>${v.channel?.name || '-'}</td>
                                <td>${v.category?.name || '-'}</td>
                                <td>${v.status}</td>
                                <td>${v.views_count}</td>
                                <td>${v.uploaded_at ? new Date(v.uploaded_at).toLocaleDateString() : '-'}</td>
                                <td>
                                    <a href="/videos/${v.id}/edit" class="btn btn-sm btn-warning">Edit</a>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="${v.id}">Hapus</button>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });

                    document.querySelectorAll('.delete-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            deleteVideoId = this.dataset.id;
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

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!deleteVideoId) return;
            this.disabled = true;
            this.innerText = 'Menghapus...';

            axios.delete(`/videos/${deleteVideoId}/delete`)
                .then(res => {
                    showToast(res.data.message, 'bg-success');
                    fetchVideos();
                    deleteModal.hide();
                })
                .catch(err => {
                    console.error(err);
                    showToast('Gagal menghapus video', 'bg-danger');
                    deleteModal.hide();
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerText = 'Hapus';
                });
        });

        document.addEventListener('DOMContentLoaded', fetchVideos);
    </script>
@endpush
