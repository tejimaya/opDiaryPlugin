<?php
$options = array(
  'target' => 'diaryList',
  'apiTarget' => 'list',
  'max' => $max,
  'title' => __('Recently Posted Diaries of All'),
  'link' => '@diary_list',
);
include_partial('diary/smtDiaryComponent', $options);
