<?php use_helper('Javascript', 'opUtil', 'opAsset') ?>
<script id="diaryMemberEntry" type="text/x-jquery-tmpl">
<div class="row">
  <div class="span3">${$item.getCreatedAt()}</div>
  <div class="span9"><a href="<?php echo public_path('diary') ?>/${id}">${title}</a>
  </div>
</div>
</script>

<script type="text/javascript">
$(function(){
  var params = {
    apiKey: openpne.apiKey,
    target: "<?php echo $target ?>",
    member_id: "<?php echo $member->id ?>",
    limit: 4
  }

  $.getJSON(openpne.apiBase + 'diary/search.json',
    params,
    function(res)
    {
      if (res.data.length > 0)
      {
        var entry = $('#diaryMemberEntry').tmpl(res.data, {
          getCreatedAt: function() {
            var date = this.data.created_at.split(' ')[0].split('-')
            return date[1] + '月' + date[2] + '日';
          }
        }
        );
        $('#diaryMember').append(entry);
        $('#diary-member-readmore').show();
      }
      else
      {
        $('#diaryMember').append("<p><?php echo __('There are no diaries.') ?></p>");
      }
    }
  )
})
</script>

<hr class="toumei" />
<div class="row">
  <div class="gadget_header span12">
    <?php echo 'list_member' == $target ? __('Diary of %1%', array('%1%' => $member->name)) : __('My Diaries') ?>
  </div>
</div>
<hr class="toumei" />
<div id="diaryMember" style="margin-left: 0px;">
</div>

<div class="row hide" id="diary-member-readmore">
<?php echo link_to(__('More'), '@diary_list_member?id='.$member->id, array('class' => 'btn btn-block span11')) ?>
</div>
