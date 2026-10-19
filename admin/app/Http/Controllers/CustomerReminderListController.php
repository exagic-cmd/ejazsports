<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\CustomerPayment;
use App\Models\CustomerReminderList;
use App\Models\CustomerReminderListItem;
use App\Models\CustomerReminderListAudit;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;

class CustomerReminderListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display listing of reminder lists.
     */
    public function index(Request $request)
    {
        $query = CustomerReminderList::with(['creator', 'items.customer.area'])
            ->withCount('items');

        if ($request->filled('date')) {
            $query->whereDate('reminder_date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reminderLists = $query->orderBy('reminder_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate(25);

        // Calculate total balances for each list
        foreach ($reminderLists as $list) {
            $list->total_balance = $list->items->sum('snapshot_balance');
            $list->completed_count = $list->items->where('status', CustomerReminderListItem::STATUS_PAID)->count();
            $list->called_count = $list->items->where('status', CustomerReminderListItem::STATUS_CALLED)->count();
        }

        return view('customer_reminder_lists.index', compact('reminderLists'));
    }

    /**
     * Show form to create a new reminder list.
     */
    public function create(Request $request)
    {
        $selectedDate = $request->get('date', date('Y-m-d'));
        // Fetch customers with their areas
        $customers = Customer::where('is_website_customer', 0)
            ->with('area')
            ->orderBy('first_name', 'ASC')
            ->get();

        // Calculate balances
        $balances = $this->calculateBatchBalances($customers->pluck('id')->toArray());

        foreach ($customers as $customer) {
            $customer->computed_balance = $balances[$customer->id] ?? 0;
            // The reminder list's location filter must use the customer's City field.
            $customer->area_label = $customer->city ? trim($customer->city) : 'General';
        }

        // Build filter options from the same labels shown in the customer table.
        // This includes customer city values that are not present in the areas table.
        $areaOptions = $customers->pluck('area_label')
            ->filter()
            ->unique(function ($label) {
                return mb_strtolower(trim($label));
            })
            ->sort()
            ->values();

        return view('customer_reminder_lists.create', compact('selectedDate', 'areaOptions', 'customers'));
    }

    /**
     * Store new reminder list and items.
     */
    public function store(Request $request)
    {
        $request->validate([
            'reminder_date' => 'required|date',
            'customer_ids'  => 'required|array|min:1',
            'customer_ids.*'=> 'distinct|exists:customers,id,is_website_customer,0',
            'title'         => 'nullable|string|max:255',
            'notes'         => 'nullable|string'
        ]);

        $duplicates = $this->findDuplicateCustomers($request->customer_ids, $request->reminder_date);
        if ($duplicates->isNotEmpty() && !$request->boolean('confirm_duplicates')) {
            $duplicateDetails = $duplicates->map(function ($item) {
                return [
                    'customer' => $item->customer->first_name . ' ' . $item->customer->last_name,
                    'lists' => $item->matching_lists,
                ];
            })->values()->all();
            return redirect()->back()->withInput()->with('duplicateCustomers', $duplicateDetails);
        }

        $title = $request->title ?: 'Recovery List - ' . Carbon::parse($request->reminder_date)->format('d M Y');

        DB::beginTransaction();
        try {
            $list = CustomerReminderList::create([
                'title'         => $title,
                'reminder_date' => $request->reminder_date,
                'created_by'    => Auth::id(),
                'status'        => CustomerReminderList::STATUS_ACTIVE,
                'notes'         => $request->notes
            ]);
            $this->recordAudit($list, 'list_created', 'list', $list->id, 'Reminder list created.', null, [
                'title' => $list->title,
                'reminder_date' => $list->reminder_date,
                'status' => $list->status,
                'notes' => $list->notes,
            ]);

            // Calculate balances for selected customers
            $balances = $this->calculateBatchBalances($request->customer_ids);

            $sortOrder = 1;
            foreach ($request->customer_ids as $customerId) {
                $newItem = CustomerReminderListItem::create([
                    'reminder_list_id' => $list->id,
                    'customer_id'      => $customerId,
                    'snapshot_balance' => $balances[$customerId] ?? 0,
                    'status'           => CustomerReminderListItem::STATUS_PENDING,
                    'sort_order'       => $sortOrder++
                ]);
                $customer = Customer::find($customerId);
                $this->recordAudit($list, 'customer_added', 'customer', $customerId,
                    'Customer ' . ($customer ? $customer->first_name : $customerId) . ' added to the list.', null,
                    ['snapshot_balance' => $balances[$customerId] ?? 0, 'status' => 'Pending']);
            }

            DB::commit();

            activity('Create')->log("Created Customer Reminder List [{$list->title}] for {$list->reminder_date}");

            return redirect()->route('customer-reminder-lists.show', $list->id)
                ->with('message', 'Daily Reminder List created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error creating list: ' . $e->getMessage());
        }
    }

    /**
     * Display sheet detail with sorting & management actions.
     */
    public function show(Request $request, $id)
    {
        $list = CustomerReminderList::with(['creator', 'items.customer.area'])
            ->findOrFail($id);

        $sortBy = $request->get('sort_by', 'area'); // 'area', 'balance_desc', 'name'

        $items = $list->items;

        // Group or sort
        if ($sortBy === 'balance_desc') {
            $sortedItems = $items->sortByDesc('snapshot_balance');
            $groupedItems = ['All Customers' => $sortedItems];
        } elseif ($sortBy === 'name') {
            $sortedItems = $items->sortBy(function ($item) {
                return $item->customer ? $item->customer->first_name : '';
            });
            $groupedItems = ['All Customers' => $sortedItems];
        } else {
            // Default: Area-wise grouping
            $groupedItems = $items->groupBy(function ($item) {
                if ($item->customer && $item->customer->area) {
                    return $item->customer->area->name;
                }
                if ($item->customer && $item->customer->city) {
                    return $item->customer->city;
                }
                return 'General / Unassigned';
            })->sortKeys();
        }

        // Customers available to add to list
        $existingCustomerIds = $items->pluck('customer_id')->toArray();
        $availableCustomers = Customer::with('area')
            ->where('is_website_customer', 0)
            ->whereNotIn('id', $existingCustomerIds)
            ->orderBy('first_name', 'ASC')
            ->get();

        $totalBalance = $items->sum('snapshot_balance');

        $auditLogs = $list->auditLogs()->with('user')->get();
        return view('customer_reminder_lists.show', compact('list', 'groupedItems', 'sortBy', 'totalBalance', 'availableCustomers', 'auditLogs'));
    }

    /**
     * Update sheet date / title / notes.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'reminder_date' => 'required|date',
            'title'         => 'required|string|max:255',
            'status'        => 'required|in:1,2',
            'notes'         => 'nullable|string'
        ]);

        $list = CustomerReminderList::findOrFail($id);
        $duplicates = $this->findDuplicateCustomers($list->items()->pluck('customer_id')->all(), $request->reminder_date, $list->id);
        if ($duplicates->isNotEmpty() && !$request->boolean('confirm_duplicates')) {
            return redirect()->back()->withInput()->with('dateDuplicateDetails', $duplicates->map(function ($item) {
                return $item->customer->first_name . ' ' . $item->customer->last_name . ' — ' . $item->matching_lists;
            })->values()->all());
        }
        $oldValues = $list->only(['reminder_date', 'title', 'status', 'notes']);
        $list->update($request->only('reminder_date', 'title', 'status', 'notes'));
        $this->recordAudit($list, 'list_updated', 'list', $list->id, 'Reminder list details changed.', $oldValues,
            $list->only(['reminder_date', 'title', 'status', 'notes']));

        return redirect()->route('customer-reminder-lists.show', $list->id)
            ->with('message', 'Reminder list details updated successfully.');
    }

    /**
     * Quick reschedule date.
     */
    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'reminder_date' => 'required|date'
        ]);

        $list = CustomerReminderList::findOrFail($id);
        $duplicates = $this->findDuplicateCustomers($list->items()->pluck('customer_id')->all(), $request->reminder_date, $list->id);
        if ($duplicates->isNotEmpty() && !$request->boolean('confirm_duplicates')) {
            return redirect()->back()->withInput()->with('dateDuplicateDetails', $duplicates->map(function ($item) {
                return $item->customer->first_name . ' ' . $item->customer->last_name . ' — ' . $item->matching_lists;
            })->values()->all());
        }
        $oldDate = $list->reminder_date;
        $list->reminder_date = $request->reminder_date;
        $list->save();
        $this->recordAudit($list, 'date_changed', 'list', $list->id, 'Reminder date changed.', ['reminder_date' => $oldDate], ['reminder_date' => $list->reminder_date]);

        activity('Update')->log("Rescheduled Customer Reminder List #{$list->id} to {$list->reminder_date}");

        return redirect()->back()->with('message', "Reminder list rescheduled to {$list->reminder_date}.");
    }

    /**
     * Add a customer to an existing list.
     */
    public function addItem(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id'
        ]);

        $list = CustomerReminderList::findOrFail($id);

        $activeDuplicates = $this->findDuplicateCustomers([$request->customer_id], $list->reminder_date, $list->id);
        if ($activeDuplicates->isNotEmpty() && !$request->boolean('confirm_duplicate')) {
            $matches = $activeDuplicates->first()->matching_lists;
            return redirect()->back()->with('error', 'This customer is already on an active list for ' . $list->reminder_date . ': ' . $matches . '. Confirm the duplicate warning to add anyway.')
                ->with('duplicateCustomerId', (int) $request->customer_id);
        }

        $exists = CustomerReminderListItem::where('reminder_list_id', $list->id)
            ->where('customer_id', $request->customer_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Customer is already in this reminder list.');
        }

        $customer = Customer::findOrFail($request->customer_id);
        $balance = $customer->remaining_balance;

        CustomerReminderListItem::create([
            'reminder_list_id' => $list->id,
            'customer_id'      => $customer->id,
            'snapshot_balance' => $balance,
            'status'           => CustomerReminderListItem::STATUS_PENDING,
            'sort_order'       => $list->items()->count() + 1
        ]);
        $this->recordAudit($list, 'customer_added', 'customer', $customer->id,
            'Customer ' . $customer->first_name . ' added to the list.', null,
            ['snapshot_balance' => $balance, 'status' => 'Pending']);

        return redirect()->back()->with('message', "{$customer->first_name} added to list.");
    }

    /** Send this customer's reminder details only to their saved email address. */
    public function emailReminder($listId, $itemId)
    {
        $item = CustomerReminderListItem::with(['customer', 'reminderList'])
            ->where('reminder_list_id', $listId)
            ->findOrFail($itemId);
        $customer = $item->customer;

        if (!$customer || !$customer->email) {
            return redirect()->back()->with('error', 'This customer does not have an email address saved.');
        }

        try {
            Mail::send('emails.customer-reminder', ['item' => $item, 'customer' => $customer], function ($message) use ($customer, $item) {
                $message->to($customer->email, trim($customer->first_name . ' ' . $customer->last_name))
                    ->subject('Payment Reminder - ' . $item->reminderList->title);
            });
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->with('error', 'Email could not be sent. Check the mail server settings and try again.');
        }

        return redirect()->back()->with('message', 'Reminder email sent to ' . $customer->email . '.');
    }

    /**
     * Remove customer item from sheet.
     */
    public function removeItem($itemId)
    {
        $item = CustomerReminderListItem::findOrFail($itemId);
        $listId = $item->reminder_list_id;
        $name = $item->customer ? $item->customer->first_name : 'Customer';
        $this->recordAudit($item->reminderList, 'customer_removed', 'customer', $item->customer_id,
            'Customer ' . $name . ' removed from the list.', ['snapshot_balance' => $item->snapshot_balance, 'status' => $item->status, 'remarks' => $item->remarks], null);
        $item->delete();

        return redirect()->route('customer-reminder-lists.show', $listId)
            ->with('message', "{$name} removed from the list.");
    }

    /**
     * Update customer item status or remarks.
     */
    public function updateItem(Request $request, $itemId)
    {
        $request->validate([
            'status'  => 'required|integer|in:1,2,3,4,5',
            'remarks' => 'nullable|string|max:500',
            'snapshot_balance' => 'nullable|numeric'
        ]);

        $item = CustomerReminderListItem::findOrFail($itemId);
        $oldValues = $item->only(['snapshot_balance', 'status', 'remarks']);
        $item->status = $request->status;
        if ($request->has('snapshot_balance')) {
            $item->snapshot_balance = $request->snapshot_balance;
        }
        if ($request->has('remarks')) {
            $item->remarks = $request->remarks;
        }
        $item->save();

        $newValues = $item->only(['snapshot_balance', 'status', 'remarks']);
        $changes = [];
        foreach ($newValues as $key => $value) {
            if ((string) $oldValues[$key] !== (string) $value) {
                $changes[$key] = ['old' => $oldValues[$key], 'new' => $value];
            }
        }
        if ($changes) {
            $customerName = $item->customer ? $item->customer->first_name : 'Customer';
            $changedFields = implode(', ', array_map(function ($field) {
                return ucfirst(str_replace('_', ' ', $field));
            }, array_keys($changes)));
            $this->recordAudit($item->reminderList, 'customer_updated', 'customer', $item->customer_id,
                $customerName . ' reminder details changed: ' . $changedFields . '.', $oldValues, $newValues);
        }

        return redirect()->back()->with('message', 'Customer status updated.');
    }

    /**
     * Delete entire reminder list.
     */
    public function destroy($id)
    {
        $list = CustomerReminderList::findOrFail($id);
        $title = $list->title;
        $this->recordAudit($list, 'list_deleted', 'list', $list->id, 'Reminder list deleted.', $list->only(['title', 'reminder_date', 'status', 'notes']), null);
        $list->delete();

        return redirect()->route('customer-reminder-lists.index')
            ->with('message', "Reminder list [{$title}] deleted successfully.");
    }

    /**
     * Print View.
     */
    public function printView(Request $request, $id)
    {
        $list = CustomerReminderList::with(['creator', 'items.customer.area'])->findOrFail($id);
        
        $sortBy = $request->get('sort_by', 'area');
        $groupedItems = $this->prepareGroupedItems($list->items, $sortBy);
        $totalBalance = $list->items->sum('snapshot_balance');

        return view('customer_reminder_lists.print', compact('list', 'groupedItems', 'totalBalance', 'sortBy'));
    }

    /**
     * Download PDF.
     */
    public function exportPdf(Request $request, $id)
    {
        $list = CustomerReminderList::with(['creator', 'items.customer.area'])->findOrFail($id);
        
        $sortBy = $request->get('sort_by', 'area');
        $groupedItems = $this->prepareGroupedItems($list->items, $sortBy);
        $totalBalance = $list->items->sum('snapshot_balance');

        $data = [
            'list'         => $list,
            'groupedItems' => $groupedItems,
            'totalBalance' => $totalBalance,
            'sortBy'       => $sortBy
        ];

        $pdf = PDF::loadView('customer_reminder_lists.pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $fileName = (Str::slug($list->title) ?: 'recovery-list') . '.pdf';
        return $pdf->stream($fileName);
    }

    /** Download a PDF containing only one customer's reminder details. */
    public function downloadCustomerPdf($listId, $itemId)
    {
        $item = CustomerReminderListItem::with(['customer', 'reminderList'])
            ->where('reminder_list_id', $listId)
            ->findOrFail($itemId);
        $customer = $item->customer;

        $pdf = PDF::loadView('customer_reminder_lists.customer_pdf', compact('item', 'customer'));
        $pdf->setPaper('A4', 'portrait');

        $customerName = $customer ? $customer->first_name : 'customer';
        $fileName = (Str::slug($customerName . '-reminder-' . $item->reminderList->reminder_date) ?: 'customer-reminder') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Helper to group items by Area or single collection.
     */
    private function prepareGroupedItems($items, $sortBy)
    {
        if ($sortBy === 'balance_desc') {
            return ['All Customers' => $items->sortByDesc('snapshot_balance')];
        } elseif ($sortBy === 'name') {
            return ['All Customers' => $items->sortBy(function ($item) {
                return $item->customer ? $item->customer->first_name : '';
            })];
        }

        return $items->groupBy(function ($item) {
            if ($item->customer && $item->customer->area) {
                return $item->customer->area->name;
            }
            if ($item->customer && $item->customer->city) {
                return $item->customer->city;
            }
            return 'General / Unassigned';
        })->sortKeys();
    }

    /**
     * Efficient Batch Balance Calculation for given customer IDs.
     */
    private function calculateBatchBalances(array $customerIds)
    {
        if (empty($customerIds)) {
            return [];
        }

        $customers = Customer::whereIn('id', $customerIds)->get()->keyBy('id');

        $billAmounts = Order::whereIn('customer_id', $customerIds)
            ->where('status', '!=', 6)
            ->groupBy('customer_id')
            ->select('customer_id', DB::raw('SUM(total_amount) as total'))
            ->pluck('total', 'customer_id')
            ->toArray();

        $returnAmounts = Order::whereIn('customer_id', $customerIds)
            ->where('return_type', 1)
            ->groupBy('customer_id')
            ->select('customer_id', DB::raw('SUM(return_amount) as total'))
            ->pluck('total', 'customer_id')
            ->toArray();

        $payments = CustomerPayment::whereIn('customer_id', $customerIds)
            ->where('status', 2)
            ->groupBy('customer_id')
            ->select('customer_id', DB::raw('SUM(amount) as total_amount, SUM(discount) as total_discount'))
            ->get()
            ->keyBy('customer_id');

        $balances = [];
        foreach ($customerIds as $id) {
            $customer = $customers[$id] ?? null;
            if (!$customer) continue;

            $opening = $customer->opening_balance ?? 0;
            $bill = $billAmounts[$id] ?? 0;
            $ret = $returnAmounts[$id] ?? 0;
            $pay = isset($payments[$id]) ? $payments[$id]->total_amount : 0;
            $disc = isset($payments[$id]) ? $payments[$id]->total_discount : 0;

            $balances[$id] = round(((($opening + $bill) - $ret) - $pay) - $disc, 2);
        }

        return $balances;
    }

    /** Find selected customers already present on active lists for the same date. */
    private function findDuplicateCustomers(array $customerIds, $date, $exceptListId = null)
    {
        $items = CustomerReminderListItem::with(['customer', 'reminderList'])
            ->whereIn('customer_id', $customerIds)
            ->whereHas('reminderList', function ($query) use ($date, $exceptListId) {
                $query->whereDate('reminder_date', $date)
                    ->where('status', CustomerReminderList::STATUS_ACTIVE);
                if ($exceptListId) {
                    $query->where('id', '!=', $exceptListId);
                }
            })
            ->get()
            ->groupBy('customer_id');

        return $items->map(function ($customerItems) {
            $first = $customerItems->first();
            $first->matching_lists = $customerItems->pluck('reminderList.title')->filter()->unique()->implode(', ');
            return $first;
        })->values();
    }

    private function recordAudit($list, $action, $subjectType, $subjectId, $description, $oldValues = null, $newValues = null)
    {
        CustomerReminderListAudit::create([
            'reminder_list_id' => $list ? $list->id : null,
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'old_values' => $oldValues === null ? null : json_encode($oldValues),
            'new_values' => $newValues === null ? null : json_encode($newValues),
        ]);
    }
}
