<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'category_id',
        'amount',
        'date',
        'detail',
        'bill_no',
        'store_id',
        'picture'
    ];

    public function category() {
        return $this->belongsTo(ExpenseCategory::class);
    }
    public function storeInfo() {
        return $this->belongsTo(Store::class,'store_id');
    }

    public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $expense = new Expense();
            $expense->category_id = $request->category_id;
            $expense->amount = $request->amount;
            $expense->date = $request->date;
            $expense->detail = $request->detail;
            $expense->store_id = $request->store_id;
            $expense->bill_no = $request->bill_no;


            if($request->picture) {
                    $name = time() . $request->file('picture')->getClientOriginalName();
                    $path = $request->file('picture')->storeAs('images/expense',$name);

                    $expense->picture = $path;
            }


            $expense->save();

            DB::commit();

            return $expense;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateExpense($request,$expense) {

        DB::beginTransaction();
        try {//dd($request);

            //insert the basic information
            $expense->category_id = $request->category_id;
            $expense->amount = $request->amount;
            $expense->date = $request->date;
            $expense->detail = $request->detail;
            $expense->store_id = $request->store_id;
            $expense->bill_no = $request->bill_no;


            if($request->picture) {
                $name = time() . $request->file('picture')->getClientOriginalName();
                $path = $request->file('picture')->storeAs('images/expense',$name);

                $expense->picture = $path;
            }


            $expense->save();

            DB::commit();

            return $expense;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
