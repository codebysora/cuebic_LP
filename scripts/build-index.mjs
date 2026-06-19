import { readFile, writeFile } from 'fs/promises';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const companyIndex = join(root, '..', '..', '..', '..', 'company', 'index.php');

const cuebicHead = `<!doctype html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>初めてファクタリングをご利用される方へ|ファクタリングなら株式会社No.1</title>
  <meta name="keywords" content="ファクタリング.資金調達,株式会社No.1,即日対応">
  <meta name="description" content="初めてファクタリングを利用される方向けのページになります。弊社は業界最低水準の手数料で即日資金調達が可能です。お気軽にお問い合わせください。">
  <link rel="stylesheet" type="text/css" href="assets/css/validationEngine.jquery.css">
  <style>
    body{margin:0;color:#3B3B40;font-family:"Hiragino Sans","Yu Gothic",Meiryo,sans-serif}
    .p-header{position:fixed;top:0;left:0;width:100%;z-index:999;background:#0D2E63;height:min(64px,calc(64/1440*100vw))}
    .p-fv{margin-top:min(64px,calc(64/1440*100vw))}
    .p-fv-info{background-color:#0D2E63}
    @media(max-width:750px){.p-header{height:min(117px,calc(117/750*100vw))}.p-fv-info{margin-top:min(117px,calc(117/750*100vw))}}
  </style>
  <link rel="preload" href="./css/main.min.css" as="style">
  <link rel="preload" href="./img/fv-info.png" as="image" media="(min-width: 751px)" fetchpriority="high">
  <link rel="preload" href="./img/fv-info-sp.png" as="image" media="(max-width: 750px)" fetchpriority="high">
  <link rel="stylesheet" href="./css/main.min.css">

  <script src="assets/js/jquery-3.5.1.min.js"></script>
  <script src="assets/js/languages/jquery.validationEngine-ja.js"></script>
  <script src="assets/js/jquery.validationEngine.js"></script>
  <script src="assets/js/common.js"></script>
  <script src="//statics.a8.net/a8sales/a8sales.js"></script>
  <script src="//statics.a8.net/a8sales/a8crossDomain.js "></script>
  <script type="text/javascript">
    //<![CDATA[(function(){vara8c=document.createElement('script'); a8c.type= 'text/javascript'; a8c.async=true; a8c.src='//statics.a8.net/a8call/a8call.js'; vars=document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(a8c,s); })();//]]>
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const requiredCheckbox = document.getElementById('id-urikake-500k');
      const submitButton = document.querySelector('input[type="submit"]');
      if (!requiredCheckbox || !submitButton) return;
      submitButton.disabled = !requiredCheckbox.checked;
      function updateSubmitButton() {
        submitButton.disabled = !requiredCheckbox.checked;
      }
      requiredCheckbox.addEventListener('change', updateSubmitButton);
      updateSubmitButton();
    });
  </script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PM2FT4JW');</script>
<!-- End Google Tag Manager -->
</head>
`;

let body = (await readFile(companyIndex, 'utf8')).split('</head>')[1];

body = body
  .replace(/GTM-NBB3LPJT/g, 'GTM-PM2FT4JW')
  .replace('<p class="p-header-cta-tel__text">0120-186-054</p>', '<p class="p-header-cta-tel__text">お電話はこちら</p>')
  .replace(/0120-186-054/g, '0120-700-339')
  .replace(
    /<span class="large">0120-700-339<\/span><br>\s*お問い合わせ・ご相談はこちら/g,
    '<span class="large">お電話での</span><br>\n                  お問い合わせ・ご相談はこちら'
  )
  .replace(/name="電話番号"/g, 'name="携帯電話番号"')
  .replace(
    '<form class="p-contact-form" id="contactForm" method="post" action="mail.php">',
    '<form class="p-contact-form" id="contactForm" method="post" action="mail.php">\n          <input type="hidden" id="token" name="token" value="">'
  )
  .replace(/\s*<script src="\.\/js\/load-analytics\.js" defer><\/script>\s*/g, '\n');

await writeFile(join(root, 'index.php'), cuebicHead + body, 'utf8');
console.log('index.php built');
