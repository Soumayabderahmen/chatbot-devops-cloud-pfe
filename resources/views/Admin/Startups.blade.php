@extends('layouts.general')

@section('title', 'Liste des Startups')



@section('content')
<div class="container-xxl flex-grow-1 container-p-y mt-0">
   <startups-admin :startups='@json($startups)'></startups-admin>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
