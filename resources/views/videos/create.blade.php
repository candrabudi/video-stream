@extends('template.app')
@section('content')
    <div class="max-w-12xl mx-auto p-4 md:p-8 space-y-6">

        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Upload Video</h1>
            <button onclick="history.back()"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-full hover:bg-gray-100 transition">Kembali</button>
        </div>

        <!-- Upload Stage -->
        <div id="upload-stage" class="bg-white shadow-xl rounded-xl p-10 text-center transition-all duration-300">
            <div id="video_file_input_area"
                class="p-16 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-red-500 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-red-500" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-xl font-semibold mt-4">Tarik & Lepas file video untuk diunggah</p>
                <label for="video_file"
                    class="mt-6 inline-block px-8 py-3 bg-red-600 text-white font-medium rounded-md cursor-pointer hover:bg-red-700 transition">
                    PILIH FILE
                    <input type="file" id="video_file" accept="video/*" class="hidden" required
                        onchange="handleVideoFileSelection(this)">
                </label>
            </div>
            <p class="text-xs text-gray-400 mt-4">Dengan mengunggah, Anda menyetujui Persyaratan Layanan dan Pedoman
                Komunitas Waskita.</p>
        </div>

        <!-- Details Stage -->
        <div id="details-stage" class="bg-white shadow-xl rounded-xl p-6 md:p-8 hidden">
            <form id="videoForm" class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="space-y-6 lg:col-span-2">
                    <h2 class="text-xl font-bold border-b pb-2 mb-4">Detail Video</h2>

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul (Wajib)</label>
                        <input type="text" id="title" class="yt-input w-full rounded-lg px-4 py-2"
                            placeholder="Tambahkan judul" required>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea id="description" class="yt-textarea rounded-lg" rows="5"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail</label>
                        <div id="thumbnail-upload-area"
                            class="border-2 border-gray-200 rounded-lg p-4 hover:border-red-400 cursor-pointer relative group">
                            <input type="file" id="thumbnail" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="previewThumbnail(this)">
                            <div class="flex items-center space-x-4">
                                <div id="thumbnail-preview"
                                    class="w-24 h-16 bg-gray-100 border border-gray-300 rounded-md flex items-center justify-center overflow-hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-9-3h.01M6 18h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Pilih file thumbnail</p>
                                    <p class="text-sm text-gray-500">Gambar untuk mewakili video.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="channel_id" class="block text-sm font-medium text-gray-700 mb-1">Channel</label>
                            <select id="channel_id" class="yt-select w-full rounded-lg px-4 py-2" required>
                                @foreach (\App\Models\Channel::orderBy('name')->get() as $channel)
                                    <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select id="category_id" class="yt-select w-full rounded-lg px-4 py-2">
                                @foreach (\App\Models\Category::orderBy('name')->get() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Visibilitas</label>
                        <select id="status" class="yt-select w-full rounded-lg px-4 py-2">
                            <option value="draft">Draft (Private)</option>
                            <option value="unlisted">Unlisted</option>
                            <option value="published">Publik</option>
                        </select>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-4 pt-6 lg:pt-0">
                    <h2 class="text-xl font-bold border-b pb-2 mb-4">Preview Video</h2>
                    <div id="video-preview-container"
                        class="relative aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-300 shadow-md flex items-center justify-center">
                        <img id="video-preview-img" class="w-full h-full object-cover rounded-lg hidden">
                        <video id="video-preview" class="w-full h-full object-cover rounded-lg hidden" controls></video>
                        <div id="video-placeholder"
                            class="absolute inset-0 flex flex-col items-center justify-center bg-gray-200 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm font-medium mt-2 text-gray-700" id="video-filename-display">Video Dipilih</p>
                            <p class="text-xs text-gray-500 mt-1" id="video-size-display">Ukuran: N/A</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3 flex justify-end pt-6 border-t mt-6">
                    <button type="button" id="saveBtn"
                        class="px-8 py-3 bg-red-600 text-white font-semibold rounded-full shadow-lg hover:bg-red-700 transition transform hover:scale-105">
                        Simpan & Publikasikan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2"></div>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        let editorInstance;
        ClassicEditor.create(document.querySelector('#description'), {
                toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote']
            })
            .then(editor => {
                editorInstance = editor;
            }).catch(console.error);

        function showToast(message, isError = false, duration = 3000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const baseClass =
                'text-white px-4 py-3 rounded-lg shadow-xl opacity-0 transform translate-y-2 transition-all duration-300 font-medium';
            toast.className = isError ? `bg-red-700 ${baseClass}` : `bg-gray-800 ${baseClass}`;
            toast.textContent = message;
            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-2');
            });
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                toast.addEventListener('transitionend', () => toast.remove());
            }, duration);
        }

        function handleVideoFileSelection(input) {
            const uploadStage = document.getElementById('upload-stage');
            const detailsStage = document.getElementById('details-stage');
            const fileNameDisplay = document.getElementById('video-filename-display');
            const fileSizeDisplay = document.getElementById('video-size-display');
            const videoPreview = document.getElementById('video-preview');
            const videoImg = document.getElementById('video-preview-img');
            const placeholder = document.getElementById('video-placeholder');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (fileNameDisplay) fileNameDisplay.textContent = file.name;
                if (fileSizeDisplay) fileSizeDisplay.textContent = `Ukuran: ${(file.size/1024/1024).toFixed(2)} MB`;

                uploadStage.classList.add('hidden');
                detailsStage.classList.remove('hidden');

                const videoURL = URL.createObjectURL(file);
                videoPreview.src = videoURL;

                const canvas = document.createElement('canvas');
                const video = document.createElement('video');
                video.src = videoURL;
                video.addEventListener('loadeddata', () => {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                    videoImg.src = canvas.toDataURL('image/jpeg');
                    videoImg.classList.remove('hidden');
                });

                placeholder.addEventListener('click', function() {
                    placeholder.classList.add('hidden');
                    videoImg.classList.add('hidden');
                    videoPreview.classList.remove('hidden');
                    videoPreview.play();
                });

                showToast(`File ${file.name} berhasil dipilih.`, false, 3000);
            }
        }

        function previewThumbnail(input) {
            const preview = document.getElementById('thumbnail-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(input.files[0]);
                const videoImg = document.getElementById('video-preview-img');
                videoImg.src = URL.createObjectURL(input.files[0]);
                videoImg.classList.remove('hidden');
            }
        }

        document.getElementById('saveBtn').addEventListener('click', function() {
            const title = document.getElementById('title').value;
            const videoFile = document.getElementById('video_file').files[0];
            if (!title || !videoFile) {
                showToast('Judul dan Video wajib diisi!', true);
                return;
            }
            const btn = this;
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';
            const formData = new FormData();
            formData.append('title', title);
            formData.append('description', editorInstance ? editorInstance.getData() : document.getElementById(
                'description').value);
            formData.append('channel_id', document.getElementById('channel_id').value);
            formData.append('category_id', document.getElementById('category_id').value);
            formData.append('status', document.getElementById('status').value);
            if (document.getElementById('thumbnail').files[0]) formData.append('thumbnail', document.getElementById(
                'thumbnail').files[0]);
            formData.append('video_file', videoFile);

            axios.post('/videos/store', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(res => {
                    showToast('Video berhasil disimpan!');
                    window.location.href = '/videos';
                })
                .catch(err => {
                    console.error(err);
                    showToast('Gagal menyimpan video!', true);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = 'Simpan & Publikasikan';
                });
        });
    </script>

    <style>
        .yt-input,
        .yt-select,
        .yt-textarea {
            border: 1px solid #ccc;
            transition: all 0.2s;
        }

        .yt-input:focus,
        .yt-select:focus,
        .yt-textarea:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.2);
        }

        .ck-editor__editable {
            min-height: 150px !important;
            border: 1px solid #ccc !important;
        }

        .ck-editor__editable:focus {
            border-color: #ff0000 !important;
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.2) !important;
        }
    </style>
@endsection
