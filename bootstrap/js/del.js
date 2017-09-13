/**
 * Created by kingmax on 16/12/4.
 */
$.fn.extend({
    delButton:  function () {
        $(this).click(function (event) {
            event.preventDefault();
            var href= $(this).attr("href");
            var $tr = $(this).parents("tr");
            if(window.confirm("您确认要删除此记录?")){
                $.get(href, function (data) {
                    if(data==true){
                        $tr.remove();
                    }else{
                        alert("删除失败。");
                    }
                })
            }
        })
    },
    delConfirm: function () {
        $(this).click(function (event) {
            event.preventDefault();
            var href= $(this).attr("href");
            if(window.confirm("您确认要删除此记录?")){
                window.location.href = href;
            }
        })
    }
});