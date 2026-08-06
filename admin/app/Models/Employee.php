<?php

namespace App\Models;

use App\Http\Controllers\SupplierController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use DB;

class Employee extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'mobile_number',
        'com_per_retail',
        'com_per_whole',
        'status'
    ];
    
    public function orders() {
        return $this->hasMany(Order::class);
    }

    
    public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $employee = new Employee();
            $employee->name = $request->name;
            $employee->mobile_number = $request->mobile_number;
            $employee->com_per_retail = $request->com_per_retail;
            $employee->com_per_whole = $request->com_per_whole;
           $employee->status = $request->status;

            $employee->save();

           

            DB::commit();

            return $employee;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateEmployee($request,$employee) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $employee->name = $request->name;
            $employee->mobile_number = $request->mobile_number;
            $employee->com_per_retail = $request->com_per_retail;
            $employee->com_per_whole = $request->com_per_whole;
            $employee->status = $request->status;
            $employee->save();

          
            DB::commit();

            return $employee;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }
}
