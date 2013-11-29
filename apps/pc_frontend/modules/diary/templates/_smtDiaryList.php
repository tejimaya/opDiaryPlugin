<?php
use_helper('Javascript', 'opUtil', 'opAsset');
op_smt_use_javascript('/opDiaryPlugin/js/smt_diary_gadget.js', 'last');
?>
<script type="text/javascript">
$(function(){
  var params = {
    apiKey: openpne.apiKey,
    target: 'list',
    limit: "<?php echo $max ?>",
  };

  var f = new diaryGadget();
  f.search(params, 'diaryList', "<?php echo __('There are no diaries.') ?>");
});
</script>

<script id="diaryListEntry" type="text/x-jquery-tmpl">
<div class="row">
  <div class="span3">${$item.getCreatedAt()}</div>
  <div class="span9"><a href="<?php echo public_path('diary') ?>/${id}">${title}</a>(<a href="${member.profile_url}">${member.name}</a>)
  </div>
</div>
</script>

<hr class="toumei" />
<div class="row">
  <div class="gadget_header span12"><?php echo __('Recently Posted Diaries of All') ?></div>
</div>
<hr class="toumei" />
<div id="diaryList">
  <div class="loading center hide">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>

<div class="row hide" id="diaryListReadmore">
<?php echo link_to(__('More'), '@diary_list', array('class' => 'btn btn-block span11')) ?>
</div>
