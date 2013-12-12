function DiaryGadget (target, apiTarget, max, memberId, noEntry) {
  var _template = {
    targetDiv: '#' + target,
    targetEntry: '#' + target + 'Entry',
    readmore: '#' + target + 'Readmore',
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
    target: apiTarget,
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
