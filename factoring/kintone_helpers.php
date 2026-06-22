<?php
/**
 * 希望金額 → CRM / kintone 用「申込時希望金額」
 * @see https://cybozu.dev/ja/kintone/docs/overview/field-types/#number-field
 */

function company_kibou_kingaku_options()
{
  return array(
    '1000000'  => '50万円〜100万円',
    '3000000'  => '101万円〜300万円',
    '5000000'  => '301万円〜500万円',
    '10000000' => '501万円〜1000万円',
    '20000000' => '1001万円〜2000万円',
    '30000000' => '2001万円〜3000万円',
    '30010000' => '3001万円〜',
  );
}

function company_kibou_kingaku_to_numeric($value)
{
  $value = trim((string)$value);
  if ($value === '') {
    return '';
  }

  $options = company_kibou_kingaku_options();
  if (isset($options[$value])) {
    return $value;
  }

  $labelToNumeric = array_flip($options);
  if (isset($labelToNumeric[$value])) {
    return (string)$labelToNumeric[$value];
  }

  return '';
}

function company_kibou_kingaku_label($numeric)
{
  $options = company_kibou_kingaku_options();
  $numeric = (string)$numeric;
  return isset($options[$numeric]) ? $options[$numeric] : $numeric;
}

/**
 * lp_post_in() / kintone 用 $_REQUEST へ同期（表示は選択項目ラベル）
 */
function company_kintone_sync_request($numericAmount, $uniqid = '')
{
  if ($numericAmount === '' || $numericAmount === null) {
    return;
  }

  $numericAmount = (string)(int)$numericAmount;
  $label = company_kibou_kingaku_label($numericAmount);

  $_POST['申込時希望金額'] = $label;
  $_REQUEST['申込時希望金額'] = $label;
  $_POST['希望金額'] = $label;
  $_REQUEST['希望金額'] = $label;
  $_POST['申込時希望金額_数値'] = $numericAmount;
  $_REQUEST['申込時希望金額_数値'] = $numericAmount;

  if ($uniqid !== '') {
    $_POST['uniqid'] = $uniqid;
    $_REQUEST['uniqid'] = $uniqid;
  }
}
