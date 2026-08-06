<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class ExpenseCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name'
    ];

   public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $expenseCategory = new ExpenseCategory();
            $expenseCategory->name = $request->name;
            


            $expenseCategory->save();

            DB::commit();

            return $expenseCategory;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateExpense($request,$expenseCategory) {

        DB::beginTransaction();
        try {//dd($request);

            //insert the basic information
           
            $expenseCategory->name = $request->name;


            $expenseCategory->save();

            DB::commit();

            return $expenseCategory;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
