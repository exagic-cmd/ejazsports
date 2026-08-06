@foreach($categoryProducts as $pro)
    <div class="col-xl-2 col-lg-3 col-md-6">
        <div class="card card-product-grid">
            <a href="#" class="img-wrap"> <img src="{{asset('storage/default.jpeg')}}" alt="Product"> </a>
            <div class="info-wrap">
                <a target="_blank" href="{{route('products.show',$pro->id)}}" title="{{$pro->title}}" class="title text-truncate">{{$pro->title}}</a>
                <div class="price mt-1">{{number_format($pro->price)}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge rounded-pill {{($pro->status) ? 'alert-success' : 'alert-danger'}}">{{($pro->status) ? 'Active' : 'InActive'}}</span></div>
                <!-- price-wrap.// -->
            </div>
        </div>
        <!-- card-product  end// -->
    </div>
@endforeach
