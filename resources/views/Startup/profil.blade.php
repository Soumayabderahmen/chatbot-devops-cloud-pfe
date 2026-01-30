@extends('layouts.general')
@section('title')
Profile Startup
@endsection
@section('page_description')

@endsection

@section('css')


@endsection

@section('content')

<div class="container-xxl flex-grow-1 container-p-y mt-0">
<profile 
  :user='@json($user)'
  :startup='@json($startup)'
  :sectors='@json($sectors)'
  :flash-message='@json(session("success"))'
></profile>


</div>

@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection