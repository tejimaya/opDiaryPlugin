<?php
function op_smt_diary_get_public_flag_list($publicFlags, $publicFlag)
{
  $lists = array();
  foreach($publicFlags as $key => $value)
  {
    $tagId = 'diary_public_flag_'.$key;
    $label = label_for($value, $tagId);
    $attr = array(
      'value' => $key,
      'name' => 'public_flag',
      'id' => $tagId,
      'class' => 'input_radio',
      'style' => 'float:left',
      'type' => 'radio'
    );
    if ($publicFlag == $key)
    {
      $attr['checked'] = 'checked';
    }

    $lists[] = '<li>'.content_tag('input', $label, $attr).'</li>';
    $lists[] = '<div class="clearfix"></div>';
  }

  return $lists;
}

function op_smt_diary_get_textarea_buttons()
{
  return array(
    '&nbsp;'.link_to_function(image_tag('/images/deco_op_emoji_docomo.gif'), "$('#diary_body').opEmoji('togglePallet', 'epDocomo')").'&nbsp;',
    link_to_function(image_tag('/images/deco_op_b.gif'), "op_mce_insert_tagname('diary_body', 'op:b')").'&nbsp;',
    link_to_function(image_tag('/images/deco_op_u.gif'), "op_mce_insert_tagname('diary_body', 'op:u')").'&nbsp;',
    link_to_function(image_tag('/images/deco_op_s.gif'), "op_mce_insert_tagname('diary_body', 'op:s')").'&nbsp;',
    link_to_function(image_tag('/images/deco_op_i.gif'), "op_mce_insert_tagname('diary_body', 'op:i')").'&nbsp;',
    link_to_function(image_tag('/images/deco_op_large.gif'), "op_mce_insert_tagname('diary_body', 'op:font', ' size=&quot;5&quot;')").'&nbsp;',
    link_to_function(image_tag('/images/deco_op_small.gif'), "op_mce_insert_tagname('diary_body', 'op:font', ' size=&quot;1&quot;')"),
  );
}

function op_smt_diary_get_post_image_form($diaryImages)
{
  $html = array();
  if (!sfConfig::get('app_diary_is_upload_images'))
  {
    return $html;
  }

  $html[] = '<table class="file_list">';

  $max = sfConfig::get('app_diary_max_image_file_num', 3);
  for ($i = 1; $i <= $max; $i++)
  {
    $tagName = 'diary_photo_'.$i;
    $html[] = '<tr>';
    $label = label_for(__('Photo').$i, $tagName);
    $html[] = content_tag('td', $label, array('class' => 'file_label'));
    $html[] = '<td>';

    if (isset($diaryImages[$i]))
    {
      $diaryImage = op_api_diary_image($diaryImages[$i], '48x48');
      $html[] = content_tag('p', link_to($diaryImage['imagetag'], $diaryImage['filename'], array('rel' => 'lightbox[image]')));
      $html[] = content_tag('input', '', array('type' => 'checkbox', 'name' => $tagName.'_photo_delete', 'id' => $tagName.'_photo_delete'));
      $html[] = label_for('&nbsp;'.__('remove the current photo'), $tagName.'_photo_delete');
    }

    $attr = array(
      'type' => 'file',
      'name' => $tagName,
      'id' => $tagName,
    );
    $html[] = content_tag('input', '', $attr);

    $html[] = '</td></tr>';
  }

  $html[] = '</table>';

  return $html;
}

function op_smt_diary_render($args = array())
{
  foreach ($args as $arg)
  {
    echo $arg;
  }
}

function label_for($text, $for)
{
  $attr = array(
    'for' => $for,
    'class' => 'control-label span12'
  );

  return content_tag('label', $text, $attr);
}
