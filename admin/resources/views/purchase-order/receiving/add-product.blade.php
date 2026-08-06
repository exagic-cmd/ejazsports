
            <tr id="r{{$randomNumber}}" style="background-color: #ffdada;">
                <td>#</td>
                
                <td>
                    <select @class('form-control select2') name="product[]">
                       
                           
                            
                            <option value="{{$product->id}}"><b>{{$product->title}}</b></option>
                                
                            
                                
                               
                       
                    </select>
                </td>
                <td>
                    
                     <select @class('form-control select2') name="variant[]">
                       
                         
                            
                          @if(count($product->variants) > 0)
                            
                            @foreach($product->variants as $v)
                                <option value="{{$v->id}}"> {{$v->shade}} - {{$v->size}}</option>
                                @endforeach
                                
                                
                                @else
                                 <option value="0"></option>
                                
                                
                                @endif
                             
                                
                              
                       
                    </select>
                </td>
              
                

                
                <td><input type="number" @class('form-control') onkeyup="updateDiff({{$randomNumber}})" name="r_qty[]" id="r_qty{{$randomNumber}}" value="0">
                           
                        </td>
                        
                        
                         <td><button class="form-control btn btn-primary" type="button" onclick="addQuantity({{$randomNumber}})" >Add</button>
                            <br> <span>Received : <span id="add_qty{{$randomNumber}}" >0</span></span><br>
                            <span id="text_qty{{$randomNumber}}" ></span>
                        </td>

                <input type="hidden" value="0" name="po_product[]">
                <input type="hidden" value="0" @class('form-control qty') name="received_qty[]" id="t_r_qty{{$randomNumber}}">
            </tr>
            
          
