@extends('layouts.app')

@section('title', 'Simulasi Tarif Umum')

@section('content')
<div class="w-full mx-auto pb-10" x-data="simulationForm()">

    @include('diagnoses.pathway._header')

    @include('diagnoses.pathway._toolbar')

    {{-- Main Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @include('diagnoses.pathway._table')
        @include('diagnoses.pathway._footer')
    </div>

    {{-- Add Service Modal --}}
    @include('diagnoses.pathway._modal_add_service')

</div>
@endsection

@push('scripts')
    @include('diagnoses.pathway._scripts')
@endpush
