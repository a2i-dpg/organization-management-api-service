<head>
</head>
<body style="background-color:#EEEEEE">
<div>
    <div
        style="text-align:center; mid-width:375px; min-height:50px; padding-left:20px; padding-right:20px; max-width:600px; margin:auto; padding-top:10px">
    </div>
    <div align="center"
         style="background-color:#FFFFFF; padding-left:20px; padding-right:20px; max-width:550px; margin:auto; border-radius:5px; padding-bottom:5px; text-align:left; margin-bottom:40px; width:80%">
        <h2 style="padding-top:25px; min-width:600px; align:center; font-family:Roboto">
            Congratulation!
        </h2>
        <p style="max-width:500px; align:center; font-family:Roboto; padding-bottom:0px; wrap:hard; line-height:25px">
            {{$mailData['message']??" You are approved as industry user.Your credential information is below,"}}
        </p>
        <p style="max-width:500px; align:center; font-family:Roboto-Bold; padding-bottom:0px; wrap:hard">
            Now, complete your payment for membership approval, here is the payment information,
        </p>
        <p style="max-width:500px; align:center; font-family:Roboto-Bold; padding-bottom:0px; wrap:hard">
            Your membership registration Fee: <strong>{{$mailData['application_fee']??""}}TK.</strong>
        </p>

        <p style="max-width:500px; align:center; font-family:Roboto-Bold; padding-bottom:0px; wrap:hard">
            Please pay now pressing the Paynow button....
        </p>
        <br>
        <br>
        <a href="{{$mailData['payment_gateway_url']}}"
           style="width:100px; height:100px; background-color:#3d9b49; font-family:Roboto-Bold; font-color:black; font-weight:2px; padding-top:15px; padding-bottom:15px; padding-left:15%; padding-right:15%; border-radius:30px; text-decoration:none; color:#FFF;">
            Paynow
        </a>
        <br>
        <br>
        <p style="max-width:500px; align:center; font-family:Roboto; padding-bottom:0px; wrap:hard">
            Thank you,
        </p>
        <p style="max-width:500px; align:center; font-family:Roboto; padding-bottom:20px; wrap:hard">
            The Coupa Cafe Team
        </p>
        <hr>
        </hr>
        <p style="max-width:100%; align:center; font-family:Roboto; padding-bottom:10px; wrap:hard; padding-top: 0px; font-size:10px">
            Powered By: <a href="nise.gov.bd">nise.gov.bd</a>
        </p>
    </div>
</div>
</body>
