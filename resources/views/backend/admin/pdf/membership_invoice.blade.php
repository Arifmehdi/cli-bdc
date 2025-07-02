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
    <title>Invoice- {{$invoice_id}}</title>
</head>

<body>
    <div class="container-fluid" id="contentToConvert">
        <div class="row">
            <div class="col-md-12">
                <table width="100%" style="font-family: sans-serif;" cellpadding="10" class="p-0">
                    <tr width="100%">
                        <td width="50%" style="font-size: 14px;  margin-top:-45px">
                            <p>BestDreamcar.com</p>
                            <!-- <p>8080 Howells Ferry Rd. <br />Semmes, AL 36575</p> -->
                            <!-- <p>Phone: (251) 281-8666</p> -->
                            <p>[Hide Information]</p>
                            <a href="{{route('home')}}">BestDreamcar.com</a>
                        </td>
                        <td width="50%" style="font-size: 14px; float:right ">
                            <h1 style="margin-top:-7px; float:right">INVOICE</h1>
                            <p style="margin-top:-17px; float:right; padding-top:45px; margin-left:7px;">Invoice no:
                                {{$invoice_id}}
                            </p>
                        </td>
                    </tr>
                </table>

                <hr style="height: 1px; background: rgb(179, 179, 179);margin-top:35px; border: none; width:97%">
                <table width="100%" style="font-family: sans-serif; margin-top:40px " cellpadding="10">
                    <tr class="m-0 p-0">
                        <td width="100%" style=" font-size: 14px;">
                            <p style="font-weight: bold; opacity:50%; color: #036a7c;">BILL TO</p>
                            <p>{{ $username }}</p>
                            <p>{{ $email }}</p>
                            <p>{{ $address }}</p>
                            <p>{{ $phone }}</p>
                        </td>

                        <td width="100%" style="font-size: 14px; padding: 40px;">
                            @php
                            $dateTime = new DateTime(now());
                            // Format the date as "j F Y" (day, month, year)
                            $formattedDate = $dateTime->format('F j, Y');
                            @endphp
                            <p> <span style="font-weight: bold">Invoice Date:</span> {{ $formattedDate }}</p>
                            {{-- <p> <span style="font-weight: bold">Amount Due (USD):</span>  ${{ $invoice->subtotal }}</p> --}}
                        </td>
                    </tr>

                </table>
                <table class="items table table-bordered" width="100%" style="font-size: 14px;" cellpadding="8">
                    <thead>
                        <tr style="background-color: #036a7c;color:white;font-weight:bold">
                            <td width="15%" style="text-align: left;"><strong>S.N</strong></td>
                            <td width="25%" style="text-align: center;"><strong>Details</strong></td>
                            <td width="20%" style="text-align: center;"><strong>Cost</strong></td>
                            <td width="20%" style="text-align: center;"><strong>Amount</strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            {{-- <td style="padding:3px 7px; line-height: 20px;">{{$invoice->inventory->stock}}</td> --}}
                            <td style="padding:3px 7px; line-height: 20px; padding-top:10px"> 01
                            </td>
                            <td style="padding:3px 7px; line-height: 20px; padding:0; margin:0" align="center">
                                {{ $membership_type }} Membership
                                <br>
                                <p style="font-size:9px; margin-top:-5px; color:darkcyan">Previous membership - ({{ $membership_type_old }}) </p>
                            </td>
                            <td align="center">
                                <p> ${{$invoice->cost}}</p>
                            </td>
                            <td style="padding: 3px 7px; line-height: 20px;" align="center"> <input type="text" value="${{$invoice->total}}" disabled class="amount-input"></td>
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
                                        ${{$invoice->subtotal}}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">
                                        <strong>Discount :</strong>
                                    </td>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">
                                        {{ $invoice->discount ? $invoice->discount . '%' : '0' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">
                                        <strong>Current balance : </strong>
                                    </td>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"> ${{$membership_price_old}}</td>
                                </tr>
                                <?php
                                $subtotal = (float) $invoice->subtotal;
                                $membershipPriceOld = (float) $membership_price_old;

                                $subTotal = $subtotal - $membershipPriceOld;
                                if ($subTotal < 0) {
                                    $amountDue = 0;
                                    $creditAvailable = abs($subTotal);
                                } else {
                                    $amountDue = $subTotal;
                                    $creditAvailable = 0;
                                }
                                ?>
                                <tr>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"><strong>Amount Due :</strong></td>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"> $<?php echo $amountDue; ?></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"><strong>Credit Available :</strong></td>
                                    <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"> $<?php echo $creditAvailable; ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- original card image  -->
                {{--<div
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

                </div>--}}
                    <!-- base 64 image  -->
                {{--<div style="height: 110px; width: 30%; background-color: #ddd; float: right; margin: 0 auto; margin-top: 20px; border-radius: 10px;">
                    <button style="border: none; background-color: black; color: white; margin-top: 19px; margin-bottom: 17px; margin-left: 15%; padding: 10px; border-radius: 10px; font-weight: bold; margin-bottom: 15px;">
                        Pay Securely Online
                    </button>
                    <br />
                    @php
                        // Convert all card images to base64
                        $visa = base64_encode(file_get_contents(public_path('dashboard/images/card/visa.jpg')));
                        $mastercard = base64_encode(file_get_contents(public_path('dashboard/images/card/mastercard.png')));
                        $discover = base64_encode(file_get_contents(public_path('dashboard/images/card/discover.png')));
                        $bankTransfer = base64_encode(file_get_contents(public_path('dashboard/images/card/bank-transfer.png')));
                        $american = base64_encode(file_get_contents(public_path('dashboard/images/card/american.png')));
                    @endphp
                    
                    <img src="data:image/jpeg;base64,{{ $visa }}" alt="Visa" width="30px" style="margin-left: 20px;">
                    <img src="data:image/png;base64,{{ $mastercard }}" alt="Mastercard" width="30px" style="margin-left: 2px;">
                    <img src="data:image/png;base64,{{ $discover }}" alt="Discover" width="30px" style="margin-left: 2px;">
                    <img src="data:image/png;base64,{{ $bankTransfer }}" alt="Bank Transfer" width="30px" style="margin-left: 2px;">
                    <img src="data:image/png;base64,{{ $american }}" alt="American Express" width="30px" style="margin-left: 2px;">
                </div>--}}
                <div style="display: block;margin-top:200px;margin-bottom:50px;">
                    <p style="font-weight: bold; font-size:17px; margin-bottom:3px;">Notes / Terms</p>
                    <p style="font-size:14px"> Make check payable to 'Best Dream Car' or you can <br />
                        Electronically send the payment. ACC info</p>
                    <p>['Hidden Information']</p>
                    <!-- <p style="font-size:14px"> Account name- Best Dream Car</p>
                    <p style="font-size:14px"> Account name- Anryd Enterprises LLC.</p>
                    <p style="font-size:14px"> Bank Name- Wells Fargo, N.A.</p>
                    <p style="font-size:14px"> Account number- 12345678</p>
                    <p style="font-size:14px"> Routing number- 062000080</p> -->
                    <p style="position:absolute; bottom:0; left:35px;">PS: Please ignore the credit card payment option.
                        (Charges 3.5% credit card processing/transaction fee).</p>
                </div>
                {{--<div style="float: right;  margin-top:-160px;border-radius:10px;">
                    <img src="{{ asset('dashboard/images/signature/ss.webp') }}" alt="" height="90px"
                        width="150px" style="margin-right:20px;">
                </div>--}}
                <div style="float: right; margin-top: -160px; border-radius: 10px;">
                    @php
                        $signaturePath = public_path('dashboard/images/signature/ss.webp');
                        $signatureBase64 = base64_encode(file_get_contents($signaturePath));
                    @endphp
                    <img src="data:image/webp;base64,{{ $signatureBase64 }}" alt="Signature" height="90px" width="150px" style="margin-right: 20px;">
                </div>
            </div>
        </div>
    </div>
</body>

</html>