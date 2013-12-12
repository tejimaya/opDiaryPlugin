$(function() {
  function DiaryList (target, memberId) {
    this.total = 0;
    this.originTotal = 0;

    var _addSuccess = function (res) {
      this.originTotal = res.data_count;
      this.total += res.data.length;
      this.apiParameter.page++
    };

    var _template = {
      loading: '#loading',
      loadmore: '#loadmore',
      diaryEntry: '#diaryEntry',
      noEntry: '#noEntry',
      list: '#list',
    };

    var _render = function (model) {
      _view = this;
      if (model.data.length === 0) {
        _view.noEntry.show();
      }
      else {
        _view.noEntry.hide();
        _view.list.append(_view.diaryEntry.tmpl(model.data, {getCreatedAt: _view.getCreatedAt }));
        (model.originTotal > model.total) ? _view.loadmore.show() : _view.loadmore.hide();
      }
      _view.loading.hide();
    };

    var _apiParameter = {
      target: target,
      member_id: memberId,
      page: 1,
    };

    this.set(_apiParameter, 'diary/search', _template, _render, _addSuccess);
  }
  DiaryList.prototype = Prototype;
  delete(DiaryList.prototype.post);
  delete(DiaryList.prototype.delete);

  // instance
  var diaryList = new DiaryList(target, memberId);

  // update on page load
  diaryList.update();

  // if loadmore clicked, update object
  diaryList.view.loadmore.on('click', function() {
    diaryList.view.loading.show();
    diaryList.view.loadmore.hide();
    diaryList.update();
  });
})
