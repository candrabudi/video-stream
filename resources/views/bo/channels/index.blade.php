@extends('bo.template.app')

@section('content')
<div class="d-flex align-items-center justify-content-between my-4 page-header-breadcrumb flex-wrap gap-2">
    <div>
        <p class="fw-medium fs-20 mb-0">Manajemen Channel</p>
    </div>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#channelModal" onclick="openCreateModal()">Tambah Channel</button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover text-nowrap" id="channelTable">
                <thead class="table-light">
                    <tr>
                        <th>Avatar</th>
                        <th>Nama</th>
                        <th>Subscribers</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr id="emptyRow" style="display:none;">
                        <td colspan="5" class="text-center">Belum ada data</td>
                    </tr>
                    <tr id="loadingRow" style="display:none;">
                        <td colspan="5" class="text-center">
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

<!-- Modal Create/Edit -->
<div class="modal fade" id="channelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="channelForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="channelModalTitle">Tambah Channel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="channelId">
                    <div class="mb-3">
                        <label class="form-label">Nama Channel</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Avatar</label>
                        <input type="file" class="form-control" id="avatar">
                        <img id="avatarPreview" class="mt-2" style="max-width: 100px; display:none;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subscribers</label>
                        <input type="number" class="form-control" id="subscribers" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let channelModal = new bootstrap.Modal(document.getElementById('channelModal'));
let saveBtn = document.getElementById('saveBtn');
let currentChannelId = null;

// Fetch & render channels
function fetchChannels() {
    toggleLoading(true);
    axios.get('/channels/list')
        .then(res => {
            toggleLoading(false);
            const channels = res.data;
            const tbody = document.getElementById('tableBody');
            tbody.querySelectorAll('tr:not(#loadingRow)').forEach(tr => tr.remove());
            if (!channels.length) {
                document.getElementById('emptyRow').style.display = '';
                return;
            }

            channels.forEach(c => {
                const avatarHtml = c.avatar ? `<img src="/storage/${c.avatar}" alt="Avatar" class="rounded-circle" width="40">` : '-';
                const row = `
<tr>
    <td>${avatarHtml}</td>
    <td>${c.name}</td>
    <td>${c.subscribers.toLocaleString()}</td>
    <td>${c.description || '-'}</td>
    <td>
        <button class="btn btn-sm btn-warning" onclick="openEditModal(${c.id})">Edit</button>
        <button class="btn btn-sm btn-danger" onclick="deleteChannel(${c.id})">Hapus</button>
    </td>
</tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        })
        .catch(err => {
            console.error(err);
            toggleLoading(false);
            document.getElementById('emptyRow').style.display = '';
        });
}

function toggleLoading(show = true) {
    document.getElementById('loadingRow').style.display = show ? '' : 'none';
    document.getElementById('emptyRow').style.display = 'none';
}

// Modal actions
function openCreateModal() {
    currentChannelId = null;
    document.getElementById('channelForm').reset();
    document.getElementById('avatarPreview').style.display = 'none';
    document.getElementById('channelModalTitle').innerText = 'Tambah Channel';
}

function openEditModal(id) {
    axios.get(`/channels/${id}/edit`)
        .then(res => {
            currentChannelId = id;
            const c = res.data;
            document.getElementById('name').value = c.name;
            document.getElementById('description').value = c.description || '';
            document.getElementById('subscribers').value = c.subscribers || 0;
            if (c.avatar) {
                document.getElementById('avatarPreview').src = `/storage/${c.avatar}`;
                document.getElementById('avatarPreview').style.display = '';
            }
            document.getElementById('channelModalTitle').innerText = 'Edit Channel';
            channelModal.show();
        })
        .catch(err => console.error(err));
}

// Save channel (create/update)
document.getElementById('channelForm').addEventListener('submit', function(e) {
    e.preventDefault();
    saveBtn.disabled = true;
    saveBtn.innerText = 'Menyimpan...';

    const formData = new FormData();
    formData.append('name', document.getElementById('name').value);
    formData.append('description', document.getElementById('description').value);
    formData.append('subscribers', document.getElementById('subscribers').value);
    const avatarFile = document.getElementById('avatar').files[0];
    if (avatarFile) formData.append('avatar', avatarFile);

    const url = currentChannelId ? `/channels/${currentChannelId}/update` : '/channels/store';

    axios.post(url, formData)
        .then(res => {
            fetchChannels();
            channelModal.hide();
        })
        .catch(err => console.error(err))
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Simpan';
        });
});

// Delete channel
function deleteChannel(id) {
    if (!confirm('Apakah yakin ingin menghapus channel ini?')) return;
    axios.delete(`/channels/${id}/delete`)
        .then(res => {
            fetchChannels();
        })
        .catch(err => console.error(err));
}

// Avatar preview
document.getElementById('avatar').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const preview = document.getElementById('avatarPreview');
    preview.src = URL.createObjectURL(file);
    preview.style.display = '';
});

// Initialize
document.addEventListener('DOMContentLoaded', fetchChannels);
</script>
@endpush
