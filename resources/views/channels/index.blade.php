@extends('template.app')

@section('content')
    <div class="max-w-12xl mx-auto p-4 md:p-8">
        <div class="flex flex-wrap justify-between items-center my-6 pb-4 border-b">
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Channel</h1>
            <button id="addChannelBtn"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition duration-150 transform hover:scale-105"
                onclick="openModal(false)">
                Tambah Channel
            </button>
        </div>

        <div id="channelGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-8">
            <div id="loadingRow" class="col-span-full text-center p-10">
                <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-red-600 mx-auto"></div>
                <p class="mt-4 text-gray-500 font-medium">Memuat data channel...</p>
            </div>
        </div>

    </div>

    <div id="channelModal" class="fixed inset-0 z-50 hidden modal-overlay items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0"
            id="modalContent">
            <form id="channelForm">
                <div class="p-6">
                    <div class="flex justify-between items-center border-b pb-3 mb-4">
                        <h5 class="text-xl font-bold" id="channelModalTitle">Tambah Channel Baru</h5>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <input type="hidden" id="channelId">
                    <div class="space-y-4">
                        <div class="text-center">
                            <img id="avatarPreview"
                                class="mx-auto rounded-full mb-3 w-24 h-24 object-cover border-4 border-gray-200 transition-all duration-300 hover:border-red-500 cursor-pointer hidden"
                                onclick="document.getElementById('avatar').click()">
                            <p id="avatarPlaceholder" class="text-gray-500 text-sm">Klik untuk memilih Avatar</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Nama Channel</label>
                            <input type="text" class="yt-input w-full border border-gray-300 rounded-lg px-3 py-2"
                                id="name" required>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Avatar (Opsional)</label>
                            <input type="file" class="yt-input w-full border border-gray-300 rounded-lg px-3 py-2"
                                id="avatar" accept="image/*">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                            <textarea class="yt-textarea w-full border border-gray-300 rounded-lg px-3 py-2 min-h-[100px]" id="description"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Subscribers</label>
                            <input type="number" class="yt-input w-full border border-gray-300 rounded-lg px-3 py-2"
                                id="subscribers" min="0" value="0">
                        </div>
                    </div>
                </div>

                <div class="modal-footer flex justify-end gap-3 p-4 bg-gray-50 rounded-b-xl border-t">
                    <button type="button"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition"
                        onclick="closeModal()">Batal</button>
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition"
                        id="saveBtn">Simpan Channel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2"></div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content');

        function showToast(message, isError = false) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const base =
                'text-white px-4 py-3 rounded-lg shadow-xl opacity-0 transform translate-y-2 transition-all duration-300 font-medium';
            toast.className = isError ? `bg-red-700 ${base}` : `bg-gray-800 ${base}`;
            toast.textContent = message;
            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-2');
            });
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                toast.addEventListener('transitionend', () => toast.remove());
            }, 3000);
        }

        function openModal(isEdit = false) {
            if (!isEdit) document.getElementById('channelForm').reset();
            document.getElementById('avatarPreview').classList.add('hidden');
            document.getElementById('avatarPlaceholder').classList.remove('hidden');
            document.getElementById('channelModalTitle').innerText = isEdit ? 'Edit Channel' : 'Tambah Channel Baru';
            document.getElementById('channelModal').classList.remove('hidden');
            document.getElementById('channelModal').classList.add('flex');
            const content = document.getElementById('modalContent');
            requestAnimationFrame(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100')
            });
        }

        function closeModal() {
            const content = document.getElementById('modalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                document.getElementById('channelModal').classList.add('hidden');
                document.getElementById('channelModal').classList.remove('flex')
            }, 300);
        }

        function fetchChannels() {
            const loadingRow = document.getElementById('loadingRow');
            if (loadingRow) loadingRow.style.display = 'block';

            axios.get('{{ route('channels.list') }}')
                .then(res => {
                    if (loadingRow) loadingRow.style.display = 'none';
                    renderChannels(res.data);
                })
                .catch(() => {
                    if (loadingRow) loadingRow.style.display = 'none';
                    showToast('Gagal memuat data', true);
                });
        }


        function renderChannels(channels) {
            const grid = document.getElementById('channelGrid');
            grid.innerHTML = '';
            if (!channels.length) {
                grid.innerHTML = '<p class="text-center col-span-full text-gray-500 p-10">Belum ada channel.</p>';
                return;
            }
            channels.forEach(c => {
                const avatar = c.avatar ? `/storage/${c.avatar}` : 'https://via.placeholder.com/80';
                const card = `
<div class="bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition duration-200 p-6 flex flex-col items-center text-center">
    <img src="${avatar}" class="rounded-full w-20 h-20 object-cover mb-4 border-2 border-red-500/50">
    <div class="mb-3"><h5 class="font-bold text-xl text-gray-900 truncate max-w-full">${c.name}</h5>
    <span class="inline-flex items-center text-xs font-semibold px-3 py-0.5 rounded-full bg-red-600 text-white mt-1">${c.subscribers.toLocaleString()} Subscribers</span></div>
    <p class="text-gray-500 text-sm mb-4 line-clamp-2 min-h-[40px]">${c.description||'-'}</p>
    <div class="flex gap-3 mt-auto">
        <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm px-4 py-1.5 rounded-full font-medium transition" onclick="editChannel(${c.id})">Edit</button>
        <button class="bg-red-500/10 hover:bg-red-500/20 text-red-600 text-sm px-4 py-1.5 rounded-full font-medium transition" onclick="deleteChannel(${c.id})">Hapus</button>
    </div>
</div>`;
                grid.insertAdjacentHTML('beforeend', card);
            });
        }

        function editChannel(id) {
            axios.get(`/channels/${id}/edit`).then(res => {
                const c = res.data;
                document.getElementById('channelId').value = c.id;
                document.getElementById('name').value = c.name;
                document.getElementById('description').value = c.description || '';
                document.getElementById('subscribers').value = c.subscribers || 0;
                if (c.avatar) {
                    document.getElementById('avatarPreview').src = `/storage/${c.avatar}`;
                    document.getElementById('avatarPreview').classList.remove('hidden');
                    document.getElementById('avatarPlaceholder').classList.add('hidden');
                } else {
                    document.getElementById('avatarPreview').classList.add('hidden');
                    document.getElementById('avatarPlaceholder').classList.remove('hidden');
                }
                openModal(true);
            }).catch(() => showToast('Gagal mengambil data channel', true));
        }

        document.getElementById('channelForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = true;
            saveBtn.innerText = 'Menyimpan...';

            const id = document.getElementById('channelId').value;
            const formData = new FormData();
            formData.append('name', document.getElementById('name').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('subscribers', document.getElementById('subscribers').value);
            if (document.getElementById('avatar').files[0])
                formData.append('avatar', document.getElementById('avatar').files[0]);

            const url = id ? `/channels/${id}/update` : '/channels/store';

            axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(res => {
                    if (res.data.success === true) {
                        fetchChannels();
                        closeModal();
                        showToast(res.data.message);
                    } else {
                        showToast('Gagal menyimpan', true);
                    }
                })
                .catch(err => {
                    let msg = err.response?.data?.message || 'Gagal menyimpan';
                    showToast(msg, true);
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    saveBtn.innerText = 'Simpan Channel';
                });
        });

        function deleteChannel(id) {
            if (!confirm('Apakah yakin ingin menghapus channel ini?')) return;
            axios.delete(`/channels/${id}/delete`)
                .then(res => {
                    fetchChannels();
                    showToast(res.data.message || 'Channel dihapus');
                })
                .catch(() => showToast('Gagal menghapus', true));
        }

        document.getElementById('avatar').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const preview = document.getElementById('avatarPreview');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            document.getElementById('avatarPlaceholder').classList.add('hidden');
        });

        document.addEventListener('DOMContentLoaded', fetchChannels);
    </script>
@endsection
