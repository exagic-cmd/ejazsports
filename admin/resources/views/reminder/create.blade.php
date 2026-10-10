@extends('layouts.app')
@section('content')
<div class="content-header">
    <div>
        <h2 class="content-title card-title">Add Reminder</h2>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('reminders.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">Reminder Title *</label>
                    <input type="text" placeholder="e.g. Cheque clearing list" class="form-control" name="title" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea placeholder="Any additional details..." class="form-control" name="description" rows="2"></textarea>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="remind_time_1" class="form-label">Time 1 *</label>
                    <input type="time" class="form-control" name="remind_time_1" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="remind_time_2" class="form-label">Time 2 (Optional)</label>
                    <input type="time" class="form-control" name="remind_time_2">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="remind_time_3" class="form-label">Time 3 (Optional)</label>
                    <input type="time" class="form-control" name="remind_time_3">
                </div>

                <div class="col-md-12 mb-3 mt-2">
                    <label class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                        <span class="form-check-label"> Active </span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('reminders.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>
</div>

@endsection
