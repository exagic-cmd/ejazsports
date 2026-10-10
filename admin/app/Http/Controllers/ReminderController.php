<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reminder;
use App\Models\ReminderLog;
use Carbon\Carbon;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::all();
        return view('reminder.index', compact('reminders'));
    }

    public function create()
    {
        return view('reminder.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'remind_time_1' => 'required'
        ]);

        Reminder::create($request->all());

        return redirect()->route('reminders.index')->with('message', 'Reminder added successfully.');
    }

    public function edit($id)
    {
        $reminder = Reminder::findOrFail($id);
        return view('reminder.edit', compact('reminder'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'remind_time_1' => 'required'
        ]);

        $reminder = Reminder::findOrFail($id);
        
        $data = $request->all();
        // If status checkbox is not checked, it won't be sent in the request
        $data['status'] = $request->has('status') ? 1 : 0;
        
        $reminder->update($data);

        return redirect()->route('reminders.index')->with('message', 'Reminder updated successfully.');
    }

    public function destroy($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->delete();

        return redirect()->route('reminders.index')->with('message', 'Reminder deleted successfully.');
    }

    /**
     * Called via AJAX to check if any active reminders are due
     */
    public function checkReminders(Request $request)
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i');
        $today = $now->toDateString();

        // 15 minutes window
        $activeReminders = Reminder::where('status', 1)->get();
        $dueReminders = [];

        foreach ($activeReminders as $reminder) {
            $times = [$reminder->remind_time_1, $reminder->remind_time_2, $reminder->remind_time_3];

            foreach ($times as $index => $time) {
                if (!$time) continue;
                
                // Compare time in HH:MM format
                $reminderTime = Carbon::parse($time);
                
                // check if the current time is within [reminder_time, reminder_time + 15 min]
                if ($now->between($reminderTime, $reminderTime->copy()->addMinutes(15))) {
                    $slot = 'remind_time_' . ($index + 1);
                    
                    // check if already dismissed today
                    $log = ReminderLog::where('reminder_id', $reminder->id)
                        ->where('log_date', $today)
                        ->where('time_slot', $slot)
                        ->first();

                    if (!$log) {
                        // Not dismissed yet, need to show
                        $dueReminders[] = [
                            'id' => $reminder->id,
                            'title' => $reminder->title,
                            'description' => $reminder->description,
                            'slot' => $slot
                        ];
                    }
                }
            }
        }

        return response()->json([
            'due' => $dueReminders
        ]);
    }

    /**
     * Called via AJAX when user clicks Dismiss
     */
    public function dismiss(Request $request)
    {
        $id = $request->input('id');
        $slot = $request->input('slot');

        if ($id && $slot) {
            ReminderLog::create([
                'reminder_id' => $id,
                'log_date' => Carbon::today()->toDateString(),
                'time_slot' => $slot,
                'dismissed_at' => Carbon::now()
            ]);
        }

        return response()->json(['success' => true]);
    }
}
