<script type="text/javascript">
$(function(){
  var params = {
    apiKey: openpne.apiKey,
    target: 'list',
    limit: 4
  }

  $.getJSON(openpne.apiBase + 'diary/search.json',
    params,
    function(res)
    {
      if (res.data.length > 0)
      {
        var entry = $('#diaryListEntry').tmpl(res.data, {
          getCreatedAt: function() {
            var date = this.data.created_at.split(' ')[0].split('-')
            return date[1] + '月' + date[2] + '日';
          }
        }
        );
        $('#diaryList').append(entry);
        $('#diary-list-readmore').show();
      }
      else
      {
        $('#diaryList').append("<p><?php echo __('There are no diaries.') ?></p>");
      }
    }
  )
})
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
<div id="diaryList" style="margin-left: 0px;">
</div>

<div class="row hide" id="diary-list-readmore">
<?php echo link_to(__('More'), '@diary_list', array('class' => 'btn btn-block span11')) ?>
</div>
