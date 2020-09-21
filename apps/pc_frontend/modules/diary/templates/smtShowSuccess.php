<?php
use_helper('opAsset');
op_smt_use_javascript('/opDiaryPlugin/js/bootstrap-transition.js', 'last');
op_smt_use_stylesheet('/opDiaryPlugin/css/smt-diary.css', 'last');
op_smt_use_javascript('/opDiaryPlugin/js/smt_diary_functions.js', 'last');
op_smt_use_stylesheet('/opDiaryPlugin/css/lightbox.css', 'last');
op_smt_use_javascript('/opDiaryPlugin/js/lightbox.js', 'last');
?>
<script id="diaryEntry" type="text/x-jquery-tmpl">
  <div class="row">
    <div class="gadget_header span12 title">${title}<br />(${$item.getCreatedAt()}の日記)</div>
  </div>

  <div class="row public_flag">
    (${public_flag})
  </div>

  <div class="row images">
    {{each images}}
      <div class="span4"><a href="${$value.filename}" target="_blank" rel="lightbox[diary]">{{html $value.imagetag}}</a></div>
    {{/each}}
  </div>

  <div class="row body">
    <div class="span12">{{html body}}</div>
  </div>

  <div class="row edit">
    {{if editable}}
    <div class="btn-group span3">
      <a href="<?php echo public_path('diary/edit'); ?>/${id}" class="btn"><i class="icon-pencil"></i></a>
      <a href="javascript:void(0)" class="btn" id="deleteEntry"><i class="icon-remove"></i></a>
    </div>
    {{/if}}
  </div>

  <div class="row siblings">
    <div class="span12 center">
      <a class="btn span5 {{if !prev}} disabled{{/if}}" {{if prev}}href="<?php echo public_path('diary/') ?>${prev}"{{/if}}>
      <?php echo __('Previous Diary') ?></a>
      <a class="btn span5{{if !next}} disabled{{/if}}" {{if next}}href="<?php echo public_path('diary/') ?>${next}"{{/if}}>
      <?php echo __('Next Diary') ?></a>
    </div>
  </div>

  {{tmpl "#commentEntry"}}
</script>

<script id="commentEntry" type="text/x-jquery-tmpl">
  <div id="comment">
    <div class="row">
      <div class="gadget_header"><?php echo __('Comment') ?></div>
    </div>
    <!-- //commentForm -->
    <?php if (opToolkit::isSecurePage()): ?>
    <div class="row" id="comment-form">
      <div class="comment-form">
        <form class="comment-form" action="javascript:void(0)">
          {{if public_flag == '<?php echo __('All Users on the Web', array(), 'publicFlags') ?>'}}
          <p class="font10"><?php echo __('Your comment is visible to all users on the Web.') ?></p>
          {{/if}}
          <div id='comment-error' class="row hide"></div>
          <textarea id="commentBody" name="body" placeholder="<?php echo __('Post a diary comment') ?>"></textarea>
          <input type="file" name="comment-image" />
          <input type="submit" name="submit" class="btn btn-primary btn-mini comment-button" id="postComment" value="<?php echo __('Post') ?>" />
        </form>
      </div>
      <div class="comment-form-loader hide">
        <?php echo op_image_tag('ajax-loader.gif', array()) ?>
      </div>
    </div>
    <?php endif; ?>
    <!-- //commentForm end -->
    <div class="row comments" id="comments">
    </div>
  </div>
</script>

<?php include_partial('diaryComment/smtCommentBox', array('target' => 'diary')) ?>

<script type="text/javascript">
var diary_id = <?php echo $id ?>;
var comment_count = 0;
var comment_page = 1;

$(function(){
  var params = getParams('diary_search');
  getEntry(params);

  $('#deleteEntryModal .modal-button').click(function(e) {
    if(e.target.id == 'execute') {
      deleteDiary( getParams('diary_delete') );
    }

    $('#deleteEntryModal').modal('hide');
  });

  $(document).on('click', '#postComment',function() {
    if (!isInputValue('textarea#commentBody')) {
      $('#comment-error').html("<?php echo __('Body is required.') ?>").show();
      return -1;
    }

    toggleSubmitState(['input[type=submit]', '.comment-form-loader']);
    postDiaryComment( getParams('diary_comment_post') );
  });

  $('#deleteCommentModal .modal-button').click(function(e) {
    if(e.target.id == 'execute') {
      deleteDiaryComment( getParams('diary_comment_delete') );
    };

    $('#deleteCommentModal').attr('data-comment-id', '').modal('hide');
  });

  $('#loadmore').click(function() {
    getComments( getParams('diary_comment_search') );
  });

})

</script>

<div id="face" class="row">
  <div class="span2">
    <?php echo link_to(op_image_tag_sf_image($member->getImageFileName(), array('size' => '48x48')), '@diary_list_member?id='.$member->id) ?>
  </div>
  <div class="span8">
    <div class="row face-name"><?php echo __('Diary of %1%', array('%1%' => $member->name)) ?></div>
  </div>
</div>

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
