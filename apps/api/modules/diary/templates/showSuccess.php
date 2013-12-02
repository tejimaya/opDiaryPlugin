<?php
use_helper('opDiary');
$data = array();

if (isset($diary))
{
  $data = op_api_diary($diary);
  $images = $diary->getDiaryImages();
  foreach($images as $image){
    $data['images'][] = op_api_diary_image($image);
  }
  $data['next'] = ($nextDiary = $diary->getNext($memberId)) ? $nextDiary->getId() : null;
  $data['prev'] = ($prevDiary = $diary->getPrevious($memberId)) ? $prevDiary->getId() : null;
  $data['editable'] = $diary->isAuthor($memberId);
}

return array(
  'status' => 'success',
  'data' => $data,
);
