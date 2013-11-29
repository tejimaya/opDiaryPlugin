function search (params, target, noEntry) {
  $.getJSON(openpne.apiBase + 'diary/search.json',
    params,
    function(res) {
      var entry;
      if (res.data.length > 0) {
        entry = $('#'+target+'Entry').tmpl(res.data, {
          getCreatedAt: function() {
            var date = this.data.created_at.split(' ')[0].split('-')
            return date[1] + '月' + date[2] + '日';
          }
        });
        $('#'+target+'Readmore').show();
      }

      $('#'+target).append(entry ? entry : noEntry);
    }
  );
}
