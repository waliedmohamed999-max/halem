@extends('layouts.admin')
@section('content')<pre>{{ json_encode($homeSection->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>@endsection



