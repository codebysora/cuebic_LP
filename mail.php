<?php
	session_start();
	if (!isset($_SESSION["mail_token"])) {
		$_SESSION["mail_token"] = 0;
	} else {
		$_SESSION["mail_token"] = $_SESSION["mail_token"] + 1;
	}

	require_once("./class.phpmailer.php");

	function ToMail($result, $uniqid)
	{
		/*==================================================
  お問合せメール設定
  ==================================================*/

		//▼▼▼▼▼メール設定ここから▼▼▼▼▼
		$from = "info@no1service.co.jp";
		// ☆
		$bcc_array[] = "taniguchi6779@gmail.com"; //同胞BCC
		$bcc_array[] = "web@crossfive.co.jp"; //同胞BCC
        // ☆
		$bcc_array2[] = "devt94260@gmail.com"; //区分のみ送る
		$bcc_array2[] = "yui_uchida@fancs.com"; //区分のみ送る
		$bcc_array2[] = "nao-sakai@cuebic.co.jp"; //区分のみ送る
		$bcc_array2[] = "narimi-ishibashi@cuebic.co.jp"; //区分のみ送る
		$bcc_array2[] = "sa_yamamoto@fancs.com"; //区分のみ送る

        // $bcc_array[] = "tsubono@wss.systems"; //同胞BCC


        $fromname = "株式会社No.1"; //送り主名
		//$fromname=$from;
		$subject = "お問い合わせありがとうございます。"; //メール件名

		$body = $result["お名前"] . " 様\n\n";

		$body .= 'お問い合わせありがとうございました。
下記内容で承りました。';

		$body .= "\n===========================================\n";
		$body .= "◆区分：" . $result["区分"] . "\n";
		$urikake = isset($result["売掛金"]) && $result["売掛金"] !== "" ? $result["売掛金"] : (isset($result["50万円以上の確定売掛金を保有している"]) ? $result["50万円以上の確定売掛金を保有している"] : "");
		$body .= "◆50万円以上の確定売掛金を保有している：" . $urikake . "\n";
		$kibou = isset($result["希望金額"]) && $result["希望金額"] !== "" ? $result["希望金額"] : "未入力";
		$body .= "◆希望金額：" . $kibou . "\n";
		$body .= "◆会社名：" . $result["会社名"] . "\n";
		$body .= "◆お名前：" . $result["お名前"] . "\n";
		$body .= "◆電話番号：" . $result["携帯電話番号"] . "\n";
		// $kote = isset($result["固定電話番号"]) && $result["固定電話番号"] !== "" ? $result["固定電話番号"] : "未入力";
		// $body .= "◆固定電話番号：" . $kote . "\n";

		$email = isset($result["メールアドレス"]) && $result["メールアドレス"] !== "" ? $result["メールアドレス"] : "未入力";
		$body .= "◆メールアドレス：" . $email . "\n";
		$body .= "◆自由記入：\n";
		$naiyo = isset($result["自由記入"]) && $result["自由記入"] !== "" ? $result["自由記入"] : "未入力";
		$body .= $naiyo . "\n";
		$body .= "===========================================\n\n";

		$body .= '内容をご確認させて頂きまして、弊社担当者より改めてご連絡致します。
お待ちいただいても返信がない場合はシステムエラーやメールが届いていない場合がございますので、
お手数ではございますが、0120-700-339（東京支店）もしくは052-414-4107（名古屋支店）までお電話していただくか
再度メールをお送りくださいますようお願いします。
お電話の際にて「HPのお問い合わせの件でお電話しました」とご連絡いただければスムーズでございます。

*****************************************************

株式会社No.1
　　東京支店：〒170-0013　東京都豊島区東池袋1-18-1 Hareza Tower 20F
　　名古屋支店：〒453-0014 名古屋市中村区則武二丁目3番2号 サン・オフィス名古屋3F
　　TEL：0120-700-339（東京）
　　　　 052-414-4107（名古屋）
弊社HP：http://no1service.co.jp

******************************************************';



		//▲▲▲▲▲メール設定ここまで▲▲▲▲▲


		// $to = isset($result["メールアドレス"]) && $result["メールアドレス"] !== NULL ? $result["メールアドレス"] : $from; //宛先
		// ☆
		$to = isset($result["メールアドレス"]) && $result["メールアドレス"] !== NULL ? $result["メールアドレス"] : ""; //宛先


		$subject = $subject; //題名
		$mail = new PHPMailer();

		$mail->CharSet = "iso-2022-jp";
		$mail->Encoding = "7bit";
		$to = str_replace(array("\r", "\n"), '', $to);
		$mail->AddAddress($to);
		$from = str_replace(array("\r", "\n"), '', $from);
		$mail->From = $from;
		//$mail->FromName = mb_encode_mimeheader(mb_convert_encoding(str_replace(array("\r", "\n"), '', $fromname),"JIS","UTF-8"));
		$mail->FromName = mb_encode_mimeheader(str_replace(array("\r", "\n"), '', $fromname), "UTF-8");
		//mb_language("Ja") ;
		//$subject_e = mb_convert_encoding($subject,"euc-jp","UTF-8");
		$subject_e = $subject; //testサーバー用
		$mail->Subject = mb_encode_mimeheader(str_replace(array("\r", "\n"), '', $subject_e), 'ISO-2022-JP');
		$mail->Body  = mb_convert_encoding($body, "JIS", "UTF-8");
		if ($result["メールアドレス"] !== "") {
			$mail->Send(); //メール送信	
		}


		//-----以下管理者用の文言
		$bcc = $from;
		$subject2 = "【A8】【cuebic】【総合ＨＰ】お問い合わせがありました。";
		//mb_language("Ja") ;
		//$subject_ee = mb_convert_encoding($subject2,"euc-jp","UTF-8");
		$subject_ee = $subject2; //testサーバー用

		//$fromname2=$result["お名前"]." 様\n\n";
		$fromname2 = $fromname;
		$from2 = $from;

		$body2 = "下記内容で「総合資金調達・ファクタリング」サイトよりお問い合わせがありました。\n";
		$body2 .= "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "\n\n";

		$body2 .= "===========================================\n";
		$body2 .= "◆識別番号：" . $uniqid . "\n";
		$body2 .= "◆区分：" . $result["区分"] . "\n";
		$body2 .= "◆50万円以上の確定売掛金を保有している：" . $urikake . "\n";
		$body2 .= "◆希望金額：" . $kibou . "\n";
		$body2 .= "◆会社名：" . $result["会社名"] . "\n";
		$body2 .= "◆お名前：" . $result["お名前"] . "\n";
		$body2 .= "◆電話番号：" . $result["携帯電話番号"] . "\n";
		// $body2 .= "◆固定電話番号：" . $kote . "\n";
		$body2 .= "◆メールアドレス：" . $email . "\n";
		$body2 .= "◆自由記入：\n";
		$body2 .= $naiyo . "\n";
		$body2 .= "=========================================== \n\n";

		$mail->ClearAddresses();
		$to2 = str_replace(array("\r", "\n"), '', $bcc);
		$mail->AddAddress($to2);
		$from22 = str_replace(array("\r", "\n"), '', $from2);
		$mail->From = $from22;
		//$mail->FromName = mb_encode_mimeheader(mb_convert_encoding(str_replace(array("\r", "\n"), '', $fromname),"JIS","UTF-8"));
		$mail->FromName = mb_encode_mimeheader(str_replace(array("\r", "\n"), '', $fromname2), "UTF-8");
		$mail->Subject = mb_encode_mimeheader(str_replace(array("\r", "\n"), '', $subject_ee), 'ISO-2022-JP');
		$mail->Body  = mb_convert_encoding($body2, "JIS", "UTF-8");
		$mail->Send(); //メール送信	
		
		//--管理者へメール
		if (count($bcc_array) > 0) {
			foreach ($bcc_array as $bccz) {
				$mail->From = $to2;
				$mail->ClearAddresses();
				$bccz = str_replace(array("\r", "\n"), '', $bccz);
				$mail->AddAddress($bccz);
				$mail->Send();
			}
		}

		//--bcc_array2へのメール送信（識別番号と区分のみ）
		if (count($bcc_array2) > 0) {
			// 簡易版のメール内容を作成
			$subject3 = "【A8】【cuebic】【総合ＨＰ】お問い合わせがありました。";
			$body3 = "お問い合わせがありました。\n\n";
			$body3 .= "===========================================\n";
			$body3 .= "◆識別番号：" . $uniqid . "\n";
			$body3 .= "◆区分：" . $result["区分"] . "\n";
			$body3 .= "===========================================\n";
			
			foreach ($bcc_array2 as $bcc2) {
				$mail->From = $to2;
				$mail->ClearAddresses();
				$bcc2 = str_replace(array("\r", "\n"), '', $bcc2);
				$mail->AddAddress($bcc2);
				$mail->Subject = mb_encode_mimeheader(str_replace(array("\r", "\n"), '', $subject3), 'ISO-2022-JP');
				$mail->Body = mb_convert_encoding($body3, "JIS", "UTF-8");
				$mail->Send();
			}
		}

	}


	if (isset($_POST['token']) && $_POST['token'] !== "" && $_SESSION["mail_token"] <= 4) {
		$posta = array();
		if (is_array($_POST) && count($_POST) > 1) {
			foreach ($_POST as $pk => $pv) {
				$pvv = htmlspecialchars($pv);
				$posta[$pk] = $pvv;
			}
			$uniqid = uniqid();
			ToMail($posta, $uniqid);
			//--TO CRMS
			//require_once($_SERVER['DOCUMENT_ROOT']."/crms/libs/mainlib.php");
			require_once("/home/no1service/no1service.co.jp/public_html/crms/libs/mainlib.php");
			$mainlib = new mainlib;
			$urls = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
			$mainlib->lp_post_in($urls);
			header("Location:./thanks.html");
			$media_all = $mainlib->media_all();
			$url = "https://" . $urls . "/";
			$baitai = array_key_exists($url, $media_all) !== false ? $media_all[$url][0] : "その他";
			$mainlib->kintone_input($mainlib->kintone_validation($url, "A8", $uniqid));
		}
	} else {
	?>
 	<!doctype html>
 	<html>

 	<head>
 		<meta charset="utf-8">
 		<title>メール送信エラー</title>
 		<style>
 			body {
 				background-color: #EEE;
 				font-size: 16px;
 				text-align: center;
 				color: #333;
 			}

 			a {
 				color: #333;
 				text-decoration: none;

 			}

 			h1 {
 				margin: 3em;
 				text-align: center;
 				color: #F00;
 				font-size: 24px;
 			}
 		</style>
 	</head>

 	<body>
 		<h1>メール送信エラーです。<br>（連続の投稿はご遠慮ください）</h1>
 		<p><a href="./">もどる</a></p>
 	</body>

 	</html>
 <?php
	}
	?>
 </body>

 </html>