@extends('layouts.app')

@section('title', 'Simulasi Unit Cost')

@section('content')
<div class="w-full mx-auto pb-10" x-data="unitCostSimulationForm()">

    @include('unit-cost.simulation._header')

    @include('unit-cost.simulation._toolbar')

    {{-- Main Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @include('unit-cost.simulation._table')
        @include('unit-cost.simulation._footer')
    </div>

    {{-- Action Buttons --}}
    <div class="mt-6 flex gap-3 justify-end">
        <a href="{{ route('unit-cost.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        <button type="button" @click="saveDraft()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            <i class="fas fa-save mr-2"></i>Simpan Draft
        </button>
    </div>

    {{-- Add Service Modal --}}
    @include('unit-cost.simulation._modal_add_service')

</div>
@endsection

@push('scripts')
    @include('unit-cost.simulation._scripts')
@endpush
