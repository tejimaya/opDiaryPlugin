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
    $validator = new opValidatorString(array('trim' => true));
    try
    {
      $cleanTitle = $validator->clean($request['title']);
      $cleanBody =  $validator->clean($request['body']);
    }
    catch (sfValidatorError $e)
    {
      $this->forward400Unless(isset($cleanTitle), 'title parameter is not specified.');
      $this->forward400Unless(isset($cleanBody), 'body parameter is not specified.');
    }
    $this->forward400If(!isset($request['public_flag']) || '' === (string)$request['public_flag'], 'public flag is not specified');

    $conn = opDoctrineQuery::getMasterConnection();
    $conn->beginTransaction();

    try
    {
      if(isset($request['id']) && '' !== $request['id'])
      {
        $diary = Doctrine::getTable('Diary')->findOneById($request['id']);
        $this->forward400Unless($diary, 'the specified diary does not exit.');
        $this->forward400Unless($diary->isAuthor($this->member->getId()), 'this diary is not yours.');
      }
      else
      {
        $diary = new Diary();
        $diary->setMemberId($this->member->getId());
      }

      $diary->setTitle($request['title']);
      $diary->setBody($request['body']);
      $diary->setPublicFlag($request['public_flag']);
      $diary->save($conn);

      $this->diary = $diary;

      $maxNum = sfConfig::get('app_diary_max_image_file_num', 3);
      for ($i = 1; $i <= $maxNum; $i++)
      {
        $diaryImage = Doctrine::getTable('DiaryImage')->retrieveByDiaryIdAndNumber($diary->getId(), $i);

        $filename = basename($_FILES['diary_photo_'.$i]['name']);
        if (!is_null($filename) && '' !== $filename)
        {
          try
          {
            $validator = new opValidatorImageFile(array('required' => false));
            $validFile = $validator->clean($_FILES['diary_photo_'.$i]);
          }
          catch (Exception $e)
          {
            $this->forward400($e->getMessage());
          }

          $f = new File();
          $f->setFromValidatedFile($validFile);
          $f->setName(hash('md5', uniqid((string)$i).$filename));
          if ($stream = fopen($_FILES['diary_photo_'.$i]['tmp_name'], 'r'))
          {
            if (!is_null($diaryImage))
            {
              $diaryImage->delete($conn);
            }

            $bin = new FileBin();
            $bin->setBin(stream_get_contents($stream));
            $f->setFileBin($bin);
            $f->save($conn);

            $di = new DiaryImage();
            $di->setDiaryId($diary->getId());
            $di->setFileId($f->getId());
            $di->setNumber($i);
            $di->save($conn);

            $diary->updateHasImages();
          }
          else
          {
            $this->forward400(__('Failed to write file to disk.'));
          }
        }

        $deleteCheck = $request['diary_photo_'.$i.'_photo_delete'];
        if ('on' === $deleteCheck && !is_null($diaryImage))
        {
          $diaryImage->delete($conn);
        }
      }

      $conn->commit();
    }
    catch (Exception $e)
    {
      $conn->rollback();
      throw $e;
    }
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
        $this->forward400If(!$diaryId, 'diary_id is not specified');

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
