@extends('layouts.app')
@section('content')
<div class="content-header">
    <div>
        <h2 class="content-title card-title">Edit Reminder</h2>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('reminders.update', $reminder->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">Reminder Title *</label>
                    <input type="text" class="form-control" name="title" value="{{ $reminder->title }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control" name="description" rows="2">{{ $reminder->description }}</textarea>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="remind_time_1" class="form-label">Time 1 *</label>
                    <input type="time" class="form-control" name="remind_time_1" value="{{ $reminder->remind_time_1 ? \Carbon\Carbon::parse($reminder->remind_time_1)->format('H:i') : '' }}" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="remind_time_2" class="form-label">Time 2 (Optional)</label>
                    <input type="time" class="form-control" name="remind_time_2" value="{{ $reminder->remind_time_2 ? \Carbon\Carbon::parse($reminder->remind_time_2)->format('H:i') : '' }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="remind_time_3" class="form-label">Time 3 (Optional)</label>
                    <input type="time" class="form-control" name="remind_time_3" value="{{ $reminder->remind_time_3 ? \Carbon\Carbon::parse($reminder->remind_time_3)->format('H:i') : '' }}">
                </div>

                <div class="col-md-12 mb-3 mt-2">
                    <label class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="status" value="1" {{ $reminder->status == 1 ? 'checked' : '' }}>
                        <span class="form-check-label"> Active </span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('reminders.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>
</div>

@endsection
