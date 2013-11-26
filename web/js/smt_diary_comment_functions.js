function getParams (target) {
  var params = {
    apiKey: openpne.apiKey,
  };

  if ('diary_search' == target) {
    params.target = 'diary';
    params.diary_id = diary_id;
  }
  else if ('diary_post' == target) {
    var query = $('form').serializeArray(),
    json = {apiKey: openpne.apiKey};
    for (i in query)
    {
      json[query[i].name] = query[i].value
    }

    $('input[type="file"]').each(function() {
      if ($(this).val())
      {
        json[$(this).attr('name')] = $(this).val();
      }
    });

    var form = $('form');
    var fd = new FormData(form[0]);

    for (i in json)
    {
      fd.append(i, json[i]);
    }

    return fd;
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

function postDiary (params) {
  var success = function (res) {
    window.location = '/diary/' + res.data.id;
  }
  var error = function (res) {
    console.log(res);
    var em = res.responseText;
    if (em.match('Invalid mime type'))
    {
      alert('ファイル形式が間違っています。');
    }
    else if (em.match('File is too large'))
    {
      alert('ファイルサイズが大きすぎます。');
    }
    else if (em.match('invalid title'))
    {
      alert('タイトルが空欄です。');
    }
    else if (em.match('invalid body'))
    {
      alert('本文が空欄です。');
    }
    else if (em.match('invalid deleteCheck'))
    {
      alert('画像を上書き投稿する場合は削除するにチェックを入れてください。');
    }
    else
    {
      alert('日記の作成に失敗しました。');
    }

    toggleSubmitState(['#loading', 'input[name=submit]']);
  }

  $.ajax({
    url: openpne.apiBase + "diary/post.json",
    type: 'POST',
    processData: false,
    contentType: false,
    data: params,
    dataType: 'json',
    success: success,
    error: error,
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
    toggleSubmitState(['input[type=submit]', '.comment-form-loader']);
  };

  $('#required').hide();
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
  args.forEach(
    function (element)
    {
      $(element).toggle();
    }
  );
}
