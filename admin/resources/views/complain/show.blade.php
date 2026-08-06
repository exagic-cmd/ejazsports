@extends('layouts.app')

@section('content')


        <div class="content-header" style="justify-content: flex-start;">
            <h2 class="content-title">Ticket # {{$complain->id}} &nbsp;&nbsp;</h2>
            <select name="form-control" style="background: #d8f1e5;" id="status_id" onchange="updateStatus('{{$complain->id}}')">
                @if($complain->status == \App\Models\Complain::PENDING)
                <option value="{{\App\Models\Complain::PENDING}}" selected>Pending</option>
                <option value="{{\App\Models\Complain::InPROGRESS}}" >In Progress</option>
                <option value="{{\App\Models\Complain::RESOLVED}}" >Resolved</option>
                <option value="{{\App\Models\Complain::CANCELED}}" >Canceled</option>
                    @elseif($complain->status == \App\Models\Complain::InPROGRESS)
                    <option value="{{\App\Models\Complain::PENDING}}" disabled >Pending</option>
                    <option value="{{\App\Models\Complain::InPROGRESS}}" selected>In Progress</option>
                    <option value="{{\App\Models\Complain::RESOLVED}}" >Resolved</option>
                    <option value="{{\App\Models\Complain::CANCELED}}" >Canceled</option>
                @elseif($complain->status == \App\Models\Complain::RESOLVED)
                    <option value="{{\App\Models\Complain::PENDING}}" disabled>Pending</option>
                    <option value="{{\App\Models\Complain::InPROGRESS}}" disabled >In Progress</option>
                    <option value="{{\App\Models\Complain::RESOLVED}}" selected>Resolved</option>
                    <option value="{{\App\Models\Complain::CANCELED}}" disabled >Canceled</option>
                @elseif($complain->status == \App\Models\Complain::CANCELED)
                    <option value="{{\App\Models\Complain::PENDING}}" disabled >Pending</option>
                    <option value="{{\App\Models\Complain::InPROGRESS}}" disabled>In Progress</option>
                    <option value="{{\App\Models\Complain::RESOLVED}}" disabled>Resolved</option>
                    <option value="{{\App\Models\Complain::CANCELED}}" selected>Canceled</option>
                    @endif
            </select>


            @if(count($complain->notes))
                <h6 style="margin-left: 5%;">Entertain By : {{$complain->notes[0]->user->name}}</h6>
                @endif

            @if($complain->status == \App\Models\Complain::RESOLVED || $complain->status == \App\Models\Complain::CANCELED)
                <span style="margin-left: 10%;">Close At : <b>{{date('M d , Y h : i A ',strtotime($complain->close_date))}}</b></span>
                @endif

        </div>
        <div class="row">
        <div class="col-lg-8">
        <div class="card mb-4 ">
            <header class="card-header">
                <h4>@if($complain->type == \App\Models\Complain::DAMAGE_PRODUCT)
                        <span class="badge rounded-pill alert-info">Damage Product</span>
                    @elseif($complain->type == \App\Models\Complain::WRONG_PRODUCT)
                        <span class="badge rounded-pill alert-primary">Wrong Product</span>
                    @elseif($complain->type == \App\Models\Complain::MISSING_PRODUCT)
                        <span class="badge rounded-pill alert-danger">Missing Product</span>
                    @elseif($complain->type == \App\Models\Complain::NOT_DELIVERED)
                        <span class="badge rounded-pill alert-success">Not Delivered</span>
                    @endif
                </h4>
            </header>
            <div class="card-body">
                <h5 class="card-title">Description</h5>
                <div class="mt-4">
                    <div class="text-muted font-size-14">
                    {{$complain->detail}}
                    </div>
                </div>
            </div>
        </div>
            <div class="card mb-4">
                <header class="card-header">
                    <h4>Activities  </h4>
                </header>
                <div class="card-body">

                    @foreach($complain->notes as $note)
                        <div class="mt-4">
                            <div class="text-muted font-size-14">
                                <b> <i class="icon material-icons md-timer"></i> {{date('M  d , Y   h : i   A',strtotime($note->created_at))}} - - - - - - - - - - - - BY {{$note->user->name}} </b>
                                <br>
                                <span style="margin-left: 2%;">{{$note->note}}</span>
                            </div>
                        </div>

                        <span style="margin-left: 20%">:</span><br>
                        <span style="margin-left: 20%">:</span><br>

                    @endforeach

                    @if($complain->status == \App\Models\Complain::CANCELED || $complain->status == \App\Models\Complain::RESOLVED)
                        <div class="mt-4">
                            <div class="text-muted font-size-14">
                                <textarea class="form-control" disabled style="cursor: not-allowed" name="note" id="note" placeholder="Add Note here..."></textarea><br>
                                <button class="btn btn-primary btn-sm" style="cursor: not-allowed" disabled onclick="addNote('{{$complain->id}}')">Add Note</button>
                            </div>
                        </div>
                        @else
                            <div class="mt-4">
                                <div class="text-muted font-size-14">
                                    <textarea class="form-control" name="note" id="note" placeholder="Add Note here..."></textarea><br>
                                    <button class="btn btn-primary btn-sm" onclick="addNote('{{$complain->id}}')">Add Note</button>
                                </div>
                            </div>
                    @endif
                </div>


            </div>
        </div>

            <div class="col-lg-4">
        <div class="card mb-4">
            <header class="card-header">
                <h4>Personal Information</h4>
            </header>
            <div class="card-body">
                <h5 class="card-title">Full Name</h5>
                <div class="">
                    <div class="text-muted font-size-14">
                        {{$complain->name}}
                    </div>
                </div>

                <h5 class="card-title mt-4">Email</h5>
                <div class="">
                    <div class="text-muted font-size-14">
                        {{$complain->email}}
                    </div>
                </div>

                <h5 class="card-title mt-4">Phone Number</h5>
                <div class="">
                    <div class="text-muted font-size-14">
                        {{$complain->phone_number}}
                    </div>
                </div>

                <h5 class="card-title mt-4">Order #</h5>
                <div class="mt-4">
                    <div class="text-muted font-size-14">
                        {{$complain->order_no}} @if($order) &nbsp;&nbsp;&nbsp;
                        <a target="_blank" href="{{route('orders.show',$order->id)}}"><i class="icon material-icons md-exposure"></i> view</a> @endif
                    </div>
                </div>

                <h5 class="card-title mt-4">Created At</h5>
                <div class="mt-4">
                    <div class="text-muted font-size-14">
                        {{date('M d , Y h:i A',strtotime($complain->created_at))}}

                    </div>
                </div>
            </div>


        </div>

                <div class="card mb-4">
                    <header class="card-header">
                        <h4>Attach Documents</h4>
                    </header>
                    <div class="card-body">
                        <?php $sr = 1;?>
                        @foreach($complain->documents as $doc)
                        <h5 class="card-title">{{$sr++}}. &nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="/storage/{{$doc->url}}">click here</a></h5>
                            @endforeach


                    </div>

                </div>
            </div>
        </div>


        <!-- card end// -->

    @stop

@section('js')

    <script>
        function addNote(complain_id) {

            note = $('#note').val();
            if(!note) {
                toastr.error('Please write some note..');
                return false;
            }

            $.confirm({
                title: 'Add Note!',
                content: 'Are you sure you want to do this!',
                buttons: {
                    confirm: function () {
                        $.ajax({
                            url: "{{ route('complain.note.add') }}",
                            type:'GET',
                            data: {complain_id:complain_id,note:note},
                            success: function(data) {
                                if(data.status == true) {
                                    toastr.success('Note Added Successfully.');
                                    setTimeout(function(){
                                        window.location.reload(1);
                                    }, 2000);
                                }
                                else
                                    toastr.error('Something went wrong');

                            }
                        });
                    },
                    cancel: function () {

                    }
                }
            });
        }

        function updateStatus(complain_id) {

            status = $('#status_id').val();
            // if(!note) {
            //     toastr.error('Please write some note..');
            //     return false;
            // }

            $.confirm({
                title: 'Status Update!',
                content: 'Are you sure you want to do this!',
                buttons: {
                    confirm: function () {
                        $.ajax({
                            url: "{{ route('complain.status.change') }}",
                            type:'GET',
                            data: {complain_id:complain_id,status:status},
                            success: function(data) {
                                if(data.status == true) {
                                    toastr.success('Status update Successfully.');
                                    setTimeout(function(){
                                        window.location.reload(1);
                                    }, 2000);
                                }
                                else
                                    toastr.error('Something went wrong');

                            }
                        });
                    },
                    cancel: function () {

                    }
                }
            });
        }
    </script>

    @stop
