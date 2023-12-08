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

        .bodycolor{
            background-color: #F0D08F;
        }

        .hi {
            font-weight: bold;
            text-align: center;
        }

        .no {
            font-weight: 0;
        }

        .user_greet {
            color: #222;
        }
        .user_greet h1{
            font-weight: bold;
            margin: 5% 0;
            font-size: 26px;
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

       /* a {
            color: #33b9ff !important;
            text-decoration: none;
            text-decoration: none!important;
        }*/

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
<table width="100%" bgcolor="#d8d8d8" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader">
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
                            <tbody class="bodycolor">
                            <tr>
                                <td>
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidthinner">
                                        <tbody>
                                    
                                        <tr>
                                            <td class="user_greet">
                                                <h1 style="padding-left:5%;padding-right:5%;">Expert Reminder:
                                                </h1>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left:5%;text-align:left;">From:{{$company->name}}</td>
                                            
                                        </tr>
                                        <tr>
                                            <td style="padding-left:5%;text-align:left;">Email:{{$company->email}}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left:5%;text-align:left;">To:{{$expert->email}}</td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left:5%;text-align:left;padding-right:5%;">New Appointment for {{$service->name}} on {{$service->created}}<br/></td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="hi">Hi,&nbsp;&nbsp;{{$expert->name}}</td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left:5%;text-align:left;padding-right:5%;">
                                                <p class="content">
                                                    The following appointment has been created:<br>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left:5%;text-align:left;"><span style="font-weight:bold">Service Type:</span>{{$appointment->service_type}}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left:5%;text-align:left;"><span style="font-weight:bold">Customer’s Name: </span>{{$customer->name}}</td>   
                                        </tr>
                                        <tr>
                                            <td style="padding-left:5%;text-align:left;"><span style="font-weight:bold">Date and Time:</span>{{$appointment->created}}</td>   
                                        </tr>
                                        
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                 
                                       
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left:5%;text-align:left;">
                                                <p class="content">
                                                    Sincerely,<br>
                                                    {{$company->name}}Team 
                                                            
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
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
