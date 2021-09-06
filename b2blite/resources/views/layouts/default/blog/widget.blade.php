
            <div class="widget">
                <div class="widget-title">
                    <h4 class="title"> Popular Articles</h4>
                </div>
                <div class="w-lists">
                  @foreach($popular as $pop)
                    <div class="row mb-4">
                        <div class="col-md-3 image-thumb">
                          <a href="{{ url('posts/read/'.$pop->alias) }}">
                             <img src="{{ $pop->image }}" alt=""  >
                            </a>
                        </div>
                        <div class="col-md-8">
                           <a href="{{ url('posts/read/'.$pop->alias) }}">
                            <h3> {{ $pop->title }} </h3>
                           </a>      
                             <div class="info ">
                                    <i class="fa fa-eye "></i>  <span>  Views (<b> {{ $pop->views }} </b>)  </span>   
                                    
                                    <i class="icon-calendar3"></i>  <span> {{ date("M j, Y " , strtotime($pop->created)) }} </span> 
                                    <i class="fa fa-comment-o "></i>   <span>  0 comment(s)  </span> 
                                </div>

                        </div>
                    </div>    

                  @endforeach

                </div>


            </div>    
             



            <div class="widget">
                <div class="widget-title">
                    <h4 class="title"> Categories </h4>
                </div>

                <ul class="w-list-categories">
                  @foreach($categories as $category)
                  <li class="">
                    <a href="{{ url('posts/category/'.$category->alias ) }}"> {{ $category->name }} ( {{ $category->total }} ) </a>
                  </li>
                  @endforeach
                </ul> 
            </div>

            <div class="widget">
                <div class="widget-title">
                    <h4 class="title"> Tags / Labels </h4>
                </div>

            </div>



           </div>
