<?php

namespace App\Models;

use App\Http\Controllers\UtilsController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;


class Courier extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'representative_name',
        'phone_number',
        'rate',
        'allow_weight_gm_ml',
        'extra_charges_above_weight',
        'status'
    ];

    public function areas() {
        return $this->hasMany(CourierArea::class)->with('area');
    }
    public function handovers() {
        return $this->hasMany(CourierHandoverOrder::class);
    }

    public function store($request) {
        DB::beginTransaction();
        try {

            //insert the basic information
            $courier = new Courier();
            $courier->name = $request->name;
            $courier->representative_name = $request->representative_name;
            $courier->phone_number = $request->phone_number;
            $courier->rate = $request->rate;
            $courier->allow_weight_gm_ml = $request->allow_weight_gm_ml;
            $courier->extra_charges_above_weight = $request->extra_charges_above_weight;
            $courier->status = $request->status;

            $courier->save();

            //manage areas
            if($request->area_id) {
                foreach ($request->area_id as $key => $val) {
                    $courierArea = new CourierArea();
                    $courierArea->courier_id = $courier->id;
                    $courierArea->area_id = $val;
                    $courierArea->save();
                }
            }

            DB::commit();

            return $courier;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function updateCourier($request,$courier) {

        DB::beginTransaction();
        try {

            //update the basic information
            $courier->name = $request->name;
            $courier->representative_name = $request->representative_name;
            $courier->phone_number = $request->phone_number;
            $courier->rate = $request->rate;
            $courier->allow_weight_gm_ml = $request->allow_weight_gm_ml;
            $courier->extra_charges_above_weight = $request->extra_charges_above_weight;
            $courier->status = $request->status;

            $courier->save();

            //manage areas
            if($request->area_id) {
                CourierArea::where('courier_id',$courier->id)->delete();
                foreach ($request->area_id as $key => $val) {
                    $courierArea = new CourierArea();
                    $courierArea->courier_id = $courier->id;
                    $courierArea->area_id = $val;
                    $courierArea->save();
                }
            }

            DB::commit();

            return $courier;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }
    }

    public function callCourierBooking($orders,$courier_id) {

        if($courier_id == 1) {
            //get the orders information
            $bookingList = array();
            foreach ($orders as $order) {
                if (($order->total_amount - $order->paid_amount) >= 100 || ($order->total_amount - $order->paid_amount) == 0) {

                    $description = '';
                    $temp = '';
                    foreach ($order->products as $product) {
                        $temp .= $product->qty . ' * ' . $product->product->product_heading;
                        if ($product->variant) {
                            if ($product->variant->shade != null)
                                $temp .= ' - ' . $product->variant->shade;
                            if ($product->variant->size != null)
                                $temp .= ' - ' . $product->variant->size;
                        }
                        if ($order->products->last() !== $product) {
                            $temp .= ' , ';
                        }
                    }

                    $description = $temp;

                    $tmp['index'] = "1";
                    $tmp['ConsigneeName'] = $order->name;
                    $tmp['ConsigneeRefNo'] = 'VEGAS-' . $order->order_no;
                    $tmp['ConsigneeCellNo'] = UtilsController::reformatMobileNumber($order->phone_number);
                    $tmp['Address'] = $order->address;
                    $tmp['DestCityId'] = $order->city;
                    $tmp['ServiceTypeId'] = ($order->paid_amount - $order->total_amount == 0) ? '1' : '7';
                    $tmp['Pcs'] = $order->total_quantity;
                    $tmp['Weight'] = '1';
                    $tmp['Description'] = $description;
                    $tmp['SelOrigin'] = 'Domestic';
                    $tmp['CodAmount'] = ($order->paid_amount - $order->total_amount == 0) ? '' : $order->total_amount;
                    $tmp['SpecialHandling'] = 'false';
                    $tmp['MyBoxId'] = '3';
                    $tmp['Holiday'] = 'false';
                    $tmp['remarks'] = 'Nothing in remarks';


                    array_push($bookingList, $tmp);
                }
            }
            //get the basic info

            $data_array['loginId'] = 'RWP-06210';
            $data_array['ShipperName'] = 'VEGAS.PK-ISLAMABAD';
            $data_array['ShipperCellNo'] = '03355388680';
            $data_array['ShipperArea'] = 571;
            $data_array['ShipperCity'] = '4';
            $data_array['ShipperAddress'] = 'Office 142, street 9 I-10-3 industrial area, Islamabad';
            $data_array['ShipperLandLineNo'] = '03355388680';
            $data_array['ShipperEmail'] = 'noreen@vegas.pk';
            $data_array['bookingList'] = $bookingList;


            $data_array = json_encode($data_array);
            // dd($data_array);

            //execute the curl request
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_array);

            $url = 'http://cod.callcourier.com.pk/api/CallCourier/BulkBookings';

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 0);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));

            // EXECUTE:
            $result = curl_exec($curl);

            $result = json_decode($result);

            //update the CN
            foreach ($result->bookingResponse as $res) {
                if ($res->Response) {
                    $orderNo = preg_split("/[-\s:]/", $res->refNo);
                    if ($res->CNNO)
                        Order::where('order_no', $orderNo[1])->update(['cn_no' => $res->CNNO, 'status' => Order::BOOKED, 'courier_id' => $courier_id, 'booking_time' => Carbon::now()]);
                }
            }

            if (!$result) {
                die("Connection Failure");
            }
            curl_close($curl);

            return $result->response;
        }
        else {
            $bookingList = array();
            foreach ($orders as $order) {

                $description = '';
                $temp = '';
                foreach ($order->products as $product) {
                    $temp .= $product->qty . ' * ' . $product->product->product_heading;
                    if ($product->variant) {
                        if ($product->variant->shade != null)
                            $temp .= ' - ' . $product->variant->shade;
                        if ($product->variant->size != null)
                            $temp .= ' - ' . $product->variant->size;
                    }
                    if ($order->products->last() !== $product) {
                        $temp .= ' , ';
                    }
                }

                $description = $temp;

                $dataArray = array(
                    'customerName' => $order->name,
                    'deliveryAddress' => $order->address,
                    'customerPhone' => UtilsController::reformatMobileNumber($order->phone_number),
                    'cityName' => $order->area->name,
                    'invoicePayment' => $order->total_amount - $order->paid_amount,
                    'orderRefNumber' => 'VEGAS-' . $order->order_no,
                    'items' => $order->total_quantity,
                    'orderDetail' => $description
                );

                $dataArray = json_encode($dataArray);


                //execute the curl request
                $curl = curl_init();
                $url = 'https://api.postex.pk/services/integration/api/order/v1/post-order';

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $dataArray);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt(
                    $curl,
                    CURLOPT_HTTPHEADER,
                    array(
                        "accept: application/json",
                        "content-type: application/json",
                        "token: NGIyYmJkZmQxMWZjNDM3YTk0NzZmY2JmNGQyYmEzMjY6OTIyMjZlODIyNjgzNDdlNjg3YjA5MjU5MzQ0ODc0NmU="
                    )
                );


                // EXECUTE:
                $result = curl_exec($curl);

                $result = json_decode($result);

                if($result->statusCode == "200") {
                    Order::where('id', $order->id)->update(['cn_no' => $result->dist->trackingNumber, 'status' => Order::BOOKED, 'courier_id' => $courier_id, 'booking_time' => Carbon::now()]);
                }

                curl_close($curl);
                //exit;
            }

            return $result->statusMessage;
        }


    }
}
