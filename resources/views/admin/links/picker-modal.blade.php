<div id="linkPickerModal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeLinkPicker()"></div>
    <div class="relative mx-auto mt-10 max-w-2xl bg-white rounded-lg shadow-xl max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-bold">{{ __('admin.insert_link') }}</h3>
            <button onclick="closeLinkPicker()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <div class="p-6 space-y-4 flex-1 overflow-y-auto">
            <div>
                <label class="block text-gray-700 mb-2">{{ __('admin.link_text') }}</label>
                <input type="text" id="linkTextInput" class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-gray-700 mb-2">{{ __('admin.link_type') }}</label>
                <select id="linkTypeSelect" onchange="onLinkTypeChange()" class="w-full border rounded px-3 py-2">
                    <option value="page">{{ __('admin.link_page') }}</option>
                    <option value="post">{{ __('admin.link_post') }}</option>
                    <option value="custom">{{ __('admin.link_custom') }}</option>
                </select>
            </div>

            <div id="linkPageSection">
                <label class="block text-gray-700 mb-2">{{ __('admin.select_page') }}</label>
                <input type="text" id="linkPageSearch" placeholder="{{ __('admin.search_pages_placeholder') }}" oninput="filterLinkPages()" class="w-full border rounded px-3 py-2 mb-2">
                <select id="linkPageSelect" size="8" class="w-full border rounded px-3 py-2" onchange="onLinkPageSelect()">
                </select>
            </div>

            <div id="linkPostSection" class="hidden">
                <label class="block text-gray-700 mb-2">{{ __('admin.select_post') }}</label>
                <input type="text" id="linkPostSearch" placeholder="{{ __('admin.search_posts_placeholder') }}" oninput="filterLinkPosts()" class="w-full border rounded px-3 py-2 mb-2">
                <select id="linkPostSelect" size="8" class="w-full border rounded px-3 py-2" onchange="onLinkPostSelect()">
                </select>
            </div>

            <div id="linkCustomSection" class="hidden">
                <label class="block text-gray-700 mb-2">{{ __('admin.url') }}</label>
                <input type="url" id="linkCustomUrl" placeholder="https://..." class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t">
            <button onclick="closeLinkPicker()" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">{{ __('admin.cancel') }}</button>
            <button onclick="insertLink()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ __('admin.insert') }}</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let linkPickerPages = [];
let linkPickerPosts = [];
let linkPickerCallback = null;

function openLinkPicker(callback) {
    linkPickerCallback = callback;
    const textarea = document.querySelector('textarea[name="content"]');
    const selectedText = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
    document.getElementById('linkTextInput').value = selectedText;
    document.getElementById('linkPickerModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    onLinkTypeChange();
    loadLinkPages();
    loadLinkPosts();
}

function closeLinkPicker() {
    document.getElementById('linkPickerModal').classList.add('hidden');
    document.body.style.overflow = '';
    linkPickerCallback = null;
}

function onLinkTypeChange() {
    const type = document.getElementById('linkTypeSelect').value;
    document.getElementById('linkPageSection').classList.toggle('hidden', type !== 'page');
    document.getElementById('linkPostSection').classList.toggle('hidden', type !== 'post');
    document.getElementById('linkCustomSection').classList.toggle('hidden', type !== 'custom');
}

function loadLinkPages() {
    fetch('{{ route('admin.link-picker.data') }}')
        .then(r => r.json())
        .then(data => {
            linkPickerPages = data.pages || [];
            renderLinkPages();
        });
}

function loadLinkPosts() {
    fetch('{{ route('admin.link-picker.data') }}')
        .then(r => r.json())
        .then(data => {
            linkPickerPosts = data.posts || [];
            renderLinkPosts();
        });
}

function renderLinkPages(filter = '') {
    const select = document.getElementById('linkPageSelect');
    const filtered = linkPickerPages.filter(p => !filter || p.title.toLowerCase().includes(filter.toLowerCase()));
    select.innerHTML = filtered.map(p =>
        `<option value="/${p.slug}" data-title="${p.title.replace(/"/g, '&quot;')}">${p.title}</option>`
    ).join('');
}

function renderLinkPosts(filter = '') {
    const select = document.getElementById('linkPostSelect');
    const filtered = linkPickerPosts.filter(p => !filter || p.title.toLowerCase().includes(filter.toLowerCase()));
    select.innerHTML = filtered.map(p =>
        `<option value="/${p.slug}" data-title="${p.title.replace(/"/g, '&quot;')}">${p.title}</option>`
    ).join('');
}

function filterLinkPages() {
    renderLinkPages(document.getElementById('linkPageSearch').value);
}

function filterLinkPosts() {
    renderLinkPosts(document.getElementById('linkPostSearch').value);
}

function onLinkPageSelect() {
    const select = document.getElementById('linkPageSelect');
    const option = select.options[select.selectedIndex];
    if (option && option.value) {
        const text = document.getElementById('linkTextInput');
        if (!text.value) text.value = option.getAttribute('data-title') || '';
    }
}

function onLinkPostSelect() {
    const select = document.getElementById('linkPostSelect');
    const option = select.options[select.selectedIndex];
    if (option && option.value) {
        const text = document.getElementById('linkTextInput');
        if (!text.value) text.value = option.getAttribute('data-title') || '';
    }
}

function insertLink() {
    const type = document.getElementById('linkTypeSelect').value;
    let url = '';
    if (type === 'page') {
        const select = document.getElementById('linkPageSelect');
        url = select.options[select.selectedIndex]?.value || '';
    } else if (type === 'post') {
        const select = document.getElementById('linkPostSelect');
        url = select.options[select.selectedIndex]?.value || '';
    } else {
        url = document.getElementById('linkCustomUrl').value.trim();
    }
    const text = document.getElementById('linkTextInput').value.trim() || url;
    if (!url) return;
    if (linkPickerCallback) linkPickerCallback(url, text);
    closeLinkPicker();
}
</script>
@endpush