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
      'limit' => $request->getParameter('limit') ? $request['limit'] : sfConfig::get('op_json_api_limit', 15),
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
}
