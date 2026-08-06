<div class="modal-dialog" role="document" style="min-width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Product Image Information</h5>
            
        </div>
        <div class="modal-body">
            <div id="invoice" class="card shadow p-4">
                <div class="row">
                            <form method="post" action="{{route('product.image.update')}}" enctype="multipart/form-data" >
        @csrf
      <div class="modal-body" style="height: 250px;">
        <input type="hidden" name="image_id" value="{{$image->id}}">

        
                                                
                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Image<span style="color: red;">*</span></label>
                                                    <div class="col-lg-9">
                                                        <img style="width:100px;height: auto"  src="{{asset('storage/'.$image->url)}}" alt="Product">
                                                        <input type="file" name="image" class="form-control " placeholder="Upload here">
                                                        
                                                    </div>
                                                    <!-- col.// -->
                                                </div>

                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Serial #<span style="color: red;">*</span></label>
                                                    <div class="col-lg-9">
                                                        <input type="numeric" value="{{$image->serial_no}}" name="image_serial_no" class="form-control " placeholder="Type here">
                                                        
                                                    </div>
                                                    <!-- col.// -->
                                                </div>

                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Status <span style="color: red;">*</span></label>
                                                    <div class="col-lg-9">
                                                        <input type="checkbox" {{$image->status ? 'checked' : ''}} name="image_status" value="1" class="form-check-input" >
                                                        
                                                    </div>
                                                    <!-- col.// -->
                                                </div>
                                                
        
        <br><br><br>
      </div>

      <div class="modal-footer" style="margin-top:100px">
        <button type="button" class="btn btn-secondary " id="closePrModal"  onclick="closeModal()">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
      </form>


                </div>

                </div>
                <hr>

            </div>

        </div>
        
    </div>
</div>



