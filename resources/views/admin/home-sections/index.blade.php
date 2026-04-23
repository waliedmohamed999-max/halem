@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Home Sections</h4>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('admin.marketing-sections.edit', app()->getLocale()) }}">Banners & Offers</a>
        <a class="btn btn-primary" href="{{ route('admin.home-sections.create', app()->getLocale()) }}">Add</a>
    </div>
</div>
<table class="table"><tr><th>Key</th><th>Active</th><th>Order</th><th></th></tr>@foreach($sections as $s)<tr><td>{{ $s->section_key }}</td><td>{{ $s->is_active ? 'Yes':'No' }}</td><td>{{ $s->sort_order }}</td><td><a class="btn btn-sm btn-warning" href="{{ route('admin.home-sections.edit',[app()->getLocale(),$s]) }}">Edit</a></td></tr>@endforeach</table>{{ $sections->links() }}
@endsection


