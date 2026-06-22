<?php
require_once("./kintone_helpers.php");
require_once("./class.phpmailer.php");

$posta = array();
foreach ($_POST as $pk => $pv) {
  $posta[$pk] = htmlspecialchars($pv);
}

$kibouKingakuNumeric = company_kibou_kingaku_to_numeric(
  $_POST["申込時希望金額"] ?? $_POST["希望金額"] ?? ""
);
if ($kibouKingakuNumeric !== "") {
  $posta["希望金額"] = company_kibou_kingaku_label($kibouKingakuNumeric);
}

function ToMail($result, $uniqid)
{
  /*==================================================
  お問合せメール設定
  ==================================================*/

  //▼▼▼▼▼メール設定ここから▼▼▼▼▼
  $from = "info@" . $_SERVER['HTTP_HOST'];
  $bcc_array[] = "factoring@no1service.co.jp";
  $bcc_array[] = "taniguchi6779@gmail.com";
  $bcc_array[] = "devt94260@gmail.com";

  $fromname = "株式会社No.1"; //送り主名
  $subject = "お問い合わせありがとうございます。"; //メール件名

  $body = $result["お名前"] . "様\n\n";
  $body .= "この度は、お問い合わせいただきありがとうございます。\n";

  $body .= "下記内容で承りました。\n\n";

  $body .= "--------------------\n";
  $body .= "◆区分：" . ($result["区分"] ?? "") . "\n";
  $body .= "◆50万円以上の確定売掛金を保有している：" . ($result["売掛金"] ?? "") . "\n";
  $body .= "◆希望金額：" . ($result["希望金額"] ?? "") . "\n";
  $body .= "◆会社名：" . ($result["会社名"] ?? "") . "\n";
  $body .= "◆お名前：" . ($result["お名前"] ?? "") . "様\n";
  $body .= "◆電話番号：" . ($result["電話番号"] ?? "") . "\n";
  $body .= "◆メールアドレス：" . ($result["メールアドレス"] ?? "") . "\n";
  $body .= "◆自由記入：" . ($result["自由記入"] ?? "") . "\n";
  $body .= "--------------------\n\n";

  $body .= "内容をご確認させて頂きまして、弊社担当者より改めてご連絡致します。\n";
  $body .= "******************************************************\n\n";

  $body .= "株式会社No.1\n";
  $body .= "東京都豊島区東池袋1-18-1 Hareza Tower 20F\n\n";

  $body .= "****************************************************** \n";

  //▲▲▲▲▲メール設定ここまで▲▲▲▲▲

  $re = false;
  $to = isset($result["メールアドレス"]) && $result["メールアドレス"] !== NULL ? $result["メールアドレス"] : $from; //宛先
  $mail = new PHPMailer();

  $mail->CharSet = "iso-2022-jp";
  $mail->Encoding = "7bit";
  $to = str_replace(array("\r", "\n"), '', $to);
  $mail->AddAddress($to);
  $from = str_replace(array("\r", "\n"), '', $from);
  $mail->From = $from;
  $mail->FromName = mb_encode_mimeheader(str_replace(array("\r", "\n"), '', $fromname), "UTF-8");
  $mail->Subject = mb_encode_mimeheader(str_replace(array("\r", "\n"), '', $subject), 'ISO-2022-JP');
  $mail->Body  = mb_convert_encoding($body, "JIS", "UTF-8");
  if ($result["メールアドレス"] !== "") {
    $re = $mail->Send(); //メール送信
  }

  //-----以下管理者用の文言
  $subject2 = "【法人広告】【顕在】お問い合わせがありました。";
  $body2 = "下記内容でお問い合わせがありました。\n\n";

  $body2 .= "--------------------\n";
  $body2 .= $body;

  //--管理者へメール
  if (is_array($bcc_array) && !empty($bcc_array)) {
    foreach ($bcc_array as $bcc) {
      $mail->ClearAddresses();
      $to2 = str_replace(array("\r", "\n"), '', $bcc);
      $mail->AddAddress($to2);
      $from22 = str_replace(array("\r", "\n"), '', $from);
      $mail->From = $from22;
      $mail->FromName = mb_encode_mimeheader(str_replace(array("\r", "\n"), '', $fromname), "UTF-8");
      $mail->Subject = mb_encode_mimeheader(str_replace(array("\r", "\n"), '', $subject2), 'ISO-2022-JP');
      $mail->Body  = mb_convert_encoding($body2, "JIS", "UTF-8");
      $mail->Send(); //メール送信
    }
  }

  return $re;
}

$mailError = false;

if (
  isset($_POST["メールアドレス"]) && $_POST["メールアドレス"] !== "" &&
  isset($_POST["売掛金"]) && $_POST["売掛金"] !== "" &&
  $kibouKingakuNumeric !== ""
) {
  $uniqid = uniqid();
  company_kintone_sync_request($kibouKingakuNumeric, $uniqid);

  $th = ToMail($posta, $uniqid);
  if ($th == true) {
    require_once("/home/no1service/no1service.co.jp/public_html/crms/libs/mainlib.php");
    $mainlib = new mainlib;
    $urls = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
    $mainlib->lp_post_in($urls);

    $url = "https://" . $urls . "/";
    $baitai = "法人広告";
    $mainlib->kintone_input($mainlib->kintone_validation($url, $baitai, $uniqid));

    header("Location: ./thanks.html");
    exit;
  }

  $mailError = true;
} else {
  $mailError = true;
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" type="text/css" href="assets/css/body.css?<?php echo time(); ?>">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" type="text/css" href="assets/css/common.css">
  <style>
    <!--
    body {
      text-align: center;
      background-color: #ECECEC;
    }

    h1 {
      font-size: 3rem;
      margin: 5em;
    }
    -->
  </style>
</head>

<body>
  <?php if ($mailError) : ?>
    <h1>メールを送信できませんでした。</h1>
  <?php endif; ?>
</body>

</html>
