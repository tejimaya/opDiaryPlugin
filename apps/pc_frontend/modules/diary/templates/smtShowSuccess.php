<?php
use_helper('opAsset');
op_smt_use_javascript('/opDiaryPlugin/js/bootstrap-transition.js', 'last');
op_smt_use_stylesheet('/opDiaryPlugin/css/smt-diary.css', 'last');
op_smt_use_javascript('/opDiaryPlugin/js/smt_diary_comment_functions.js', 'last');
?>

<script id="diaryEntry" type="text/x-jquery-tmpl">
  <div class="row">
    <div class="gadget_header span12">${$item.formatTitle()}</div>
  </div>
  <div class="row">
    {{if editable}}
    <h3 class="span9">${title}</h3>
    <div class="btn-group span3">
      <a href="<?php echo public_path('diary/edit'); ?>/${id}" class="btn"><i class="icon-pencil"></i></a>
      <a href="javascript:void(0)" class="btn" id="deleteEntry"><i class="icon-remove"></i></a>
    </div>
    {{else}}
    <h3 class="span12">${title}</h3>
    {{/if}}
  </div>
  <div class="row images">
    {{each images}}
      <div class="span4"><a href="${$value.filename}" target="_blank">{{html $value.imagetag}}</a></div>
    {{/each}}
  </div>
  <div class="row body">
    <div class="span12">{{html body}}</div>
  </div>
  {{tmpl "#diarySiblings"}}
  <div class="row" id="comments">
  </div>
  <div class="row" id="commentForm">
    <div class="span1">
    &nbsp;
    </div>
    <textarea id="commentBody"></textarea>
    <div class="commentForm">
    <input type="submit" name="submit" class="btn btn-primary btn-mini comment-button" id="postComment" value="投稿" />
    </div>
    <div class="comment-form-loader hide">
      <?php echo op_image_tag('ajax-loader.gif', array()) ?>
    </div>
  </div>
  {{tmpl "#diarySiblings"}}
</script>

<script id="diaryComment" type="text/x-jquery-tmpl">
  <div class="row" id="comment${id}">
    <div class="span1">
      &nbsp;
    </div>
    <div class="span3">
      <a href="${member.profile_url}"><img src="${member.profile_image}" class="rad10" width="57" height="57"></a>
    </div>
    <div class="span8">
      <div>
        <a href="${member.profile_url}">{{if member.screen_name}} ${member.screen_name} {{else}} ${member.name} {{/if}}</a>
        {{html body}}
      </div>
      <div class="row">
        <span>${ago}</span>
        {{if deletable}}
        <a href="javascript:void(0);" class="deleteComment" data-comment-id="${id}"><i class="icon-remove"></i></a>
        {{/if}}
      </div>
      <div class="images center">
        {{each images}}
          <div class="span3"><a href="${$value.filename}" target="_blank">{{html $value.imagetag}}</a></div>
        {{/each}}
      </div>
    </div>
  </div>
</script>

<script id="diarySiblings" type="text/x-jquery-tmpl">
  <div class="row siblings">
    <div class="span12 center">
      {{if prev}}
      <a href="<?php echo public_path('diary') ?>/${prev}" class="btn span5"><?php echo __('Previous Diary') ?></a>
      {{else}}
      <div class="disabled btn span5"><?php echo __('Previous Diary') ?></div>
      {{/if}}
      {{if next}}
      <a href="<?php echo public_path('diary') ?>/${next}" class="btn span5"><?php echo __('Next Diary') ?></a>
      {{else}}
      <div class="disabled btn span5"><?php echo __('Next Diary') ?></div>
      {{/if}}
    </div>
  </div>
</script>

<script type="text/javascript">
var diary_id = <?php echo $id ?>;
var comment_count = 0;
var comment_page = 1;

$(function(){
  var params = getParams('diary_search');
  getEntry(params);

  $('#deleteEntryModal .modal-button').click(function(e){
    if(e.target.id == 'execute')
    {
      deleteDiary( getParams('diary_delete') );
    };

    $('#deleteEntryModal').modal('hide');
  })

  $(document).on('click', '#postComment',function(){
    toggleSubmitState(['input[type=submit]', '.commet-form-loader']);
    postDiaryComment( getParams('diary_comment_post') );
  })

  $('#deleteCommentModal .modal-button').click(function(e){
    if(e.target.id == 'execute')
    {
      deleteDiaryComment( getParams('diary_comment_delete') );
    };

    $('#deleteCommentModal').attr('data-comment-id', '').modal('hide');
  });

  $('#loadmore').click(function()
  {
    getComments(getParams('diary_comment_search'));
  })
})

</script>
<div class="row">
  <div id="show"></div>
</div>
<div class="row">
  <div id="loading" class="center">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>
<div class="row">
  <button class="span12 btn small hide" id="loadmore"><?php echo __('More'); ?></button>
</div>
<?php include_partial('smtModal') ?>
