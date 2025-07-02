<script>
            // add new customer js
$(document).ready(function(){
$('#create_new_customer').on('click',function(){

var isChecked = this.checked;
if(isChecked == true)
{
    $('#create_hidden_button').css({"display":"block"});
}else
{
    $('#create_hidden_button').css('display','none');
}

});


// choose vevhile modal open

    $('#choose_vechile').on('click',function(){
        $('#chose_vechile_modal').modal('show');
    });


    $('.search_query').on('keyup',function(){

        var search_type = $(this).val();
        $.ajax({
        url:"{{ route('admin.vichele.dealer.lead')}}",
        type:"get",
        data:{search:search_type},
        success: function(data){
            var cars = data.cars;
            console.log(cars);
            if(cars.length > 0)
            {
                $('#carShow').html("");
                $.each(cars, function(key,car){
                    var basePath = "{{ asset('frontend/') }}/";
                    var car_image = basePath + car.local_img_url;
                    $('#carShow').append('<div class="row">\
                                                    <div class="col-md-2 mb-3 p-0">\
                                                        <img src="'+ car_image +'"\
                                                            alt="" style="width: 100%">\
                                                    </div>\
                                                    <div class="col-md-8 mb-3 text-left">\
                                                        <span class="text-left " style="font-weight:bold">'+ car.title +'<br/> <span style="color: #bdbaba">  # '+ car.stock +'</span></span><br/>\
                                                        <span class="text-left" style="font-weight:bold;color:red">$ '+ car.price+'</span>\
                                                    </div>\
                                                    <div class="col-md-2 mb-3 p-0 text-right">\
                                                        <button type="button" class="btn text-white select_car" style="background-color: #103a6a" value="'+car.id+'">select</button>\
                                                    </div>\
                                                </div>');
                });

            }else
            {
                $('#carShow').append('<div class="row"><h3>No Car Found! </h3></div>');
            }




        }
        });
        });


        // lead submit route code here

        $('#Lead_submit').on('submit',function(e){
                e.preventDefault();
                $.ajax({
                    url: $(this).attr("action"),
                    method: $(this).attr("method"),
                    data: new FormData(this),
                    processData: false,
                    datatype: JSON,
                    contentType: false,
                    success: function (response) {
                        console.log(response);
                        if (response.error) {
                            $(document).find("div.create_hidden_button").css('display','block');
                            if(response.error.first_name)
                            {
                                $('.invalid-feedback1').text(response.error.first_name);
                            }
                            if(response.error.last_name)
                            {
                                $('.invalid-feedback2').text(response.error.last_name);
                            }
                            if(response.error.email)
                            {
                                $('.invalid-feedback3').text(response.error.email);
                            }
                            if(response.error.phone)
                            {
                                $('.invalid-feedback4').text(response.error.phone);
                            }
                            if(response.error.lead_type)
                            {
                                $('.invalid-feedback8').text(response.error.lead_type);
                            }
                            if(response.error.source)
                            {
                                $('.invalid-feedback9').text(response.error.source);
                            }
                        }

                        if(response.message)
                        {
                            toastr.success(response.message);
                            $('#staticBackdrop').modal('hide');
                            $('#Lead_submit')[0].reset();
                            $('.lead_table').DataTable().draw(false);


                        }



                    }
                });


            });



});

$(document).on('click','.select_car',function(e){
            e.preventDefault();
            var id = $(this).val();
           $.ajax({
            url:"{{ route('admin.select.car')}}",
            type:"post",
            data:{car_id:id},
            success:function(res){
                var basePath = "{{ asset('frontend/') }}/";
                var image = basePath + res.car.local_img_url;
                var select_car = res.car;
               if(select_car)
               {
                $('.selected_car').html("");
                $('.selected_car').append('<div class="col-md-12 mb-3 p-0">\
                                                                            <img src="'+image+'"\
                                                                                alt="" style="width: 30%">\
                                                                                <span class="text-left" style="font-weight:bold">'+select_car.title+'</span><br/>\
                                                                                <a href="javascript:void(0)" type="button" class="remove_car" style="color: rgb(92, 55, 55);text-decoration:underline">Remove Vechile Selection</a>\
                                                                                <input type="hidden" value ="'+select_car.id+'" name="vechile_id" />\
                                                                        </div>');

               }

               $('#chose_vechile_modal').modal('hide');

            }

           });
        });

        $(document).on('click','.remove_car',function(e){
            e.preventDefault();

            $('.selected_car').html("");
            $('.selected_car').append('<span style="font-size: 10px">No Vechile chosen</span>');


        });




</script>
