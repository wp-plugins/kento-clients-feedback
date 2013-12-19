function tinyplugin() {
return "[doublerainbow-plugin]";
}
(function() {
tinymce.create('tinymce.plugins.kentocf_button_plugin', {
init : function(ed, url){
ed.addButton('kentocf_button_plugin', {
title : 'Add Kento Clients Feedback',
onclick : function() {
var ed = tinyMCE.activeEditor;
ed.focus();
var sel = ed.selection;
var content = sel.getContent();
content='[KentoCF  postcount="3" current="2" bgcolor="#08cd98" ]';
sel.setContent(content);
},
image: url + "/kento-cf.png"
});
},

});
tinymce.PluginManager.add('kentocf_button_plugin', tinymce.plugins.kentocf_button_plugin);
})();

