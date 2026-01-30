@extends('layouts.HomeView.generale')

@section('title')
  Notre Équipe
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.23.6/dist/css/uikit.min.css" />
@endsection
@section('content')
<div id="app">
  <equipe-page
    :founders='@json($founders)'
    :chefs='@json($chefs)'
    :members='@json($members)'
  ></equipe-page>
  <chatbotia />
</div>
@endsection


@section('script')
  <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.6/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.6/dist/js/uikit-icons.min.js"></script>
@endsection
