@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- The content of this section will be dynamically filled by admin_panel.js --}}
    {{-- The script looks for an element with id="contentArea" --}}
    <div id="contentArea" class="content-area">
        {{-- Initial loading state or placeholder content can go here if desired, --}}
        {{-- but admin_panel.js will populate this on load. --}}
    </div>
@endsection
