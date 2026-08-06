<div class="card-body" id="update-table">
            <div class="table-responsive" >
                <h3>Retail Orders</h3>
                <table id="myTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                      
                        <th scope="col">Order Amt.</th>
                        <th scope="col">whole Sale Margin</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum=0;?>
                    @foreach($orders as $o)
                    
                    @if($o->customer_id == 1)
                    @if($o->return_amount == 0 || $o->return_amount != $o->total_amount)
                            <tr>

                                <td>{{$sr++}}</td>
                                <td>{{date('d-m-Y',strtotime($o->created_at))}}</td>
                                
                              <td><a href="{{route('orders.show',$o->id)}}" target="_blank">{{$o->order_no}}</a></td>
                             
                                <td>{{number_format(($o->total_amount - $o->return_amount))}}
                               <?php $orderSum += ($o->total_amount  - $o->return_amount);?></td>
                               <td>{{($o->margin - $o->discount_amount)}}
                               <?php $marginSum += ($o->margin - $o->discount_amount);?></td>
                                 <td>{{round(($o->margin - $o->discount_amount) * ($employee->com_per_retail / 100))}}</td>
                                
                               
                               <?php $t += round(($o->margin - $o->discount_amount) * ($employee->com_per_retail/100) , 2);?>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                            
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>{{number_format($orderSum)}}</b></td>
                                <td><b>{{number_format($marginSum)}}</b></td>
                                <td><b>{{$t}}</b></td>
                            </tr>
                            
                           

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
       <div class="card-body">
            <div class="table-responsive" >
                <h3>whole Sale Orders</h3>
                <table id="myTable1" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                        
                        <th scope="col">Order Amt.</th>
                        <th scope="col">Retail Margin</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum=0;?>
                    
                    @foreach($orders as $o)
                    
                    @if($o->customer_id != 1 && ($o->pay_amount >= $o->total_amount || $o->paid_amount == $o->total_amount || (($o->total_amount - $o->paid_amount) < 10 )))
                    @if($o->return_amount == 0 || $o->return_amount != $o->total_amount)
                            <tr>

                                <td>{{$sr++}}</td>
                                <td>{{date('d-m-Y',strtotime($o->created_at))}}</td>
                                
                              <td><a href="{{route('orders.show',$o->id)}}" target="_blank">{{$o->order_no}}</a></td>
                              
                               <td>{{number_format(($o->total_amount - $o->return_amount))}}
                               <?php $orderSum += ($o->total_amount - $o->return_amount);?></td>
                               
                               <td>{{($o->margin - $o->discount_amount)}}
                               <?php $marginSum += ($o->margin - $o->discount_amount);?></td>
                                 <td>{{round(($o->margin - $o->discount_amount) * ($employee->com_per_whole / 100))}}</td>
                                
                               
                               <?php $t += round(($o->margin - $o->discount_amount) * ($employee->com_per_whole/100) , 2);?>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>{{number_format($orderSum)}}</b></td>
                                <td><b>{{number_format($marginSum)}}</b></td>
                                <td><b>{{$t}}</b></td>
                            </tr>
                    

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
         <div class="card-body" >
            <div class="table-responsive" >
                <h3>Credit Orders</h3>
                <table id="myTable2" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                        
                        <th scope="col">Order Amt.</th>
                        <th scope="col">Purchase - Whole Margin</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum=0;?>
                    
                    @foreach($orders as $o)
                    
                    @if($o->customer_id != 1 && ($o->pay_amount < $o->total_amount || $o->paid_amount < $o->total_amount))
                     @if($o->return_amount == 0 || $o->return_amount != $o->total_amount)
                            <tr>

                                <td>{{$sr++}}</td>
                                <td>{{date('d-m-Y',strtotime($o->created_at))}}</td>
                                
                              <td><a href="{{route('orders.show',$o->id)}}" target="_blank">{{$o->order_no}}</a></td>
                              
                               <td>{{number_format(($o->total_amount - $o->return_amount))}}
                               <?php $orderSum += ($o->total_amount - $o->return_amount);?></td>
                               
                               <td>{{($o->margin - $o->discount_amount)}}
                               <?php $marginSum += ($o->margin - $o->discount_amount);?></td>
                                <td>{{round(($o->margin - $o->discount_amount) * ($employee->com_per_whole / 100))}}</td>
                                
                               
                               <?php $t += round(($o->margin - $o->discount_amount) * ($employee->com_per_whole/100) , 2);?>
                            </tr>
                            
                            @endif
                            
                            @endif
                            
                          
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                 <td><b>{{number_format($orderSum)}}</b></td>
                                <td><b>{{number_format($marginSum)}}</b></td>
                                <td><b>{{number_format($t)}}</b></td>
                            </tr>
                    

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
        <div class="card-body" >
            <div class="table-responsive" >
                <h3>Return Orders</h3>
                <table id="myTable4" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                        <th scope="col">Order Amt.</th>
                        <th scope="col">Return Amt.</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum=0;?>
                    
                    @foreach($returnOrders as $o)
                    @php
                        $rate = ($o->customer_id == 1) ? $employee->com_per_retail : $employee->com_per_whole;
                        $com = -round(($o->margin - $o->discount_amount) * ($rate / 100));
                    @endphp
                            <tr>

                                <td>{{$sr++}}</td>
                                <td>{{date('d-m-Y',strtotime($o->return_date ?? $o->created_at))}}</td>
                                
                              <td><a href="{{route('orders.show',$o->id)}}" target="_blank">{{$o->order_no}}</a></td>
                              
                               <td>{{number_format(($o->total_amount ))}}
                               <?php $orderSum += ($o->total_amount);?></td>
                               
                               <td>{{number_format($o->return_amount)}}
                               <?php $marginSum += ($o->return_amount);?></td>
                                <td>{{$com}}</td>
                                
                               
                               <?php $t += $com;?>
                            </tr>
                    @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                 <td><b>{{number_format($orderSum)}}</b></td>
                                <td><b>{{number_format($marginSum)}}</b></td>
                                <td><b>{{number_format($t)}}</b></td>
                            </tr>
                    

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
        <div class="card-body" >
            <div class="table-responsive" >
                <h3>Customer Payments</h3>
                <table id="myTable3" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th scope="col">Date</th>
                        <th scope="col">Customer </th>
                        <th scope="col">Received Amount</th>
                        <th scope="col">Payment Method</th>
                        <th scope="col">Created by</th>
                        <th scope="col">Approved By</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($payments as $sP)

                        <tr>

                            <td>{{$sr++}}</td>
                            <td>{{date('d-m-Y',strtotime($sP->date))}}</td>

                            <td>{{$sP->customer ? $sP->customer->first_name : ''}}</td>
                           
                            <td style="text-align: center;">{{number_format($sP->amount)}}</td>
                            <td>
                                @if($sP->payment_method == \App\Models\CustomerPayment::CASH)
                                    <span class="badge rounded-pill  alert-success">
                                        CASH
                                </span>
                                @elseif($sP->payment_method == \App\Models\CustomerPayment::BANK_TRANSFER)
                                    <span class="badge rounded-pill  alert-success">
                                        BANK TRANSFER
                                </span>
                                    @elseif($sP->payment_method == \App\Models\CustomerPayment::CHEQUE)
                                        <span class="badge rounded-pill  alert-success">
                                        CHEQUE
                                </span>
                                    @endif
                            </td>
                            <td>{{$sP->createdBy ? $sP->createdBy->name : ''}}</td>
                            <td>{{$sP->approvedBy ? $sP->approvedBy->name : ''}}</td>
                            <td>

                                @if($sP->status == \App\Models\CustomerPayment::APPROVAL_PENDING)
                                    <span class="badge rounded-pill  alert-danger">
                                        APPROVAL PENDING
                                </span>
                                @elseif($sP->status == \App\Models\CustomerPayment::APPROVED)
                                    <span class="badge rounded-pill  alert-success">
                                        APPROVED
                                </span>
                                @endif
                            </td>


                            <td class="text-end">

                                @can('View Customer Payment')
                                    <a href="{{route('customer-payments.show',$sP->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan
                                
                                    <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            @can('Edit Customer Payment')
                                                <a class="dropdown-item" href="{{route('customer-payments.edit',$sP->id)}}">Edit info</a>
                                            @endcan

                                            @can('Delete Customer Payment')
                                                <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('customer-payments.destroy',$sP->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"  type="submit">Delete</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                    <!-- dropdown //end -->
                                
                            </td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
