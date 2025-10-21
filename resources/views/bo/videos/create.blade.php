@extends('bo.template.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between my-4 page-header-breadcrumb flex-wrap gap-2">
        <div>
            <p class="fw-medium fs-20 mb-0">Tambah Video</p>
        </div>
        <div><a href="{{ route('videos.index') }}" class="btn btn-secondary">Kembali</a></div>
    </div>

    <div class="row">
        <div class="card shadow-sm">
            <div class="card-body">
                <form id="videoForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Channel</label>
                        <select class="form-select" id="channel_id" required>
                            <option value="">Pilih Channel</option>
                            @foreach (\App\Models\Channel::orderBy('name')->get() as $channel)
                                <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" id="category_id">
                            <option value="">Pilih Kategori</option>
                            @foreach (\App\Models\Category::orderBy('name')->get() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thumbnail</label>
                        <input type="file" class="form-control" id="thumbnail" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File Video</label>
                        <input type="file" class="form-control" id="video_file" accept="video/*" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-primary" id="saveBtn">Simpan</button>
                </form>
            </div>
        </div>
    </div>

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

        const toastEl = document.getElementById('statusToast');
        const toast = new bootstrap.Toast(toastEl, {
            delay: 3000
        });

        function showToast(msg, bg = 'bg-primary') {
            document.getElementById('toastMessage').innerText = msg;
            toastEl.className = `toast align-items-center text-white ${bg} border-0`;
            toast.show();
        }

        document.getElementById('saveBtn').addEventListener('click', function() {
            const formData = new FormData();
            formData.append('title', document.getElementById('title').value);
            formData.append('channel_id', document.getElementById('channel_id').value);
            formData.append('category_id', document.getElementById('category_id').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('status', document.getElementById('status').value);
            if (document.getElementById('thumbnail').files[0]) {
                formData.append('thumbnail', document.getElementById('thumbnail').files[0]);
            }
            if (document.getElementById('video_file').files[0]) {
                formData.append('video_file', document.getElementById('video_file').files[0]);
            }

            this.disabled = true;
            this.innerText = 'Menyimpan...';

            axios.post('/videos/store', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(res => {
                    showToast('Video berhasil dibuat', 'bg-success');
                    window.location.href = '/videos';
                })
                .catch(err => {
                    console.error(err);
                    showToast('Gagal menyimpan video', 'bg-danger');
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerText = 'Simpan';
                });
        });
    </script>
@endpush
