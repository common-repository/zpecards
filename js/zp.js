var expr = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{1,200}\.[a-zA-Z]{2,6}$/;

function searchTemplate(page) {
    
    Search = jQuery("#Search").attr("value");
    Width = jQuery("#Width").attr("value");
    Height = jQuery("#Height").attr("value");
    Units = jQuery("#Units").attr("value");
    if (jQuery("#SortBy1").attr("checked")) {
        SortBy = 1;
    } else {
        SortBy = 0;
    }
    jQuery("#loading1").fadeIn() ;
    jQuery("#contentTemplates").empty();
    jQuery.post("../wp-content/plugins/zpecards/ajax/getTemplates"+page+".php", {
        Search: Search,
        Width: Width,
        Height: Height,
        SortBy: SortBy,
        Units: Units
    },
    function(data){
        jQuery("#contentTemplates").html(data) ;
        jQuery("#loading1").fadeOut() ;
        Wrap = jQuery(".zpwrap:first");
        wrapWidth = Wrap.css("width");
        contentMask = jQuery("#contentMask");
        contentMask.css("width",wrapWidth);
    },
    "html"
    );
    return false;
}


function selectOpt(id,cid,optstr,select) {
    opt = parseInt(optstr);
    select.value=0;
    switch (opt) {
        case 0:
            break;
        case 1:
            window.open("http://zetaprints.com/w.aspx?t=" + id, "View Template", "width=900,height=600");
            break;
        case 2:
            jQuery("#feed").val("http://zetaprints.com/?page=template-xml;TemplateID=" + id);
            jQuery("#contentTemplates").fadeOut() ;
            jQuery("#menuSearch").fadeOut();
            break;
        case 3:
            window.open("http://zetaprints.com/w.aspx?c=" + cid, "View Catalog", "width=900,height=600");
            break;
        case 4:
            jQuery("#feed").val("http://zetaprints.com/RssTemplates.aspx?c=" + cid);
            jQuery("#contentTemplates").fadeOut() ;
            jQuery("#menuSearch").fadeOut();
            break;
        default:
            break;
    }
}
function tryTemplate(id) {
    window.open("http://zetaprints.com/w.aspx?t=" + id, "View Template", "width=900,height=600");
}
function findTemplate() {
    jQuery("#menuSearch").fadeIn();
    jQuery("#contentTemplates").fadeIn() ;
}

jQuery(document).ready(function() {
    jQuery("#Search").bind (
        "keydown",
        function(e){
            var evt = e || window.event;
            if (evt.keyCode=='13') {
                evt.stopPropagation;
                searchTemplate('');
                return false;
            }
            return true;
    });
});
