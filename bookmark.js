// BilibiliDownload 书签v0.0.2
(function(){
    if(location.hostname != "www.bilibili.com" || !$(".v-title > h1").length) {
        alert("然而并没有用: \n只能在bilibili视频页面使用哦");
        return;
    }
    function downloadURL(url,filename) {
        var i = document.createElement("a"), o = document.createEvent("MouseEvent");
        i.href = url;
        i.download = filename;
        i.rel = "noreferrer";
        o.initEvent("click", !0, !0, window, 1, 0, 0, 0, 0, !1, !1, !1, !1, 0, null);
        i.dispatchEvent(o);
    }

    if(typeof window.bd_fi != "boolean") window.bd_fi = !0;
    if(bd_fi) {
        bd_fi = !1;
        $.ajax({
            type: 'GET',
            url: 'http://dynamic.bilibili.download:82/get.php?callback=?',
            data: { url: location.origin+location.pathname }, // no hash
            dataType:"jsonp",
            success: function(url) {
                var filename = $(".v-title > h1").attr("title")+" ";
                filename += location.pathname.substring(7)
                    .replace(/\/|\.html/g,"").replace(/index_(\d+)/g,function(a,b){return "_p"+b});
                console.log(url, filename);
                downloadURL(url, filename);
                bd_fi = !0;
            },
            error: function() {
                alert("有麻烦了\n o(` · ~ · ′。)o 也许是网络问题...");
                bd_fi = !0;
            }
        });
    } else alert("别着急, 正在努力解析中");
})();