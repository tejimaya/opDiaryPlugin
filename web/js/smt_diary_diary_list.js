DiaryListPrototype = {

  data: null,
  apiParameter: {
    apiKey: openpne.apiKey,
    target: '',
    member_id: '',
    page: 1,
  },
  total: 0,
  originTotal: 0,
  status: '',

  success: function (res) {
    this.data = res.data;
    this.originTotal = res.data_count;
    this.total += this.data.length;
    this.apiParameter.page++;
    this.status = 'success';
  },

  set: function (target, memberId, view) {
    this.setApiParameter({
      target: target,
      member_id: memberId,
    });
    this.data = null;
    this.originTotal = 0,
    this.total = 0;
    this.status = '';
    this.view = view;
  },

  setApiParameter: function (params) {
    for (var i in params) {
      this.apiParameter[i] = params[i]
    }
  },

  complete: function () {
    if ('success' == this.status) {
      this.view.render(this);
    }
  },

  update: function () {
    $.ajax({
      url: openpne.apiBase + 'diary/search.json',
      type: 'GET',
      context: this,
      data: this.apiParameter,
      success: this.success,
      complete: this.complete,
    });
  },
};

ViewPrototype = {
  set: function (templ, render) {
    for (var i in templ) {
      this[i] = $(templ[i]);
    };
    this.render = render;
  },

  render: null,

  getCreatedAt: function () {
    var date = this.data.created_at.split(' ')[0].split('-')
    return date[1] + '月' + date[2] + '日';
  },
};

function View () {}

function DiaryList (target, memberId) {
  var template = {
    loading: '#loading',
    loadmore: '#loadmore',
    diaryEntry: '#diaryEntry',
    noEntry: '#noEntry',
    list: '#list',
  };

  var render = function (model) {
    if (model.data.length === 0) {
      this.noEntry.show();
    }
    else {
      this.noEntry.hide();
      this.list.append(this.diaryEntry.tmpl(model.data, {getCreatedAt: this.getCreatedAt }));
      (model.originTotal > model.total) ? this.loadmore.show() : this.loadmore.hide();
    }
    this.loading.hide();
  }

  var view = new View();
  view.set(template, render);

  this.set(target, memberId, view);
}

DiaryList.prototype = DiaryListPrototype;
View.prototype = ViewPrototype;
