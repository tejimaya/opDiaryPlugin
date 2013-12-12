<?php
use_helper('Javascript', 'opUtil', 'opAsset');
op_smt_use_javascript('/opDiaryPlugin/js/prototype.js', 'last');
op_smt_use_javascript('/opDiaryPlugin/js/smt_diary_gadget.js', 'last');
?>
<script type="text/javascript">
$(function(){
  var f = new DiaryGadget(
    "<?php echo $target ?>",
    "<?php echo $max ?>",
    "<?php echo $memberId ?>",
    "<?php echo __('There are no diaries.') ?>"
  );
  f.view.loading.show();
  f.update();
});
</script>

<script id="diary_<?php echo $target ?>_entry" type="text/x-jquery-tmpl">
<div class="row">
  <div class="span3">${$item.getCreatedAt()}</div>
  <div class="span9"><a href="<?php echo public_path('diary') ?>/${id}">${title}</a>
  <?php if ('list' == $target || 'list_friend' == $target): ?>
  (<a href="${member.profile_url}">${member.name}</a>)
  <?php endif; ?>
  </div>
</div>
</script>

<hr class="toumei" />
<div class="row">
  <div class="gadget_header span12"><?php echo $title ?></div>
</div>
<hr class="toumei" />
<div id="diary_<?php echo $target ?>">
  <div class="loading center hide">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>

<?php if ('list_mine' === $target): ?>
<div class="row">
<?php echo link_to(__('Post a diary'), '@diary_new', array('style' => 'float:right')) ?>
</div>
<?php endif; ?>

<div class="row hide" id="diary_<?php echo $target ?>_readmore">
<?php echo link_to(__('More'), $link, array('class' => 'btn btn-block span11')) ?>
</div>
