<?php

include(dirname(__FILE__).'/../../bootstrap/unit.php');
include(dirname(__FILE__).'/../../bootstrap/database.php');

$t = new lime_test(8);

$table = Doctrine::getTable('DiaryCommentUpdate');
$conn = $table->getConnection();


$t->diag('DiaryCommentUpdateTable::getList()');

$member1 = Doctrine::getTable('Member')->find(1);

$t->is($table->getList($member1)->count(), 5, 'count limit');


$t->diag('DiaryCommentUpdateTable::getPager()');

$pager = $table->getPager($member1);
$pager->init();
$t->is(count($pager), 22, 'total count');


$t->diag('Diary visibility test (#1938)');
$conn->beginTransaction();

$t->is($table->getList($member1, false)->count(), 22, 'initial count');

$diary = Doctrine_Core::getTable('Diary')->createQuery('d')
  ->innerJoin('d.DiaryCommentUpdate u WITH u.member_id = ?', $member1->id)
  ->where('d.member_id = 4') // not friend
  ->fetchOne();

$t->info('DiaryTable::PUBLIC_FLAG_OPEN');

$diary->public_flag = DiaryTable::PUBLIC_FLAG_OPEN;
$diary->save($conn);
$t->is($table->getList($member1, false)->count(), 22, 'visible');

$t->info('DiaryTable::PUBLIC_FLAG_SNS');

$diary->public_flag = DiaryTable::PUBLIC_FLAG_SNS;
$diary->save($conn);
$t->is($table->getList($member1, false)->count(), 22, 'visible');

$t->info('DiaryTable::PUBLIC_FLAG_FRIEND');

$diary->public_flag = DiaryTable::PUBLIC_FLAG_FRIEND;
$diary->save($conn);
$t->is($table->getList($member1, false)->count(), 21, 'not visible when author is not friend');

Doctrine_Core::getTable('MemberRelationship')->create(array(
  'member_id_from' => $diary->member_id,
  'member_id_to' => $member1->id,
))->setFriend();

$t->is($table->getList($member1, false)->count(), 22, 'visible when author is friend');

$t->info('DiaryTable::PUBLIC_FLAG_PRIVATE');

$diary->public_flag = DiaryTable::PUBLIC_FLAG_PRIVATE;
$diary->save($conn);
$t->is($table->getList($member1, false)->count(), 21, 'not visible');

$conn->rollback();
