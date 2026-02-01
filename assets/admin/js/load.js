(function ($) {
  "use strict";

  $(document).ready(function () {
    //cart item remove code
    $(".cart-remove").on("click", function () {
      $(this).parent().parent().remove();
    });
    //cart item remove code ends

    /*  Bootstrap colorpicker js  */
    //$(".cp").colorpicker();
    // Colorpicker Ends Here

    // IMAGE UPLOADING :)
    $(".img-upload").on("change", function () {
      var imgpath = $(this).parent();
      var file = $(this);
      readURL(this, imgpath);
    });

    function readURL(input, imgpath) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          imgpath.css("background", "url(" + e.target.result + ")");
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
    // IMAGE UPLOADING ENDS :)

    // GENERAL IMAGE UPLOADING :)
    $(".img-upload1").on("change", function () {
      var imgpath = $(this).parent().prev().find("img");
      var file = $(this);
      readURL1(this, imgpath);
    });

    function readURL1(input, imgpath) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          imgpath.attr("src", e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
    // GENERAL IMAGE UPLOADING ENDS :)

    // Text Editor

    // NIC EDITOR :)
    var elementArray = document.getElementsByClassName("nic-edit");
    for (var i = 0; i < elementArray.length; ++i) {
      nicEditors.editors.push(new nicEditor().panelInstance(elementArray[i]));
      $(".nicEdit-panelContain").parent().width("100%");
      $(".nicEdit-panelContain").parent().next().width("98%");
    }
    //]]>
    // NIC EDITOR ENDS :)

    //]]>
    // NIC EDITOR FULL ENDS :)

    // Category Description Character Counter and Validation
    function initCategoryDescriptionValidation() {
      var $descriptionField = $('#category-description');
      if (!$descriptionField.length) return;
      
      var $charCount = $('#char-count');
      var $charWarning = $('.char-warning');
      var maxChars = 2000;
      
      // Function to get plain text length from rich text editor
      function getPlainTextLength() {
        try {
          var editor = nicEditors.findEditor('category-description');
          if (editor) {
            var content = editor.getContent();
            // Strip HTML tags and get plain text length
            var tempDiv = $('<div>').html(content);
            var plainText = tempDiv.text().trim();
            return plainText.length;
          }
        } catch (e) {
          // Fallback to textarea value
        }
        return $descriptionField.val() ? $descriptionField.val().trim().length : 0;
      }
      
      // Update character count
      function updateCharCount() {
        var currentLength = getPlainTextLength();
        if ($charCount.length) {
          $charCount.text(currentLength);
          
          if (currentLength >= maxChars) {
            $charCount.css('color', '#e11d2e');
            if ($charWarning.length) {
              $charWarning.show();
            }
          } else {
            $charCount.css('color', '#6b7280');
            if ($charWarning.length) {
              $charWarning.hide();
            }
          }
        }
      }
      
      // Listen for changes in NicEditor
      setTimeout(function() {
        try {
          var editor = nicEditors.findEditor('category-description');
          if (editor) {
            // Add event listener for content changes
            editor.addEvent('key', function() {
              updateCharCount();
            });
            editor.addEvent('blur', function() {
              updateCharCount();
            });
          }
        } catch (e) {
          console.log('NicEditor not ready yet');
        }
      }, 1000);
      
      // Initial count
      setTimeout(function() {
        updateCharCount();
      }, 1500);
      
      // Also listen to textarea directly (fallback)
      $descriptionField.on('input keyup', function() {
        updateCharCount();
      });
    }

    // Initialize category description validation after page load
    setTimeout(function() {
      initCategoryDescriptionValidation();
    }, 2000);
    // Category Description Validation Ends

    // Check Click :)
    $(".checkclick").on("change", function () {
      if (this.checked) {
        $(this).parent().parent().parent().next().removeClass("showbox");
      } else {
        $(this).parent().parent().parent().next().addClass("showbox");
      }
    });
    // Check Click Ends :)

    // Check Click1 :)
    $(".checkclick1").on("change", function () {
      if (this.checked) {
        $(this)
          .parent()
          .parent()
          .parent()
          .parent()
          .next()
          .removeClass("showbox");
      } else {
        $(this).parent().parent().parent().parent().next().addClass("showbox");
      }
    });
    // Check Click1 Ends :)

    //  Alert Close
    $("button.alert-close").on("click", function () {
      $(this).parent().hide();
    });
  });

  // Drop Down Section Ends
})(jQuery);
