<?php
if ($diary)
{
  $title = __('Edit the diary');
  $diaryId    = $diary->getId();
  $diaryTitle = $diary->getTitle();
  $diaryBody  = $diary->getBody();
  $publicFlag = $diary->getPublicFlag();
  if ($diary->is_open)
  {
    $publicFlag = 4;
  }
}
else
{
  $title = __('Post a diary');
  $diaryId    = '';
  $diaryTitle = '';
  $diaryBody  = '';
  $publicFlag = 1;
}
use_helper('opAsset', 'opDiary', 'Javascript', 'opSmtDiary');
op_smt_use_stylesheet('/opDiaryPlugin/css/smt-diary.css', 'last');
op_smt_use_javascript('jquery-ui.min.js', 'last');
op_smt_use_javascript('op_emoji.js', 'last');
op_smt_use_javascript('Selection.js', 'last');
op_smt_use_javascript('decoration.js', 'last');
op_smt_use_javascript('/opDiaryPlugin/js/smt_diary_functions.js', 'last');
op_smt_use_stylesheet('/opDiaryPlugin/css/lightbox.css', 'last');
op_smt_use_javascript('/opDiaryPlugin/js/lightbox.js', 'last');
?>

<script type="text/javascript">
$(function(){
  $("#diary_body").opEmoji();

  $("#post_diary").click(function()
  {
    toggleSubmitState(['input[name=submit]', '#loading']);
    postDiary( getParams('diary_post') );
  });
})
</script>

<div class="row">
  <div class="gadget_header span12"><?php echo __($title) ?></div>
</div>

<div class="row">
  <div class="span12">
    <div class="error hide"></div>
    <form>
      <input type="hidden" name="id" value="<?php echo $diaryId ?>" />
      <?php echo label_for(__('Title'), 'title') ?>
      <input type="text" name="title" id="title" class="span12" value="<?php echo $diaryTitle ?>" />
      <?php echo label_for(__('Body'), 'diary_body') ?>
      <?php op_smt_diary_render(op_smt_diary_get_textarea_buttons()) ?>
      <textarea name="body" id="diary_body" class="span12" rows="10"><?php echo $diaryBody ?></textarea>
      <?php echo label_for(__('Public flag')) ?>
      <ul class="radio_list">
        <?php op_smt_diary_render(op_smt_diary_get_public_flag_list($publicFlags, $publicFlag)) ?>
      </ul>
      <?php op_smt_diary_render(op_smt_diary_get_post_image_form($diaryImages)) ?>
    </form>
    <div class="center">
      <input type="submit" name="submit" value="<?php echo __('Post') ?>" id="post_diary" class="btn btn-primary span12" />
    </div>
  </div>
  <hr class="toumei">
  <div id="loading" class="center hide">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>
