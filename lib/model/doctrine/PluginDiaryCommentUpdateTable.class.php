<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * PluginDiaryComment
 *
 * @package    opDiaryPlugin
 * @author     Rimpei Ogawa <ogawa@tejimaya.com>
 */
class PluginDiaryCommentUpdateTable extends Doctrine_Table
{
  public function update(Diary $diary, Member $member)
  {
    $object = $this->find(array($diary->id, $member->id));

    if (!$object)
    {
      $object = new DiaryCommentUpdate();
      $object->setDiary($diary);
      $object->setMember($member);
    }

    $now = date('Y-m-d H:i:s');

    $object->setMyLastCommentTime($now);
    $object->setLastCommentTime($now);
    $object->save();

    $this->createQuery()->update()
      ->set('last_comment_time', '?', $now)
      ->where('diary_id = ?', $diary->id)
      ->execute();
  }

  public function getList(Member $member, $limit = 5)
  {
    $q = $this->getQuery($member);

    return $q->limit($limit)->execute();
  }

  public function getPager(Member $member, $page = 1, $size = 20)
  {
    $q = $this->getQuery($member);

    $pager = new sfDoctrinePager('DiaryCommentUpdate', $size);
    $pager->setQuery($q);
    $pager->setPage($page);

    return $pager;
  }

  protected function getQuery(Member $member)
  {
    $query = $this->createQuery('u')
      ->select('u.*, d.*')
      ->innerJoin('u.Diary d')
      ->where('u.member_id = ?', $member->id)
      ->orderBy('last_comment_time DESC');

    // Check public_flag
    $query
      ->leftJoin('d.Member m')
      ->leftJoin('m.MemberRelationship mr WITH mr.member_id_from = ?', $member->id)
      ->andWhere('(d.public_flag = ? OR d.public_flag = ? OR (d.public_flag = ? AND mr.is_friend = true))',
        array(DiaryTable::PUBLIC_FLAG_OPEN, DiaryTable::PUBLIC_FLAG_SNS, DiaryTable::PUBLIC_FLAG_FRIEND));

    return $query;
  }
}
