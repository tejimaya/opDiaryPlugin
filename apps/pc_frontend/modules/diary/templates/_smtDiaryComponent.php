<?php
use_helper('Javascript', 'opUtil', 'opAsset');
op_smt_use_javascript('/opDiaryPlugin/js/smt_diary_gadget.js', 'last');
?>
<script type="text/javascript">
$(function(){
  var params = {
    apiKey: openpne.apiKey,
    target: "<?php echo $apiTarget ?>",
    limit: "<?php echo $max ?>",
    member_id: "<?php echo $memberId ?>" || undefined,
  };

  var f = new diaryGadget();
  f.search(params, "<?php echo $target ?>", "<?php echo __('There are no diaries.') ?>");
});
</script>

<script id="<?php echo $target ?>Entry" type="text/x-jquery-tmpl">
<div class="row">
  <div class="span3">${$item.getCreatedAt()}</div>
  <div class="span9"><a href="<?php echo public_path('diary') ?>/${id}">${title}</a>(<a href="${member.profile_url}">${member.name}</a>)
  </div>
</div>
</script>

<hr class="toumei" />
<div class="row">
  <div class="gadget_header span12"><?php echo $title ?></div>
</div>
<hr class="toumei" />
<div id="<?php echo $target ?>">
  <div class="loading center hide">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>

<?php if ('list_mine' === $apiTarget): ?>
<div class="row">
<?php echo link_to(__('Post a diary'), '@diary_new', array('style' => 'float:right')) ?>
</div>
<?php endif; ?>

<div class="row hide" id="<?php echo $target ?>Readmore">
<?php echo link_to(__('More'), $link, array('class' => 'btn btn-block span11')) ?>
</div>
