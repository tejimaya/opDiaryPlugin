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
  else if ('diary_list_friend' == target) {
    params.target = 'list_friend';
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
    var image = $('input[name=comment-image]').val();
    params['comment-iamge'] = image ? image : undefined;

    var form = $('form.comment-form').get()[0];
    var fd = new FormData(form);

    for (i in params)
    {
      fd.append(i, params[i]);
    }

    return fd;
  }
  else if ('diary_comment_delete' == target) {
    params.comment_id = $("#deleteCommentModal").attr('data-comment-id');
  };

  return params;
}

function getEntry (params) {
  var success = function (res) {
    var entry = $('#diaryEntry').tmpl(res.data, { getCreatedAt: getCreatedAt, });
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
      var comments = $('#diaryComment').tmpl(res.data.comments, { getCreatedAt: getCreatedAt });
      $('#comments').append(comments);

      if (res.data_count - comment_count == 0) {
        $('#loadmore').hide();
      }
      else {
        $('#loadmore').show();
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
    var mes = getErrorMessage(res.responseText);
    if (!mes) {
      mes = '日記の作成に失敗しました。';
    }
    alert(mes);
    $('.error').html('<p>' + mes + '</p>').show();

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
    $('#comments').prepend($('#diaryComment').tmpl(res.data, { getCreatedAt: getCreatedAt }));
    $('textarea#commentBody').val('');
    $('input[type=file]').val('');
  };
  var error = function (res) {
    var mes = getErrorMessage(res.responseText);
    if (!mes) {
      mes = '投稿に失敗しました。';
    }
    $('#comment-error').html('<p>' + mes + '</p>').show();
  };
  var complete = function (res) {
    toggleSubmitState(['input[type=submit]', '.comment-form-loader']);
  };

  $('#comment-error').hide();
  $.ajax({
    url: openpne.apiBase + "diary_comment/post.json",
    type: 'POST',
    processData: false,
    contentType: false,
    data: params,
    dataType: 'json',
    success: success,
    error: error,
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

function getErrorMessage (arg) {
  var mes = null;
  if (arg.match('Invalid mime type')) {
    mes = 'ファイル形式が間違っています。';
  }
  else if (arg.match('File is too large')) {
    mes = 'ファイルサイズが大きすぎます。';
  }
  else if (arg.match('invalid title')) {
    mes = 'タイトルが空欄です。';
  }
  else if (arg.match('invalid body')) {
    mes = '本文が空欄です。';
  }
  else if (arg.match('invalid deleteCheck')) {
    mes = '画像を上書き投稿する場合は削除するにチェックを入れてください。';
  }

  return mes;
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

function getCreatedAt (option) {
  if ('ago' == option) {
    if (this.data.ago.length < 10) {
      return this.data.ago;
    }
  }
  var date = this.data.created_at.split(' ')[0].split('-')
  return date[1] + '月' + date[2] + '日';
}

function isInputValue (arg) {
  if (0 >= jQuery.trim($(arg).val()).length) {
    return false;
  }

  return true;
}
