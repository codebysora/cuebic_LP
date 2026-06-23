<?php
mb_language('Japanese');
mb_internal_encoding('UTF-8');

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$config = [
    'site_label'        => 'ファクポート',
    'admin_subject'     => '【緑ファクポート】新規お申込みがありました',
    'thanks_subject'    => '【ファクポート】お申込みありがとうございます',
    'thanks_brand'      => 'ファクポート',
    'line_url'          => 'https://lin.ee/6p27MUL',
    'mail_from'         => 'support@factor-pay.net',
    'thanks_from'       => 'support@factor-pay.net',
    'reply_to'          => 'support@onfact.jp',
    'mail_from_name'    => 'ファクポート',
    'admin_to'          => 'support@onfact.jp',
];

function post_value($key)
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

function map_business($value)
{
    $map = [
        'kojin' => '個人事業主・フリーランス',
        'hojin' => '法人',
        'p'     => '個人事業主・フリーランス',
        'c'     => '法人',
    ];

    return isset($map[$value]) ? $map[$value] : $value;
}

function build_field_block(array $fields)
{
    $lines = [];
    foreach ($fields as $label => $value) {
        $lines[] = '◆' . $label . '：' . ($value !== '' ? $value : '（未入力）');
    }

    return implode("\n", $lines);
}

function build_admin_body(array $data, array $config)
{
    $fields = [
        'お名前'         => $data['name'],
        '屋号・会社名'   => $data['company'],
        '事業形態'       => $data['business'],
        '買取希望金額'   => $data['amount'],
        'ご希望の入金時期' => $data['due_date'],
        '電話番号'       => $data['tel'],
        'メールアドレス' => $data['email'],
        'ご相談内容'     => $data['message'],
    ];

    $body = "新しいお申込みがありました。下記の内容をご確認ください。\n";
    $body .= "============================================\n";
    $body .= build_field_block($fields) . "\n";
    $body .= "============================================\n";
    $body .= "【受付情報】\n";
    $body .= '申込日時:' . date('Y/m/d (D) H:i:s') . "\n";
    $body .= 'IPアドレス:' . $data['ip'] . "\n";
    $body .= "同意フラグ:同意済み\n";
    $body .= '申込ページURL:' . $data['page_url'] . "\n";
    $body .= 'リファラー:' . $data['referer'] . "\n";

    return $body;
}

function build_thanks_body(array $data, array $config)
{
    $fields = [
        'お名前'         => $data['name'],
        '屋号・会社名'   => $data['company'],
        '事業形態'       => $data['business'],
        '買取希望金額'   => $data['amount'],
        'ご希望の入金時期' => $data['due_date'],
        '電話番号'       => $data['tel'],
        'メールアドレス' => $data['email'],
        'ご相談内容'     => $data['message'],
    ];

    $body = $data['name'] . " 様\n\n";
    $body .= "この度はお申込頂き誠にありがとうございます。\n";
    $body .= "迅速に簡易審査を行いますので、下記必要書類を【ご返信先】まで、ご返信ください。\n";
    $body .= "--------------------------------------------------------------------------------------------------\n";
    $body .= "【必要書類】\n";
    $body .= "①買取をご希望の請求書\n";
    $body .= "②売掛先からの入金がある口座の通帳（口座名義人のページ＋直近3か月分）\n";
    $body .= "③今回不足している買掛金の請求書\n";
    $body .= "④運転免許証（表・裏）\n";
    $body .= "⑤確定申告書（法人の方は決算書）※あるとお手続きがスムーズです\n";
    $body .= "-------------------------------------------------\n";
    $body .= "【ご返信先】\n";
    $body .= "メール  support@onfact.jp\n";
    $body .= 'LINE    ' . $config['line_url'] . "\n";
    $body .= "-------------------------------------------------\n\n";
    $body .= "【契約までの流れ】\n";
    $body .= "①必要書類のご返信\n";
    $body .= "②買取審査/お見積り\n";
    $body .= "③クラウド契約等のご案内/同日お振込み\n\n";
    $body .= "お問い合わせいただきました内容は以下の通りとなります。\n";
    $body .= "============================================\n";
    $body .= build_field_block($fields) . "\n";
    $body .= "============================================\n";
    $body .= "ｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰ\n";
    $body .= '請求書買取の' . $config['thanks_brand'] . "\n";
    $body .= "運営会社：株式会社onfact\n";
    $body .= "東京都品川区西五反田2丁目14-13\n";
    $body .= "NICハイム五反田　2階\n";
    $body .= "TEL：03-6822-6499\n";
    $body .= "メール　support@onfact.jp\n";
    $body .= "ｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰ\n";

    return $body;
}

function send_mail_message($to, $subject, $body, $from, $fromName, $replyTo)
{
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'Content-Transfer-Encoding: 8bit';
    $headers[] = 'From: ' . mb_encode_mimeheader($fromName, 'UTF-8') . ' <' . $from . '>';
    $headers[] = 'Reply-To: ' . $replyTo;
    $headers[] = 'Return-Path: ' . $from;

    $params = '-f' . $from;

    return mb_send_mail($to, $subject, $body, implode("\r\n", $headers), $params);
}

$name = post_value('name');
$tel = post_value('tel');
$email = post_value('email');
$business = map_business(post_value('business'));
$amount = post_value('amount');
$due_date = post_value('due_date');

if ($name === '' || $tel === '' || $email === '' || $business === '' || $amount === '' || $due_date === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email']);
    exit;
}

$page_url = post_value('page_url');
if ($page_url === '') {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    $page_url = $scheme . '://' . $host . preg_replace('#/mail\.php.*$#', '/', $uri);
}

$referer = isset($_SERVER['HTTP_REFERER']) ? trim($_SERVER['HTTP_REFERER']) : '';
if ($referer === '') {
    $referer = $page_url;
}

$data = [
    'name'      => $name,
    'company'   => post_value('company'),
    'tel'       => $tel,
    'email'     => $email,
    'business'  => $business,
    'amount'    => $amount,
    'due_date'  => $due_date,
    'message'   => post_value('message'),
    'page_url'  => $page_url,
    'referer'   => $referer,
    'ip'        => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
];

$admin_ok = send_mail_message(
    $config['admin_to'],
    $config['admin_subject'],
    build_admin_body($data, $config),
    $config['mail_from'],
    $config['mail_from_name'],
    $config['reply_to']
);

$thanks_ok = send_mail_message(
    $data['email'],
    $config['thanks_subject'],
    build_thanks_body($data, $config),
    $config['thanks_from'],
    $config['mail_from_name'],
    $config['reply_to']
);

if ($admin_ok && $thanks_ok) {
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(500);
echo json_encode(['success' => false, 'message' => 'Mail send failed']);
