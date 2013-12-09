<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

function op_diary_link_to_show($diary, $withName = true, $withIcon = true)
{
  $html = '';

  $html .= link_to(op_diary_get_title_and_count($diary), op_diary_url_for_show($diary));

  if ($withName)
  {
    $html .= ' ('.$diary->getMember()->getName().')';
  }

  if ($withIcon)
  {
    $html .= op_diary_image_icon($diary);
  }

  return $html;

}

function op_diary_get_title_and_count($diary, $space = true, $width = 36)
{
  return sprintf('%s%s(%d)',
           op_truncate($diary->getTitle(), $width),
           $space ? ' ' : '',
           $diary->countDiaryComments());
}

function op_diary_image_icon($diary)
{
  $html = '';
  if ($diary->has_images)
  {
    $html = ' '.image_tag('icon_camera.gif', array('alt' => 'photo'));
  }

  return $html;
}

function op_diary_url_for_show($diary)
{
  $internalUri = '@diary_show?id='.$diary->getId();

  if ($count = $diary->countDiaryComments())
  {
    $internalUri .= '&comment_count='.$count;
  }

  return $internalUri;
}

function emojicode_to_image($matches)
{
  $emoji = new OpenPNE_KtaiEmoji_Img();
  return $emoji->get_emoji4emoji_code_id($matches[1]);
}

function op_api_diary_convert_emoji($str)
{
    $pattern = '/\[([ies]:[0-9]{1,3})\]/';
    return preg_replace_callback($pattern, 'emojicode_to_image', $str);
}

function op_api_diary($diary, $option = null)
{
  if($diary)
  {
    //モデルクラス内でsns_termの値が取れずgetPublicFlagLabelでコケるため，緊急処置(see #3502, #3503)
    Doctrine::getTable('SnsTerm')->configure('ja_JP', 'pc_frontend');

    $data = array(
      'id'          => $diary->id,
      'member'      => op_api_member($diary->getMember()),
      'title'       => $diary->title,
      'public_flag' => $diary->getPublicFlagLabel(),
      'created_at'  => $diary->created_at,
    );

    if ('short' == $option)
    {
      $bodyShort = op_truncate(op_decoration($diary->body, true), 60);
      if (mb_strlen($diary->body) >= 60)
      {
        $bodyShort .= '&hellip;';
      }
      $data['body_short'] = op_api_diary_convert_emoji($bodyShort);
    }
    else
    {
      $body = op_auto_link_text(op_decoration($diary->body));
      $data['body'] = nl2br(op_api_diary_convert_emoji($body));
      $images = $diary->getDiaryImages();
      foreach ($images as $image)
      {
        $data['images'][] = op_api_diary_image($image);
      }
    }

    return $data;
  }
}

function op_api_diary_image($image, $size = '120x120')
{
  if($image)
  {
    return array(
      'filename' => sf_image_path($image->getFile()->getName()),
      'imagetag' => image_tag_sf_image($image->getFile()->getName(), array('size' => $size))
    );
  }
}

function op_api_diary_comment($comment)
{
  if($comment)
  {
    $images = array();
    if ($comment->getHasImages())
    {
      foreach($comment->getDiaryCommentImages() as $image)
      {
        $images[] = op_api_diary_image($image);
      }
    }
    return array(
      'id'         => $comment->getId(),
      'diary_id'   => $comment->getDiaryId(),
      'number'     => $comment->getNumber(),
      'member'     => op_api_member($comment->getMember()),
      'body'       => nl2br(op_auto_link_text($comment->getBody())),
      'ago'        => op_format_activity_time(strtotime($comment->getCreatedAt())),
      'created_at' => $comment->getCreatedAt(),
      'images'     => $images
    );
  }
}
