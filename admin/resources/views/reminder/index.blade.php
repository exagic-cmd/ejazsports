@extends('layouts.app')
@section('content')
<div class="content-header">
    <div>
        <h2 class="content-title card-title">Reminders</h2>
        <p>Manage your daily reminders.</p>
    </div>
    <div>
        <a href="{{ route('reminders.create') }}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New Reminder</a>
    </div>
</div>
<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="example">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Time 1</th>
                        <th>Time 2</th>
                        <th>Time 3</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reminders as $reminder)
                    <tr>
                        <td>{{ $reminder->id }}</td>
                        <td><b>{{ $reminder->title }}</b></td>
                        <td>{{ $reminder->remind_time_1 ? \Carbon\Carbon::parse($reminder->remind_time_1)->format('h:i A') : '-' }}</td>
                        <td>{{ $reminder->remind_time_2 ? \Carbon\Carbon::parse($reminder->remind_time_2)->format('h:i A') : '-' }}</td>
                        <td>{{ $reminder->remind_time_3 ? \Carbon\Carbon::parse($reminder->remind_time_3)->format('h:i A') : '-' }}</td>
                        <td>
                            @if($reminder->status == 1)
                                <span class="badge rounded-pill alert-success">Active</span>
                            @else
                                <span class="badge rounded-pill alert-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('reminders.edit', $reminder->id) }}" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Edit </a>
                            
                            <form action="{{ route('reminders.destroy', $reminder->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this reminder?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm font-sm rounded btn-danger"> <i class="material-icons md-delete_forever"></i> Delete </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div> <!-- table-responsive //end -->
    </div> <!-- card-body end// -->
</div> <!-- card end// -->

@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#example').DataTable();
    });
</script>
@endsection
