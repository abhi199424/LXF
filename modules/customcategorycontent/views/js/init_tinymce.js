$(document).ready(function () {
    setTimeout(function(){ 
        console.log('tiny');
        if (typeof tinyMCE !== 'undefined') {
            tinyMCE.init({
                selector: 'textarea[name="category[custom_content]"]',
                menubar: false,
                statusbar: false,
                plugins: 'link image code',
                toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | code',
            });
        }
    }, 4000);
});