function getParams (target) {
  var params = {
    apiKey: openpne.apiKey,
  };

  if ('diary_search' == target) {
    params.target = 'diary';
    params.diary_id = diary_id;
  }
  else if ('diary_list' == target) {
    params.target = 'list';
  }
  else if ('diary_list_mine' == target) {
    params.target = 'list_mine';
  }
  else if ('diary_list_member' == target) {
    params.target = 'list_member';
    params.member_id = member_id || undefined;
  }
  else if ('diary_delete' == target) {
    params.diary_id = diary_id;
  }
  else if ('diary_comment_search' == target) {
    params.diary_id = diary_id;
    params.page = comment_page;
  }
  else if ('diary_comment_post' == target) {
    params.diary_id = diary_id;
    params.body = $('textarea#commentBody').val();
  }
  else if ('diary_comment_delete' == target) {
    params.comment_id = $("#deleteCommentModal").attr('data-comment-id');
  };

  return params;
}

function getEntry (params) {
  var success = function (res) {
    var entry = $('#diaryEntry').tmpl(res.data,
    {
      formatTitle: function ()
      {
        var _date = new Date(this.data.created_at.replace(/-/g,'/'));
        return _date.getMonth()+1 + '月' + _date.getDate() + '日の日記';
      }
    });
    $('#show').html(entry);
    getComments( getParams('diary_comment_search') );
  }

  $('#loading').show();
  ajax({
    url: 'diary/search',
    data: params,
    success: success,
  });
}

function getComments (params) {
  var success = function (res) {
    if (0 == res.data.comments.length)
    {
      $('#loadmore').hide();
    }
    else
    {
      comment_count += res.data.comments.length;
      var comments = $('#diaryComment').tmpl(res.data.comments);
      $('#loadmore').show();
      $('#comments').append(comments);

      if (res.data_count - comment_count == 0)
      {
        $('#loadmore').hide();
      }
    }
    $('#loading').hide();
    comment_page++;
  }

  ajax({
    url: 'diary_comment/search',
    data: params,
    success: success,
  });
}

function deleteDiary (params) {
  var success = function (res) {
      window.location = '/diary/listMember/' + res.data.member.id;
  };

  ajax({
    url: 'diary/delete',
    data: params,
    type: 'POST',
    id: diary_id,
    success: success,
  });
}

function postDiaryComment (params) {
  var success = function (res) {
        $('#comments').prepend($('#diaryComment').tmpl(res.data));
        $('textarea#commentBody').val('');
    };
  var complete = function (res) {
    toggleSubmitState(['input[type=submit]', '.commet-form-loader']);
  };

  ajax({
    url: 'diary_comment/post',
    data: params,
    type: 'POST',
    success: success,
    complete: complete,
  });
}

function deleteDiaryComment (params) {
  var success = function (res) {
    $('#comment'+res.data.comment_id).remove();
  }

  ajax({
    url: 'diary_comment/delete',
    data: params,
    type: 'POST',
    success: success,
  });
}

function ajax (args) {
  $.ajax({
    url: openpne.apiBase + args.url + '.json',
    type: args.type || 'GET',
    data: args.data,
    dataType: 'json',
    success: args.success,
    error: args.error || function (res) { console.log(res); },
    complete: args.complete,
  });
}

function toggleSubmitState (args) {
  for (arg in args) {
    $(arg).toggle();
  }
}
