<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mailbox</title>
    <style type="text/css">
        body {
            font-family: Helvetica, Arial, Verdana, sans-serif;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
        }

        #backgroundTable {
            margin: 0;
            padding: 0;
            width: 100% !important;
            line-height: 100% !important;
        }

        .user_greet {
            color: #222;
        }
        .user_greet h1{
            font-weight: 100;
            margin: 5% 0;
        }
        .content {
            font-size: 16px;
        }
        .submit_btn{
            background-color: #22beef;
            border: none;
            color: white;
            padding: 10px;
            font-weight: bold;
            font-size: 16px;
        }
        p {
            margin: 0px 0px !important;
        }

        table td {
            border-collapse: collapse;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        a {
            color: #33b9ff !important;
            text-decoration: none;
            text-decoration: none!important;
        }

        @media only screen and (max-width: 480px) {
            table[class=devicewidth] {
                width: 440px!important;
                text-align: center!important;
            }
            table[class=devicewidthmob] {
                width: 420px!important;
                text-align: center!important;
            }
            table[class=devicewidthinner] {
                /*width: 420px!important;*/
                text-align: center!important;
            }
            img[class=banner] {
                width: 440px!important;
                height: 157px!important;
            }
            img[class=col2img] {
                width: 440px!important;
                height: 330px!important;
            }
            table[class="cols3inner"] {
                width: 100px!important;
            }
            table[class="col3img"] {
                width: 131px!important;
            }
            img[class="col3img"] {
                width: 131px!important;
                height: 82px!important;
            }
            table[class='removeMobile'] {
                width: 10px!important;
            }
        }

        @media only screen and (max-width: 480px) {
            table[class=devicewidth] {
                width: 280px!important;
                text-align: center!important;
            }
            table[class=devicewidthmob] {
                width: 260px!important;
                text-align: center!important;
            }
            table[class=devicewidthinner] {
                /*width: 260px!important;*/
                text-align: center!important;
            }
            img[class=banner] {
                width: 280px!important;
                height: 100px!important;
            }
            img[class=col2img] {
                width: 280px!important;
                height: 210px!important;
            }
            table[class="cols3inner"] {
                width: 260px!important;
            }
            img[class="col3img"] {
                width: 280px!important;
                height: 175px!important;
            }
            table[class="col3img"] {
                width: 280px!important;
            }
            td[class="padding-top-right15"] {
                padding: 15px 15px 0 0 !important;
            }
            td[class="padding-right15"] {
                padding-right: 15px !important;
            }
        }
    </style>
</head>

<body background="d8d8d8" style="background:#d8d8d8">
<table width="100%" bgcolor="#dbdbdb" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader">
    <tbody>
    <tr>
        <td>
            <table width="480" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                <tbody>
                <tr>
                    <td width="100%">
                        <table width="480" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                            <tbody>
                            <!-- Spacing -->
                            <tr>
                                <td width="100%" height="50"></td>
                            </tr>
                            <!-- Spacing -->
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader">
    <tbody>
    <tr>
        <td>
            <table width="480" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                <tbody>
                <tr>
                    <td width="100%">
                        <table bgcolor="#ffffff" width="480" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                            <tbody>
                            <tr>
                                <td>
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidthinner">
                                        <tbody>
                                        <tr>
                                            <td style="background-color:#51445F;padding:20% 20% 28% 20%;border-bottom:1px solid #22BEEF">
                                                <img src="{{ $company->logo }}" alt="{{$company->name}}" border="0" width="90%" height="45%" style="display:block; border:none; outline:none; text-decoration:none;margin-left:7%;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="user_greet">
                                                <h1 align="center">Dear <span class="username">{{$customer->name}}</span>!</h1>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6% 10%">
                                                <P class="content">
                                                    We have confirmed you for the following appointment:<br><br>

                                                    Service Type: {{$service->name}}<br>
                                                    Team Member: {{$expert->firstname}} {{$expert->lastname}}<br>
                                                    Date and Time: {{ date('M j, Y ',strtotime($appointment['appointment_date']." ".$appointment['start_time']))."at ".date('g:i A',strtotime($appointment['appointment_date']." ".$appointment['start_time'])) }}<br>
                                                    Timezone: {{$company->Timezone()->first()->timezone_name}}
                                                    <br><br>
                                                    Please note you can make changes to the appointment at any time by visiting our online booking website:<br>
                                                    <br>
                                                    URL: http://pulse247.net/company/frontent/<br>
                                                    Username: {{$customer->email}}<br>
                                                    Password : {{$customerPassword}}<br>
                                                    <br>
                                                    Please don’t hesitate to call us  at {{preg_replace('/(\d{3})(\d{3})(\d{4})/','$1-$2-$3',$company->phone_number)}} if you have any questions.  We look forward to seeing you.
                                                    <br>
                                                    <br>
                                                    Sincerely,<br>
                                                    {{$company->name}} Team
                                                    <br>
                                                    {{$company->address}},
                                                    <br>
                                                    {{$company->city}},
                                                    <br>
                                                    {{$company->State()->first()->state_name}},
                                                    <br>
                                                    {{$company->zipcode}}.

                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td height="50" style="background-color:#E05D6F;text-align:center;color:white">
                                                This email was sent from Pulse 24/7.<br>
                                                (C) Web Apps LLC. All rights reserved.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;background-color:#504c4b">&nbsp;</td>
                                        </tr>
                                        </tbody>
                                    </table>
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
    </tbody>
</table>
</body>

</html>
