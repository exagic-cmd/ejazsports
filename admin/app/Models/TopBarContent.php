<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class TopBarContent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'text',
        'mobile_active',
        'web_active',
        'status',
        'serial_no'
    ];

    public function store($request) {

        DB::beginTransaction();
        try {

            //insert the basic information
            $topBarContent = new TopBarContent();
            $topBarContent->text = $request->text;
            $topBarContent->serial_no = $request->serial_no ? $request->serial_no : 0;
            $topBarContent->mobile_active = $request->mobile_active ? $request->mobile_active : 0;
            $topBarContent->web_active = $request->web_active ? $request->web_active : 0;
            $topBarContent->status = $request->status;

            $topBarContent->save();

            DB::commit();

            return $topBarContent;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }

    }

    public function updateTopBarContent($request,$id) {

        DB::beginTransaction();
        try {

            //insert the basic information

            $topBarContent = TopBarContent::find($id);
            $topBarContent->text = $request->text;
            $topBarContent->serial_no = $request->serial_no ? $request->serial_no : 0;
            $topBarContent->mobile_active = $request->mobile_active ? $request->mobile_active : 0;
            $topBarContent->web_active = $request->web_active ? $request->web_active : 0;
            $topBarContent->status = $request->status;

            $topBarContent->save();

            DB::commit();

            return $topBarContent;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }

    }
}
