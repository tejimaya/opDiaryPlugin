<?php
use_helper('opAsset');
op_smt_use_javascript('/opDiaryPlugin/js/bootstrap-transition.js', 'last');
op_smt_use_stylesheet('/opDiaryPlugin/css/smt-diary.css', 'last');
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
    <input type="submit" class="btn" id="postComment" value="投稿">
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
      {{if next}}
      <a href="<?php echo public_path('diary') ?>/${next}" class="btn span5">新しい日記</a>
      {{else}}
      <div class="disabled btn span5">新しい日記</div>
      {{/if}}
      {{if prev}}
      <a href="<?php echo public_path('diary') ?>/${prev}" class="btn span5">古い日記</a>
      {{else}}
      <div class="disabled btn span5">古い日記</div>
      {{/if}}
    </div>
  </div>
</script>

<script type="text/javascript">
var diary_id = <?php echo $id ?>;

function getEntry(params)
{
  params.diary_id = diary_id;
  $('#loading').show();
  $.getJSON( openpne.apiBase + 'diary/search.json',
    params,
    function(json)
    {
      var entry = $('#diaryEntry').tmpl(json.data,
      {
        formatTitle: function()
        {
          var _date = new Date(this.data.created_at.replace(/-/g,'/'));
          return _date.getMonth()+1 + '月' + _date.getDate() + '日の日記';
        }
      });
      $('#show').html(entry);

      var params = {
        apiKey: openpne.apiKey,
        diary_id: diary_id
      }
      $.getJSON( openpne.apiBase + 'diary_comment/search.json',
        params,
        function(res)
        {
          var comments = $('#diaryComment').tmpl(res.data.comments);
          $('#comments').html(comments);
          $('#loading').hide();
        }
      );

    }
  );

}

$(function(){
  var params = {
    apiKey: openpne.apiKey,
    target: 'diary',
    diary_id: diary_id,
  }
  getEntry(params);

  $('#deleteEntryModal .modal-button').click(function(e){
    if(e.target.id == 'execute')
    {
      var params = {
        apiKey: openpne.apiKey,
        id: diary_id,
      };

      $.post(openpne.apiBase + "diary/delete.json",
        params,
        'json'
      )
      .success(
        function(res)
        {
          window.location = '/diary/listMember/' + res.data.member.id;
        }
      )
      .error(
        function(res)
        {
          console.log(res);
        }
      )
    }
    else
    {
      $('#deleteEntryModal').modal('hide');
    };
  })

  $(document).on('click', '#postComment',function(){
    $('input[name=submit]').toggle();
    var params = {
      apiKey: openpne.apiKey,
      diary_id: diary_id,
      body: $('textarea#commentBody').val()
    };

    $.post(openpne.apiBase + "diary_comment/post.json",
      params,
      'json'
    )
    .success(
      function(res)
      {
        $('#comments').append($('#diaryComment').tmpl(res.data));
        $('textarea#commentBody').val('');
      }
    )
    .error(
      function(res)
      {
        console.log(res);
      }
    )
    .complete(
      function(res)
      {
        $('input[name=submit]').toggle();
      }
    );
  })

  $('#deleteCommentModal .modal-button').click(function(e){
    if(e.target.id == 'execute')
    {
      var params = {
        apiKey: openpne.apiKey,
        id: $("#deleteCommentModal").attr('data-comment-id'),
      };

      $.post(openpne.apiBase + "diary_comment/delete.json",
        params,
        'json'
      )
      .success(
        function(res)
        {
          $('#comment'+res.data.id).remove();
        }
      )
      .error(
        function(res)
        {
          console.log(res);
        }
      )
      .complete(
        function(res)
        {
          $('#deleteCommentModal').attr('data-comment-id', '').modal('hide');
        }
      );
    }
    else
    {
      $('#deleteCommentModal').attr('data-comment-id', '').modal('hide');
    };
  });
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
<?php include_partial('smtModal') ?>
