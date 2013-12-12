// prototypes

Prototype = {
  data: null,
  apiParameter: {
    apiKey: openpne.apiKey,
    target: '',
  },
  targetUrl: '',
  status: '',
  view: null,

  set: function (apiParameter, url, template, render, addSuccess) {
    this.setApiParameter(apiParameter);
    this.targetUrl = url;
    this.data = null;
    this.status = '';
    this.view = this.getViewInstance(template, render);
    this.addSuccess = addSuccess || undefined;
  },

  setApiParameter: function (apiParameter) {
    for (var i in apiParameter) {
      this.apiParameter[i] = apiParameter[i];
    }
  },

  success: function (res) {
    this.data = res.data;
    this.status = 'success';
    if (typeof this.addSuccess == 'function') {
      this.addSuccess(res);
    }
  },

  error: function (res) {
    console.log(res)
  },

  complete: function () {
    if ('success' == this.status) {
      this.view.render(this);
    }
  },

  update: function () {
    $.ajax({
      url: openpne.apiBase + this.targetUrl + '.json',
      context: this,
      data: this.apiParameter,
      success: this.success,
      error: this.error,
      complete: this.complete,
    });
  },

  post: function () {
    $.ajax({
      url: openpne.apiBase + this.targetUrl + '.json',
      type: 'POST',
      processData: false,
      contentType: false,
      data: this.apiParameter,
      context: this,
      success: this.success,
      error: this.error,
      complete: this.complete,
    });
  },

  delete: function () {
    $.ajax({
      url: openpne.apiBase + this.targetUrl + '.json',
      type: 'POST',
      data: this.apiParameter,
      context: this,
      success: this.success,
      error: this.error,
      complete: this.complete,
    });
  },

  getViewInstance: function (template, render) {
    function View() {}
    View.prototype = ViewPrototype;
    var view = new View();
    view.set(template, render);

    return view;
  }
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
