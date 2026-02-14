@extends('layouts.admin')

@section('header', 'Master Data: Hero Slides')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tambah Hero Slide Baru</h3>
            <form action="{{ route('admin.master.hero-slides.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Slide</label>
                        <select name="type" id="slideType" onchange="toggleSlideFields()"
                            class="w-full px-4 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none"
                            required>
                            <option value="text">Text (Tagline & Subtagline)</option>
                            <option value="image">Image</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                        <input type="number" name="order" value="{{ $slides->count() }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                </div>

                <div id="textFields">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tagline/Title</label>
                        <input type="text" name="title" placeholder="Produk Susu Terbaik untuk Anda"
                            class="w-full px-4 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtagline/Subtitle</label>
                        <textarea name="subtitle" rows="3"
                            placeholder="Nikmati kesegaran dan kualitas terbaik dari berbagai pilihan produk susu premium"
                            class="w-full px-4 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none"></textarea>
                    </div>
                </div>

                <div id="imageField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Gambar</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">Maximum file size: 2MB. Format: JPG, PNG, GIF</p>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Tambah Slide
                </button>
            </form>
        </div>

        <hr class="my-6">

        <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Hero Slides</h3>
        <div class="space-y-3">
            @forelse($slides as $slide)
                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white hover:shadow-md transition-shadow">
                    <!-- View Mode -->
                    <div class="view-mode-{{ $slide->id }} p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">{{ $slide->order }}</span>
                                    @if ($slide->type === 'text')
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Text</span>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $slide->title ?: '(No Title)' }}</p>
                                            <p class="text-sm text-gray-600">{{ Str::limit($slide->subtitle, 100) }}</p>
                                        </div>
                                    @else
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Image</span>
                                        @if ($slide->image_path)
                                            <img src="{{ asset('storage/' . $slide->image_path) }}" alt="{{ $slide->title }}"
                                                class="h-16 w-24 object-cover rounded">
                                        @endif
                                        <p class="text-sm text-gray-600">{{ $slide->title }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-xs px-2 py-1 rounded {{ $slide->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $slide->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <button onclick="editSlide({{ $slide->id }})"
                                    class="text-blue-600 hover:text-blue-800 text-sm px-3 py-1 rounded hover:bg-blue-50 transition-colors flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Edit
                                </button>
                                <form action="{{ route('admin.master.hero-slides.destroy', $slide->id) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDeleteSlide(this)"
                                        class="text-red-600 hover:text-red-800 text-sm px-3 py-1 rounded hover:bg-red-50 transition-colors flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div class="edit-mode-{{ $slide->id }} hidden p-4 bg-gray-50">
                        <form action="{{ route('admin.master.hero-slides.update', $slide->id) }}" method="POST"
                            enctype="multipart/form-data" class="space-y-3">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipe</label>
                                    <select name="type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                                        required>
                                        <option value="text" {{ $slide->type === 'text' ? 'selected' : '' }}>Text
                                        </option>
                                        <option value="image" {{ $slide->type === 'image' ? 'selected' : '' }}>Image
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        <input type="checkbox" name="is_active" {{ $slide->is_active ? 'checked' : '' }}
                                            class="mr-1">
                                        Active
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Title/Tagline</label>
                                <input type="text" name="title" value="{{ $slide->title }}"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>
                            @if ($slide->type === 'text')
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Subtitle/Subtagline</label>
                                    <textarea name="subtitle" rows="2"
                                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">{{ $slide->subtitle }}</textarea>
                                </div>
                            @endif
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Upload New Image
                                    (Optional)</label>
                                <input type="file" name="image" accept="image/*"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                @if ($slide->image_path)
                                    <p class="text-xs text-gray-500 mt-1">Current:
                                        {{ basename($slide->image_path) }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 transition-colors">
                                    Simpan
                                </button>
                                <button type="button" onclick="cancelEditSlide({{ $slide->id }})"
                                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition-colors">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <p>Belum ada hero slides. Tambahkan slide pertama di atas.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        // Toggle fields based on slide type in add form
        function toggleSlideFields() {
            const type = document.getElementById('slideType').value;
            const textFields = document.getElementById('textFields');
            const imageField = document.getElementById('imageField');

            if (type === 'text') {
                textFields.classList.remove('hidden');
                imageField.classList.add('hidden');
            } else {
                textFields.classList.add('hidden');
                imageField.classList.remove('hidden');
            }
        }

        // Edit slide
        function editSlide(slideId) {
            document.querySelector('.view-mode-' + slideId).classList.add('hidden');
            document.querySelector('.edit-mode-' + slideId).classList.remove('hidden');
        }

        // Cancel edit
        function cancelEditSlide(slideId) {
            document.querySelector('.view-mode-' + slideId).classList.remove('hidden');
            document.querySelector('.edit-mode-' + slideId).classList.add('hidden');
        }

        // Confirm delete
        function confirmDeleteSlide(button) {
            if (confirm('Apakah Anda yakin ingin menghapus slide ini?')) {
                button.closest('form').submit();
            }
        }
    </script>
@endsection