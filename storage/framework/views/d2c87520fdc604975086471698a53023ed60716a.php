<html>
    <head>
    </head>
    <body>
        <div style="position: relative;padding: 10px;width: 100%;max-width:600px;height: 220px;background-image: url('https://www.brf.cathaylandinc.com:8086/images/<?php echo Session::get('Pstatus');?>.png');background-repeat: no-repeat;background-size: cover;">
            <div style="margin-top:70px;margin-left:275px;position:absolute;color:#ffffff; top:20px; right:40px; font-size: 30px; font-family: Comic Sans MS; font-weight:bold;"><b>RFP Number: <span style="text-decoration: underline; font-weight: 900; font-size:30px"><?php echo Session::get('Prfpnum');?></span><br><br></div>
        </div>
        <br>
        <br>
        <button style="border-radius: 10px;color:white;margin-left:55px;background-color:#0db3a6;padding:10px;border-color: transparent;font-size: 15px;cursor:pointer;"><a href='https://www.jodoo.com/f/63ab830a62223e000761ca7b' style="text-decoration:none; color:#ffffff;">Add new request</a></button><br>
    </body>
</html>

