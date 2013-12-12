function DiaryGadget (target, max, memberId, noEntry) {
  var _template = {
    targetDiv: '#diary_' + target,
    targetEntry: '#diary_' + target + '_entry',
    readmore: '#diary_' + target + '_readmore',
  };

  var _render = function(model) {
    var _view = this
    if (model.data.length > 0) {
      var entry = _view.targetEntry.tmpl(model.data, { getCreatedAt: _view.getCreatedAt });
      _view.readmore.show();
    }
    _view.targetDiv.append(entry ? entry : _view.noEntry);
    _view.loading.hide();
  };

  var _apiParameter = {
    target: target,
    limit: max,
    member_id: memberId || undefined,
  };

  this.set(_apiParameter, 'diary/search', _template, _render);
  this.view.loading = this.view.targetDiv.children(0);
  this.noEntry = noEntry;
}
DiaryGadget.prototype = Prototype;
delete(DiaryGadget.prototype.post);
delete(DiaryGadget.prototype.delete);
