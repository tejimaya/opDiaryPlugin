<?php
$title = 'list_member' == $target ? __('Diary of %1%', array('%1%' => $member->name)) : __('My Diaries');
$options = array(
  'target' => $target,
  'max' => $max,
  'title' => $title,
  'link' => '@diary_list_member?id='.$member->id,
  'memberId' => $member->id,
);
include_partial('diary/smtDiaryComponent', $options);
