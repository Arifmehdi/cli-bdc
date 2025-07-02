@extends('frontend.website.layout.app')
@section('meta_description', app('globalSeo')['description'])
@section('meta_keyword', app('globalSeo')['keyword'])
@section('gtm')
    {!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])
@section('og_title', app('globalSeo')['og_title'])
@section('og_description', app('globalSeo')['og_description'])
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])
@section('twitter_title', app('globalSeo')['twitter_title'])
@section('twitter_description', app('globalSeo')['twitter_description'])
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])
@section('title', 'Compare | ' . app('globalSeo')['name'])
@section('content')
    <!--Breadcrumb-->
    <div>
        <div class="bannerimg cover-image bg-background3" data-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white ">
                        <h1 class="com-bar-title">Compare Listing</h1>

                        <ol class="breadcrumb text-center">
                            <li class=""><a class="com-bar-para" style="color:white" href="{{ route('auto') }}">Auto<span
                                        style="margin-left:4px; margin-right:4px;">/</span> </a></li>
                            <li class=""><a class="com-bar-para" style="color:white" href="javascript:void(0);">Compare</a></li>

                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/Breadcrumb-->

    <!--Section-->
    <section class="sptb bg-white">
        <div class="container">
            <div class="row">
                <!-- Middle Content Area -->
                <div class="col-md-12 col-xs-12 col-sm-12">

                   <ul class="accordion row">
                      <li>
                         <h3 class="accordion-title mb-3"><a href="#">Comparision Table </a></h3>
                         <div class="accordion-content">
                            <table class="table table-bordered table-striped compare_table">
                               <tbody class="com-tbody">



                                   {{--<tr>
                                     <td class="compare_side">
                                        Image
                                     </td>
                                     @foreach ($items as $item)
                                       @php
                                        $image_obj = $item->mainInventory->additionalInventory->local_img_url;
                                        $image_splice = explode(',', $image_obj);
                                        $image = str_replace(['[', "'"], '', $image_splice[0]);
                                       @endphp
                                           <td class="compare-res">
                                            @if ($image_obj != '' && $image_obj != '[]')

                                            <img  src="{{ asset($image) }}"
                                                alt="Used cars for sale"
                                                class="compare-photo"/>
                                        @elseif ($image_obj == '[]')
                                            <img width="100%" src="{{ asset('frontend/uploads/NotFound.png') }}"
                                                alt="Used cars for sale coming soon image dream best">
                                        @else
                                            <img width="100%" src="{{ asset('frontend/uploads/NotFound.png') }}"
                                                alt="Used cars for sale coming soon image dream best">
                                        @endif

                                           </td>
                                           @endforeach

                                  </tr>--}}

                                  <tr>
                                       <td class="compare_side">
                                          Image
                                       </td>
                                       @foreach ($items as $item)
                                          @php
                                                $image_obj = $item->mainInventory->additionalInventory->local_img_url ?? null; // Use null coalescing to handle undefined or null
                                                $image = null; // Default value in case of null
                                                
                                                if ($image_obj) {
                                                   $image_splice = explode(',', $image_obj);
                                                   $image = str_replace(['[', "'"], '', $image_splice[0]);
                                                }
                                          @endphp
                                          <td class="compare-res">
                                                @if ($image_obj && $image_obj !== '[]') <!-- Ensure local_img_url is not null or empty -->
                                                   <img src="{{ asset( $image) }}" 
                                                         alt="Used cars for sale" 
                                                         class="compare-photo" />
                                                @else
                                                   <img width="100%" 
                                                         src="{{ asset('frontend/uploads/NotFound.png') }}" 
                                                         alt="Used cars for sale coming soon image dream best" />
                                                @endif
                                          </td>
                                       @endforeach
                                    </tr>
                                  <tr>
                                    <td class="compare_side">
                                      Condition
                                    </td>
                                    @foreach ($items as $item)
                                          <td class="compare-res">
                                          {{ $item->mainInventory->type ?? 'Not Available' }}
                                          {{--{{$item->mainInventory->type ? $item->mainInventory->type : 'null'}}--}}
                                          </td>
                                          @endforeach

                                 </tr>
                                   <tr>
                                     <td class="compare_side">
                                        Title
                                     </td>

                                           @foreach ($items as $item)
                                           <td class="compare-res">
                                               <span class="compare_title">{{$item->mainInventory->title ?? 'No title'}}</span>


                                           </td>
                                           @endforeach

                                  </tr>
                                   <tr>
                                     <td class="compare_side">
                                        Price
                                     </td>

                                           @foreach ($items as $item)
                                           <td class="compare-res">
                                           {{$item->mainInventory->price_formate ??  'No Price formate'}}
                                           </td>
                                           @endforeach

                                  </tr>
                                   <tr>
                                     <td class="compare_side">
                                       Mileage
                                     </td>

                                           @foreach ($items as $item)
                                           <td class="compare-res">
                                            {{ $item->mainInventory->miles ??  'No mileage' }}

                                           </td>
                                           @endforeach

                                  </tr>
                                  <tr>
                                   <td class="compare_side">
                                     Year
                                   </td>
                                   @foreach ($items as $item)
                                         <td class="compare-res">
                                         {{$item->mainInventory->year ?? 'No Year'}}
                                         </td>
                                         @endforeach

                                </tr>
                                  <tr>
                                   <td class="compare_side">
                                    Make
                                   </td>
                                   @foreach ($items as $item)
                                   <td class="compare-res">
                                   {{$item->mainInventory->make ?? 'No make' }}
                                   </td>
                                   @endforeach

                                </tr>
                                  <tr>
                                   <td class="compare_side">
                                    Model
                                   </td>
                                   @foreach ($items as $item)
                                   <td class="compare-res">
                                   {{$item->mainInventory->model ?? 'No model'}}
                                   </td>
                                   @endforeach

                                </tr>
                                  <tr>
                                   <td class="compare_side">
                                    Trim
                                   </td>
                                   @foreach ($items as $item)
                                   <td class="compare-res">
                                   {{$item->mainInventory->trim ?? 'No trim'}}
                                   </td>
                                   @endforeach

                                </tr>

                                <tr>
                                   <td class="compare_side">
                                     Body Style
                                   </td>
                                   @foreach ($items as $item)
                                         <td class="compare-res">
                                         {{$item->mainInventory->body_formated ?? 'No body formatted' }}
                                         </td>
                                         @endforeach

                                </tr>
                                  <tr>
                                     <td class="compare_side">
                                        Engine Type
                                     </td>
                                     @foreach ($items as $item)
                                           <td class="compare-res">
                                           {{$item->mainInventory->engine_details ?? 'No engine details' }}
                                           </td>
                                           @endforeach

                                  </tr>
                                  <tr>
                                     <td class="compare_side">
                                        Transmission
                                     </td>
                                     @foreach ($items as $item)
                                           <td class="compare-res">
                                           {{$item->mainInventory->transmission ?? 'No transmission'}}
                                           </td>
                                           @endforeach

                                  </tr>




                                  <tr>
                                     <td class="compare_side">
                                       Fuel Type
                                     </td>
                                     @foreach ($items as $item)
                                           <td class="compare-res">
                                           {{$item->mainInventory->fuel ?? 'No fuel'}}
                                           </td>
                                           @endforeach

                                  </tr>
                                  <tr>
                                     <td class="compare_side">
                                       Drive Train
                                     </td>
                                     @foreach ($items as $item)
                                           <td class="compare-res">
                                           {{$item->mainInventory->drive_info ?? 'No drive info'}}
                                           </td>
                                           @endforeach

                                  </tr>

                                  <tr>
                                     <td class="compare_side">
                                       Stock
                                     </td>
                                     @foreach ($items as $item)
                                           <td class="compare-res">
                                           {{$item->mainInventory->stock ?? 'No stock no' }}
                                           </td>
                                           @endforeach

                                  </tr>

                                  <tr>
                                     <td class="compare_side">
                                       Exterior Color
                                     </td>
                                     @foreach ($items as $item)
                                           <td class="compare-res">
                                           {{$item->mainInventory->exterior_color ?? 'No exterior color'}}
                                           </td>
                                           @endforeach

                                  </tr>

                                  <tr>
                                     <td class="compare_side">
                                       MPG City
                                     </td>
                                     @foreach ($items as $item)
                                           <td class="compare-res">
                                           {{$item->mainInventory->mpg_city ?? 'No mpg City'}}
                                           </td>
                                           @endforeach

                                  </tr>
                                  <tr>
                                     <td class="compare_side">
                                       MPG Hwy
                                     </td>
                                     @foreach ($items as $item)
                                           <td class="compare-res">
                                           {{$item->mainInventory->mpg_highway ?? 'No mpg highway'}}
                                           </td>
                                           @endforeach

                                  </tr>




                               </tbody>
                            </table>
                         </div>
                      </li>



                   </ul>
                </div>
             </div>
        </div>
    </section>
		<!--Section-->



@endsection

@push('js')
    <script>
        $(document).ready(function() {



        });
    </script>
@endpush
