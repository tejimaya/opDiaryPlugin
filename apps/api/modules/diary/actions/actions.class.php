<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * diary api actions.
 *
 * @package    OpenPNE
 * @subpackage action
 * @author     Shunsuke Watanabe <watanabe@craftgear.net>
 */
class diaryActions extends opDiaryPluginAPIActions
{
  public function executePost(sfWebRequest $request)
  {
    $conn = opDoctrineQuery::getMasterConnection();
    $conn->beginTransaction();

    try
    {
      $params = $this->getRequestedFormParameter($request);
      $diary = $this->getDiaryObject($this->member->id, $request->getParameter('id'));
      $diary->setTitle($params['title']);
      $diary->setBody($params['body']);
      $diary->setPublicFlag($params['public_flag']);
      $diary->save($conn);

      $oldDiaryImages = $diary->getDiaryImages();

      foreach ($oldDiaryImages as $oldDiaryImage)
      {
        if ($request['diary_photo_'.$oldDiaryImage->number.'_photo_delete'])
        {
          $oldDiaryImage->delete($conn);
          unset($oldDiaryImages[$oldDiaryImage->number]);
        }
      }

      if ($params['image'])
      {
        foreach ($params['image'] as $key => $image)
        {
          $number = substr($key, -1);
          if ($oldDiaryImages[$number])
          {
            throw new opDiaryPluginAPIException('invalid deleteCheck');
          }
          $diaryImage = new DiaryImage();
          $diaryImage->setDiaryId($diary->getId());
          $diaryImage->setFile($image);
          $diaryImage->setNumber($number);
          $diaryImage->save($conn);

          $diary->updateHasImages();
        }
      }

      $conn->commit();
    }
    catch (opDiaryPluginAPIException $e)
    {
      $conn->rollback();
      $this->forward400($e->getMessage());
    }
    catch (Exception $e)
    {
      $conn->rollback();
      throw $e;
    }

    $this->diary = $diary;
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward400If(!$diaryId = $request->getParameter('diary_id'), 'diary_id parameter is not specified');

    $diary = Doctrine::getTable('Diary')->findOneById($diaryId);
    $this->forward400If(false == $diary->isAuthor($this->member->getId()), 'this diary entry is not yours');

    $isDeleted = $diary->delete();

    if ($isDeleted)
    {
      $this->diary = $diary;
    }
    else
    {
      $this->forward400('failed to delete the entry. errorStack:'.$diary->getErrorStackAsString());
    }

  }

  public function executeSearch(sfWebRequest $request)
  {
    try
    {
      if ('diary' === $request->getParameter('target'))
      {
        $diaryId = $request->getParameter('diary_id');
        $this->forward400If(!$diaryId, 'diary_id parameter is not specified');

        $this->memberId = $this->getUser()->getMemberId();
        $this->diary = Doctrine::getTable('Diary')->findOneById($diaryId);

        $this->setTemplate('show');
      }
      else
      {
        $pager = $this->getTargetPager($request, $this->member);
        $this->diaries = $pager->getResults();
        $this->count = $pager->count();
      }
    }
    catch (Exception $e)
    {
      $this->forward400($e->getMessage());
    }
  }
}
