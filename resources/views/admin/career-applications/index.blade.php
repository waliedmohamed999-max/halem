@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Career Applications</h4>
    <form class="d-flex gap-2" method="GET">
        <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(['new','reviewed','interview','rejected','hired'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </form>
</div>
<table class="table table-bordered align-middle">
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Position</th>
        <th>Phone</th>
        <th>Status</th>
        <th>Created</th>
        <th></th>
    </tr>
    @foreach($applications as $application)
        <tr>
            <td>{{ $application->id }}</td>
            <td>{{ $application->full_name }}</td>
            <td>{{ $application->position?->title_en ?? '-' }}</td>
            <td>{{ $application->phone }}</td>
            <td><span class="badge text-bg-secondary">{{ $application->status }}</span></td>
            <td>{{ $application->created_at?->format('Y-m-d H:i') }}</td>
            <td class="d-flex gap-1">
                <a class="btn btn-sm btn-info" href="{{ route('admin.career-applications.show', [app()->getLocale(), $application]) }}">Show</a>
                <form method="POST" action="{{ route('admin.career-applications.destroy', [app()->getLocale(), $application]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Del</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
{{ $applications->links() }}
@endsection
