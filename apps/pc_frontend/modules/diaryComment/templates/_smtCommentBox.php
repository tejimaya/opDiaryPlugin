<script id="<?php echo $target ?>Comment" type="text/x-jquery-tmpl">
  <div class="row" id="comment${id}">
    <div class="span11 comment-wrapper">

      <div class="comment-member row">
        <span class="member-image">
          <a href="${member.profile_url}"><img src="${member.profile_image}" alt="${member.name}" /></a>
        </span>
        <span class="member-name">
          <a href="${member.profile_url}">{{if member.screen_name}} ${member.screen_name} {{else}} ${member.name} {{/if}}</a>
        </span>
      </div>

      <div class="clearfix"></div>

      <div class="comment-content">
        <span class="row comment-body">{{html body}}</span>
        <span class="created_at">${ago}</span>
        {{if images.length > 0}}
          {{each images}}
            <span class="image"><a href="${$value.filename}" target="_blank" rel="lightbox[comment]">{{html $value.imagetag}}</a></span>
          {{/each}}
        {{/if}}
        {{if deletable}}
          <div class="comment-control">
            <span class="delete"><a href="javascript:void(0);" class="deleteComment" data-comment-id="${id}"><i class="icon-remove"></i></a></span>
          </div>
        {{/if}}
      </div>

      <div class="clearfix"></div>
    </div>
  </div>
</script>
