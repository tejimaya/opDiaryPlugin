function diaryGadget (args) {
  this.init(args);
  this.loading.toggle();
};

diaryGadget.prototype = {
  init: function (args) {
    this.targetDiv = $("#"+args.target);
    this.loading = this.targetDiv.children(0);
    this.targetEntry = $('#'+args.target+'Entry');
    this.readmore = $('#'+args.target+'Readmore');
    this.noEntry = args.noEntry || '';

    this.responseData = null;
    this.state = '';
    this.err = {};
  },

  success: function (res) {
    this.responseData = res.data;
    this.state = res.status;
  },

  error: function (res) {
    var er = res.responseText.split(': ');
    this.state = 'error';
    this.err = {code: er[0], message: er[1]};
  },

  complete: function () {
    if ('success' == this.state) {
      this.render(this.responseData);
      this.loading.toggle();
    }
    else {
      this.loading.html('エラーが発生しました。再度読みなおしてください。');
    }
  },

  search: function (params) {
    $.ajax({
      url: openpne.apiBase + 'diary/search.json',
      type: 'GET',
      data: params,
      context: this,
      success: this.success,
      error: this.error || function (res) { console.log(res); },
      complete: this.complete,
    });
  },

  render: function (data) {
    if (data.length > 0) {
      var entry = this.targetEntry.tmpl(data, { getCreatedAt: this.getCreatedAt });
      this.readmore.show();
    }
    this.targetDiv.append(entry ? entry : this.noEntry);
  },

  getCreatedAt: function () {
    var date = this.data.created_at.split(' ')[0].split('-')
    return date[1] + '月' + date[2] + '日';
  },

};
