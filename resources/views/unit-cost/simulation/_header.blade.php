{{-- _header.blade.php --}}
<div class="mb-6">
    <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li>
                <a href="{{ route('unit-cost.index') }}" class="hover:text-green-800 transition-colors">Unit Cost</a>
            </li>
            <li>
                <span class="mx-1 text-gray-400">/</span>
            </li>
            <li aria-current="page">
                <span class="text-gray-700 font-medium">Simulasi Unit Cost</span>
            </li>
        </ol>
    </nav>
    <div class="flex items-center gap-3">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $diagnosis->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                Kode: <span class="font-mono">{{ $diagnosis->icd_code }}</span> | 
                Unit: <span class="font-medium">{{ $organizationUnit->name }}</span>
            </p>
        </div>
        @if($assignment->is_customized)
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 ml-auto">
            <i class="fas fa-check-circle mr-2"></i>Sudah Dikustomisasi
        </span>
        @endif
    </div>
</div>
