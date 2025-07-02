<script>
    $(document).ready(function() {
        pageLoad();
        $(document).on('change', '#city_sort_search, #state_sort_search', function() {
            pageLoad();
        });

        $(document).on('click', '#search_pointer', function() {
            pageLoad();
        });

        $(document).on('keydown', '#dealer_name', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                pageLoad();
            }
        });


        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];

            console.log(page);
            pageLoad(page);
        });

        //auto list filter start here
        function pageLoad(page = 1) {

            $(document).ready(function() {
                var loaderId = 'loading_dealer';
                var loader =
                    '<div id="loading_dealer" style="position: fixed; width: 100%; height: 100%; display: block; background-color: rgba(218, 233, 232, 0.7); left: 0; top: 0;"></div>';
                if ($('#' + loaderId).length) {
                    $('#' + loaderId).remove();
                }
                $('#main_container').append(loader);
                var screenWidth = window.innerWidth || document.documentElement.clientWidth || document
                    .body
                    .clientWidth;
                var screenHeight = window.innerHeight || document.documentElement.clientHeight ||
                    document.body
                    .clientHeight;

                var vins = @json($vinArray ?? []);

                if (screenWidth < 1000) {
                    // loader;
                    var city = $('#city_sort_search').val();
                    var state = $('#state_sort_search').val();
                    var name = $('#dealer_name').val();
                    $.ajax({
                        url: "{{ route('frontend.find.dealership') }}?page=" + page,
                        type: "get",
                        data: {
                            state: state,
                            city: city,
                            name: name,
                            vins: vins.join(',')
                        },
                        success: function(res) {
                            // $('#total-count').text(res.total_count);

                            $('#main_container').html(res.view);

                        },
                        error: function(error) {

                        }
                    });

                } else {

                    var state = $('#state_sort_search').val();
                    var city = $('#city_sort_search').val();
                    var name = $('#dealer_name').val();
                    // var rangerYearMaxPriceSlider = $('#firstFilterMakeInput').val();
                    $.ajax({
                        url: "{{ route('frontend.find.dealership') }}?page=" + page,
                        type: "get",
                        data: {
                            state: state,
                            city: city,
                            name: name,
                            vins: vins.join(',')
                        },
                        success: function(res) {
                            // console.log(res.select_cities)
                            // var cityInfos = res.select_cities
                            // if(cityInfos.length != 0) {
                            //     // Loop through the array and get the id and city for each item
                            //     cityInfos.forEach(function(cityInfo) {
                            //         var cityInfosId = cityInfo.id;
                            //         var cityInfosCity = cityInfo.city;

                            //         // You can now use these values, for example:
                            //         console.log('City ID: ' + cityInfosId + ', City Name: ' + cityInfosCity);
                            //     });
                            // }
                            $('#main_container').html(res.view);

                        },
                        error: function(error) {

                        }
                    });
                }
            });
        }
    });
</script>
