<?php
use_helper('opAsset', 'Javascript');
op_smt_use_stylesheet('/opDiaryPlugin/css/smt-diary.css', 'last');
op_smt_use_javascript('/opDiaryPlugin/js/smt_diary_diary_list.js', 'last');

$gadgetTitle = array(
  'list_member' => __('Diaries of %1%', array('%1%' => $member->name)),
  'list_friend' => __('Diaries of %my_friend%'),
  'list' => __('Recently Posted Diaries of All'),
  'list_mine' => __('My Diaries'),
);

echo javascript_tag('
  var target = "'.$target.'";
  var memberId = "'.$id.'";'
);
?>

<script id="diaryEntry" type="text/x-jquery-tmpl">
<div class="row entry">

  <div class="span3 member-information">
    <span class="image">
      <a href="${member.profile_url}"><img src="${member.profile_image}" class="rad10"></a>
    </span>
    <br />
    <span class="member-name">
      <a href="${member.profile_url}">${member.name}</a>
    </span>
  </div>

  <div class="span9 diary-information">
    <span class="title">${title}</span>
    <span class="ago">${$item.getCreatedAt()}</span>
    <div class="clearfix"></div>
    <span class="body">{{html body_short}}</span>
    <span class="view"><a href="<?php echo public_path('diary') ?>/${id}" class="readmore"><?php echo __('View this diary') ?></a></span>
  </div>
  <div class="clearfix"></div>
</div>

</script>

<script type="text/javascript">
$(function() {
  var diaryList = new DiaryList(target, memberId);
  diaryList.update();

  diaryList.view.loadmore.click( function() {
    diaryList.view.loading.show();
    diaryList.view.loadmore.hide();
    diaryList.update();
  });
});
</script>

<div class="row">
  <div class="gadget_header span12"><?php echo $gadgetTitle[$target]; ?></div>
</div>
<?php if ('list_mine' == $target): ?>
<div class="row">
  <?php echo link_to(__('Post a diary'), '@diary_new', array('class' => 'btn span11')) ?>
</div>
<?php endif; ?>
<div id="list"></div>
<div class="row hide" id="noEntry">
  <div class="center span12"><?php echo __('There are no diaries.') ?></div>
</div>
<div class="row">
  <div id="loading" class="center">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>
<div class="row">
  <button class="span12 btn small hide" id="loadmore"><?php echo __('More'); ?></button>
</div>
