@extends('layouts.general')

@section('title', 'Gestion de l’équipe')

@section('page_description', 'Tutoriels de la plateforme BrainCode')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y mt-0">
  <team-page :members='@json($members)'/>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
