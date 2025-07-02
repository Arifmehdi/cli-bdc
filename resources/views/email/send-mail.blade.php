<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background-color: #ffffff; color: #718096; line-height: 1.4; margin: 0; padding: 0; width: 100%;">

    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="border-collapse: collapse;">
        <tbody>
            <tr>
                <td align="center" valign="top">
                    <table border="0" cellpadding="0" cellspacing="0" width="630px">
                        <tbody>
                            <tr>
                                <td style="padding: 20px 5px 10px 33px;">
                                    <img src="{{ asset('/') }}/frontend/assets/images/logos/1732623452.png"
                                        width="80" alt="bestdreamcar.com Logo" />
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="background: #fff; border-radius: 4px; border: 1px solid #ddd; width: 100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="background: darkcyan; padding: 20px; color: #fff; text-align: center; font-size: 25px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; border-top: 1px solid #dddddd; border-bottom: 1px solid #dddddd;">
                                                    You have a NEW Email Lead!
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" bgcolor="#F2F2F2" style="padding: 20px; color: #c72931; font-size: 18px; font-weight: bold; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; border-bottom: 1px solid #dddddd;">
                                                    <img alt="" height="12" style="padding-right: 5px;" width="12" />Email your customer back in Account Center or the App.
                                                    <p style="margin-top: 10px; margin-bottom: 0; color: #575757; font-size: 14px;">
                                                        <i>Please do not reply to this email, it cannot be delivered.</i>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="padding: 15px 50px 10px; color: #575757; font-size: 16px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                                                    <b>Source:</b> <a href="{{ route('home') }}" target="_blank">bestdreamcar.com</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td style="padding: 30px 50px 10px; font-weight: bold; font-size: 22px; color: #575757; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                                                                    Vehicle Information
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 0px 50px 30px; color: #575757; font-size: 16px; line-height: 1.5; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                                                                    <b>Year</b>: {{$data['year'][0]}}<br>
                                                                    <b>Make</b>: {{$data['make'][0]}}<br>
                                                                    <b>Model</b>: {{$data['model'][0]}}<br>
                                                                    <b>Color</b>: {{$data['color'][0]}}<br>
                                                                    <b>Price</b>: ${{$data['price']}}<br>
                                                                    <b>Mileage</b>: {{$data['miles'][0]}} Miles
                                                                </td>
                                                                <td align="right" style="padding-right: 50px;">
                                                                    @if ($data['image'] != '' && $data['image'] != '[]')
                                                                    <img style="width: 210px; height: 158px;" src="{{ asset('frontend/') }}/{{ $data['image'] }}" alt="Vehicle Image" />
                                                                    @else
                                                                    <img style="width: 210px; height: 158px;" src="{{ asset('frontend/uploads/NotFound.png') }}" alt="Image Not Found" />
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 0 50px;">
                                                    <p style="border-bottom: 2px solid rgb(234, 234, 234); color: rgb(103, 103, 103); font-size: 16px;">&nbsp;</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td style="padding: 30px 50px 10px; font-weight: bold; font-size: 22px; color: #575757; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                                                                    Comments
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 0 50px 10px; color: #575757; font-size: 16px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                                                                    <i>{{$data['email_message']}}</i>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 0 50px 30px; font-size: 16px; color: #575757;">
                                                                    <p>Customer Information:</p>
                                                                    <b>Name</b>: {{$data['customer_name']}}<br>
                                                                    <b>Cell</b>: {{$data['phone']}}<br>
                                                                    <b>E-mail</b>: {{$data['customer_email']}}<br>
                                                                </td>
                                                                <td align="right" style="padding-right: 50px;">
                                                                    @if (
                                                                    (isset($data['year'][1]) && $data['year'][1]) ||
                                                                    (isset($data['make'][1]) && $data['make'][1]) ||
                                                                    (isset($data['model'][1]) && $data['model'][1]) ||
                                                                    (isset($data['color'][1]) && $data['color'][1]) ||
                                                                    (isset($data['miles'][1]) && $data['miles'][1]) ||
                                                                    (isset($data['vin']) && $data['vin'])
                                                                    )
                                                                    <p>Trade In:</p>
                                                                    @if (isset($data['year'][1])) <b>Year</b>: {{ $data['year'][1] }}<br> @endif
                                                                    @if (isset($data['make'][1])) <b>Make</b>: {{ $data['make'][1] }}<br> @endif
                                                                    @if (isset($data['model'][1])) <b>Model</b>: {{ $data['model'][1] }}<br> @endif
                                                                    @if (isset($data['color'][1])) <b>Color</b>: {{ $data['color'][1] }}<br> @endif
                                                                    @if (isset($data['miles'][1])) <b>Mileage</b>: {{ $data['miles'][1] }} Miles<br> @endif
                                                                    @if (isset($data['vin'])) <b>Vin</b>: {{ $data['vin'] }}<br> @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="padding-right: 50px; text-align: justify;">
                                                                    <p style="font-weight: bold; text-align: center;">Your Secret Key : {{ $data['hashkey'] }}</p>
                                                                    <p style="padding:5px;font-weight:bold; text-align:center">Use Hash key and See Your Profile in <a href="{{route('home')}}">bestdreamcar.com</a> Quick check <a href="{{ route('frontend.dealer.login')}}">Click here</a></p>
                                                                </td>
                                                            </tr>




                                                        </tbody>
                                                    </table>

                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center"
                                    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #575757; font-size: 13px; line-height: 170%; padding: 10px 50px 70px 50px; text-align: center; margin: 0; padding-bottom: 20px;"
                                    valign="top">
                                    © 2023 <a href="{{ route('home') }}" style="color: red; text-decoration: none;">bestdreamcar.com</a>
                                    <sup>®</sup> All rights reserved.<br>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
