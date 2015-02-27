/**
 * Created by Mischenko Ilya on 22.02.2015.
 */

$(function () {
    $("body").on("click", '.like', function () {
        var $this = $(this);
        var status = $this.attr("data-status");
        var idTarget = $this.attr('data-target-id');
        var typeLike = $this.attr('data-type-like');
        if (status == 'unlike') {
            $.post('/like', {'data': {'idTarget':idTarget, 'typeLike':typeLike}})
                .done(function (data) {
                    if (data.response) {
                        $this.attr('data-target-id', idTarget);
                        $this.attr('data-type-like', typeLike);
                        $this.attr('data-status', 'like');
                        $this.removeClass('btn-success');
                        $this.addClass('btn-danger');
                        $this.html('<span class="glyphicon glyphicon-thumbs-down"></span> unLike')

                    }
                });
        } else if (status == 'like') {
            $.post('/unlike',{'data': {'idTarget':idTarget, 'typeLike':typeLike}})
                .done(function(data) {
                    if (data.response) {
                        $this.attr('data-target-id', idTarget);
                        $this.attr('data-type-like', typeLike);
                        $this.attr('data-status', 'unlike');
                        $this.removeClass('btn-danger');
                        $this.addClass('btn-success');
                        $this.html('<span class="glyphicon glyphicon-thumbs-up"></span> Like')
                    }
                });
        }

    });
});