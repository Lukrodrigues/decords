$(document).ready(function(){
   $("#get-data-form").submit(function(e){
      var content = tinymce.get("texteditor").getContent();
      $("#data-container").php(content);
        
        return false;
    
	});


  });