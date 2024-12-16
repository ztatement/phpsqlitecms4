<<<<<<< HEAD
$(function() {

$("a[data-delete-confirm]").click(function(e) { e.preventDefault();
                                            message = $(this).data('delete-confirm') ? decodeURIComponent($(this).data('delete-confirm')) : 'Delete?';
                                            $(this).parents("tr").addClass('danger');
                                            $(this).parents("div .comment").addClass('comment-delete');
                                            $(this).parents("div .simple-news").addClass('simple-news-delete');
                                            var confirmed = confirm(decodeURIComponent(message));
                                            if(confirmed) window.location.href = $(this).attr("href") + '&confirmed=true';
                                            $(this).parents("tr").removeClass('danger');
                                            $(this).parents("div .comment").removeClass('comment-delete');
                                            $(this).parents("div .simple-news").removeClass('simple-news-delete'); });

});
=======
$(function() {

$("a[data-delete-confirm]").click(function(e) { e.preventDefault();
                                            message = $(this).data('delete-confirm') ? decodeURIComponent($(this).data('delete-confirm')) : 'Delete?';
                                            $(this).parents("tr").addClass('danger');
                                            $(this).parents("div .comment").addClass('comment-delete');
                                            $(this).parents("div .simple-news").addClass('simple-news-delete');
                                            var confirmed = confirm(decodeURIComponent(message));
                                            if(confirmed) window.location.href = $(this).attr("href") + '&confirmed=true';
                                            $(this).parents("tr").removeClass('danger');
                                            $(this).parents("div .comment").removeClass('comment-delete');
                                            $(this).parents("div .simple-news").removeClass('simple-news-delete'); });

});
>>>>>>> c975f1ffd942608271d45ab3711bcbf9076ebad4
