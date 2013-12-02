<?php
use_helper('opAsset');
op_smt_use_stylesheet('/opDiaryPlugin/css/smt-diary.css', 'last');
op_smt_use_javascript('/opDiaryPlugin/js/smt_diary_functions.js', 'last');

if ('list_member' == $target || 'list_mine' == $target)
{
  $gadgetTitle = __('Diaries of %1%', array('%1%' => $member->getName()));
}
elseif ('list_friend' == $target)
{
  $gadgetTitle = __('Diaries of %my_friend%');
}
else
{
  $gadgetTitle = __('Recently Posted Diaries of All');
}
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
var target = "<?php echo $target ?>";
var member_id = "<?php echo $id ?>";
var count = 0;
var page = 1;

function getList (params) {
  var success = function (json) {
    if (json.data.length === 0) {
      $('#noEntry').show();
    }
    else {
      var entry = $('#diaryEntry').tmpl(json.data, { getCreatedAt: getCreatedAt });
      $('#list').append(entry);
      count += json.data.length;
      page++;
    }

    (count < json.data_count) ? $('#loadmore').show() : $('#loadmore').hide();

    loading.hide();
  }

  var loading = $('#loading');
  loading.show();
  $.getJSON( openpne.apiBase + 'diary/search.json', params, success );
}

$(function() {
  var params = getParams( 'diary_' + target );
  getList(params);

  $('#loadmore').click( function() {
    params.page = page;
    getList(params);
  });
});
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

