@extends('template.app')
@section('content')
    <div class="max-w-12xl mx-auto p-4 md:p-8 space-y-6">

        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Edit Video</h1>
            <button onclick="history.back()"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-full hover:bg-gray-100 transition">Kembali</button>
        </div>

        <div class="bg-white shadow-xl rounded-xl p-6 md:p-8">
            <form id="videoForm" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <input type="hidden" id="video_id" value="{{ $video->id }}">

                <div class="space-y-6 lg:col-span-2">
                    <h2 class="text-xl font-bold border-b pb-2 mb-4">Detail Video</h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul (Wajib)</label>
                        <input type="text" id="title" class="yt-input w-full rounded-lg px-4 py-2"
                            value="{{ $video->title }}" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea id="description" class="yt-textarea rounded-lg" rows="5">{{ $video->description }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Report Link</label>
                        <textarea id="report_link" class="yt-textarea rounded-lg" rows="5">{{ $video->report_link }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail</label>
                        <div id="thumbnail-upload-area"
                            class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer relative group">
                            <input type="file" id="thumbnail" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="previewThumbnail(this)">
                            <div class="flex items-center space-x-4">
                                <div id="thumbnail-preview"
                                    class="w-24 h-16 bg-gray-100 border border-gray-300 rounded-md flex items-center justify-center overflow-hidden">
                                    <img src="{{ asset('storage/' . $video->thumbnail) }}"
                                        class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Ganti thumbnail (opsional)</p>
                                    <p class="text-sm text-gray-500">Biarkan jika tidak ingin diganti.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Channel</label>
                            <select id="channel_id" class="yt-select w-full rounded-lg px-4 py-2" required>
                                @foreach (\App\Models\Channel::orderBy('name')->get() as $channel)
                                    <option value="{{ $channel->id }}"
                                        {{ $video->channel_id == $channel->id ? 'selected' : '' }}>
                                        {{ $channel->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select id="category_id" class="yt-select w-full rounded-lg px-4 py-2">
                                @foreach (\App\Models\Category::orderBy('name')->get() as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ $video->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Visibilitas</label>
                        <select id="status" class="yt-select w-full rounded-lg px-4 py-2">
                            <option value="draft" {{ $video->status == 'draft' ? 'selected' : '' }}>Draft (Private)
                            </option>
                            <option value="unlisted" {{ $video->status == 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                            <option value="published" {{ $video->status == 'published' ? 'selected' : '' }}>Publik</option>
                        </select>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-4 pt-6 lg:pt-0">
                    <h2 class="text-xl font-bold border-b pb-2 mb-4">Preview Video</h2>
                    <div id="video-preview-container"
                        class="relative aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-300 shadow-md flex items-center justify-center">
                        <video id="video-preview" class="w-full h-full object-cover rounded-lg" controls>
                            <source src="{{ asset('storage/' . $video->video_path) }}">
                        </video>
                    </div>
                </div>

                <div class="lg:col-span-3 flex justify-end pt-6 border-t mt-6">
                    <button type="button" id="saveBtn"
                        class="px-8 py-3 bg-red-600 text-white font-semibold rounded-full shadow-lg hover:bg-red-700 transition transform hover:scale-105">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2"></div>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let descriptionEditor, reportEditor;

        ClassicEditor.create(document.querySelector('#description'))
            .then(editor => descriptionEditor = editor);

        ClassicEditor.create(document.querySelector('#report_link'))
            .then(editor => reportEditor = editor);

        function previewThumbnail(input) {
            const preview = document.getElementById('thumbnail-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function showToast(message, isError = false) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = (isError ? 'bg-red-700' : 'bg-gray-800') +
                ' text-white px-4 py-3 rounded-lg shadow-xl mb-2';
            toast.innerHTML = message;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        }

        document.getElementById('saveBtn').addEventListener('click', () => {
            let id = document.getElementById('video_id').value;
            let formData = new FormData();
            formData.append('title', document.getElementById('title').value);
            formData.append('description', descriptionEditor.getData());
            formData.append('report_link', reportEditor.getData());
            formData.append('channel_id', document.getElementById('channel_id').value);
            formData.append('category_id', document.getElementById('category_id').value);
            formData.append('status', document.getElementById('status').value);

            let thumbnail = document.getElementById('thumbnail').files[0];
            if (thumbnail) formData.append('thumbnail', thumbnail);

            axios.post(`/videos/update/${id}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(() => {
                    showToast('Video berhasil diperbarui!');
                    setTimeout(() => window.location.href = '/videos', 1000);
                })
                .catch(() => showToast('Gagal update video!', true));
        });
    </script>

    <style>
        .yt-input,
        .yt-select,
        .yt-textarea {
            border: 1px solid #ccc;
        }

        .yt-input:focus,
        .yt-select:focus,
        .yt-textarea:focus,
        .ck-editor__editable:focus {
            border-color: #ff0000 !important;
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.2) !important;
        }

        .ck-editor__editable {
            min-height: 150px !important;
            border: 1px solid #ccc !important;
        }
    </style>
@endsection
