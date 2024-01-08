<?php 

$data = [
  'bulten_no' => '435',
  'bulten_tarihi' => '07.02.2024',
];

$html = '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>

  <style>
        * {
        margin: 0;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        box-sizing: border-box;
        font-size: 14px;
      }

      body {
        width: 100% !important;
        height: 100%;
        line-height: 1.6em;

        background-color: #ecf0f5;
        color: #6c7b88;
        padding: 50px;
      }


      .container {
        width: 50%;
        margin: 0 auto;
        padding: 20px;
        background-color: white;
        margin-top: 20px;
        border-radius: 10px;
      }
    
      .bg-title {
        background-color: #339999;
        height: 90px;
        padding-left: 10px;
        margin: 30px 0;
        display: table;
        width: 100%;
      }

      .bg-title h3 {
        display: table-cell;
        vertical-align: middle;
        font-weight: bold;
        color: white;
        line-height: 45px;
        margin: 0;
        font-size: 22px;
      }
      .bold {
        font-weight: bold;
      }
      
  </style>
</head>
<body>


<bo>

  <div class="container">
    <img src="https://ankarapatent.com/wp-content/uploads/2021/05/cropped-aplogoweb.png" alt="Ankara Patent" style="width: 200px; height: 100px;   margin-left: -16px;"/>

    <div class="bg-title">
      <h3 style="font-weight: bold; color: white; line-height: 45px; margin: 0;">Bülten Bildirimi</h3>
    </div>

     <div>
      <h3 style="font-size: 18px; font-weight: bold;"> Değerli Müvekkillimiz, </h3>
    </div>

    <div style="margin-top: 10px;">
      <p>
        <span class="bold">'.$data['bulten_no'].'.bülten</span>  Apiz gözlem paneline yüklenmiştir.
      </p>
    </div>

    <div style="margin-top: 10px;">
      <p>
        <span class="bold"> '.$data['bulten_no'].'.</span> bülten için talimatlarınızı en geç <span class="bold">'.$data['bulten_tarihi'].'</span> tarihine kadar sistem üzerinden iletmenizi rica ederiz.
      </p>
      </div>

    <div style="margin-top: 10px;">
      <p>
        Saygılarımızla,
      </p>
    </div>
  </div>
  
</body>

</html>

';

return [
    'html' => $html
];