/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */


CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here. For example:
     config.language = 'pl';
    // config.uiColor = '#AADC6E';
    // config.protectedSource.push( /<\?[\s\S]*?\?>/g );   // PHP Code
    
    config.filebrowserBrowseUrl = '/js/ckeditor/kcfinder/browse.php?type=files';
    config.filebrowserImageBrowseUrl = '/js/ckeditor/kcfinder/browse.php?type=images';
    config.filebrowserFlashBrowseUrl = '/js/ckeditor/kcfinder/browse.php?type=flash';
    config.filebrowserUploadUrl = '/jsckeditor/kcfinder/upload.php?type=files';
    config.filebrowserImageUploadUrl = '/js/ckeditor/kcfinder/upload.php?type=images';
    config.filebrowserFlashUploadUrl = '/js/ckeditor/kcfinder/upload.php?type=flash';
    config.width = '700px';
    
    config.toolbar =
    [
            { name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
            { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
            { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
            { name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 
            'HiddenField' ] },
            { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
            { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
            '/',
            { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
            { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
            '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
            
            '/',
            { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
            { name: 'colors', items : [ 'TextColor','BGColor' ] },
            { name: 'tools', items : [ 'Maximize', 'ShowBlocks' ] }
    ];  
    /*
    config.toolbar.basic =
    [
            { name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
            { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
            { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
            { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
            { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
            { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
    ];
    
    */
};
