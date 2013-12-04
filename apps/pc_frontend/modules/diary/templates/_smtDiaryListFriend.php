<?php
$options = array(
  'target' => 'diaryListFriend',
  'apiTarget' => 'list_friend',
  'max' => $max,
  'title' => __('Recently Posted Diaries of %my_friend%'),
  'link' => '@diary_list_friend',
);
include_partial('diary/smtDiaryComponent', $options);
