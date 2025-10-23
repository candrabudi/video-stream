@extends('template.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <div class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white shadow rounded-xl p-5">
            <div class="flex justify-between mb-4 pb-3 border-b">
                <h2 class="text-xl font-bold flex items-center gap-2 text-red-600">
                    <i class="ri-youtube-line"></i> Data Channel
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
                        <th class="px-3 py-2 text-left">Nama Channel</th>
                        <th class="px-3 py-2 text-left">Deskripsi</th>
                        <th class="px-3 py-2 text-left">Dibuat Oleh</th>
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr id="loadingRow">
                        <td colspan="4" class="text-center py-6 text-gray-500">
                            <i class="ri-loader-4-line animate-spin text-xl"></i> Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white shadow rounded-xl p-5">
            <h2 class="text-xl font-bold mb-3 text-red-600 flex items-center gap-2">
                <i class="ri-edit-line"></i> Kelola Channel
            </h2>

            <form id="channelForm" class="space-y-3">
                <input type="hidden" id="channelId">

                <div>
                    <label class="text-sm font-semibold">Nama Channel</label>
                    <input type="text" id="channel_name" class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="text-sm font-semibold">Deskripsi</label>
                    <textarea id="description" class="w-full border rounded-lg px-3 py-2"></textarea>
                </div>

                <div>
                    <label class="text-sm font-semibold">Avatar (Opsional)</label>
                    <input type="file" id="avatar" class="w-full border rounded-lg px-3 py-2" accept="image/*">
                    <img id="avatarPreview" class="mt-2 w-24 h-24 rounded-full object-cover hidden">
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
        axios.defaults.headers.common['X-CSRF-TOKEN'] =
            document.querySelector('meta[name="csrf-token"]').content;

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

        function fetchChannels() {
            axios.get("{{ route('channels.list') }}").then(res => {
                const channels = res.data.data ?? [];
                const tb = document.getElementById('tableBody');
                tb.innerHTML = '';
                if (channels.length === 0) {
                    tb.innerHTML =
                        '<tr><td colspan="4" class="text-center py-6 text-gray-500">Belum ada channel.</td></tr>';
                    return;
                }
                channels.forEach(c => {
                    tb.insertAdjacentHTML('beforeend', `
                <tr class="border-b hover:bg-red-50/20">
                    <td class="px-3 py-2 font-medium">${escape(c.name)}</td>
                    <td class="px-3 py-2 text-gray-600">${escape(c.description ?? '-')}</td>
                    <td class="px-3 py-2 text-gray-500 italic">${c.creator?.username ?? '-'}</td>
                    <td class="px-3 py-2 text-center flex gap-2 justify-center">
                        ${isSuperAdmin ? `
                                    <button onclick="editChannel(${c.id})" title="Edit" class="text-blue-600 hover:scale-110 transition">
                                        <i class="ri-edit-line text-lg"></i>
                                    </button>
                                    <button onclick="deleteChannel(${c.id})" title="Hapus" class="text-red-600 hover:scale-110 transition">
                                        <i class="ri-delete-bin-6-line text-lg"></i>
                                    </button>` :
                            `<i class="ri-lock-line text-gray-400 text-lg"></i>`
                        }
                    </td>
                </tr>
            `);
                });
            });
        }

        function resetForm() {
            channelId.value = "";
            channel_name.value = "";
            description.value = "";
            avatarPreview.classList.add('hidden');
        }

        function editChannel(id) {
            if (!isSuperAdmin) return;
            axios.get(`/channels/${id}/edit`).then(res => {
                const c = res.data.data ?? res.data;
                channelId.value = c.id;
                channel_name.value = c.name ?? "";
                description.value = c.description ?? "";
                if (c.avatar) {
                    avatarPreview.src = `/storage/${c.avatar}`;
                    avatarPreview.classList.remove('hidden');
                } else avatarPreview.classList.add('hidden');
            }).catch(() => toast('Gagal mengambil data channel', true));
        }

        document.getElementById('channelForm').addEventListener('submit', e => {
            e.preventDefault();
            if (!isSuperAdmin) return;

            const id = channelId.value;
            const formData = new FormData();
            formData.append('name', channel_name.value);
            formData.append('description', description.value);
            if (avatar.files[0]) formData.append('avatar', avatar.files[0]);

            const url = id ? `/channels/${id}/update` : '/channels/store';

            axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(res => {
                    fetchChannels();
                    resetForm();
                    toast(res.data.message || "Berhasil disimpan");
                }).catch(err => {
                    toast(err.response?.data?.message || "Gagal menyimpan", true);
                });
        });

        function deleteChannel(id) {
            if (!confirm('Apakah yakin ingin menghapus channel ini?')) return;
            axios.delete(`/channels/${id}/delete`)
                .then(res => {
                    fetchChannels();
                    toast(res.data.message || 'Channel dihapus');
                }).catch(() => toast('Gagal menghapus', true));
        }

        avatar.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            avatarPreview.src = URL.createObjectURL(file);
            avatarPreview.classList.remove('hidden');
        });

        document.addEventListener('DOMContentLoaded', fetchChannels);
    </script>
@endsection
