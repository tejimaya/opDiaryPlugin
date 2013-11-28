<?php
if ($diary)
{
  $title = __('Edit the diary');
  $diaryId    = $diary->getId();
  $diaryTitle = $diary->getTitle();
  $diaryBody  = $diary->getBody();
  $publicFlag = $diary->getPublicFlag();
}
else
{
  $title = __('Post a diary');
  $diaryId    = '';
  $diaryTitle = '';
  $diaryBody  = '';
  $publicFlag = 1;
}
use_helper('opAsset', 'opDiary');
op_smt_use_stylesheet('/opDiaryPlugin/css/smt-diary.css', 'last');
op_smt_use_javascript('jquery-ui.min.js', 'last');
op_smt_use_javascript('/opDiaryPlugin/js/jquery.upload-1.0.2.min.js', 'last');
op_smt_use_javascript('op_emoji.js', 'last');
op_smt_use_javascript('Selection.js', 'last');
op_smt_use_javascript('decoration.js', 'last');
op_smt_use_javascript('/opDiaryPlugin/js/smt_diary_functions.js', 'last');
?>

<script type="text/javascript">
function op_get_relative_uri_root()
{
  return "<?php echo $relativeUrlRoot;?>";
}

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
    <input type="hidden" name="id" id="id" value="<?php echo $diaryId ?>"/>
    <label class="control-label span12"><?php echo __('Title') ?></label>
    <input type="text" name="title" id="title" class="span12" value="<?php echo $diaryTitle ?>">
    <label class="control-label span12"><?php echo __('Body') ?></label>
<a id="diary_body_button_op_emoji_docomo" href="#" onclick="$('#diary_body').opEmoji('togglePallet', 'epDocomo'); return false;">
<img alt="" src="/images/deco_op_emoji_docomo.gif" /></a>
    <textarea name="body" id="diary_body" class="span12" rows="10"><?php echo $diaryBody ?></textarea>
    <label class="control-label span12"><?php echo __('Public flag') ?></label>
    <ul class="radio_list">
    <?php foreach($publicFlags as $key=>$value):?>
      <li><input name="public_flag" value="<?php echo $key;?>" id="diary_public_flag_<?php echo $key;?>" class="input_radio" type="radio" <?php if($publicFlag == $key) echo 'checked'?>>&nbsp;<label for="diary_public_flag_<?php echo $key;?>"><?php echo $value;?></label></li>
    <?php endforeach; ?>
    </ul>
    <table class="file_list">
      <?php if (!is_null($diary) && $diary->has_images): ?>
        <?php $diaryImages = array() ?>
        <?php foreach ($diary->getDiaryImages() as $image): ?>
          <?php $diaryImages[$image->getNumber()] = $image ?>
        <?php endforeach; ?>
      <?php endif; ?>
      <?php for ($i = 1; $i <= 3; $i++): ?>
      <tr>
        <td class="file_label"><label for="diary_photo_<?php echo $i ?>"><?php echo __('Photo').$i ?></label></td>
        <td>
          <?php if (isset($diaryImages[$i])): ?>
          <?php $diaryImage = op_api_diary_image($diaryImages[$i]) ?>
          <p><a href="<?php echo $diaryImage['filename'] ?>"><?php echo $diaryImage['imagetag'] ?></a></p>
          <input type="checkbox" name="diary_photo_<?php echo $i ?>_photo_delete" id="diary_photo_<?php echo $i ?>_photo_delete" />
          <label for="diary_photo_<?php echo $i ?>_photo_delete"><?php echo __('remove the current photo') ?></label>
          <?php endif; ?>
          <input type="file" name="diary_photo_<?php echo $i ?>" id="diary_photo_<?php echo $i ?>" />
        </td>
      </tr>
      <?php endfor; ?>
    </table>
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
