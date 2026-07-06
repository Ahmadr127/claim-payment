@extends('layouts.app')

@section('title', 'Master Data Golongan Layanan')

@section('content')
<div class="w-full mx-auto pb-10" x-data="groupForm()">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <span class="text-gray-500">Master Data</span>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-xs mx-2"></i>
                            <span class="text-gray-800 font-medium">Golongan Layanan</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Golongan Layanan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data golongan / kelompok Jasa Medis dan Tindakan</p>
        </div>

        <button @click="openModal('create')" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg shadow-sm font-medium transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Golongan
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-start">
            <i class="fas fa-check-circle mt-0.5 mr-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-start">
            <i class="fas fa-exclamation-circle mt-0.5 mr-3"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-start">
            <i class="fas fa-exclamation-circle mt-0.5 mr-3"></i>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Toolbar -->
    <div class="bg-white p-4 rounded-t-lg border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('service-groups.index') }}" method="GET" class="w-full md:w-96 flex">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari kode atau nama golongan..." 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm transition-colors">
            </div>
            <button type="submit" class="ml-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg border border-gray-300 transition-colors">Cari</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-b-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Kode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Golongan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Urutan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Layanan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Dibuat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Diubah</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($groups as $index => $g)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">{{ $groups->firstItem() + $index }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $g->code }}</td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 font-semibold">{{ $g->name }}</div>
                                @if($g->description)
                                    <div class="text-xs text-gray-500 mt-1">{{ $g->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $g->display_order }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-blue-600 rounded-full">{{ $g->medical_services_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($g->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                <div class="font-medium text-gray-700">{{ $g->creator->name ?? 'System' }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $g->created_at ? $g->created_at->format('d/m/Y H:i') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                @if($g->updated_at && $g->updated_at != $g->created_at)
                                    <div class="font-medium text-gray-700">{{ $g->editor->name ?? 'System' }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $g->updated_at->format('d/m/Y H:i') }}</div>
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <button @click='openModal("edit", @json($g))' class="text-blue-600 hover:text-blue-900 transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="confirmDelete('{{ route('service-groups.destroy', $g->id) }}', '{{ $g->name }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p>Tidak ada data golongan layanan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($groups->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $groups->links() }}
            </div>
        @endif
    </div>

    @include('service-groups._modal_form')

    <!-- Hidden Delete Form -->
    <form id="deleteForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('groupForm', () => ({
        modalOpen: false,
        isEdit: false,
        formAction: '{{ route('service-groups.store') }}',
        formData: {
            code: '',
            name: '',
            description: '',
            display_order: 1,
            is_active: 1
        },
        
        openModal(mode, data = null) {
            this.isEdit = mode === 'edit';
            if (this.isEdit && data) {
                this.formAction = `/service-groups/${data.id}`;
                this.formData = {
                    code: data.code,
                    name: data.name,
                    description: data.description || '',
                    display_order: data.display_order || 1,
                    is_active: data.is_active ? 1 : 0
                };
            } else {
                this.formAction = '{{ route('service-groups.store') }}';
                this.formData = {
                    code: '',
                    name: '',
                    description: '',
                    display_order: 1,
                    is_active: 1
                };
            }
            this.modalOpen = true;
        },
        
        closeModal() {
            this.modalOpen = false;
        },
        
        confirmDelete(url, name) {
            if(confirm(`Apakah Anda yakin ingin menghapus golongan "${name}"?`)) {
                const form = document.getElementById('deleteForm');
                form.action = url;
                form.submit();
            }
        }
    }));
});
</script>
@endsection
