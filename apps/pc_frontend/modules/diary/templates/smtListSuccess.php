<?php
use_helper('opAsset');
op_smt_use_stylesheet('/opDiaryPlugin/css/smt-diary.css', 'last');

if ('list_member' == $target || 'list_mine' == $target)
{
  $gadgetTitle = __('Diaries of %1%', array('%1%' => $member->getName()));
}
else
{
  $gadgetTitle = __('Recently Posted Diaries of All');
}
?>

<script id="diaryEntry" type="text/x-jquery-tmpl">
<div class="row entry">
  <div class="span3">
    <a href="${member.profile_url}"><img src="${member.profile_image}" class="rad10" width="57" height="57"></a>
  </div>
  <div class="span9">
    <div>
      <span class="title">${title}</span>
      {{html body_short}}
      <a href="<?php echo public_path('diary') ?>/${id}" class="readmore">続き</a>
    </div>
    <div class="clearfix"></div>
    <div class="row">
      <p class="span3"><a href="${member.profile_url}">{{if member.screen_name}} ${member.screen_name} {{else}} ${member.name} {{/if}}</a></p>
      <p class="span6">${ago}</p>
    </div>
  </div>
</div>
</script>

<script type="text/javascript">
var target = "<?php echo $target ?>";
var id = "<?php echo $id ?>";
var count = 0;
var page = 1;

function getList(params)
{
  $('#loading').show();
  $.getJSON( openpne.apiBase + 'diary/search.json',
    params,
    function(json)
    {
      if (json.data.length === 0)
      {
        $('#noEntry').show();
      }
      else
      {
        var entry = $('#diaryEntry').tmpl(json.data);
        $('#list').append(entry);
        count += json.data.length;
        page++;
      }

      if (count < json.data_count)
      {
        $('#loadmore').show();
      }
      else
      {
        $('#loadmore').hide();
      }

      $('#loading').hide();
    }
  );
}

$(function(){
  var params = {
    apiKey: openpne.apiKey,
    target: target,
  }

  if ('list_member' == params.target) {
    params.member_id = id;
  }

  getList(params);

  $('#loadmore').click(function()
  {
    params.page = page;
    getList(params);
  })
})
</script>

<div class="row">
  <div class="gadget_header span12"><?php echo $gadgetTitle; ?></div>
</div>
<?php if ('list_mine' == $target): ?>
<div class="row">
  <?php echo link_to(__('Post a diary'), '@diary_new', array('class' => 'btn span11')) ?>
</div>
<?php endif; ?>
<div id="list"></div>
<div class="row hide" id="noEntry">
  <div class="center span12">まだ日記はありません</div>
</div>
<div class="row">
  <div id="loading" class="center">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>
<div class="row">
  <button class="span12 btn small hide" id="loadmore"><?php echo __('More'); ?></button>
</div>

