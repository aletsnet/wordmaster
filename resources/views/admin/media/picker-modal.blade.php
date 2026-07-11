<div id="mediaPickerModal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeMediaPicker()"></div>
    <div class="relative mx-auto mt-10 max-w-4xl bg-white rounded-lg shadow-xl max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-bold">{{ __('admin.select_media') }}</h3>
            <button onclick="closeMediaPicker()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <div class="px-6 py-3 border-b flex items-center gap-4">
            <div class="flex-1">
                <input type="text" id="mediaPickerSearch" placeholder="{{ __('admin.search_media') }}" class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <label class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 cursor-pointer whitespace-nowrap">
                {{ __('admin.upload_new') }}
                <input type="file" id="mediaPickerUpload" accept="image/*" class="hidden">
            </label>
        </div>

        <div id="mediaPickerGrid" class="flex-1 overflow-y-auto p-6 grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div class="col-span-full text-center text-gray-500 py-8">{{ __('admin.loading') }}</div>
        </div>

        <div id="mediaPickerStatus" class="hidden px-6 py-3 border-t text-sm"></div>

        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t">
            <button onclick="closeMediaPicker()" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">{{ __('admin.cancel') }}</button>
            <button id="mediaPickerSelectBtn" disabled class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50">{{ __('admin.select_file') }}</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let mediaPickerCallback = null;
let mediaPickerSelected = null;

function openMediaPicker(callback) {
    mediaPickerCallback = callback;
    mediaPickerSelected = null;
    document.getElementById('mediaPickerSelectBtn').disabled = true;
    document.getElementById('mediaPickerModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    loadMediaItems();
}

function closeMediaPicker() {
    document.getElementById('mediaPickerModal').classList.add('hidden');
    document.body.style.overflow = '';
    mediaPickerCallback = null;
    mediaPickerSelected = null;
}

function loadMediaItems(query = '') {
    const grid = document.getElementById('mediaPickerGrid');
    grid.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8">{{ __('admin.loading') }}</div>';

    let url = '{{ route('admin.media.json') }}';
    if (query) url += '?search=' + encodeURIComponent(query);

    fetch(url)
        .then(r => r.json())
        .then(items => {
            if (!items.length) {
                grid.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8">{{ __('admin.no_media') }}</div>';
                return;
            }
            grid.innerHTML = items.map(item => `
                <div class="media-item cursor-pointer border rounded-lg overflow-hidden hover:ring-2 hover:ring-blue-500 transition ${mediaPickerSelected?.id === item.id ? 'ring-2 ring-blue-500' : ''}"
                     data-id="${item.id}"
                     data-url="${item.url}"
                     data-alt="${item.alt_text || item.original_name}"
                     onclick="selectMediaItem(this, ${item.id}, '${item.url}', '${(item.alt_text || item.original_name).replace(/'/g, "\\'")}')">
                    <img src="${item.url}" alt="${item.alt_text || item.original_name}" class="w-full h-24 object-cover" loading="lazy">
                    <div class="p-1.5">
                        <p class="text-xs truncate">${item.original_name}</p>
                        <p class="text-xs text-gray-500">${(item.size / 1024).toFixed(1)} KB</p>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => {
            grid.innerHTML = '<div class="col-span-full text-center text-red-500 py-8">{{ __('admin.error_loading_media') }}</div>';
        });
}

function selectMediaItem(el, id, url, alt) {
    document.querySelectorAll('.media-item').forEach(i => i.classList.remove('ring-2', 'ring-blue-500'));
    el.classList.add('ring-2', 'ring-blue-500');
    mediaPickerSelected = { id, url, alt };
    document.getElementById('mediaPickerSelectBtn').disabled = false;
}

document.getElementById('mediaPickerSelectBtn').addEventListener('click', function () {
    if (mediaPickerSelected && mediaPickerCallback) {
        mediaPickerCallback(mediaPickerSelected);
    }
    closeMediaPicker();
});

document.getElementById('mediaPickerSearch').addEventListener('input', function () {
    loadMediaItems(this.value);
});

document.getElementById('mediaPickerUpload').addEventListener('change', function () {
    if (!this.files.length) return;
    const formData = new FormData();
    formData.append('file', this.files[0]);
    formData.append('_token', '{{ csrf_token() }}');

    const status = document.getElementById('mediaPickerStatus');
    status.className = 'px-6 py-3 border-t text-sm text-blue-600';
    status.textContent = '{{ __('admin.uploading') }}';
    status.classList.remove('hidden');

    fetch('{{ route('admin.media.upload-ajax') }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(item => {
        status.className = 'px-6 py-3 border-t text-sm text-green-600';
        status.textContent = '{{ __('admin.upload_success') }}';
        loadMediaItems();
        this.value = '';
        setTimeout(() => status.classList.add('hidden'), 2000);
    })
    .catch(() => {
        status.className = 'px-6 py-3 border-t text-sm text-red-600';
        status.textContent = '{{ __('admin.upload_error') }}';
        this.value = '';
    });
});
</script>
@endpush
