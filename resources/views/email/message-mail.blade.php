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

<body
    style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;">

    <table border="0" cellpadding="0" cellspacing="0" height="100%"
        id="m_-6213805131743743389x_30955800m_7214636301576423636m_-8383321887939428501bodyTable"
        style="border-collapse:collapse;margin:0;padding:0;height:100%;width:100%" width="100%">
        <tbody>
            <tr>
                <td align="center"
                    id="m_-6213805131743743389x_30955800m_7214636301576423636m_-8383321887939428501bodyCell"
                    valign="top">
                    <table border="0" cellpadding="0" cellspacing="0" width="630px">
                        <tbody>
                            <tr>
                                <td id="m_-6213805131743743389x_30955800m_7214636301576423636m_-8383321887939428501Logo"
                                    style="padding:20px 5px 10px 33px" valign="top">
                                    <img width="80" alt="bestdreamcar.com Logo"
                                        src="{{asset('/')}}/frontend/assets/images/logos/1732623452.png" />
                                    <br>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="margin:0;padding:0;height:100%;width:100%">
                                    <table border="0" cellpadding="0" cellspacing="0"
                                        style="border-collapse:collapse;margin-bottom:10px;background:#fff;border-radius:4px;border:1px solid #ddd;width:100%">
                                        <tbody>
                                            <tr>
                                                <td align="center"
                                                    style="padding:20px;background:darkcyan;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;color:#fff;text-align:center;font-size:25px;border-top:1px solid #dddddd;border-bottom:1px solid #dddddd;vertical-align:top"
                                                    valign="top">We have a message from bestdreamcar<br></td>
                                            </tr>
                                            <tr>
                                                <td align="center" bgcolor="#F2F2F2"
                                                    style="padding:20px;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;color:#c72931;text-align:center;font-size:18px;border-bottom:1px solid #dddddd;vertical-align:top;font-weight:bold"
                                                    valign="top">
                                                    <p
                                                        style="margin-top:10px;margin-bottom:0px;text-align:center;vertical-align:top;font-weight:normal">
                                                        <span style="color:rgb(87,87,87)"><i><span
                                                                    style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif"><span
                                                                        style="font-size:14px;margin-top:10px;margin-bottom:0px;text-align:center;vertical-align:top;font-weight:normal">Please
                                                                        do not reply to this email, it cannot be
                                                                        delivered.</span></span></i></span><br>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center"
                                                    style="padding:15px 50px 10px 50px;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;color:#575757;text-align:center;font-size:16px;border-top:1px solid #dddddd;vertical-align:top"
                                                    valign="top"><b>Source:</b> <a
                                                        href="{{route('home')}}"
                                                        target="_blank">bestdreamcar.com</a><br>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                        width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td align="left"
                                                                    style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;color:#575757;line-height:150%;margin-bottom:0;padding:30px 50px 10px 50px;font-weight:bold;font-size:22px">
                                                                    Vehicle Information<br></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;color:#575757;font-size:16px;line-height:150%;padding:0px 50px 30px 50px">
                                                                    <b>Year</b>: {{ $data['year'][0] }}<br> <b>Make</b>:
                                                                    {{ $data['make'][0] }}<br>
                                                                    <b>Model</b>: {{ $data['model'][0] }}<br>
                                                                    <b>Color</b>: {{ $data['color'][0] }}<br>
                                                                    <b>Price</b>: ${{ $data['price'] }}<br>
                                                                    <b>Mileage</b>: {{ $data['miles'][0] }}
                                                                    Miles<br> <b>Stock #</b>: {{ $data['stock'] }}<br>
                                                                    {{-- <b>Lot</b>:
                                                                    SKCO Automotive --}}
                                                                </td>
                                                                <td align="right"
                                                                    style="text-align:right;vertical-align:top;padding-right:50px">
                                                                    <img style="width:210px;height:158px;margin:0px 0px 0px 30px"
                                                                        src="{{ asset($data['image'][0]) }}"
                                                                        width="210" height="158"><br>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0 50px">
                                                    <p
                                                        style="line-height:170%;border-bottom:2px solid rgb(234,234,234);margin:0px">
                                                        <span style="color:rgb(103,103,103)"><span
                                                                style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif"><span
                                                                    style="font-size:16px;line-height:170%;border-bottom:2px solid rgb(234,234,234);margin:0px">&nbsp;</span></span></span><br>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                        width="100%">
                                                        <tbody>

                                                            <tr>
                                                                <td align="left"
                                                                    style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;color:#575757;font-size:16px;line-height:150%;margin-bottom:0;padding:0 50px 10px 50px">
                                                                    <p>Dear {{ $data['name'] ?? 'User' }},</p>

                                                                    <p>You have received a new message regarding the car you were interested in.</p>

                                                                    <p>Click the button below to visit our website and log in to your profile to view the message:</p>

                                                                    <p style="text-align: center; margin: 20px 0;">
                                                                        <a href="{{ route('home') }}"
                                                                           style="background-color: #0033cc; color: #ffffff; padding: 12px 24px; border-radius: 5px; display: inline-block; text-decoration: none; font-size: 16px;">
                                                                            View Message
                                                                        </a>
                                                                    </p>

                                                                    <br>
                                                                    If you haven't heard anything within 24 hours,
                                                                    please let us know <a href="{{ route('contact') }}">
                                                                        <br> Contact Us</a></b></span></span></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left"
                                                                    style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;color:#575757;font-size:16px;line-height:150%;margin-bottom:0;padding:0 50px 10px 50px">
                                                                    @if(
                                                                    (isset($data['year'][1]) && $data['year'][1]) ||
                                                                    (isset($data['make'][1]) && $data['make'][1]) ||
                                                                    (isset($data['model'][1]) && $data['model'][1]) ||
                                                                    (isset($data['color'][1]) && $data['color'][1]) ||
                                                                    (isset($data['miles'][1]) && $data['miles'][1]) ||
                                                                    (isset($data['vin']) && $data['vin'])
                                                                    )
                                                                    <p>Trade In:</p>
                                                                    @if(isset($data['year'][1]) && $data['year'][1])
                                                                    <b>Year</b>: {{ $data['year'][1] }}<br>
                                                                    @endif
                                                                    @if(isset($data['make'][1]) && $data['make'][1])
                                                                    <b>Make</b>: {{ $data['make'][1] }}<br>
                                                                    @endif
                                                                    @if(isset($data['model'][1]) && $data['model'][1])
                                                                    <b>Model</b>: {{ $data['model'][1] }}<br>
                                                                    @endif
                                                                    @if(isset($data['color'][1]) && $data['color'][1])
                                                                    <b>Color</b>: {{ $data['color'][1] }}<br>
                                                                    @endif
                                                                    @if(isset($data['miles'][1]) && $data['miles'][1])
                                                                    <b>Mileage</b>: {{ $data['miles'][1] }} Miles<br>
                                                                    @endif
                                                                    @if(isset($data['vin']) && $data['vin'])
                                                                    <b>Vin</b>: {{ $data['vin'] }}<br>
                                                                    @endif
                                                                    @endif


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
                                    style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;color:#676767;font-size:14px;line-height:150%;padding:10px 50px 70px 50px;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;color:#575757;font-size:13px;line-height:170%;text-align:center;margin:0;padding-bottom:20px"
                                    valign="top">© 2023 <a href="{{route('home')}}">bestdreamcar.com</a> <sup>®</sup> All rights reserved.<br></td>
                            </tr>

                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
