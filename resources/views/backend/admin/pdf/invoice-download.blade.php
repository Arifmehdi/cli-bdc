<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }

        p {
            margin: 0pt;
        }

        table.items {
            border: 0.1mm solid #e7e7e7;
        }

        td {
            vertical-align: top;
        }

        .items td {
            border-left: 0.1mm solid #e7e7e7;
            border-right: 0.1mm solid #e7e7e7;
        }

        table thead td {

            border: 0.1mm solid #e7e7e7;
        }

        .items td.blanktotal {
            background-color: #EEEEEE;
            border: 0.1mm solid #e7e7e7;
            background-color: #FFFFFF;
            border: 0mm none #e7e7e7;
            border-top: 0.1mm solid #e7e7e7;
            border-right: 0.1mm solid #e7e7e7;
        }

        .items td.totals {
            text-align: right;
            border: 0.1mm solid #e7e7e7;
        }

        .items td.cost {
            text-align: "." center;
        }
    </style>
    <title>Invoice- {{$dataToCompact->generated_id}}</title>

</head>

<body>
    <div class="container-fluid" id="contentToConvert">
        <div class="row">
            <div class="col-md-12">
                <table width="100%" style="font-family: sans-serif;" cellpadding="10" class="p-0">
                    <tr width="100%">

                        <td width="50%" style="font-size: 14px;  margin-top:-45px">

                            <p>Dreambestcar.com</p>
                            <p>8080 Howells Ferry Rd. <br />Semmes, AL 36575</p>


                            <p>Phone: (251) 281-8666</p>
                            <a href="{{ route('home')}}">dreambestcar.com</a>

                        </td>
                        <td width="50%" style="font-size: 14px; float:right ">

                            <h1 style="margin-top:-7px; float:right">INVOICE</h1>
                            <p style="margin-top:-17px; float:right; padding-top:45px; margin-left:7px;">Invoice no:
                                {{$dataToCompact->generated_id}}</p>

                        </td>
                    </tr>


                </table>


                <hr style="height: 1px; background: rgb(179, 179, 179);margin-top:35px; border: none; width:97%">
                <table width="100%" style="font-family: sans-serif; margin-top:40px " cellpadding="10">
                    <tr class="m-0 p-0">
                        <td width="100%" style=" font-size: 14px;">
                            <p style="font-weight: bold; opacity:50%; color: #036a7c;">BILL TO</p>
                            @php
                            $nameBeforeIn = \Illuminate\Support\Str::before($user_info->name, 'in');
                            @endphp
                            <p>{{$nameBeforeIn ?? ''}}</p>
                            <p>{{$user_info->email ?? ''}}</p>
                            <p>
                                @php
                                $mapPosition = strpos($user_info->address, 'Map');
                                if ($mapPosition !== false) {
                                        $mapPosition  = substr($user_info->address, 0, $mapPosition); // Exclude 'Map'
                                    }
                            @endphp
                            {{ trim($mapPosition) ?? '' }}

                            </p>
                            <p>{{$user_info->phone ?? ''}}</p><br />
                        </td>
                        <td width="100%" style="font-size: 14px; padding: 40px;">

                            @php
                                $dateTime = new DateTime(now());
                                // Format the date as "j F Y" (day, month, year)
                                $formattedDate = $dateTime->format('F j, Y');
                            @endphp
                            <p> <span style="font-weight: bold">Invoice Date:</span> {{ $formattedDate }}</p>
                            <p> <span style="font-weight: bold">Amount Due (USD):</span>  ${{ $dataToCompact->total }}</p>

                        </td>

                    </tr>

                </table>
                <table class="items table table-bordered" width="100%" style="font-size: 14px;" cellpadding="8">
                    <thead>
                        <tr style="background-color: #036a7c;color:white;font-weight:bold">
                            <td width="20%" style="text-align: left;"><strong>Details</strong></td>
                            <td width="20%" style="text-align: left;"><strong>Quantity</strong></td>
                            <td width="40%" style="text-align: left;"><strong>Cost</strong></td>
                            <td width="40%" style="text-align: left;"><strong>Amount</strong></td>

                        </tr>
                    </thead>
                    <tbody>


                        <!-- ITEMS HERE -->

                        <tr>

                            <td style="padding:3px 7px; line-height: 20px;">{{$dataToCompact->type}}
                            </td>
                            <td style="padding:3px 7px; line-height: 20px;">{{$dataToCompact->total_count}}
                            </td>
                            <td style="padding:3px 7px; line-height: 20px;">
                                $ {{ $dataToCompact->cost }}


                            </td>
                            <td style="padding:3px 7px; line-height: 20px;">
                                $ {{ $dataToCompact->total }}


                            </td>
                        </tr>
                    </tbody>
                </table>

                <table width="100%"
                    style="font-family: sans-serif; font-size: 14px; margin-left:5px; margin-top:-25px">
                    <tr>
                        <td>
                            <table width="60%" align="left" style="font-family: sans-serif; font-size: 14px;">
                                <tr>
                                    <td style="padding: 0px; line-height: 20px;">&nbsp;</td>
                                </tr>
                            </table>
                            <table width="30%" align="right" style="font-family: sans-serif; font-size: 14px;">
                                <tr>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">
                                        <strong>Subtotal: </strong>
                                    </td>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">
                                        $ {{ $dataToCompact->subtotal }}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">
                                        <strong>Discount :</strong>
                                    </td>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">
                                        {{ $dataToCompact->discount ? $dataToCompact->discount . '%' : '0' }}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">
                                        <strong>Total :</strong>
                                    </td>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">
                                        $ {{ $dataToCompact->total }}</td>
                                </tr>

                                <tr>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">
                                        <strong>Amount Due:</strong>
                                    </td>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">
                                        $ {{ $dataToCompact->total }}</td>

                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>


                <div
                    style="height: 110px;
                width: 30%;
                background-color: #ddd;
                float: right; margin:0 auto;margin-top:20px;border-radius:10px;">

                    <button
                        style="border: none;
                background-color: black;
                color: white;
                margin-top: 19px;
                margin-bottom:17px;
                margin-left: 15%;
                padding: 10px;
                border-radius: 10px;font-weight:bold;margin-bottom:15px">Pay
                        Securely Online</button><br />
                    <img src="{{ asset('dashboard/images/card/visa.jpg') }}" alt="" width="30px"
                        style="margin-left:20px ">
                    <img src="{{ asset('dashboard/images/card/mastercard.png') }}" alt="" width="30px"
                        style="margin-left:2px ">
                    <img src="{{ asset('dashboard/images/card/discover.png') }}" alt="" width="30px"
                        style="margin-left:2px ">
                    <img src="{{ asset('dashboard/images/card/bank-transfer.png') }}" alt="" width="30px"
                        style="margin-left:2px ">
                    <img src="{{ asset('dashboard/images/card/american.png') }}" alt="" width="30px"
                        style="margin-left:2px ">

                </div>
                <div style="display: block;margin-top:200px;margin-bottom:50px;">
                    <p style="font-weight: bold; font-size:17px; margin-bottom:3px;">Notes / Terms</p>
                    <p style="font-size:14px"> Make check payable to 'Dream Best Car' or you can <br />
                        electronically send the payment. ACH info</p>
                    <p style="font-size:14px"> Account name- Dream Best Car</p>
                    <p style="font-size:14px"> Account name- Anryd Enterprises LLC.</p>
                    <p style="font-size:14px"> Bank Name- Wells Fargo, N.A.</p>
                    <p style="font-size:14px"> Account number- 12345678</p>
                    <p style="font-size:14px"> Routing number- 062000080</p>
                    <p style="position:absolute; bottom:0; left:35px;">PS: Please ignore the credit card payment option.
                        (Charges 3.5% credit card processing/transaction fee).</p>
                </div>
                <div style="float: right;  margin-top:-160px;border-radius:10px;">
                    <img src="{{ asset('dashboard/images/signature/ss.webp') }}" alt="" height="90px"
                        width="150px" style="margin-right:20px;">


                </div>



            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                @if (isset($dataToCompact->inventories))
                <div style="height:150px"></div>
                <p>Listing Details: </p>
                    <table class="items table table-bordered margin-top:50px" width="100%" style="font-size: 14px;"
                    cellpadding="8">
                    <thead>
                        <tr style="background-color: #028299;color:white;font-weight:bold">

                            <td width="20%" style="text-align: left;" align="center">
                                <strong>Image</strong>
                            </td>
                            <td width="20%" style="text-align: left;" align="center">
                                <strong>Title</strong>
                            </td>
                            <td width="20%" style="text-align: left;" align="center">
                                <strong>Vin</strong>
                            </td>

                            <td width="20%" style="text-align: left;" align="center">
                                <strong>Price</strong>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataToCompact->inventories as $inventory)
                        @php
                        $image_obj = $inventory->local_img_url;
                        $image_splice = explode(',', $image_obj);
                        $image = trim(str_replace(['[', "'"], '', $image_splice[0]));
                        @endphp
                            <tr>
                                <td style="padding:12px 7px; line-height: 20px;">

                                    @if (!empty($image) && $image != '[]')
                                    <img width="100" height="100" src="{{ asset('frontend/') }}/{{ $image }}"
                                        alt="Used cars for sale: {{ $inventory->title }}, price: {{ $inventory->price }}, VIN: {{ $inventory->vin }} in {{ $inventory->dealer_city }}, {{ $inventory->dealer_state }}, dealer name: {{ $inventory->dealer_name }}. Dream Best Car image"
                                        class="auto-ajax-photo" loading="lazy"
                                        onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';">

                                    @endif

                                </td>
                                <td style="padding:3px 7px; line-height: 20px;">
                                    <p class="mt-2">{{$inventory->title}}</p>
                                </td>
                                <td style="padding: 3px 7px; line-height: 20px;">
                                    <p class="mt-2">{{ $inventory->vin }}</p>
                                </td>
                                <td style="padding: 3px 7px; line-height: 20px;">
                                    <p>{{ $inventory->price_formate }}</p>
                                </td>
                            </tr>

                            @endforeach
                    </tbody>
                </table>

                @endif
            </div>
        </div>
    </div>
</body>

</html>
