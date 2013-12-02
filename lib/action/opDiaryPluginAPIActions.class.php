<?php
class opDiaryPluginAPIActions extends opJsonApiActions
{
  public function preExecute()
  {
    parent::preExecute();
    $this->member = $this->getUser()->getMember();
  }

  protected function getOptions(sfWebRequest $request)
  {
    return array(
      'page' => $request->getParameter('page') ? $request['page'] : 1,
      'limit' => $request->getParameter('limit') ? $request['limit'] : sfConfig::get('app_json_api_limit', 15),
    );
  }

  protected function getTargetPager(sfWebrequest $request, Member $myMember)
  {
    $target = $request->getParameter('target');
    $options = $this->getOptions($request);
    $table = Doctrine::getTable('Diary');

    switch ($target)
    {
      case 'list' :
        $publicFlag = $this->getUser()->getMember()->getIsActive() ? DiaryTable::PUBLIC_FLAG_SNS : DiaryTable::PUBLIC_FLAG_OPEN;
        $pager = $table->getDiaryPager($options['page'], $options['limit'], $publicFlag);
        break;
      case 'list_mine':
        $pager = $table->getMemberDiaryPager($myMember->id, $options['page'], $options['limit'], $myMember->id);
        break;
      case 'list_member' :
        $memberId = $request->getParameter('member_id');
        if (!$memberId)
        {
          $pager = $table->getMemberDiaryPager($myMember->id, $options['page'], $options['limit'], $myMember->id);
        }
        else
        {
          $pager = $table->getMemberDiaryPager($memberId, $options['page'], $options['limit'], $myMember->id);
        }
        break;
      case 'list_friend' :
        $pager = $table->getFriendDiaryPager($myMember->id, $options['page'], $options['limit']);
        break;
      default:
        throw new Exception('invalid target');
        break;
    }

    $pager->init();

    return $pager;
  }

  protected function getRequestedFormParameter(sfWebRequest $request)
  {
    $form = array(
      'title' => null,
      'body' => null,
      'public_flag' => null,
      'image' => array(),
    );

    try
    {
      $validator = new opValidatorString(array('trim' => true, 'required' => true));
      $form['title'] = $validator->clean($request->getParameter('title'));
      $form['body'] =  $validator->clean($request->getParameter('body'));
    }
    catch (sfValidatorError $e)
    {
      $target = !$form['title'] ? 'title' : 'body';
      throw new opDiaryPluginAPIException('invalid '.$target);
    }
    $form['public_flag'] = $request->getParameter('public_flag');

    if (!$form['public_flag'] || (int)$form['public_flag'] < 1 || (int)$form['public_flag'] > 4)
    {
      throw new opDiaryPluginAPIException('invalid public_flag');
    }

    $form['image'] = $this->getImageFiles($request->getFiles());
    if (count($form['image']) > sfConfig::get('app_diary_max_image_file_num', 3))
    {
      throw new opDiaryPluginAPIException('too many image file');
    }

    return $form;
  }

  protected function getDiaryCommentFormParameter(sfWebRequest $request)
  {
    $form = array(
      'diary_id' => null,
      'body' => null,
      'image' => null,
    );

    $form['diary_id'] = $request->getParameter('diary_id');
    if (!$form['diary_id'])
    {
      throw new opDiaryPluginAPIException('diary_id parameter is not specified.');
    }
    if (!Doctrine::getTable('Diary')->findOneById($form['diary_id']))
    {
      throw new opDiaryPluginAPIException('invalid diary_id');
    }

    try
    {
      $validator = new opValidatorString(array('trim' => true, 'required' => true));
      $form['body'] = $validator->clean($request->getParameter('body'));
    }
    catch (sfValidatorError $e)
    {
      throw new opDiaryPluginAPIException('invalid body');
    }
    $limit = sfConfig::get('app_smt_comment_post_limit');
    if ($limit && mb_strlen($request['body']) > $limit)
    {
      throw new opDiaryPluginAPIException('body parameter is too long');
    }

    $images = $this->getImageFiles($request->getFiles());
    $form['image'] = $images['comment-image'];

    return $form;
  }

  protected function getImageFiles($files)
  {
    $images = array();
    $validImages = array();

    foreach ($files as $key => $file)
    {
      $file['name'] ? $images[$key] = $file : null;
    }

    if (!$images)
    {
      return $images;
    }

    try
    {
      $validator = new opValidatorImageFile(array('required' => false));
      foreach ($images as $key => $image)
      {
        $validImage = $validator->clean($image);

        $f = new File();
        $f->setFromValidatedFile($validImage);

        $validImages[$key] = $f;
      }

      return $validImages;
    }
    catch (sfValidatorError $e)
    {
      throw new opDiaryPluginAPIException($e->getMessage());
    }
  }

  protected function getDiaryObject($memberId, $id = null)
  {
    if($id)
    {
      if (!$diary = Doctrine::getTable('Diary')->findOneById($id))
      {
        throw new opDiaryPluginAPIException('diary does not exist');
      }
      if (!$diary->isAuthor($memberId))
      {
        throw new opDiaryPluginAPIException('this diary is not yours.');
      }
    }
    else
    {
      $diary = new Diary();
      $diary->setMemberId($memberId);
    }

    return $diary;
  }
}
