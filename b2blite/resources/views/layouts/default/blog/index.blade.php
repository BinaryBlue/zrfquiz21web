    <link href="{{ asset('frontend/default/js/owlcarousel/assets/owl.carousel.css')}}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('frontend/default/js/owlcarousel/owl.carousel.min.js') }}"></script>
<!--==========================
  Headline Section
============================-->
@if( $mode =='all')
<section id="headline" class="wow fadeInUp">
  
    <div class="owl-carousel headline-carousel">
      @foreach( $headline as $hl)     
        <div class="headline-item">         
           
          <img src="{{ $hl->image }}" alt="" lass="headline-img" >
          <div class="headline-info">
            <h3> {{ $hl->title }}</h3>
            <h4> {{ $hl->sinopsis }} </h4>
          </div>     
        </div>
      @endforeach
    </div>     
</section><!-- #Headline -->
@else


  <div class="container">   
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item " ><a href="{{ url('') }}"> Home </a></li>
         <li class="breadcrumb-item " ><a href="{{ url('posts') }}"> Posts </a></li>
         <li class="breadcrumb-item " aria-current="page" > {{ $categoryDetail->name }} </li>
      </ol>
    </nav>

    <div class="section-header">
      <h2>  Category : {{ $categoryDetail->name }} </h2>
      
    </div>
  </div>
@endif 


<section id="blog" class="section">
      <!-- Container Starts -->
      <div class="container">  


   
        <!-- Row Starts -->
        <div class="row">  

          



          <div class="col-md-8 ">

            @foreach( $posts as $post)        
            
                <!-- Blog Item Starts -->
                <div class="blog-item-wrapper wow fadeIn animated" >
                    <div class="row">
                        <div class="col-md-4">
                          <div class="blog-item-img">
                            <a href="{{ url('posts/read/'.$post->alias) }}">
                              @if(file_exists('./uploads/images/posts/'.$post->image) && $post->image !='' )
                              <img src="{{ asset('uploads/images/posts/'.$post->image) }}" alt="" class="img-responisve">
                              @else
                              <img src="{{ asset('uploads/images/no-image.png') }}" alt="" class="img-responisve">
                              @endif
                            </a>   
                          </div>
                        </div>
                        
                        <div class="col-md-8">  

                            <div class="blog-item-text">
                                <h3 class="small-title"><a href="{{ url('posts/read/'.$post->alias) }}">{{ $post->title }}</a></h3>
                                <div class="section-tool text-left ">
                                    <i class="fa fa-eye "></i>  <span>  Views (<b> {{ $post->views }} </b>)  </span>   
                                    <i class="fa fa-user "></i>  <span>  {{ ucwords($post->username) }}  </span>   
                                    <i class="icon-calendar3"></i>  <span> {{ date("M j, Y " , strtotime($post->created)) }} </span> 
                                    <i class="fa fa-comment-o "></i>   <span>  {{ $post->comments }} comment(s)  </span> 
                                </div>
                                <p>
                                    Lorem ipsum dolor sit amet, adipisicing elit. Eos rerum dolorum, est voluptatem modi accusantium perspiciatis ...
                                </p>
                                <div class="blog-one-footer text-right">
                                    <a href="{{ url('posts/read/'.$post->alias) }}">Read More   >> </a>             
                                </div>
                            </div>
                        </div>  
                    </div>    
                </div><!-- Blog Item Wrapper Ends-->
              
              @endforeach
          </div>
          
          <div class="col-md-4">
              @include('layouts.default.blog.widget')
          </div>
        

        </div><!-- Row Ends -->
        <div class="row text-center">
        {!!  $posts->links() !!}
        </div>
      </div><!-- Container Ends -->
    </section>



    <script type="text/javascript">
      $(function(){
        $("ul.pagination li a").addClass("page-link")

          // Testimonials carousel (uses the Owl Carousel library)
          $(".headline-carousel").owlCarousel({
            autoplay: true,
            dots: true,
            loop: true,
            responsive: { 0: { items: 1 }, 768: { items: 2 }, 900: { items: 3 } }
          });

      })
    </script>