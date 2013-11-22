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
class diaryCommentActions extends opDiaryPluginAPIActions
{
  public function executeSearch(sfWebRequest $request)
  {
    $this->forward400If(!$diaryId = $request->getParameter('diary_id'), 'diary_id parameter is not specified.');

    $this->memberId = $this->getUser()->getMemberId();
    $options = $this->getOptions($request);
    $pager = Doctrine::getTable('DiaryComment')->getDiaryCommentPagerForDiary($diaryId, $options['page'], $options['limit']);
    $pager->init();

    $this->count = $pager->count();
    $this->comments = $pager->getResults();
  }

  public function executePost(sfWebRequest $request)
  {
    $this->forward400If('' === (string)$request['diary_id'], 'diary_id parameter is not specified.');
    $this->forward400If('' === (string)$request['body'], 'body parameter is not specified.');

    $diary = Doctrine::getTable('Diary')->findOneById($request['diary_id']);
    $this->forward400If(false === $diary, 'the specified diary does not exist');


    $diaryComment = new DiaryComment();
    $diaryComment->setMemberId($this->member->getId());
    $diaryComment->setDiaryId($request['diary_id']);
    $diaryComment->setBody($request['body']);
    $diaryComment->save();

    $this->memberId = $this->getUser()->getMemberId();
    $this->comment = $diaryComment;
  }

  public function executeDelete(sfWebRequest $request)
  {
    $id = $request['id'];
    $this->forward400If('' === (string)$id, 'id parameter is not specified.');

    $comment = Doctrine::getTable('DiaryComment')->findOneById($id);

    $this->forward400If(false === $comment, 'the comment does not exist. id:'.$id);
    $this->forward400If(false === $comment->isDeletable($this->member->getId()), 'you can not delete this comment. id:'.$id);

    $isDeleted = $comment->delete();
    if ($isDeleted)
    {
      $this->id = $id;
    }
    else
    {
      $this->forward400('failed to delete the comment. errorStack:'.$comment->getErrorStackAsString());
    }
  }

}
