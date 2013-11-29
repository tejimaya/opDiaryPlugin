<?php
use_helper('Javascript', 'opUtil', 'opAsset');
op_smt_use_javascript('/opDiaryPlugin/js/smt_diary_gadget.js', 'last');
?>
<script type="text/javascript">
$(function(){
  var params = {
    apiKey: openpne.apiKey,
    target: "<?php echo $target ?>",
    member_id: "<?php echo $member->id ?>",
    limit: "<?php echo $max ?>",
  };

  search(params, 'diaryMember', "<?php echo __('There are no diaries.') ?>");
});
</script>

<script id="diaryMemberEntry" type="text/x-jquery-tmpl">
<div class="row">
  <div class="span3">${$item.getCreatedAt()}</div>
  <div class="span9"><a href="<?php echo public_path('diary') ?>/${id}">${title}</a>
  </div>
</div>
</script>

<hr class="toumei" />
<div class="row">
  <div class="gadget_header span12">
    <?php echo 'list_member' == $target ? __('Diary of %1%', array('%1%' => $member->name)) : __('My Diaries') ?>
  </div>
</div>
<hr class="toumei" />
<div id="diaryMember" style="margin-left: 0px;">
  <div class="loading center hide">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>

<?php if ('list_mine' === $target): ?>
<div class="row">
<?php echo link_to(__('Post a diary'), '@diary_new', array('style' => 'float:right')) ?>
</div>
<?php endif; ?>
<div class="row hide" id="diaryMemberReadmore">
<?php echo link_to(__('More'), '@diary_list_member?id='.$member->id, array('class' => 'btn btn-block span11')) ?>
</div>
